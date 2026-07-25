<?php

namespace App\Console\Commands;

use App\Models\CouncilDecision;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImportMeclisFromResmiDuyurular extends Command
{
    protected $signature = 'import:meclis-resmi
        {--year=2026 : Meclis yılı}
        {--source=https://kirklareli.bel.tr : Kaynak site}
        {--pages=2 : Resmi duyurular sayfa adedi}
        {--dry-run : Sadece listele, kaydetme}';

    protected $description = 'Resmi duyurular altındaki meclis gündem/karar/komisyon PDF’lerini aylık CouncilDecision kayıtlarına aktarır';

    /** @var array<string, int> */
    private const MONTHS = [
        'ocak' => 1,
        'şubat' => 2,
        'subat' => 2,
        'mart' => 3,
        'nisan' => 4,
        'mayıs' => 5,
        'mayis' => 5,
        'haziran' => 6,
        'temmuz' => 7,
        'ağustos' => 8,
        'agustos' => 8,
        'eylül' => 9,
        'eylul' => 9,
        'ekim' => 10,
        'kasım' => 11,
        'kasim' => 11,
        'aralık' => 12,
        'aralik' => 12,
    ];

    /** @var array<int, string> */
    private const MONTH_LABELS = [
        1 => 'Ocak', 2 => 'Şubat', 3 => 'Mart', 4 => 'Nisan',
        5 => 'Mayıs', 6 => 'Haziran', 7 => 'Temmuz', 8 => 'Ağustos',
        9 => 'Eylül', 10 => 'Ekim', 11 => 'Kasım', 12 => 'Aralık',
    ];

    public function handle(): int
    {
        $year = (int) $this->option('year');
        $source = rtrim((string) $this->option('source'), '/');
        $pages = max(1, (int) $this->option('pages'));
        $dryRun = (bool) $this->option('dry-run');

        $docs = [];
        for ($page = 1; $page <= $pages; $page++) {
            $html = $this->fetchHtml("{$source}/resmi-duyurular/{$page}");
            if ($html === '') {
                $this->warn("Sayfa alınamadı: resmi-duyurular/{$page}");
                continue;
            }
            $found = $this->extractMeclisLinks($html, $year);
            $this->line("resmi-duyurular/{$page}: ".count($found).' meclis PDF');
            $docs = array_merge($docs, $found);
        }

        $docs = collect($docs)->unique('url')->values()->all();
        if ($docs === []) {
            $this->error("{$year} için meclis PDF bulunamadı.");

            return self::FAILURE;
        }

        $grouped = $this->groupDocuments($docs, $year);
        $this->info('Oturum grubu: '.count($grouped));

        $saved = 0;
        foreach ($grouped as $group) {
            $this->line("→ {$group['title']} (gündem=".($group['agenda_url'] ? 'var' : 'yok').', karar='.($group['decision_url'] ? 'var' : 'yok').', komisyon='.($group['commission_url'] ? 'var' : 'yok').')');

            if ($dryRun) {
                continue;
            }

            $agenda = $this->downloadToMeclis($group['agenda_url'], $year, $group['month_num'], 'gundem', $group['session_key']);
            $decision = $this->downloadToMeclis($group['decision_url'], $year, $group['month_num'], 'karar', $group['session_key']);
            $commission = $this->downloadToMeclis($group['commission_url'], $year, $group['month_num'], 'komisyon', $group['session_key']);

            CouncilDecision::updateOrCreate(
                [
                    'year' => $year,
                    'month' => $group['month_label'],
                    'title' => $group['title'],
                ],
                [
                    'meeting_date' => $group['meeting_date'],
                    'agenda_file' => $agenda,
                    'decision_file' => $decision,
                    'commission_file' => $commission,
                ]
            );
            $saved++;
        }

        $this->info($dryRun ? 'Dry-run tamamlandı.' : "Kaydedilen oturum: {$saved}");

        return self::SUCCESS;
    }

    /**
     * @return list<array{title: string, url: string, type: string, month_num: ?int, session_key: string}>
     */
    private function extractMeclisLinks(string $html, int $year): array
    {
        if (! preg_match_all(
            '/<a href="([^"]+)"[^>]*class="[^"]*calling__link[^"]*"[^>]*>.*?<span>(.*?)<\/span>/su',
            $html,
            $matches,
            PREG_SET_ORDER
        )) {
            return [];
        }

        $out = [];
        foreach ($matches as $match) {
            $url = html_entity_decode($match[1], ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $title = trim(strip_tags(html_entity_decode($match[2], ENT_QUOTES | ENT_HTML5, 'UTF-8')));
            $title = preg_replace('/\s+/u', ' ', $title) ?? $title;

            if (! $this->isMonthlyMeclisDocument($title, $year)) {
                continue;
            }

            $type = $this->detectDocType($title);
            if ($type === null) {
                continue;
            }

            $out[] = [
                'title' => $title,
                'url' => $url,
                'type' => $type,
                'month_num' => $this->detectMonth($title),
                'session_key' => $this->detectSessionKey($title),
            ];
        }

        return $out;
    }

    private function isMonthlyMeclisDocument(string $title, int $year): bool
    {
        $t = Str::lower($this->trLower($title));

        if (! str_contains($t, (string) $year)) {
            return false;
        }

        if (! str_contains($t, 'meclis')) {
            return false;
        }

        // İmar / onay ilanları meclis aylık evrak paketi değil
        if (str_contains($t, 'ile onaylanan')
            || str_contains($t, 'imar plan')
            || str_contains($t, 'itiraz')
            || str_contains($t, 'parsel')) {
            return false;
        }

        return (bool) preg_match('/gündem|gundem|karar tutana|komisyon|toplantı karar|toplanti karar/u', $t);
    }

    private function detectDocType(string $title): ?string
    {
        $t = Str::lower($this->trLower($title));

        if (preg_match('/gündem|gundem/u', $t)) {
            return 'agenda';
        }
        if (preg_match('/komisyon/u', $t)) {
            return 'commission';
        }
        if (preg_match('/karar tutana|toplantı karar|toplanti karar|kararları|kararlari/u', $t)) {
            return 'decision';
        }

        return null;
    }

    private function detectMonth(string $title): ?int
    {
        $t = Str::lower($this->trLower($title));
        foreach (self::MONTHS as $name => $num) {
            if (preg_match('/\b'.preg_quote($name, '/').'\b/u', $t)) {
                return $num;
            }
        }

        return null;
    }

    private function detectSessionKey(string $title): string
    {
        $t = Str::lower($this->trLower($title));

        if (preg_match('/(\d+)\.\s*olağanüstü|(\d+)\.\s*olaganustu/u', $t, $m)) {
            $n = $m[1] !== '' ? $m[1] : $m[2];

            return 'olaganustu-'.$n;
        }
        if (str_contains($t, 'olağanüstü') || str_contains($t, 'olaganustu')) {
            return 'olaganustu';
        }
        if (preg_match('/(\d+)\.\s*birleşim|(\d+)\.\s*birlesim/u', $t, $m)) {
            $n = $m[1] !== '' ? $m[1] : $m[2];

            return 'birlesim-'.$n;
        }

        return 'aylik';
    }

    /**
     * @param  list<array{title: string, url: string, type: string, month_num: ?int, session_key: string}>  $docs
     * @return list<array{title: string, month_label: string, month_num: int, session_key: string, meeting_date: string, agenda_url: ?string, decision_url: ?string, commission_url: ?string}>
     */
    private function groupDocuments(array $docs, int $year): array
    {
        $groups = [];

        foreach ($docs as $doc) {
            $monthNum = $doc['month_num'] ?? 0;
            if ($monthNum < 1 || $monthNum > 12) {
                // Olağanüstü ay yoksa Temmuz varsayma; atla veya yıl başına koy
                if ($doc['session_key'] === 'aylik') {
                    $this->warn('Ay çıkarılamadı, atlandı: '.$doc['title']);
                    continue;
                }
                // Ay belirtilmeyen olağanüstü oturumlar (liste Temmuz civarında)
                $monthNum = 7;
            }

            $key = $year.'-'.$monthNum.'-'.$doc['session_key'];
            if (! isset($groups[$key])) {
                $monthLabel = self::MONTH_LABELS[$monthNum];
                $title = $this->buildSessionTitle($year, $monthLabel, $doc['session_key']);
                $groups[$key] = [
                    'title' => $title,
                    'month_label' => $monthLabel,
                    'month_num' => $monthNum,
                    'session_key' => $doc['session_key'],
                    'meeting_date' => Carbon::create($year, $monthNum, 15)->toDateString(),
                    'agenda_url' => null,
                    'decision_url' => null,
                    'commission_url' => null,
                ];
            }

            $field = match ($doc['type']) {
                'agenda' => 'agenda_url',
                'decision' => 'decision_url',
                'commission' => 'commission_url',
                default => null,
            };
            if ($field) {
                // Keep first found; later overrides same type if empty
                if (empty($groups[$key][$field])) {
                    $groups[$key][$field] = $doc['url'];
                }
            }
        }

        // Sort by month then session
        uasort($groups, function ($a, $b) {
            return [$a['month_num'], $a['session_key']] <=> [$b['month_num'], $b['session_key']];
        });

        return array_values($groups);
    }

    private function buildSessionTitle(int $year, string $monthLabel, string $sessionKey): string
    {
        if (str_starts_with($sessionKey, 'olaganustu')) {
            $n = Str::after($sessionKey, 'olaganustu-');
            if ($n !== '' && $n !== $sessionKey) {
                return "{$year} {$monthLabel} Ayı {$n}. Olağanüstü Meclis Toplantısı";
            }

            return "{$year} {$monthLabel} Ayı Olağanüstü Meclis Toplantısı";
        }
        if (str_starts_with($sessionKey, 'birlesim-')) {
            $n = Str::after($sessionKey, 'birlesim-');

            return "{$year} {$monthLabel} Ayı {$n}. Birleşim Meclis Evrakları";
        }

        return "{$year} {$monthLabel} Ayı Meclis Evrakları";
    }

    private function downloadToMeclis(?string $url, int $year, int $monthNum, string $kind, string $sessionKey): ?string
    {
        if (! filled($url)) {
            return null;
        }

        try {
            $response = Http::timeout(120)
                ->withHeaders(['User-Agent' => 'Mozilla/5.0 (compatible; KirklareliImporter/1.0)'])
                ->retry(2, 800)
                ->get($url);

            if (! $response->successful()) {
                $this->warn("İndirilemedi ({$response->status()}): {$url}");

                return null;
            }

            $ext = str_contains(Str::lower($url), '.zip') ? 'zip' : 'pdf';
            $dir = "meclis/{$kind}";
            $filename = sprintf(
                '%d-%02d-%s-%s.%s',
                $year,
                $monthNum,
                $sessionKey,
                $kind,
                $ext
            );
            $path = $dir.'/'.$filename;
            Storage::disk('public')->put($path, $response->body());

            return $path;
        } catch (\Throwable $e) {
            $this->warn("İndirme hatası: {$url} — {$e->getMessage()}");

            return null;
        }
    }

    private function fetchHtml(string $url): string
    {
        try {
            $response = Http::timeout(45)
                ->withHeaders(['User-Agent' => 'Mozilla/5.0 (compatible; KirklareliImporter/1.0)'])
                ->get($url);

            return $response->successful() ? $response->body() : '';
        } catch (\Throwable) {
            return '';
        }
    }

    private function trLower(string $value): string
    {
        return strtr($value, [
            'I' => 'ı',
            'İ' => 'i',
            'Ş' => 'ş',
            'Ğ' => 'ğ',
            'Ü' => 'ü',
            'Ö' => 'ö',
            'Ç' => 'ç',
        ]);
    }
}

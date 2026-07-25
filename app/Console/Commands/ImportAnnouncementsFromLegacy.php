<?php

namespace App\Console\Commands;

use App\Models\Announcement;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImportAnnouncementsFromLegacy extends Command
{
    protected $signature = 'import:duyurular
        {--source=https://kirklareli.bel.tr : Kaynak site}
        {--api=https://api.kirklarelibelediyesi.com : Duyuru API kökü}
        {--only= : Sadece tip: duyuru|resmi|ihale}
        {--limit=0 : Maksimum yeni kayıt (0 = limitsiz)}
        {--since-id=0 : Genel duyurularda yalnızca bu id’den yenileri dene (0 = tüm liste, mevcutlar atlanır)}
        {--fix-dates : Mevcut kayıtlarda yayın tarihini API’den güncelle (yeni import yapma)}
        {--dry-run : Sadece listele, kaydetme}';

    protected $description = 'Eski siteden genel / resmi / ihale duyurularını Announcement tablosuna aktarır';

    private string $apiBase = 'https://api.kirklarelibelediyesi.com';

    /** @var array<string, array{date: string, created_at: ?string, unpublished_at: ?string}> */
    private array $fileDateCache = [];

    public function handle(): int
    {
        $this->apiBase = rtrim((string) $this->option('api'), '/');

        if ($this->option('fix-dates')) {
            return $this->fixDatesFromApi((bool) $this->option('dry-run'));
        }

        $source = rtrim((string) $this->option('source'), '/');
        $dryRun = (bool) $this->option('dry-run');
        $limit = max(0, (int) $this->option('limit'));
        $only = strtolower(trim((string) $this->option('only')));

        $created = 0;
        $skipped = 0;
        $remaining = $limit > 0 ? $limit : null;

        if ($only === '' || $only === 'duyuru') {
            [$c, $s] = $this->importGeneral($source, $dryRun, $remaining);
            $created += $c;
            $skipped += $s;
            if ($remaining !== null) {
                $remaining = max(0, $remaining - $c);
            }
        }

        if (($only === '' || $only === 'ihale') && ($remaining === null || $remaining > 0)) {
            $this->warmFileDateCache();
            [$c, $s] = $this->importPdfList(
                type: 'ihale',
                listUrls: [$source.'/'.rawurlencode('ihale-duyuruları').'/1'],
                dryRun: $dryRun,
                limit: $remaining,
            );
            $created += $c;
            $skipped += $s;
            if ($remaining !== null) {
                $remaining = max(0, $remaining - $c);
            }
        }

        if (($only === '' || $only === 'resmi') && ($remaining === null || $remaining > 0)) {
            $this->warmFileDateCache();
            [$c, $s] = $this->importPdfList(
                type: 'resmi',
                listUrls: [
                    $source.'/resmi-duyurular/1',
                    $source.'/resmi-duyurular/2',
                ],
                dryRun: $dryRun,
                limit: $remaining,
            );
            $created += $c;
            $skipped += $s;
        }

        $this->info($dryRun
            ? "Dry-run: eklenecek ~{$created}, atlanacak {$skipped}"
            : "Tamamlandı: yeni {$created}, atlanan {$skipped}");

        return self::SUCCESS;
    }

    private function fixDatesFromApi(bool $dryRun): int
    {
        $this->info('=== Yayın tarihlerini API’den güncelle ===');

        $byLegacy = 0;
        $byFile = 0;
        $missing = 0;
        $unchanged = 0;

        // 1) Genel duyurular: /duyurular/{id}/
        $legacyIds = [];
        Announcement::query()
            ->where('content', 'like', '%/duyurular/%')
            ->orderBy('id')
            ->chunkById(100, function ($rows) use (&$legacyIds) {
                foreach ($rows as $row) {
                    if (preg_match('#/duyurular/(\d+)/#', (string) $row->content, $m)) {
                        $legacyIds[(int) $m[1]][] = $row->id;
                    }
                }
            });

        $this->line('Legacy id’li kayıt: '.array_sum(array_map('count', $legacyIds)).' ('.count($legacyIds).' id)');
        $metaById = $this->fetchAnnouncementMetaMany(array_keys($legacyIds));

        foreach ($legacyIds as $legacyId => $rowIds) {
            $meta = $metaById[$legacyId] ?? null;
            if ($meta === null || $meta['date'] === null) {
                $missing += count($rowIds);

                continue;
            }
            foreach ($rowIds as $rowId) {
                $result = $this->applyPublishDates($rowId, $meta, $dryRun);
                if ($result === 'updated') {
                    $byLegacy++;
                } elseif ($result === 'unchanged') {
                    $unchanged++;
                }
            }
        }

        // 2) PDF duyurular: Dokuman/{hash}.pdf
        $this->warmFileDateCache();
        Announcement::query()
            ->where(function ($q) {
                $q->where('content', 'like', '%/Dokuman/%')
                    ->orWhere('content', 'like', '%/dokuman/%')
                    ->orWhere('content', 'like', '%/ilanlar/%');
            })
            ->orderBy('id')
            ->chunkById(100, function ($rows) use ($dryRun, &$byFile, &$missing, &$unchanged) {
                foreach ($rows as $row) {
                    $basename = $this->extractPdfBasename((string) $row->content);
                    if ($basename === null) {
                        $missing++;

                        continue;
                    }
                    $meta = $this->fileDateCache[Str::lower($basename)] ?? null;
                    if ($meta === null || ($meta['date'] ?? null) === null) {
                        $missing++;

                        continue;
                    }
                    $result = $this->applyPublishDates((int) $row->id, $meta, $dryRun);
                    if ($result === 'updated') {
                        $byFile++;
                    } elseif ($result === 'unchanged') {
                        $unchanged++;
                    }
                }
            });

        $this->info($dryRun
            ? "Dry-run: legacy≈{$byLegacy}, pdf≈{$byFile}, eksik {$missing}, aynı {$unchanged}"
            : "Güncellenen: legacy {$byLegacy}, pdf {$byFile}; eksik {$missing}; değişmeyen {$unchanged}");

        return self::SUCCESS;
    }

    /**
     * @param  array{date: ?string, created_at: ?string, unpublished_at: ?string}  $meta
     */
    private function applyPublishDates(int $id, array $meta, bool $dryRun): string
    {
        $row = Announcement::query()->find($id);
        if (! $row || $meta['date'] === null) {
            return 'missing';
        }

        $date = $meta['date'];
        $created = $meta['created_at'] ?? $date.' 00:00:00';
        $unpublished = $meta['unpublished_at'] ?? null;

        $same = $row->date?->toDateString() === $date
            && $row->published_at?->toDateString() === $date
            && ($row->unpublished_at?->toDateString() ?? null) === $unpublished;

        if ($same) {
            return 'unchanged';
        }

        $this->line("  #{$id} {$row->date?->toDateString()} → {$date} | ".Str::limit((string) $row->title, 50));

        if ($dryRun) {
            return 'updated';
        }

        $row->timestamps = false;
        $row->date = $date;
        $row->published_at = $date;
        $row->unpublished_at = $unpublished;
        $row->created_at = $created;
        $row->updated_at = $created;
        $row->save();

        return 'updated';
    }

    /**
     * @return array{0: int, 1: int}
     */
    private function importGeneral(string $source, bool $dryRun, ?int $limit): array
    {
        $this->info('=== Genel duyurular (tum-duyurular) ===');
        $html = $this->fetchHtml($source.'/tum-duyurular');
        if ($html === '') {
            $this->error('tum-duyurular alınamadı');

            return [0, 0];
        }

        if (! preg_match_all('#href="(/duyurular/(\d+)/[^"]+)"#', $html, $matches, PREG_SET_ORDER)) {
            $this->warn('Liste boş');

            return [0, 0];
        }

        $items = [];
        foreach ($matches as $m) {
            $id = (int) $m[2];
            $items[$id] = $m[1];
        }
        krsort($items);

        $sinceId = max(0, (int) $this->option('since-id'));
        $knownIds = $this->importedLegacyIds();
        $this->line('Listede '.count($items).' duyuru, DB’de bilinen legacy id: '.count($knownIds)
            .($sinceId > 0 ? ", eşik > {$sinceId}" : ''));

        $created = 0;
        $skipped = 0;

        foreach ($items as $legacyId => $path) {
            if ($sinceId > 0 && $legacyId <= $sinceId) {
                $skipped++;

                continue;
            }

            if (isset($knownIds[$legacyId])) {
                $skipped++;

                continue;
            }

            if ($limit !== null && $created >= $limit) {
                break;
            }

            $detailUrl = $source.$path;
            $this->line("→ [{$legacyId}] {$detailUrl}");

            if ($dryRun) {
                $created++;

                continue;
            }

            $detailHtml = $this->fetchHtml($detailUrl);
            if ($detailHtml === '') {
                $this->warn("  detay alınamadı: {$detailUrl}");
                $skipped++;

                continue;
            }

            $parsed = $this->parseGeneralDetail($detailHtml, $detailUrl);
            if ($parsed['title'] === '') {
                $this->warn('  başlık yok, atlandı');
                $skipped++;

                continue;
            }

            $imagePath = null;
            if (filled($parsed['image_url'])) {
                $imagePath = $this->downloadAsset($parsed['image_url'], 'announcements/imported');
            }

            $content = $parsed['content_html'];
            if ($content === '') {
                $content = '<p>'.e($parsed['title']).'</p>';
            }
            $content .= '<p><a href="'.e($detailUrl).'" target="_blank" rel="noopener noreferrer">Kaynak sayfayı aç</a></p>';

            $meta = $this->fetchAnnouncementMeta($legacyId);
            $date = $meta['date'] ?? Carbon::today()->toDateString();
            $stamp = $meta['created_at'] ?? ($date.' 12:00:00');
            $unpublished = $meta['unpublished_at'] ?? null;

            Announcement::create([
                'title' => Str::limit($parsed['title'], 250, ''),
                'slug' => $this->uniqueSlug($parsed['title'], (string) $legacyId),
                'content' => $content,
                'image_path' => $imagePath,
                'type' => 'duyuru',
                'date' => $date,
                'published_at' => $date,
                'unpublished_at' => $unpublished,
                'file_path' => null,
                'is_active' => true,
                'created_at' => $stamp,
                'updated_at' => $stamp,
            ]);
            $knownIds[$legacyId] = true;
            $created++;
        }

        $this->info("Genel: yeni {$created}, atlanan {$skipped}");

        return [$created, $skipped];
    }

    /**
     * @param  list<string>  $listUrls
     * @return array{0: int, 1: int}
     */
    private function importPdfList(string $type, array $listUrls, bool $dryRun, ?int $limit): array
    {
        $this->info("=== PDF duyurular ({$type}) ===");

        $cards = [];
        foreach ($listUrls as $listUrl) {
            $html = $this->fetchHtml($listUrl);
            if ($html === '') {
                $this->warn("Liste alınamadı: {$listUrl}");

                continue;
            }
            $found = $this->extractCallingLinks($html);
            $this->line(basename(parse_url($listUrl, PHP_URL_PATH) ?: $listUrl).': '.count($found).' kart');
            foreach ($found as $card) {
                $cards[$card['url']] = $card;
            }
        }

        $created = 0;
        $skipped = 0;

        foreach ($cards as $card) {
            if ($this->alreadyHasPdfUrl($card['url'])) {
                $skipped++;

                continue;
            }

            if ($limit !== null && $created >= $limit) {
                break;
            }

            $this->line('→ '.$card['title']);

            if ($dryRun) {
                $created++;

                continue;
            }

            $hash = substr(hash('sha256', $card['url']), 0, 20);
            $filePath = $this->downloadAsset($card['url'], "announcements/{$type}", $hash);
            if ($filePath === null) {
                $this->warn('  PDF indirilemedi');
                $skipped++;

                continue;
            }

            $label = $type === 'ihale' ? 'İhale' : 'Resmî';
            $content = '<p>'.$label.' duyurusu metni PDF belgesinde yer almaktadır.</p>'
                .'<p><a href="'.e($card['url']).'" target="_blank" rel="noopener noreferrer">PDF belgesini görüntüle veya indir (kaynak)</a></p>';

            $basename = Str::lower(basename(parse_url($card['url'], PHP_URL_PATH) ?: ''));
            $meta = $this->fileDateCache[$basename] ?? [
                'date' => Carbon::today()->toDateString(),
                'created_at' => null,
                'unpublished_at' => null,
            ];
            $date = $meta['date'] ?? Carbon::today()->toDateString();
            $stamp = $meta['created_at'] ?? ($date.' 12:00:00');

            Announcement::create([
                'title' => Str::limit($card['title'], 250, ''),
                'slug' => $this->uniqueSlug($type.'-'.$card['title'], $hash),
                'content' => $content,
                'image_path' => null,
                'type' => $type,
                'date' => $date,
                'published_at' => $date,
                'unpublished_at' => $meta['unpublished_at'] ?? null,
                'file_path' => $filePath,
                'is_active' => true,
                'created_at' => $stamp,
                'updated_at' => $stamp,
            ]);
            $created++;
        }

        $this->info(ucfirst($type).": yeni {$created}, atlanan {$skipped}");

        return [$created, $skipped];
    }

    /**
     * @return list<array{title: string, url: string}>
     */
    private function extractCallingLinks(string $html): array
    {
        if (! preg_match_all(
            '/<a href="([^"]+)"[^>]*class="[^"]*calling__link[^"]*"[^>]*>.*?<span>(.*?)<\/span>/su',
            $html,
            $matches,
            PREG_SET_ORDER
        )) {
            return [];
        }

        $items = [];
        foreach ($matches as $m) {
            $url = html_entity_decode(trim($m[1]), ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $title = trim(html_entity_decode(strip_tags($m[2]), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
            $title = preg_replace('/\s+/u', ' ', $title) ?? $title;
            if ($url === '' || $title === '') {
                continue;
            }
            if (! preg_match('#^https?://#i', $url)) {
                continue;
            }
            $items[] = ['title' => $title, 'url' => $url];
        }

        return $items;
    }

    /**
     * @return array{title: string, content_html: string, image_url: ?string}
     */
    private function parseGeneralDetail(string $html, string $detailUrl): array
    {
        $title = '';
        if (preg_match('/<h1 class="culture-fife__heading-secondary[^"]*"[^>]*>(.*?)<\/h1>/su', $html, $m)) {
            $title = trim(html_entity_decode(strip_tags($m[1]), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        } elseif (preg_match('/<h1[^>]*>(.*?)<\/h1>/su', $html, $m)) {
            $title = trim(html_entity_decode(strip_tags($m[1]), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        }

        $contentHtml = '';
        if (preg_match('/<p class="culture-fife__p[^"]*"[^>]*>([\s\S]*?)<\/div>\s*(?:<img|<\/div>)/u', $html, $m)) {
            $inner = trim($m[1]);
            $inner = preg_replace('/<\/p>\s*$/u', '', $inner) ?? $inner;
            $inner = trim($inner);
            if (str_contains($inner, '<p')) {
                $contentHtml = $inner;
            } else {
                $text = trim(html_entity_decode(strip_tags($inner), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
                $contentHtml = $text !== '' ? '<p>'.e($text).'</p>' : '';
            }
        }

        $imageUrl = null;
        if (preg_match('/culture-fife__content-box[\s\S]{0,8000}?<img[^>]+src="([^"]+)"/u', $html, $m)) {
            $src = html_entity_decode(trim($m[1]), ENT_QUOTES | ENT_HTML5, 'UTF-8');
            if (str_contains($src, 'api.kirklarelibelediyesi.com') || str_contains($src, '/Files/')) {
                $imageUrl = $this->absoluteUrl($src, $detailUrl);
            }
        }

        return [
            'title' => $title,
            'content_html' => $contentHtml,
            'image_url' => $imageUrl,
        ];
    }

    /**
     * @return array<int, true>
     */
    private function importedLegacyIds(): array
    {
        $known = [];
        Announcement::query()
            ->where('type', 'duyuru')
            ->where('content', 'like', '%/duyurular/%')
            ->orderBy('id')
            ->chunkById(200, function ($rows) use (&$known) {
                foreach ($rows as $row) {
                    if (preg_match_all('#/duyurular/(\d+)/#', (string) $row->content, $m)) {
                        foreach ($m[1] as $id) {
                            $known[(int) $id] = true;
                        }
                    }
                }
            });

        return $known;
    }

    private function alreadyHasPdfUrl(string $url): bool
    {
        $needle = basename(parse_url($url, PHP_URL_PATH) ?: $url);
        if ($needle === '' || $needle === '/') {
            return false;
        }

        return Announcement::query()
            ->where(function ($q) use ($url, $needle) {
                $q->where('content', 'like', '%'.$needle.'%')
                    ->orWhere('content', 'like', '%'.$url.'%')
                    ->orWhere('file_path', 'like', '%'.pathinfo($needle, PATHINFO_FILENAME).'%');
            })
            ->exists();
    }

    private function uniqueSlug(string $title, string $salt): string
    {
        $base = Str::slug(Str::limit($title, 80, '')) ?: 'duyuru';
        $hash = substr(hash('sha256', $salt.'|'.$title), 0, 8);
        $slug = $base.'-'.$hash;
        $i = 0;
        while (Announcement::where('slug', $slug)->exists()) {
            $i++;
            $slug = $base.'-'.$hash.'-'.$i;
        }

        return $slug;
    }

    private function downloadAsset(string $url, string $dir, ?string $basename = null): ?string
    {
        try {
            $response = Http::timeout(120)
                ->withHeaders(['User-Agent' => 'Mozilla/5.0 (compatible; KirklareliImporter/1.0)'])
                ->retry(2, 800)
                ->get($url);

            if (! $response->successful()) {
                return null;
            }

            $pathPart = parse_url($url, PHP_URL_PATH) ?: '';
            $ext = strtolower(pathinfo($pathPart, PATHINFO_EXTENSION) ?: '');
            if (! in_array($ext, ['pdf', 'zip', 'jpg', 'jpeg', 'png', 'gif', 'webp'], true)) {
                $ct = strtolower((string) $response->header('Content-Type'));
                $ext = match (true) {
                    str_contains($ct, 'pdf') => 'pdf',
                    str_contains($ct, 'png') => 'png',
                    str_contains($ct, 'jpeg'), str_contains($ct, 'jpg') => 'jpg',
                    str_contains($ct, 'webp') => 'webp',
                    str_contains($ct, 'gif') => 'gif',
                    str_contains($ct, 'zip') => 'zip',
                    default => 'bin',
                };
            }

            $name = $basename ?: substr(hash('sha256', $url), 0, 20);
            $path = trim($dir, '/').'/'.$name.'.'.$ext;

            if (Storage::disk('public')->exists($path) && Storage::disk('public')->size($path) > 500) {
                return $path;
            }

            Storage::disk('public')->put($path, $response->body());

            return $path;
        } catch (\Throwable $e) {
            $this->warn('  indirme: '.$e->getMessage());

            return null;
        }
    }

    private function fetchHtml(string $url): string
    {
        try {
            $response = Http::timeout(60)
                ->withHeaders(['User-Agent' => 'Mozilla/5.0 (compatible; KirklareliImporter/1.0)'])
                ->get($url);

            return $response->successful() ? $response->body() : '';
        } catch (\Throwable) {
            return '';
        }
    }

    private function absoluteUrl(string $src, string $base): string
    {
        if (preg_match('#^https?://#i', $src)) {
            return $src;
        }
        if (str_starts_with($src, '//')) {
            return 'https:'.$src;
        }
        $parts = parse_url($base);
        $origin = ($parts['scheme'] ?? 'https').'://'.($parts['host'] ?? 'kirklareli.bel.tr');

        return $origin.'/'.ltrim($src, '/');
    }

    /**
     * @return array{date: ?string, created_at: ?string, unpublished_at: ?string}
     */
    private function fetchAnnouncementMeta(int $legacyId): array
    {
        $all = $this->fetchAnnouncementMetaMany([$legacyId]);

        return $all[$legacyId] ?? ['date' => null, 'created_at' => null, 'unpublished_at' => null];
    }

    /**
     * @param  list<int>  $ids
     * @return array<int, array{date: ?string, created_at: ?string, unpublished_at: ?string}>
     */
    private function fetchAnnouncementMetaMany(array $ids): array
    {
        $out = [];
        $ids = array_values(array_unique(array_map('intval', $ids)));
        foreach (array_chunk($ids, 40) as $chunk) {
            $responses = Http::pool(function ($pool) use ($chunk) {
                foreach ($chunk as $id) {
                    $pool->as((string) $id)
                        ->timeout(20)
                        ->withHeaders([
                            'User-Agent' => 'Mozilla/5.0 (compatible; KirklareliImporter/1.0)',
                            'Accept' => 'application/json',
                        ])
                        ->get("{$this->apiBase}/api/announcement/{$id}");
                }
            });

            foreach ($chunk as $id) {
                $response = $responses[(string) $id] ?? null;
                if (! $response || $response instanceof \Throwable || ! $response->successful()) {
                    continue;
                }
                $data = $response->json('data');
                if (! is_array($data)) {
                    continue;
                }
                $parsed = $this->parseApiAnnouncementDates($data);
                if ($parsed['date'] !== null) {
                    $out[$id] = $parsed;
                }
            }
        }

        return $out;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array{date: ?string, created_at: ?string, unpublished_at: ?string}
     */
    private function parseApiAnnouncementDates(array $data): array
    {
        $publish = $data['dateToBePublished'] ?? null;
        $created = $data['createdDate'] ?? null;
        $expire = $data['publishingExpireDate'] ?? null;

        $date = null;
        if (is_string($publish) && $publish !== '') {
            try {
                $date = Carbon::parse($publish)->toDateString();
            } catch (\Throwable) {
                $date = null;
            }
        }

        $createdAt = null;
        if (is_string($created) && $created !== '') {
            try {
                $createdAt = Carbon::parse($created)->format('Y-m-d H:i:s');
            } catch (\Throwable) {
                $createdAt = null;
            }
        }

        $unpublishedAt = null;
        if (is_string($expire) && $expire !== '') {
            try {
                $exp = Carbon::parse($expire);
                // Çok uzak “sonsuza yakın” tarihler için null bırak
                if ($exp->year < 2100) {
                    $unpublishedAt = $exp->toDateString();
                }
            } catch (\Throwable) {
                $unpublishedAt = null;
            }
        }

        return [
            'date' => $date,
            'created_at' => $createdAt,
            'unpublished_at' => $unpublishedAt,
        ];
    }

    private function warmFileDateCache(): void
    {
        if ($this->fileDateCache !== []) {
            return;
        }

        $maxId = 0;
        Announcement::query()
            ->where('content', 'like', '%/duyurular/%')
            ->orderByDesc('id')
            ->limit(500)
            ->pluck('content')
            ->each(function ($content) use (&$maxId) {
                if (preg_match_all('#/duyurular/(\d+)/#', (string) $content, $m)) {
                    foreach ($m[1] as $id) {
                        $maxId = max($maxId, (int) $id);
                    }
                }
            });

        if ($maxId < 100) {
            $maxId = 4900;
        }

        $this->line("PDF tarih eşlemesi için API taranıyor (1–{$maxId})…");
        $ids = range(1, $maxId + 50);
        $found = 0;

        foreach (array_chunk($ids, 50) as $chunk) {
            $responses = Http::pool(function ($pool) use ($chunk) {
                foreach ($chunk as $id) {
                    $pool->as((string) $id)
                        ->timeout(15)
                        ->withHeaders([
                            'User-Agent' => 'Mozilla/5.0 (compatible; KirklareliImporter/1.0)',
                            'Accept' => 'application/json',
                        ])
                        ->get("{$this->apiBase}/api/announcement/{$id}");
                }
            });

            foreach ($chunk as $id) {
                $response = $responses[(string) $id] ?? null;
                if (! $response || $response instanceof \Throwable || ! $response->successful()) {
                    continue;
                }
                $data = $response->json('data');
                if (! is_array($data)) {
                    continue;
                }
                $fileUrl = $data['fileUrl'] ?? null;
                if (! is_string($fileUrl) || $fileUrl === '') {
                    // announcementFiles
                    $files = $data['announcementFiles'] ?? [];
                    if (is_array($files)) {
                        foreach ($files as $file) {
                            if (is_array($file) && is_string($file['url'] ?? null)) {
                                $fileUrl = $file['url'];
                                break;
                            }
                        }
                    }
                }
                if (! is_string($fileUrl) || $fileUrl === '') {
                    continue;
                }
                $basename = Str::lower(basename(parse_url($fileUrl, PHP_URL_PATH) ?: ''));
                if ($basename === '') {
                    continue;
                }
                $parsed = $this->parseApiAnnouncementDates($data);
                if ($parsed['date'] === null) {
                    continue;
                }
                $this->fileDateCache[$basename] = $parsed;
                $found++;
            }
        }

        $this->line("PDF tarih eşlemesi: {$found} dosya");
    }

    private function extractPdfBasename(string $content): ?string
    {
        if (preg_match('#/(?:Files/)?(?:Dokuman|dokuman|ilanlar)/([^\"\'\s<]+\.(?:pdf|zip))#i', $content, $m)) {
            return Str::lower($m[1]);
        }
        if (preg_match('#https?://api\.kirklarelibelediyesi\.com/[^\"\'\s<]+\.(?:pdf|zip)#i', $content, $m)) {
            return Str::lower(basename(parse_url($m[0], PHP_URL_PATH) ?: ''));
        }

        return null;
    }
}

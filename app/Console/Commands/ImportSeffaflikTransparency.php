<?php

namespace App\Console\Commands;

use App\Support\Transparency\TransparencySections;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class ImportSeffaflikTransparency extends Command
{
    protected $signature = 'import:seffaflik';

    protected $description = 'kirklareli.bel.tr sitesinden Şeffaflık ve Hesap Verilebilirlik bölümünü içe aktarır';

    private const SOURCE_BASE = 'https://kirklareli.bel.tr';

    /**
     * @var list<array{slug: string, source_path: string, title: string}>
     */
    private array $sectionDefinitions = [
        ['slug' => 'stratejik-plan', 'source_path' => '/diger-duyurular/25', 'title' => 'Stratejik Plan'],
        ['slug' => 'faaliyet-raporlari', 'source_path' => '/diger-duyurular/76', 'title' => 'Faaliyet Raporları'],
        ['slug' => 'mali-durum', 'source_path' => '/diger-duyurular/89', 'title' => 'Mali Durum Beklentiler Raporu'],
        ['slug' => 'ucret-tarifesi', 'source_path' => '/diger-duyurular/1012', 'title' => 'Ücret Tarifesi'],
        ['slug' => 'su-ucret-tarifesi', 'source_path' => '/diger-duyurular/1013', 'title' => 'Su ve Ücret Tarifesi'],
        ['slug' => 'performans-programi', 'source_path' => '/diger-duyurular/1053', 'title' => 'Performans Programı'],
        ['slug' => 'denetim-raporlari', 'source_path' => '/diger-duyurular/1248', 'title' => 'Sayıştay ve İçişleri Bakanlığı Dış Denetim ve İç Denetim Raporları'],
        ['slug' => 'butce-kesin-hesap', 'source_path' => '/diger-duyurular/1345', 'title' => 'Bütçe ve Kesin Hesap'],
        ['slug' => 'enerji-iklim-plani', 'source_path' => '/diger-duyurular/1509', 'title' => 'Sürdürülebilir Enerji ve İklim Eylem Planı'],
    ];

    public function handle(): int
    {
        $sections = [];

        foreach ($this->sectionDefinitions as $definition) {
            $html = $this->fetchHtml($definition['source_path']);
            $documents = $this->extractDocuments($html);

            $sections[] = [
                'slug' => $definition['slug'],
                'title' => $definition['title'],
                'source_path' => $definition['source_path'],
                'documents' => $documents,
            ];

            $this->line(sprintf('  ✓ %s (%d doküman)', $definition['title'], count($documents)));
        }

        TransparencySections::save($sections);

        $this->info('Şeffaflık ve Hesap Verilebilirlik içerikleri başarıyla içe aktarıldı.');

        return self::SUCCESS;
    }

    private function fetchHtml(string $path): string
    {
        $response = Http::timeout(30)
            ->withHeaders(['User-Agent' => 'Mozilla/5.0 (compatible; KirklareliImporter/1.0)'])
            ->get(self::SOURCE_BASE . $path);

        if (! $response->successful()) {
            $this->warn("Sayfa alınamadı: {$path}");

            return '';
        }

        return $response->body();
    }

    /**
     * @return list<array{title: string, url: string}>
     */
    private function extractDocuments(string $html): array
    {
        if (! preg_match_all(
            '/<a href="([^"]+)"[^>]*class="calling__link"[^>]*>\s*<span>(.*?)<\/span>/su',
            $html,
            $matches,
            PREG_SET_ORDER
        )) {
            return [];
        }

        return collect($matches)
            ->map(function (array $match) {
                return [
                    'url' => html_entity_decode($match[1], ENT_QUOTES | ENT_HTML5, 'UTF-8'),
                    'title' => trim(strip_tags(html_entity_decode($match[2], ENT_QUOTES | ENT_HTML5, 'UTF-8'))),
                ];
            })
            ->filter(fn (array $document) => filled($document['title']) && filled($document['url']))
            ->unique('url')
            ->values()
            ->all();
    }
}

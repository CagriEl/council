<?php

namespace App\Console\Commands;

use App\Models\Menu;
use App\Models\Page;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ImportKurumsalMevzuatMenu extends Command
{
    protected $signature = 'import:kurumsal-mevzuat {--fresh : Mevcut Kurumsal/Mevzuat menülerini ve sayfalarını sil}';

    protected $description = 'kirklareli.bel.tr sitesinden Kurumsal ve Mevzuat menülerini içerikleriyle birlikte içe aktarır';

    private const SOURCE_BASE = 'https://kirklareli.bel.tr';

    public function handle(): int
    {
        if ($this->option('fresh')) {
            $this->purgeExisting();
        }

        $this->deactivateLegacyHeaderItems();

        $kurumsal = $this->seedKurumsalMenu();
        $this->seedMevzuatMenu($kurumsal->order + 1);

        $this->info('Kurumsal ve Mevzuat menüleri başarıyla içe aktarıldı.');

        return self::SUCCESS;
    }

    private function deactivateLegacyHeaderItems(): void
    {
        $legacyTitles = [
            'Organizasyon Şeması',
            'MECLİS',
            'Meclis',
            'Meclis Kararları',
        ];

        Menu::where('location', 'header')
            ->whereNull('parent_id')
            ->whereIn('title', $legacyTitles)
            ->update(['is_active' => false]);
    }

    private function purgeExisting(): void
    {
        $parents = Menu::whereIn('title', ['Kurumsal', 'Mevzuat'])
            ->where('location', 'header')
            ->whereNull('parent_id')
            ->get();

        foreach ($parents as $parent) {
            Menu::where('parent_id', $parent->id)->delete();
            $parent->delete();
        }

        $slugs = collect($this->kurumsalPageDefinitions())->pluck('slug')
            ->push('yonergeler-ve-yonetmelikler')
            ->all();

        Page::whereIn('slug', $slugs)->delete();

        $this->warn('Mevcut Kurumsal/Mevzuat kayıtları temizlendi.');
    }

    private function seedKurumsalMenu(): Menu
    {
        $parent = Menu::updateOrCreate(
            ['title' => 'Kurumsal', 'location' => 'header', 'parent_id' => null],
            ['url' => '#', 'order' => 2, 'is_active' => true]
        );

        $order = 1;

        foreach ($this->kurumsalPageDefinitions() as $item) {
            $pageId = null;
            $url = $item['url'];

            if (($item['type'] ?? null) === 'yonergeler') {
                $page = $this->importYonergelerPage();
                $pageId = $page->id;
                $url = '/sayfa/yonergeler-ve-yonetmelikler';
            } elseif ($item['source_path'] ?? null) {
                $page = $this->importPage($item['title'], $item['slug'], $item['source_path']);
                $pageId = $page->id;
                $url = '/sayfa/' . $item['slug'];
            }

            Menu::updateOrCreate(
                ['title' => $item['title'], 'parent_id' => $parent->id],
                [
                    'url' => $url,
                    'page_id' => $pageId,
                    'location' => 'header',
                    'order' => $order++,
                    'is_active' => true,
                ]
            );

            $this->line("  ✓ {$item['title']}");
        }

        return $parent;
    }

    private function seedMevzuatMenu(int $order): void
    {
        $parent = Menu::updateOrCreate(
            ['title' => 'Mevzuat', 'location' => 'header', 'parent_id' => null],
            ['url' => '#', 'order' => $order, 'is_active' => true]
        );

        $items = [
            ['title' => 'Belediye Kanunu', 'url' => 'https://www.mevzuat.gov.tr/mevzuat?MevzuatNo=5393&MevzuatTur=1&MevzuatTertip=5'],
            ['title' => 'Belediye Gelirler Kanunu', 'url' => 'https://www.mevzuat.gov.tr/mevzuat?MevzuatNo=2464&MevzuatTur=1&MevzuatTertip=5'],
            ['title' => 'Verilecek Ücretler Hakkında Kanun', 'url' => 'https://www.mevzuat.gov.tr/mevzuat?MevzuatNo=37&MevzuatTur=1&MevzuatTertip=4'],
            ['title' => 'İçme, Kullanma ve Endüstri Suyu Temini Hakkında Kanun', 'url' => 'https://www.mevzuat.gov.tr/mevzuat?MevzuatNo=1053&MevzuatTur=1&MevzuatTertip=5'],
            ['title' => 'Belediyelere Genel Bütçe ve Vergiden Pay Verilmesi Hak. Kanun', 'url' => 'https://www.mevzuat.gov.tr/mevzuat?MevzuatNo=5779&MevzuatTur=1&MevzuatTertip=5'],
        ];

        foreach ($items as $index => $item) {
            Menu::updateOrCreate(
                ['title' => $item['title'], 'parent_id' => $parent->id],
                [
                    'url' => $item['url'],
                    'location' => 'header',
                    'order' => $index + 1,
                    'is_active' => true,
                ]
            );

            $this->line("  ✓ {$item['title']}");
        }
    }

    /**
     * @return list<array{title: string, slug: string, source_path: ?string, url: string}>
     */
    private function kurumsalPageDefinitions(): array
    {
        return [
            ['title' => 'Misyon & Vizyon', 'slug' => 'misyon-vizyon', 'source_path' => '/sayfalar/1/misyon-vizyon', 'url' => ''],
            ['title' => 'Politikalarımız', 'slug' => 'politikalarimiz', 'source_path' => '/sayfalar/2/politikalarmz', 'url' => ''],
            ['title' => 'İlkelerimiz', 'slug' => 'ilkelerimiz', 'source_path' => '/sayfalar/3/lkelerimiz', 'url' => ''],
            ['title' => 'Belediye Meclisi', 'slug' => 'belediye-meclisi', 'source_path' => null, 'url' => '/meclis'],
            ['title' => 'Muhtarlıklarımız', 'slug' => 'muhtarliklarimiz', 'source_path' => '/sayfalar/16/muhtarlklarmz', 'url' => ''],
            ['title' => 'Geçmiş Dönem Belediye Başkanlarımız', 'slug' => 'gecmis-donem-belediye-baskanlarimiz', 'source_path' => '/sayfalar/73/gecmis-donem-belediye-baskanlarmz', 'url' => ''],
            ['title' => 'Organizasyon Şeması', 'slug' => 'organizasyon-semasi', 'source_path' => '/sayfalar/65/organizasyon-semas', 'url' => ''],
            ['title' => 'Yönergeler ve Yönetmelikler', 'slug' => 'yonergeler-ve-yonetmelikler', 'type' => 'yonergeler', 'url' => ''],
            ['title' => 'Meclis Kararları', 'slug' => 'meclis-kararlari', 'source_path' => null, 'url' => '/meclis-kararlari'],
            ['title' => 'Müdürlüklerimiz', 'slug' => 'mudurluklerimiz', 'source_path' => '/sayfalar/66/mudurluklerimiz', 'url' => ''],
            ['title' => 'Komisyonlar', 'slug' => 'komisyonlar', 'source_path' => '/sayfalar/70/komisyonlar', 'url' => ''],
        ];
    }

    private function importPage(string $title, string $slug, string $sourcePath): Page
    {
        $html = $this->fetchHtml($sourcePath);

        if ($slug === 'gecmis-donem-belediye-baskanlarimiz') {
            $extracted = [
                'title' => $title,
                'content' => $this->extractPastMayorsNamesContent($html),
            ];
        } else {
            $extracted = $this->extractSayfaContent($html, $title);
        }

        return Page::updateOrCreate(
            ['slug' => $slug],
            [
                'title' => $extracted['title'] ?: $title,
                'content' => $extracted['content'] ?: '<p>İçerik henüz yüklenemedi.</p>',
                'is_active' => true,
            ]
        );
    }

    private function importYonergelerPage(): Page
    {
        $html = $this->fetchHtml('/yonergeler/1');
        $content = $this->extractYonergelerContent($html);

        return Page::updateOrCreate(
            ['slug' => 'yonergeler-ve-yonetmelikler'],
            [
                'title' => 'Yönergeler ve Yönetmelikler',
                'content' => $content,
                'is_active' => true,
            ]
        );
    }

    private function fetchHtml(string $path): string
    {
        $url = Str::startsWith($path, 'http') ? $path : self::SOURCE_BASE . $path;

        $response = Http::timeout(30)
            ->withHeaders(['User-Agent' => 'Mozilla/5.0 (compatible; KirklareliImporter/1.0)'])
            ->get($url);

        if (! $response->successful()) {
            $this->warn("Sayfa alınamadı: {$url}");

            return '';
        }

        return $response->body();
    }

    /**
     * @return array{title: string, content: string}
     */
    private function extractSayfaContent(string $html, string $fallbackTitle): array
    {
        $title = $fallbackTitle;

        if (preg_match_all('/<p class="orientation-p">\s*([^<]+)/u', $html, $matches)) {
            $last = trim(html_entity_decode(end($matches[1]), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
            if ($last !== '' && ! Str::contains(Str::lower($last), 'anasayfa')) {
                $title = $last;
            }
        }

        $content = '';

        if (preg_match('/<div class="misandvis"[^>]*>(.*?)<\/div>\s*(?:<\/div>\s*){0,3}\s*<\/div>\s*<\/main>/su', $html, $match)) {
            $content = trim($match[1]);
        } elseif (preg_match('/<figure class="table">(.*?)<\/figure>\s*(?:<\/div>\s*){0,6}\s*<\/main>/su', $html, $match)) {
            $content = '<figure class="table">' . trim($match[1]) . '</figure>';
        } elseif (preg_match('/<div class="col-sm-12 col-md-12 col-lg-10 col-xl-10">\s*(.*?)\s*<\/div>\s*(?:<\/div>\s*){0,3}\s*<\/main>/su', $html, $match)) {
            $content = trim($match[1]);
        }

        $content = $this->normalizeContent($content);

        return compact('title', 'content');
    }

    private function extractPastMayorsNamesContent(string $html): string
    {
        if (! preg_match_all('/<figcaption[^>]*>(.*?)<\/figcaption>/su', $html, $matches)) {
            return '<p>Geçmiş dönem belediye başkanları listesi yüklenemedi.</p>';
        }

        $names = collect($matches[1])
            ->map(fn (string $name) => trim(strip_tags(html_entity_decode($name, ENT_QUOTES | ENT_HTML5, 'UTF-8'))))
            ->filter()
            ->unique()
            ->values();

        if ($names->isEmpty()) {
            return '<p>Geçmiş dönem belediye başkanları listesi yüklenemedi.</p>';
        }

        $items = $names
            ->map(fn (string $name) => '<span class="past-mayor-chip">' . e($name) . '</span>')
            ->implode("\n");

        return '<div class="past-mayors-grid">' . $items . '</div>';
    }

    private function extractYonergelerContent(string $html): string
    {
        if (! preg_match('/<div class="calling">(.*?)<\/div>\s*<\/div>\s*<\/main>/su', $html, $match)) {
            return '<p>Yönerge listesi yüklenemedi.</p>';
        }

        $block = $match[1];

        if (! preg_match_all(
            '/<a href="([^"]+)"[^>]*class="calling__link"[^>]*>\s*.*?<span>(.*?)<\/span>/su',
            $block,
            $links,
            PREG_SET_ORDER
        )) {
            return '<p>Yönerge listesi bulunamadı.</p>';
        }

        $items = collect($links)->map(function (array $link) {
            $href = html_entity_decode($link[1], ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $label = trim(strip_tags(html_entity_decode($link[2], ENT_QUOTES | ENT_HTML5, 'UTF-8')));

            return '<li><a href="' . e($href) . '" target="_blank" rel="noopener noreferrer">' . e($label) . '</a></li>';
        })->implode("\n");

        return '<div class="page-content-list"><ul class="list-unstyled">' . $items . '</ul></div>';
    }

    private function normalizeContent(string $content): string
    {
        $content = preg_replace('/\sstyle="[^"]*"/i', '', $content) ?? $content;
        $content = preg_replace('/class="img-fluid"/', 'class="img-fluid w-100"', $content) ?? $content;

        return trim($content);
    }
}

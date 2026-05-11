<?php

namespace Database\Seeders;

use App\Models\Mayor;
use App\Models\Menu;
use App\Models\News;
use App\Models\PoliticalParty;
use App\Models\QuickLink;
use App\Models\Slider;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

/**
 * Boş veritabanında site ve API'nin çalışması için temel kayıtlar (idempotent).
 * Gerçek içerik yedeği değildir; canlı veri için SQL yedeğinden restore gerekir.
 */
class SiteContentRestoreSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedMayor();
        $this->seedPoliticalParties();
        $this->seedQuickLinks();
        $this->seedSliders();
        $this->seedNewsIfEmpty();
        $this->seedMenus();
    }

    private function seedMayor(): void
    {
        if (! Schema::hasTable('mayors')) {
            return;
        }

        if (Mayor::query()->exists()) {
            return;
        }

        $message = '<p>Değerli hemşehrilerim, şehrimiz için çalışmaya devam ediyoruz.</p><p>Bu mesajı panelden (Başkan Yönetimi) düzenleyebilirsiniz.</p>';
        $description = '<p>T.C. Kırklareli Belediyesi Belediye Başkanı.</p><p>Bu metin veritabanı sıfırlandıktan sonra otomatik oluşturulmuş özet bir yer tutucudur; panelden güncelleyiniz.</p>';

        $row = [
            'name' => 'Derya BULUT',
            'title' => 'Belediye Başkanı',
            'image_path' => null,
            'description' => $description,
            'message' => $message,
            'is_active' => true,
        ];

        // Eski şema: `content` zorunlu olabiliyor
        if (Schema::hasColumn('mayors', 'content')) {
            $row['content'] = $message;
        }
        if (Schema::hasColumn('mayors', 'biography')) {
            $row['biography'] = strip_tags($description);
        }

        Mayor::query()->create($row);
    }

    private function seedPoliticalParties(): void
    {
        if (! Schema::hasTable('political_parties')) {
            return;
        }

        if (PoliticalParty::query()->exists()) {
            return;
        }

        $rows = [
            ['name' => 'Cumhuriyet Halk Partisi', 'color_code' => '#e30a17'],
            ['name' => 'Adalet ve Kalkınma Partisi', 'color_code' => '#f7c600'],
            ['name' => 'Diğer / Bağımsız', 'color_code' => '#6b7280'],
        ];

        foreach ($rows as $row) {
            PoliticalParty::query()->create($row);
        }
    }

    private function seedQuickLinks(): void
    {
        if (! Schema::hasTable('quick_links')) {
            return;
        }

        if (QuickLink::query()->exists()) {
            return;
        }

        $links = [
            ['title' => 'E-Belediye', 'url' => '/e-belediye', 'icon_class' => 'fas fa-laptop-house', 'order' => 1],
            ['title' => 'İletişim', 'url' => '/iletisim', 'icon_class' => 'fas fa-phone', 'order' => 2],
            ['title' => 'Haberler', 'url' => '/haberler', 'icon_class' => 'fas fa-newspaper', 'order' => 3],
            ['title' => 'Duyurular', 'url' => '/duyurular', 'icon_class' => 'fas fa-bullhorn', 'order' => 4],
        ];

        foreach ($links as $link) {
            QuickLink::query()->create($link);
        }
    }

    private function seedSliders(): void
    {
        if (! Schema::hasTable('sliders')) {
            return;
        }

        if (Slider::query()->exists()) {
            return;
        }

        $imagePath = $this->copyPublicAssetToStorage('images/logo.png', 'seed/slider-1.jpg');
        if ($imagePath === null) {
            Slider::query()->create([
                'title' => 'Kırklareli Belediyesi',
                'image_path' => null,
                'video_path' => null,
                'link' => url('/haberler'),
                'order' => 0,
                'is_active' => true,
            ]);

            return;
        }

        Slider::query()->create([
            'title' => 'Kırklareli Belediyesi',
            'image_path' => $imagePath,
            'video_path' => null,
            'link' => url('/haberler'),
            'order' => 0,
            'is_active' => true,
        ]);
    }

    /**
     * Haber yokken ana sayfa manşet slider’ının kaybolmaması için minimal kayıtlar.
     */
    private function seedNewsIfEmpty(): void
    {
        if (! Schema::hasTable('news')) {
            return;
        }

        if (News::query()->exists()) {
            return;
        }

        $img = $this->copyPublicAssetToStorage('images/logo.png', 'seed/news-headline.jpg');
        if ($img === null) {
            return;
        }

        $today = now()->toDateString();
        $base = [
            'summary' => 'Panelden düzenleyebileceğiniz yer tutucu haber metnidir.',
            'content' => '<p>Veritabanında yayımlanmış haber bulunmadığında ana sayfa modüllerinin çalışması için otomatik oluşturulmuştur.</p><p>Güncel haberleri yönetim panelinden ekleyiniz.</p>',
            'image_path' => $img,
            'published_at' => $today,
            'is_active' => true,
        ];

        News::query()->create(array_merge($base, [
            'title' => 'Kırklareli Belediyesi web sitesine hoş geldiniz',
            'slug' => 'hos-geldiniz-yer-tutucu-haber',
            'category' => 'belediye',
            'is_headline' => true,
        ]));

        News::query()->create(array_merge($base, [
            'title' => 'Haberler bölümünü kullanmaya başlayın',
            'slug' => 'haberler-bolumunu-kullanin',
            'category' => 'belediye',
            'is_headline' => false,
        ]));

        News::query()->create(array_merge($base, [
            'title' => 'Yerel duyurular ve etkinlikler',
            'slug' => 'yerel-duyurular-ve-etkinlikler',
            'category' => 'kultur',
            'is_headline' => false,
        ]));
    }

    private function seedMenus(): void
    {
        if (! Schema::hasTable('menus')) {
            return;
        }

        if (Menu::query()->exists()) {
            return;
        }

        $header = [
            ['title' => 'BAŞKAN', 'url' => '/baskan', 'order' => 1],
            ['title' => 'Organizasyon Şeması', 'url' => '/mudurler', 'order' => 2],
            ['title' => 'MECLİS', 'url' => '/meclis', 'order' => 3],
            ['title' => 'HABERLER', 'url' => '/haberler', 'order' => 4],
            ['title' => 'DUYURULAR', 'url' => '/duyurular', 'order' => 5],
            ['title' => 'Meclis Kararları', 'url' => '/meclis-kararlari', 'order' => 6],
            ['title' => 'İLETİŞİM', 'url' => '/iletisim', 'order' => 7],
        ];

        foreach ($header as $row) {
            Menu::query()->create([
                'title' => $row['title'],
                'url' => $row['url'],
                'order' => $row['order'],
                'location' => 'header',
                'page_id' => null,
                'parent_id' => null,
                'is_active' => true,
            ]);
        }

        $footer = [
            ['title' => 'Ana Sayfa', 'url' => '/', 'order' => 1],
            ['title' => 'E-Belediye', 'url' => '/e-belediye', 'order' => 2],
            ['title' => 'İletişim', 'url' => '/iletisim', 'order' => 3],
        ];

        foreach ($footer as $row) {
            Menu::query()->create([
                'title' => $row['title'],
                'url' => $row['url'],
                'order' => $row['order'],
                'location' => 'footer',
                'page_id' => null,
                'parent_id' => null,
                'is_active' => true,
            ]);
        }
    }

    private function copyPublicAssetToStorage(string $publicRelative, string $storagePath): ?string
    {
        $src = public_path($publicRelative);
        if (! is_readable($src)) {
            return null;
        }

        $binary = file_get_contents($src);
        if ($binary === false || $binary === '') {
            return null;
        }

        Storage::disk('public')->put($storagePath, $binary);

        return $storagePath;
    }
}

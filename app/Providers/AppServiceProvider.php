<?php

namespace App\Providers;

use App\Models\Menu;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // Livewire / Filament yükleme dizinleri (yazılabilir olmalı)
        foreach ([
            storage_path('app/public/livewire-tmp'),
            storage_path('app/private/livewire-tmp'),
            storage_path('app/public/sliders'),
            storage_path('app/public/sliders/videos'),
        ] as $directory) {
            if (! is_dir($directory)) {
                @mkdir($directory, 0775, true);
            }
            @chmod($directory, 0775);
        }

        Blade::directive('parseContent', function ($expression) {
            return "<?php echo \App\Providers\AppServiceProvider::parseShortcodes($expression); ?>";
        });
        try {
            // Tüm view dosyalarıyla headerMenus değişkenini paylaş
            View::composer('*', function ($view) {
                
                // Sadece Header ve Ana Menüleri (parent_id olmayanları) çekiyoruz
                // Alt menüleri 'children' ilişkisiyle alacağız
                $headerMenus = Menu::where('location', 'header')
                                   ->where('is_active', true)
                                   ->whereNull('parent_id')
                                   ->orderBy('order')
                                   ->with(['children' => fn ($query) => $query
                                       ->where('is_active', true)
                                       ->orderBy('order')])
                                   ->get();

                $view->with('headerMenus', $headerMenus);
            });
        } catch (\Exception $e) {
            // Tablo yoksa hata verme
        }
    }
    public static function parseShortcodes($content)
    {
        // Regex ile [form code="xyz"] desenini arıyoruz
        return preg_replace_callback('/\[form code="(.*?)"\]/', function ($matches) {
            $formCode = $matches[1]; // "baskan-iletisim" gibi kodu alır
            
            // Component'i render edip string olarak döner
            return view('components.api-form', ['code' => $formCode])->render();
        }, $content);
    }
}
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View; // Gerekli
use App\Models\Menu; 
use Illuminate\Support\Facades\Blade;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
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
                                   ->with('children') 
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
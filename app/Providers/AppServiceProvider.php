<?php

namespace App\Providers;

use App\Models\Menu;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        RateLimiter::for('contact', function (Request $request) {
            $key = $request->ip() ?: 'unknown';

            return [
                Limit::perMinute(5)->by('contact-minute:'.$key),
                Limit::perHour(20)->by('contact-hour:'.$key),
            ];
        });

        // Canlı sitede APP_URL yanlış olsa bile Livewire imzalı upload URL'leri
        // tarayıcıdaki gerçek host ile üretilsin (aksi halde upload 401/500 olur).
        if (! $this->app->runningInConsole()) {
            $request = request();
            if ($request) {
                URL::forceRootUrl($request->getSchemeAndHttpHost());
            }
            URL::forceScheme('https');
        } elseif ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        foreach ([
            storage_path('app/public/livewire-tmp'),
            storage_path('app/private/livewire-tmp'),
            storage_path('app/public/sliders'),
            storage_path('app/public/sliders/videos'),
        ] as $directory) {
            if (! is_dir($directory)) {
                @mkdir($directory, 0777, true);
            }
            @chmod($directory, 0777);
        }

        Blade::directive('parseContent', function ($expression) {
            return "<?php echo \App\Providers\AppServiceProvider::parseShortcodes($expression); ?>";
        });
        try {
            View::composer('*', function ($view) {
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
        return preg_replace_callback('/\[form code="(.*?)"\]/', function ($matches) {
            $formCode = $matches[1];

            return view('components.api-form', ['code' => $formCode])->render();
        }, $content);
    }
}

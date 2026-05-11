<?php

namespace App\Providers;

use App\Models\DebtQueryAudit;
use App\Models\Menu;
use Illuminate\Cache\RateLimiting\Limit; // Gerekli
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Throwable;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        $this->blockDestructiveDatabaseCommandsInProduction();

        RateLimiter::for('eodeme-debt-query', function (Request $request): array {
            $decayMinutes = max(1, (int) config('services.e_odeme.rate_limit_decay_minutes', 10));
            $ipAttempts = max(1, (int) config('services.e_odeme.rate_limit_ip_attempts', 60));
            $idAttempts = max(1, (int) config('services.e_odeme.rate_limit_id_attempts', 30));
            if (app()->environment('local')) {
                $ipAttempts = max($ipAttempts, 200);
                $idAttempts = max($idAttempts, 100);
            }
            $normalizedId = preg_replace('/\D+/', '', (string) $request->input('mukellef_no', '')) ?: '';

            $responseCallback = function (Request $request, array $headers) use ($decayMinutes) {
                $requestId = (string) Str::uuid();
                $maskedMukellefNo = self::maskIdentifier((string) $request->input('mukellef_no', ''));
                $payload = [
                    'request_id' => $requestId,
                    'ip_address' => $request->ip(),
                    'user_agent' => (string) $request->userAgent(),
                    'mukellef_tipi' => (string) $request->input('mukellef_tipi', ''),
                    'masked_mukellef_no' => $maskedMukellefNo,
                    'captcha_ok' => false,
                    'rate_limited' => true,
                    'upstream_result_code' => null,
                    'status' => 'rate_limited',
                    'duration_ms' => 0,
                ];

                try {
                    DebtQueryAudit::query()->create($payload);
                } catch (Throwable) {
                    // Limitleme logunda hata olursa akışı kesme.
                }

                Log::channel('debt_query_audit')->warning('Debt query rate limited', $payload);

                $retryAfter = max(60, $decayMinutes * 60);

                return response()->json([
                    'status' => 'error',
                    'message' => 'Çok fazla istek gönderildi. Lütfen bir süre sonra tekrar deneyin.',
                    'error_code' => 'RATE_LIMITED',
                    'request_id' => $requestId,
                    'retry_after_seconds' => $retryAfter,
                ], 429, array_merge($headers, ['Retry-After' => (string) $retryAfter]));
            };

            $limits = [
                Limit::perMinutes($decayMinutes, $ipAttempts)
                    ->by('eodeme-ip:'.$request->ip())
                    ->response($responseCallback),
            ];

            // Boş numara tüm istekleri tek "id" kovasına düşürüp birkaç denemede 429 üretmesin diye yalnızca doluysa uygula.
            if ($normalizedId !== '') {
                $limits[] = Limit::perMinutes($decayMinutes, $idAttempts)
                    ->by('eodeme-id:'.sha1($normalizedId))
                    ->response($responseCallback);
            }

            return $limits;
        });

        Blade::directive('parseContent', function ($expression) {
            return "<?php echo \App\Providers\AppServiceProvider::parseShortcodes($expression); ?>";
        });
        View::composer('*', function ($view) {
            if (! Schema::hasTable('menus')) {
                $view->with('headerMenus', collect());

                return;
            }

            try {
                $headerMenus = Menu::query()
                    ->where('location', 'header')
                    ->where('is_active', true)
                    ->whereNull('parent_id')
                    ->orderBy('order')
                    ->with('children')
                    ->get();
            } catch (Throwable) {
                $headerMenus = collect();
            }

            $view->with('headerMenus', $headerMenus);
        });
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

    protected static function maskIdentifier(string $value): string
    {
        $trimmed = trim($value);
        if ($trimmed === '') {
            return '';
        }

        $length = mb_strlen($trimmed);
        if ($length <= 2) {
            return str_repeat('*', $length);
        }

        if ($length <= 4) {
            return mb_substr($trimmed, 0, 1).str_repeat('*', $length - 1);
        }

        return mb_substr($trimmed, 0, 2).str_repeat('*', $length - 4).mb_substr($trimmed, -2);
    }

    /**
     * Üretimde migrate:fresh / db:wipe ile tüm verinin silinmesini engeller.
     * İstisna: .env içinde ALLOW_DESTRUCTIVE_DB_COMMANDS=true (yedek sonrası bilinçli kullanım).
     */
    protected function blockDestructiveDatabaseCommandsInProduction(): void
    {
        if (PHP_SAPI !== 'cli') {
            return;
        }

        if (! app()->environment(['production', 'staging'])) {
            return;
        }

        if (filter_var(env('ALLOW_DESTRUCTIVE_DB_COMMANDS', false), FILTER_VALIDATE_BOOL)) {
            return;
        }

        $argv = $_SERVER['argv'] ?? [];
        $blocked = ['migrate:fresh', 'db:wipe'];
        foreach ($blocked as $cmd) {
            if (in_array($cmd, $argv, true)) {
                throw new \RuntimeException(
                    'Bu komut '.$cmd.' üretim ortamında devre dışı (tüm veriyi siler). '.
                    'Önce veritabanı yedeği alın. Bilinçli kullanım için .env içine ALLOW_DESTRUCTIVE_DB_COMMANDS=true ekleyin.'
                );
            }
        }
    }
}

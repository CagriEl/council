<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;
use Throwable;

class TurnstileService
{
    /**
     * Yalnızca hem site hem secret anahtarı varken etkin sayılır.
     * Aksi halde (ör. lokal .env’de TURNSTILE_ENABLED=true ama anahtar yok) borç formu doğrulanamaz.
     */
    public function isEnabled(): bool
    {
        if (! filter_var(config('services.turnstile.enabled'), FILTER_VALIDATE_BOOL)) {
            return false;
        }

        $site = trim((string) config('services.turnstile.site_key', ''));
        $secret = trim((string) config('services.turnstile.secret_key', ''));

        return $site !== '' && $secret !== '';
    }

    public function siteKey(): string
    {
        return (string) config('services.turnstile.site_key', '');
    }

    public function verify(string $token, ?string $ipAddress = null): bool
    {
        if (! $this->isEnabled()) {
            return true;
        }

        if ($token === '') {
            return false;
        }

        $secretKey = (string) config('services.turnstile.secret_key', '');
        if ($secretKey === '') {
            throw new RuntimeException('Turnstile secret key tanimli degil.');
        }

        try {
            $response = Http::asForm()
                ->timeout(10)
                ->post((string) config('services.turnstile.verify_url'), [
                    'secret' => $secretKey,
                    'response' => $token,
                    'remoteip' => $ipAddress,
                ]);

            if (! $response->successful()) {
                return false;
            }

            return (bool) data_get($response->json(), 'success', false);
        } catch (Throwable $e) {
            throw new RuntimeException('Turnstile dogrulamasi sirasinda hata olustu.', 0, $e);
        }
    }
}

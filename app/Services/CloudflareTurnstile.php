<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CloudflareTurnstile
{
    public function enabled(): bool
    {
        return filled(config('services.turnstile.secret_key'))
            && filled($this->siteKey());
    }

    public function siteKey(): ?string
    {
        $key = config('services.turnstile.site_key');

        return filled($key) ? (string) $key : null;
    }

    /**
     * Cloudflare Turnstile canonical siteverify.
     *
     * @see https://developers.cloudflare.com/turnstile/get-started/server-side-validation/
     */
    public function verify(?string $token, ?string $remoteIp = null): bool
    {
        if (! $this->enabled()) {
            return true;
        }

        if (! filled($token)) {
            return false;
        }

        try {
            $response = Http::asForm()
                ->timeout(8)
                ->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', array_filter([
                    'secret' => (string) config('services.turnstile.secret_key'),
                    'response' => $token,
                    'remoteip' => $remoteIp,
                ], fn ($value) => $value !== null && $value !== ''));

            if (! $response->successful()) {
                Log::warning('Turnstile siteverify HTTP error', [
                    'status' => $response->status(),
                ]);

                return false;
            }

            $payload = $response->json();

            return (bool) ($payload['success'] ?? false);
        } catch (\Throwable $e) {
            Log::warning('Turnstile siteverify failed', [
                'message' => $e->getMessage(),
            ]);

            return false;
        }
    }

    public function isMobilePlatform(string $platform): bool
    {
        return in_array(mb_strtolower($platform), ['ios', 'android'], true);
    }
}

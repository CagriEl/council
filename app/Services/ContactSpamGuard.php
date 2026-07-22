<?php

namespace App\Services;

class ContactSpamGuard
{
    /**
     * Bilinen bot / yük testi kalıpları (küçük harf, Türkçe karakter normalize).
     *
     * @var list<string>
     */
    private const PATTERNS = [
        'rate test',
        'ratetest',
        'rate_test',
        'rate-test',
        'load test',
        'loadtest',
        'stress test',
        'stresstest',
        'penetration test',
        'sqlmap',
        'nikto',
        'curl test',
        'asdfasdf',
        'qwertyuiop',
        'lorem ipsum',
        '[url=',
        '<a href',
        'viagra',
        'casino',
        'crypto airdrop',
    ];

    public function isSpam(?string ...$fields): bool
    {
        $haystack = $this->normalize(implode(' ', array_filter($fields, fn ($v) => filled($v))));

        if ($haystack === '') {
            return false;
        }

        foreach (self::PATTERNS as $pattern) {
            if (str_contains($haystack, $this->normalize($pattern))) {
                return true;
            }
        }

        // Aynı karakterin aşırı tekrarı (ör. aaaaaaaa)
        if (preg_match('/(.)\1{8,}/u', $haystack)) {
            return true;
        }

        // Sadece rakam / anlamsız kısa mesaj
        if (preg_match('/^\d{1,6}$/', $haystack)) {
            return true;
        }

        return false;
    }

    /**
     * Kayıtlı mesaj payload'ı spam mı?
     *
     * @param  array<string, mixed>|null  $payload
     */
    public function payloadLooksLikeSpam(?array $payload): bool
    {
        if (! is_array($payload)) {
            return false;
        }

        return $this->isSpam(
            isset($payload['name']) ? (string) $payload['name'] : null,
            isset($payload['email']) ? (string) $payload['email'] : null,
            isset($payload['subject']) ? (string) $payload['subject'] : null,
            isset($payload['message']) ? (string) $payload['message'] : null,
            isset($payload['phone']) ? (string) $payload['phone'] : null,
        );
    }

    private function normalize(string $value): string
    {
        $value = mb_strtolower(trim($value));
        $value = strtr($value, [
            'ı' => 'i',
            'İ' => 'i',
            'ş' => 's',
            'Ş' => 's',
            'ğ' => 'g',
            'Ğ' => 'g',
            'ü' => 'u',
            'Ü' => 'u',
            'ö' => 'o',
            'Ö' => 'o',
            'ç' => 'c',
            'Ç' => 'c',
        ]);

        return preg_replace('/\s+/u', ' ', $value) ?? $value;
    }
}

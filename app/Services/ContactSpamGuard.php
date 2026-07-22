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
        return $this->isBlocked(...$fields);
    }

    /**
     * Spam + XSS/HTML + klasik enjeksiyon denemelerini reddeder.
     */
    public function isBlocked(?string ...$fields): bool
    {
        $raw = trim(implode(' ', array_filter($fields, fn ($v) => filled($v))));
        if ($raw === '') {
            return false;
        }

        if ($this->looksLikeXssOrHtml($raw)) {
            return true;
        }

        if ($this->looksLikeInjectionProbe($raw)) {
            return true;
        }

        $haystack = $this->normalize($raw);

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
     * @param  array<string, mixed>|null  $payload
     */
    public function payloadLooksLikeSpam(?array $payload): bool
    {
        if (! is_array($payload)) {
            return false;
        }

        return $this->isBlocked(
            isset($payload['name']) ? (string) $payload['name'] : null,
            isset($payload['email']) ? (string) $payload['email'] : null,
            isset($payload['subject']) ? (string) $payload['subject'] : null,
            isset($payload['message']) ? (string) $payload['message'] : null,
            isset($payload['phone']) ? (string) $payload['phone'] : null,
        );
    }

    private function looksLikeXssOrHtml(string $value): bool
    {
        $lower = mb_strtolower($value);

        // HTML / script etiketleri
        if (preg_match('/<\s*\/?\s*(script|img|svg|iframe|object|embed|link|meta|style|form|input|button|video|audio|base|math|body|html)\b/i', $value)) {
            return true;
        }

        // Genel HTML etiketi denemesi: <tag ...> veya <>
        if (preg_match('/<\s*[a-zA-Z!\/?]/', $value)) {
            return true;
        }

        // Event handler / javascript: URI
        if (preg_match('/\bon[a-z]+\s*=/i', $value)) {
            return true;
        }

        if (str_contains($lower, 'javascript:')
            || str_contains($lower, 'vbscript:')
            || str_contains($lower, 'data:text/html')
            || str_contains($lower, 'expression(')
            || str_contains($lower, '&#')
        ) {
            return true;
        }

        return false;
    }

    private function looksLikeInjectionProbe(string $value): bool
    {
        $lower = mb_strtolower($value);

        $probes = [
            "' or '1'='1",
            '" or "1"="1',
            "' or 1=1",
            '" or 1=1',
            'or 1=1--',
            'union select',
            'drop table',
            'insert into',
            'delete from',
            'update set',
            'information_schema',
            'sleep(',
            'benchmark(',
            '../',
            '..\\',
            '%3cscript',
            '%3cimg',
        ];

        foreach ($probes as $probe) {
            if (str_contains($lower, $probe)) {
                return true;
            }
        }

        return false;
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

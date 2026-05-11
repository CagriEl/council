<?php

namespace App\Support;

use Illuminate\Support\Str;

/**
 * Duyuru URL slug’ları: başlıktan Türkçe uyumlu kısaltma + benzersizlik.
 */
final class AnnouncementSlug
{
    /** Başlıktan slug kökü (benzersizlik eki yok). */
    public static function baseFromTitle(string $title, int $maxChars = 200): string
    {
        $trimmed = Str::limit(trim($title), $maxChars, '');
        $slug = Str::slug($trimmed, '-', 'tr');

        return $slug !== '' ? Str::limit($slug, 220, '') : 'duyuru';
    }

    /**
     * Başlığa göre veritabanında çakışmayan slug üretir.
     *
     * @param  callable(string):bool  $exists  true = slug dolu, başka kullan
     */
    public static function uniqueFromTitle(string $title, callable $exists): string
    {
        $base = self::baseFromTitle($title);

        for ($i = 1; $i < 5000; $i++) {
            $candidate = self::withNumericSuffix($base, $i);
            if (! $exists($candidate)) {
                return $candidate;
            }
        }

        return Str::limit($base, 240, '').'-'.substr(sha1($title.microtime(true)), 0, 8);
    }

    /** $n === 1 → taban slug; aksi halde taban-2, taban-3 … (255 karakter sınırı). */
    public static function withNumericSuffix(string $base, int $n): string
    {
        if ($n <= 1) {
            return Str::limit($base, 255, '');
        }

        $suffix = '-'.$n;

        return Str::limit($base, max(1, 255 - strlen($suffix)), '').$suffix;
    }
}

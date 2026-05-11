<?php

namespace App\Support\Api;

final class FullNameParts
{
    /**
     * Panelde tek alanda tutulan "Ad Soyad" değerini son boşluktan bölerek
     * mobil tarafta ayrı gösterim için ad / soyad üretir.
     *
     * @return array{0: string, 1: string|null} [ad, soyad]
     */
    public static function split(?string $fullName): array
    {
        $s = trim((string) $fullName);
        if ($s === '') {
            return ['', null];
        }

        $pos = mb_strrpos($s, ' ');
        if ($pos === false) {
            return [$s, null];
        }

        $given = trim(mb_substr($s, 0, $pos));
        $family = trim(mb_substr($s, $pos + 1));

        return [
            $given !== '' ? $given : $s,
            $family !== '' ? $family : null,
        ];
    }
}

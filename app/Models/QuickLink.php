<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuickLink extends Model
{
    use HasFactory;

    protected $guarded = [];

    private const E_BELEDIYE_URL = 'https://e-belediye.kirklareli.bel.tr';

    public function getUrlAttribute(?string $value): ?string
    {
        return self::normalizeEBelediyeUrl($value);
    }

    public static function normalizeEBelediyeUrl(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return $value;
        }

        $normalized = strtolower(trim($value));

        if (
            $normalized === '/e-belediye'
            || $normalized === '/e-belediye/'
            || $normalized === 'e-belediye'
            || str_ends_with($normalized, '/e-belediye')
            || str_ends_with($normalized, '/e-belediye/')
            || str_contains($normalized, 'kirklareli.bel.tr/e-belediye')
        ) {
            return self::E_BELEDIYE_URL;
        }

        return $value;
    }
}

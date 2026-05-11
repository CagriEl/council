<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Directorate extends Model
{
    use HasFactory;

    // Veritabanına kaydedilecek alanların izni
    protected $guarded = [];

    // İLİŞKİ TANIMI: Bir müdürlük, bir başkan yardımcısına bağlıdır.
    public function vicePresident(): BelongsTo
    {
        return $this->belongsTo(VicePresident::class);
    }

    /**
     * Müdür kartında gösterilecek unvan: müdürlük adından türetilir (örn. "Bilgi İşlem Müdürü").
     * Panelde "Müdür V." dışında özelleştirilmiş manager_title varsa o kullanılır.
     */
    public function displayManagerRole(): string
    {
        $custom = trim((string) $this->manager_title);
        if ($custom !== '' && $custom !== 'Müdür V.') {
            return $custom;
        }

        $name = trim((string) $this->name);
        if (preg_match('/Müdürlüğü$/u', $name)) {
            return (string) preg_replace('/Müdürlüğü$/u', 'Müdürü', $name);
        }

        return $name !== '' ? $name.' Müdürü' : 'Müdür';
    }
}

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
}
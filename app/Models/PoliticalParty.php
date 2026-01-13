<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PoliticalParty extends Model
{
    use HasFactory;

    /**
     * Veritabanına kaydedilmesine izin verilen alanlar.
     * Bu diziye tablonuzdaki sütun isimlerini eklemelisiniz.
     */
    protected $fillable = [
        'name',         // Parti Adı (Hata veren kısım burasıydı)
        'short_name',   // Kısaltma (Varsa)
        'logo_path',    // Logo (Varsa)
        'color',        // Renk (Varsa)
    ];
}
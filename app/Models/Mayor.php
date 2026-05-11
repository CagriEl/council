<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mayor extends Model
{
    use HasFactory;

    /**
     * Veritabanına kaydedilmesine izin verilen alanlar.
     * Formunuzdaki tüm input isimlerini buraya eklemelisiniz.
     */
    protected $fillable = [
        'name',
        'title',
        'image_path',
        'image',
        'description',
        'message',
        'content',
        'biography',
        'is_active',
    ];
}

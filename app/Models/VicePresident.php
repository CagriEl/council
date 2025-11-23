<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VicePresident extends Model
{
    use HasFactory;

    // Veritabanına toplu atama izni (tüm alanlar doldurulabilir)
    protected $guarded = [];

    /**
     * İlişki: Bir Başkan Yardımcısı, birden fazla müdürlüğe sahip olabilir.
     * Bu fonksiyon sayesinde $vicePresident->directorates diyerek müdürlükleri çekebiliriz.
     */
    public function directorates(): HasMany
    {
        return $this->hasMany(Directorate::class);
    }
}
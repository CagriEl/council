<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CouncilMember extends Model
{
    /**
     * Veritabanına yazılmasına izin verilen alanlar.
     */
    protected $fillable = [
        'name',
        'title',
        'party',              // String olarak parti adı (opsiyonel)
        'image_path',
        'order',              // Sıralama sütunu
        'political_party_id', // İlişki ID'si
        'is_active'
    ];

    /**
     * Varsayılan değerler
     */
    protected $attributes = [
        'is_active' => true,
        'order' => 0,
    ];

    /**
     * İlişkiler
     * Filament'teki Select::make('political_party_id')->relationship(...) 
     * fonksiyonunun çalışması için bu gereklidir.
     */
    public function politicalParty(): BelongsTo
    {
        return $this->belongsTo(PoliticalParty::class);
    }
}
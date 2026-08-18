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

    protected static function booted(): void
    {
        static::saving(function (CouncilMember $member): void {
            // Panelden parti seçilince party metin alanını da güncel tut.
            if ($member->isDirty('political_party_id') && $member->political_party_id) {
                $partyName = PoliticalParty::query()->whereKey($member->political_party_id)->value('name');
                if (is_string($partyName) && $partyName !== '') {
                    $member->party = $partyName;
                }
            }
        });
    }

    /**
     * İlişkiler
     * Filament'teki Select::make('political_party_id')->relationship(...)
     * fonksiyonunun çalışması için bu gereklidir.
     */
    public function politicalParty(): BelongsTo
    {
        return $this->belongsTo(PoliticalParty::class);
    }

    /**
     * Gösterim için parti adı — web ile aynı: political_party ilişkisi öncelikli.
     */
    public function displayPartyName(): ?string
    {
        $relationName = $this->politicalParty?->name;
        $partyField = is_string($this->party) ? trim($this->party) : '';

        if (is_string($relationName) && trim($relationName) !== '') {
            return $this->isIndependentLabel($relationName) ? 'Bağımsız' : $relationName;
        }

        if ($partyField !== '') {
            return $this->isIndependentLabel($partyField) ? 'Bağımsız' : $partyField;
        }

        return null;
    }

    private function isIndependentLabel(?string $label): bool
    {
        if ($label === null || trim($label) === '') {
            return false;
        }

        $normalized = mb_strtolower($label, 'UTF-8');

        return str_contains($normalized, 'bağımsız')
            || str_contains($normalized, 'bagimsiz')
            || str_contains($normalized, 'diğer')
            || str_contains($normalized, 'diger');
    }
}
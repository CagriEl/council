<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    use HasFactory;

    /**
     * BU SATIR EKSİK OLDUĞU İÇİN HATA ALIYORSUNUZ.
     * $guarded = []; demek "Hiçbir sütunu koruma, hepsine veri yazılmasına izin ver" demektir.
     */
    protected $guarded = [];

    public function coverImageUrl(): string
    {
        if (filled($this->image_path)) {
            return asset('storage/'.$this->image_path);
        }

        return asset('images/logo.png');
    }

    /**
     * Ön yüzde: aktif, yayın tarihi gelmiş ve yayından kalkma tarihi geçmemiş kayıtlar.
     */
    public function scopePublishedForPublic(Builder $query): Builder
    {
        $today = now()->toDateString();

        return $query
            ->where('is_active', true)
            ->whereDate('published_at', '<=', $today)
            ->where(function (Builder $q) use ($today) {
                $q->whereNull('unpublished_at')
                    ->orWhereDate('unpublished_at', '>', $today);
            });
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'published_at' => 'date',
            'unpublished_at' => 'date',
            'is_headline' => 'boolean',
            'is_active' => 'boolean',
        ];
    }
}

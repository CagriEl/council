<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;

    /**
     * BU SATIR EKSİK OLDUĞU İÇİN HATA ALIYORSUNUZ.
     * $guarded = []; demek "Hiçbir sütunu koruma, hepsine veri yazılmasına izin ver" demektir.
     */
    protected $guarded = [];

    /**
     * Sitede görünürlük başlangıcı: published_at doluysa o, değilse date.
     * Yayından kalkma: unpublished_at seçilen günden sonra gizlenir (o gün dahil değil).
     */
    public function scopePublishedForPublic(Builder $query): Builder
    {
        $today = now()->toDateString();

        return $query
            ->where('is_active', true)
            ->whereRaw('COALESCE(published_at, date) <= ?', [$today])
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
            'date' => 'date',
            'published_at' => 'date',
            'unpublished_at' => 'date',
            'is_active' => 'boolean',
        ];
    }
}

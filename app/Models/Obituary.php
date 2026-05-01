<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Obituary extends Model
{
    use HasFactory;

    protected $attributes = [
        'burial_place_type' => 'city_cemetery',
        'is_active' => true,
        'sort_order' => 0,
    ];

    protected $fillable = [
        'full_name',
        'death_date',
        'prayer_time',
        'mosque',
        'burial_place_type',
        'burial_place_other',
        'is_active',
        'sort_order',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $obituary): void {
            if (! blank($obituary->sort_order)) {
                return;
            }

            $obituary->sort_order = ((int) self::query()->max('sort_order')) + 1;
        });
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'death_date' => 'date',
            'prayer_time' => 'string',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}

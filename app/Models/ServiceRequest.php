<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ServiceRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'tracking_no',
        'full_name',
        'phone',
        'email',
        'subject',
        'description',
        'status',
        'source',
        'platform',
        'ip_address',
        'assigned_unit',
        'admin_note',
        'response_text',
        'resolved_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'resolved_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $serviceRequest): void {
            if (! blank($serviceRequest->tracking_no)) {
                return;
            }

            $serviceRequest->tracking_no = self::generateTrackingNo();
        });
    }

    public function scopePublicStatuses(Builder $query): Builder
    {
        return $query->select([
            'tracking_no',
            'full_name',
            'subject',
            'status',
            'response_text',
            'created_at',
            'resolved_at',
        ]);
    }

    private static function generateTrackingNo(): string
    {
        do {
            $value = 'TS-'.now()->format('Ymd').'-'.Str::upper(Str::random(6));
        } while (self::query()->where('tracking_no', $value)->exists());

        return $value;
    }
}

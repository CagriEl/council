<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CitizenApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'tracking_no',
        'service_type',
        'full_name',
        'identity_no',
        'phone',
        'email',
        'address',
        'request_summary',
        'status',
        'source',
        'platform',
        'ip_address',
        'assigned_unit',
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
        static::creating(function (self $application): void {
            if (! blank($application->tracking_no)) {
                return;
            }

            $application->tracking_no = self::generateTrackingNo();
        });
    }

    private static function generateTrackingNo(): string
    {
        do {
            $value = 'EB-'.now()->format('Ymd').'-'.Str::upper(Str::random(6));
        } while (self::query()->where('tracking_no', $value)->exists());

        return $value;
    }
}

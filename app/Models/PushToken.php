<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PushToken extends Model
{
    protected $fillable = [
        'token',
        'platform',
        'last_seen_at',
    ];

    protected function casts(): array
    {
        return [
            'last_seen_at' => 'datetime',
        ];
    }
}

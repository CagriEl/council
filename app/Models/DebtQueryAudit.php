<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DebtQueryAudit extends Model
{
    protected $fillable = [
        'request_id',
        'ip_address',
        'user_agent',
        'mukellef_tipi',
        'masked_mukellef_no',
        'captcha_ok',
        'rate_limited',
        'upstream_result_code',
        'status',
        'duration_ms',
    ];

    protected function casts(): array
    {
        return [
            'captcha_ok' => 'boolean',
            'rate_limited' => 'boolean',
            'duration_ms' => 'integer',
        ];
    }
}

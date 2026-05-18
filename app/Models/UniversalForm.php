<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UniversalForm extends Model
{
    protected $table = 'form_submissions';

    protected $fillable = [
        'source',
        'platform',
        'ip_address',
        'data',
    ];

    protected function casts(): array
    {
        return [
            'data' => 'array',
        ];
    }
}

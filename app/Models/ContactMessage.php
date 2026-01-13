<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    protected $fillable = ['platform', 'source', 'payload', 'ip_address'];

    protected $casts = [
        'payload' => 'array', // JSON veriyi otomatik diziye çevirir
    ];
}
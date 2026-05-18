<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CouncilDecision extends Model
{
    /**
     * Veritabanı tablosundaki alanlar
     */
    protected $fillable = [
        'year',
        'month',
        'title',
        'meeting_date',
        'agenda_file',
        'decision_file',
        'commission_file',
    ];

    /**
     * Tarih alanı için tip dönüşümü
     */
    protected $casts = [
        'meeting_date' => 'date',
    ];
}
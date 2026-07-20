<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PushNotificationLog extends Model
{
    protected $fillable = [
        'content_type',
        'content_id',
        'title',
        'body',
        'sent_count',
        'failed_count',
        'sent_by',
    ];

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sent_by');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MobileNotificationLog extends Model
{
    protected $fillable = [
        'student_id',
        'device_id',
        'notification_type',
        'title',
        'body',
        'reference_type',
        'reference_id',
        'unique_event_key',
        'sent_at',
        'delivery_status',
        'error_message',
    ];

    protected $casts = ['sent_at' => 'datetime'];
}

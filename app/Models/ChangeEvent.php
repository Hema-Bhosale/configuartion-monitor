<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChangeEvent extends Model
{
    use HasFactory;
    protected $fillable = [
        'monitored_file_id',
        'file_path',
        'event_type',
        'changes',
        'detected_at',
        'webhook_status',
        'webhook_sent_at',
    ];

    protected $casts = [
        'changes' => 'array',
        'detected_at' => 'datetime',
        'webhook_sent_at' => 'datetime',
    ];
}

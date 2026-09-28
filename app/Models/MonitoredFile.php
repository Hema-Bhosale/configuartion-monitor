<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonitoredFile extends Model
{
    protected $fillable = [
        'path',
        'file_type',
        'status',
        'last_hash',
        'last_snapshot',
        'last_checked_at',
        'last_changed_at',
    ];

    protected $casts = [
        'last_snapshot' => 'array',
        'last_checked_at' => 'datetime',
        'last_changed_at' => 'datetime',
    ];
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ScheduleLog extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'log_content' => 'array',
    ];

    public function loggable(): MorphTo
    {
        return $this->morphTo();
    }
}

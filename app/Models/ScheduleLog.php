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

    public static function recentLogs(
        Model $loggable,
        int $limit = 5): string {
        return ScheduleLog::query()
            ->where('loggable_id', $loggable->id)
            ->where("loggable_type", get_class($loggable))
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->transform(function ($log) {
                $date = $log->created_at->toDateTimeString();
                $content = json_encode($log->log_content, JSON_PRETTY_PRINT);
                return sprintf(
                    "Ran at: %s  Results: %s",
                    $date,
                    $content
                );
            })->implode("\n");
    }
}

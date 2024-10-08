<?php

namespace App\Listeners;

use App\Events\ScheduleLogEvent;
use App\Models\ScheduleLog;
use Illuminate\Contracts\Queue\ShouldQueue;

class ScheduleLogEventListener implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ScheduleLogEvent $event): void
    {
        ScheduleLog::create([
            /** @phpstan-ignore-next-line */
            'loggable_id' => $event->model?->id,
            'loggable_type' => get_class($event->model),
            'log_content' => $event->data,
        ]);
    }
}

<?php

namespace Tests\Feature\Models;

use App\Models\ScheduleLog;
use Tests\TestCase;

class ScheduleLogTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_logging(): void
    {
        $model = ScheduleLog::factory()->create();

        $this->assertNotNull($model->id);
        $this->assertNotNull($model->log_content);
        $this->assertNotNull($model->loggable);
    }
}

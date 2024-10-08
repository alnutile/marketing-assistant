<?php

namespace Tests\Feature\Models;

use App\Domains\Campaigns\StatusEnum;
use App\Models\Project;
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

    public function test_recent_logs(): void
    {
        $project = Project::factory()->create([
            'status' => StatusEnum::Active,
        ]);

        ScheduleLog::factory()->count(5)->create([
            'loggable_id' => $project->id,
            'loggable_type' => Project::class,
        ]);

        $logs = ScheduleLog::recentLogs($project);

        $this->assertNotNull($logs);
    }
}

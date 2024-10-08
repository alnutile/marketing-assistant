<?php

namespace Tests\Feature;

use App\Domains\Campaigns\DailyReportService;
use App\Domains\Campaigns\StatusEnum;
use App\Models\Project;
use App\Models\Task;
use App\Models\Team;
use App\Models\User;
use App\Notifications\DailyReport;
use App\Services\LlmServices\LlmDriverFacade;
use App\Services\LlmServices\Responses\CompletionResponse;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class DailyReportServiceTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_notify(): void
    {

        Notification::fake();

        $user = User::factory()->create();

        $team = Team::factory()->create([
            'user_id' => $user->id,
        ]);

        $team->users()->attach($user, ['role' => 'admin']);

        LlmDriverFacade::shouldReceive('driver->chat')
            ->once()
            ->andReturn(
                CompletionResponse::from([
                    'content' => 'Test Content',
                ])
            );

        $project = Project::factory()->create([
            'status' => StatusEnum::Active,
            'user_id' => $user->id,
            'team_id' => $team->id,
        ]);

        Task::factory()->create([
            'user_id' => $user->id,
            'project_id' => $project->id,
            'completed_at' => null,
        ]);

        (new DailyReportService)->handle();

        Notification::assertSentTo($user, DailyReport::class);

    }
}

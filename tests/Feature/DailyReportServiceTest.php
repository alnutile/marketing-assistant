<?php

namespace Tests\Feature;

use App\Domains\Campaigns\DailyReportService;
use App\Models\Campaign;
use App\Models\Task;
use App\Models\User;
use App\Notifications\DailyReport;
use App\Services\LlmServices\LlmDriverFacade;
use App\Services\LlmServices\Responses\CompletionResponse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
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

        LlmDriverFacade::shouldReceive('driver->chat')
            ->once()
            ->andReturn(
                CompletionResponse::from([
                    'content' => 'Test Content',
                ])
            );

        $campaign = Campaign::factory()->create([
            'user_id' => $user->id,
            'end_date' => now()->addDays(7),
        ]);

        Task::factory()->create([
            'user_id' => $user->id,
            'campaign_id' => $campaign->id,
        ]);

        (new DailyReportService())->handle();

        Notification::assertSentTo($user, DailyReport::class);

    }
}

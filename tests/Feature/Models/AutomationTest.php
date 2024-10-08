<?php

namespace Tests\Feature\Models;

use App\Models\Feedback;
use App\Services\LlmServices\LlmDriverFacade;
use App\Services\LlmServices\Responses\CompletionResponse;
use Facades\App\Services\LlmServices\Orchestration\Orchestrate;
use App\Models\Automation;
use http\Client\Curl\User;
use Tests\TestCase;

class AutomationTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_model(): void
    {
        $automation = Automation::factory()->create([
            'slug' => null,
        ]);

        Feedback::factory()->count(config('assistant.feedback_count'))->create([
            'feedbackable_id' => $automation->id,
            'rating' => true,
            'feedbackable_type' => Automation::class,
        ]);

        $this->assertNotNull($automation->feedback()->latest()->first()?->id);

        $this->assertNotNull($automation->id);
        $this->assertNotNull($automation->name);
        $this->assertNotNull($automation->prompt);
        $this->assertNotNull($automation->slug);
        $this->assertNotNull($automation->enabled);
        $this->assertNotNull($automation->scheduled);
        $this->assertNotNull($automation->user->id);
        $this->assertNotNull($automation->project->id);

        $this->assertNotNull($automation->slug);
    }

    public function test_feebback_count() {
        $automation = Automation::factory()->create([
            'feedback_required' => true,
        ]);

        Feedback::factory()->count(config('assistant.feedback_count'))->create([
            'feedbackable_id' => $automation->id,
            'rating' => true,
            'feedbackable_type' => Automation::class,
        ]);

        $passed = $automation->passedFeedbackCount();

        $this->assertEquals(config('assistant.feedback_count'), $passed);
    }

    public function test_feedback(): void
    {
        Orchestrate::shouldReceive('handle')->never();

        LlmDriverFacade::shouldReceive('driver->completion')
            ->once()
            ->andReturn(
                CompletionResponse::from([
                    'content' => 'Test Content',
                ])
            );

        $automation = Automation::factory()->create([
            'feedback_required' => true,
        ]);


        $automation->run('Test Payload');

        $this->assertDatabaseCount('messages', 2);

    }

    public function test_feedback_exists(): void
    {
        Orchestrate::shouldReceive('handle')->once();

        LlmDriverFacade::shouldReceive('driver->completion')
            ->never();

        $automation = Automation::factory()->create([
            'feedback_required' => true,
        ]);

        Feedback::factory()->count(config('assistant.feedback_count'))->create([
            'feedbackable_id' => $automation->id,
            'rating' => true,
            'feedbackable_type' => Automation::class,
        ]);

        $automation->run('Test Payload');
    }
}

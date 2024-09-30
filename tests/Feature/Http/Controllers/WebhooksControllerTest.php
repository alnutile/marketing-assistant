<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Automation;
use Facades\App\Services\LlmServices\Orchestration\Orchestrate;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class WebhooksControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_slug(): void
    {
        Bus::fake();

        Orchestrate::shouldReceive('handle')->once();

        $automation = Automation::factory()->create([
            'enabled' => true,
        ]);

        $this->post(route('webhooks.show', $automation))
            ->assertStatus(200);

        Bus::assertBatchCount(1);
    }
}

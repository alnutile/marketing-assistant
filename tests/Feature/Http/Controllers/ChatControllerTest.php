<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use App\Services\LlmServices\LlmDriverFacade;
use App\Services\LlmServices\Responses\CompletionResponse;
use Tests\TestCase;

class ChatControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $user = User::factory()->create();
        LlmDriverFacade::shouldReceive('driver->chat')
            ->once()
            ->andReturn(
                CompletionResponse::from([
                    'content' => 'Hello world! um just reply with hello back',
                ])
            );

        $project = Project::factory()
            ->hasAttached(User::factory(2))
            ->create();

        $this->actingAs(User::factory()->create());

        $this->actingAs($user)
            ->post(route('chat.chat', [
                'project' => $project->id,
            ]), [
                'input' => 'Hello World',
            ])
            ->assertSessionHasNoErrors()
            ->assertStatus(302);

        $this->assertNotNull($project->messages->first()->id);
    }
}

<?php

namespace Tests\Feature\Models;

use App\Models\Project;
use App\Models\Message;
use App\Services\LlmServices\RoleEnum;
use Tests\TestCase;

class ProjectTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_model(): void
    {
        $project = Project::factory()->hasAttached(\App\Models\User::factory(2))->create();

        $this->assertNotNull($project->name);
        $this->assertNotNull($project->start_date);
        $this->assertNotNull($project->end_date);
        $this->assertNotNull($project->status);
        $this->assertNotNull($project->content);
        $this->assertNotNull($project->product_or_service);
        $this->assertNotNull($project->target_audience);
        $this->assertNotNull($project->budget);

        $this->assertNotNull($project->users->first()->id);
        $this->assertNotNull($project->team->id);
    }

    public function test_add_input(): void
    {
        $project = Project::factory()->create();

        $message = $project->addInput('Hello World', RoleEnum::User);

        $this->assertNotNull($message->id);
        $this->assertNotNull($message->content);
        $this->assertNotNull($message->role);
        $this->assertNotNull($message->created_at);
        $this->assertNotNull($message->updated_at);
    }

    public function test_message_thread(): void
    {
        $project = Project::factory()
            ->has(Message::factory(5), 'messages')
            ->create();

        $messageThread = $project->getMessageThread();

        $this->assertCount(5, $messageThread);
    }
}

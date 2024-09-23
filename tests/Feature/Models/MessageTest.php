<?php

namespace Tests\Feature\Models;

use App\Models\Message;
use Tests\TestCase;

class MessageTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_model(): void
    {
        $message = Message::factory()->create();

        $this->assertNotNull($message->content);
        $this->assertNotNull($message->role);
        $this->assertNotNull($message->user->id);
        $this->assertNotNull($message->project->id);
        $this->assertNotNull($message->tool_args);
    }
}

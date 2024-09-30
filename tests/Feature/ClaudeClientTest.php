<?php

namespace Tests\Feature;

use App\Services\LlmServices\ClaudeClient;
use App\Services\LlmServices\Requests\MessageInDto;
use Tests\TestCase;

class ClaudeClientTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_mapping(): void
    {

        $messages = collect([
            MessageInDto::from([
                'content' => 'Test Content System',
                'role' => 'system',
            ]),
            MessageInDto::from([
                'content' => 'Test Content User',
                'role' => 'user',
            ]),
            MessageInDto::from([
                'content' => 'Test Content Assistant',
                'role' => 'assistant',
            ]),
        ]);
        $results = (new ClaudeClient)->remapMessages($messages);

        $this->assertCount(3, $results);
        $this->assertEquals('user', $results[0]['role']);
        $this->assertEquals('assistant', $results[1]['role']);
        $this->assertEquals('user', $results[2]['role']);
    }
}

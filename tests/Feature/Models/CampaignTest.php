<?php

namespace Tests\Feature\Models;

use App\Models\Campaign;
use App\Models\Message;
use App\Services\LlmServices\RoleEnum;
use Tests\TestCase;

class CampaignTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_model(): void
    {
        $campaign = Campaign::factory()->hasAttached(\App\Models\User::factory(2))->create();

        $this->assertNotNull($campaign->name);
        $this->assertNotNull($campaign->start_date);
        $this->assertNotNull($campaign->end_date);
        $this->assertNotNull($campaign->status);
        $this->assertNotNull($campaign->content);
        $this->assertNotNull($campaign->product_or_service);
        $this->assertNotNull($campaign->target_audience);
        $this->assertNotNull($campaign->budget);

        $this->assertNotNull($campaign->users->first()->id);
    }

    public function test_add_input(): void
    {
        $campaign = Campaign::factory()->create();

        $message = $campaign->addInput('Hello World', RoleEnum::User);

        $this->assertNotNull($message->id);
        $this->assertNotNull($message->content);
        $this->assertNotNull($message->role);
        $this->assertNotNull($message->created_at);
        $this->assertNotNull($message->updated_at);
    }

    public function test_message_thread(): void
    {
        $campaign = Campaign::factory()
            ->has(Message::factory(5), 'messages')
            ->create();

        $messageThread = $campaign->getMessageThread();

        $this->assertCount(5, $messageThread);
    }
}

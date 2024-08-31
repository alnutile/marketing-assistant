<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Campaign;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TaskControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_index(): void
    {
        $user = User::factory()->create();

        $campaign = Campaign::factory()->create([
            'user_id' => $user->id,
        ]);

        $task = Task::factory()->create([
            'campaign_id' => $campaign->id,
        ]);

        $this->actingAs($user)->get(
            route('tasks.index', [
                'campaign' => $campaign->id,
            ])
        )->assertStatus(200);
    }
}

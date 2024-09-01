<?php

namespace Tests\Feature\Http\Controllers;

use App\Domains\Campaigns\ChatStatusEnum;
use App\Domains\Campaigns\ProductServiceEnum;
use App\Domains\Campaigns\StatusEnum;
use App\Models\Campaign;
use App\Models\Team;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class CampaignControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_index(): void
    {
        $user = User::factory()->create();

        $team = Team::factory()->create([
            'user_id' => $user->id,
        ]);

        $team->users()->attach($user, ['role' => 'admin']);

        $user->current_team_id = $team->id;
        $user->updateQuietly();

        Campaign::factory(3)->create([
            'user_id' => $user->id,
            'team_id' => $team->id,
        ]);

        $teamNot = Team::factory()->create();
        Campaign::factory()->create([
            'team_id' => $teamNot->id,
        ]);

        $this->actingAs($user)->get(
            route('campaigns.index')
        )->assertStatus(200)
            ->assertInertia(fn (Assert $assert) => $assert
                ->has('campaigns.data', 3)
            );
    }

    public function test_create(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(
            route('campaigns.create')
        )->assertStatus(200)
            ->assertInertia(fn (Assert $assert) => $assert
                ->has('statuses')
                ->has('productServices')
            );
    }

    public function test_store(): void
    {
        $user = User::factory()->create();

        $team = Team::factory()->create([
            'user_id' => $user->id,
        ]);

        $user->current_team_id = $team->id;
        $user->updateQuietly();

        $this->actingAs($user)->post(
            route('campaigns.store'), [
                'name' => 'Test Campaign',
                'start_date' => '2023-01-01',
                'end_date' => '2023-01-01',
                'content' => 'Test Description',
                'status' => StatusEnum::Draft->value,
                'product_or_service' => ProductServiceEnum::PhysicalProduct->value,
                'target_audience' => 'Test Audience',
                'budget' => '1000',
            ]
        )
            ->assertSessionHasNoErrors()
            ->assertStatus(302);

        $campaign = Campaign::first();

        $this->assertEquals(
            ChatStatusEnum::Pending->value,
            $campaign->chat_status->value
        );

        $this->assertNotNull($campaign->user_id);
        $this->assertNotNull($campaign->team_id);

    }

    public function test_show(): void
    {
        $user = User::factory()->create();

        $campaign = Campaign::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->actingAs($user)->get(
            route('campaigns.show', $campaign)
        )->assertStatus(200)
            ->assertInertia(fn (Assert $assert) => $assert
                ->has('campaign.data')
            );
    }

    public function test_edit(): void
    {
        $user = User::factory()->create();

        $campaign = Campaign::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->actingAs($user)->get(
            route('campaigns.edit', $campaign)
        )->assertStatus(200)
            ->assertInertia(fn (Assert $assert) => $assert
                ->has('campaign.data')
            );
    }

    public function test_update(): void
    {
        $user = User::factory()->create();

        $campaign = Campaign::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->actingAs($user)->put(
            route('campaigns.update', $campaign)
        )->assertStatus(302);
    }

    public function test_destroy(): void
    {
        $user = User::factory()->create();

        $campaign = Campaign::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->actingAs($user)->delete(
            route('campaigns.destroy', $campaign)
        )->assertStatus(302);

        $this->assertDatabaseCount('campaigns', 0);
    }
}

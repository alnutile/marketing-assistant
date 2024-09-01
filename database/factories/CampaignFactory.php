<?php

namespace Database\Factories;

use App\Domains\Campaigns\ChatStatusEnum;
use App\Domains\Campaigns\ProductServiceEnum;
use App\Domains\Campaigns\StatusEnum;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Campaign>
 */
class CampaignFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'start_date' => $this->faker->date(),
            'user_id' => User::factory(),
            'end_date' => $this->faker->date(),
            'status' => StatusEnum::Draft,
            'team_id' => Team::factory(),
            'chat_status' => ChatStatusEnum::Complete,
            'content' => $this->faker->paragraph(),
            'product_or_service' => ProductServiceEnum::PhysicalProduct,
            'target_audience' => $this->faker->paragraph(),
            'budget' => $this->faker->randomFloat(2, 0, 1000),
        ];
    }
}

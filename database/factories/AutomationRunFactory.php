<?php

namespace Database\Factories;

use App\Models\Automation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AutomationRun>
 */
class AutomationRunFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'automation_id' => Automation::factory(),
            'payload' => $this->faker->paragraph(), // longText
            'status' => $this->faker->randomElement(['pending', 'running', 'completed']),
            'completed_at' => $this->faker->date(),
        ];
    }
}

<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Feedback>
 */
class FeedbackFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'comment' => $this->faker->paragraph(3, true),
            'rating' => $this->faker->boolean(),
            'feedbackable_type' => \App\Models\Automation::class,
            'feedbackable_id' => \App\Models\Automation::factory(),
        ];
    }
}

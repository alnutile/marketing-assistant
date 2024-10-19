<?php

namespace Database\Factories;

use App\Domains\Reports\StatusEnum;
use App\Models\Report;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ReportPage>
 */
class ReportPageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'report_id' => Report::factory(),
            'content' => fake()->paragraphs(3, true),
            'score' => random_int(1, 5),
            'status' => StatusEnum::Pending,
        ];
    }
}

<?php

namespace Tests\Feature\Models;

use App\Models\Feedback;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FeedbackTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_model(): void
    {
        $model = Feedback::factory()->create();

        $this->assertNotNull($model->id);
        $this->assertNotNull($model->feedbackable->id);
        $this->assertNotNull($model->comment);
        $this->assertNotNull($model->rating);
    }
}

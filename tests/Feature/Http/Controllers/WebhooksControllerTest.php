<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Automation;
use Tests\TestCase;

class WebhooksControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_slug(): void
    {
        $automation = Automation::factory()->create();

        $this->get(route('webhooks.show', $automation))
            ->assertStatus(200);
    }
}

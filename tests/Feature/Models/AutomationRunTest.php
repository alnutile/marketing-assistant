<?php

namespace Tests\Feature\Models;

use App\Models\AutomationRun;
use Tests\TestCase;

class AutomationRunTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_model(): void
    {
        $automationRun = AutomationRun::factory()->create();

        $this->assertNotNull($automationRun->id);
        $this->assertNotNull($automationRun->automation->id);
        $this->assertNotNull($automationRun->payload);
        $this->assertNotNull($automationRun->completed_at); // nullable
        $this->assertNotNull($automationRun->status);

    }
}

<?php

namespace Tests\Feature\Models;

use App\Models\Automation;
use Tests\TestCase;

class AutomationTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_model(): void
    {
        $automation = Automation::factory()->create([
            'slug' => null,
        ]);

        $this->assertNotNull($automation->id);
        $this->assertNotNull($automation->name);
        $this->assertNotNull($automation->prompt);
        $this->assertNotNull($automation->slug);
        $this->assertNotNull($automation->enabled);
        $this->assertNotNull($automation->scheduled);
        $this->assertNotNull($automation->user->id);
        $this->assertNotNull($automation->project->id);

        $this->assertNotNull($automation->slug);
    }
}

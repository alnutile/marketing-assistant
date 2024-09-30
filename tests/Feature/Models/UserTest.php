<?php

namespace Feature\Models;

use App\Models\User;
use Tests\TestCase;

class OpenAiClientTest.pUserTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_model(): void
    {
        $user = User::factory()
            ->withPersonalTeam()
            ->create();

        $this->assertNotNull($user->ownedTeams);
        $this->assertNotNull($user->teams);

    }
}

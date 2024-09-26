<?php

namespace Tests\Feature;

use App\Services\LlmServices\OpenAiClient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class OpenAiClientTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_function(): void
    {
        $shouldBe = get_fixture('openai_chat_payload_with_functions.json', false);

        $client = new OpenAiClient();
        $functions = $client->getFunctions();
        $functions = (new OpenAiClient())->remapFunctions($functions);
        dd($functions);
    }
}

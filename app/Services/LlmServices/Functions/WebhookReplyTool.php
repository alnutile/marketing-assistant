<?php

namespace App\Services\LlmServices\Functions;

use App\Models\Project;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WebhookReplyTool extends FunctionContract
{
    protected string $name = 'reply_to_webhook';

    protected string $description = 'This tool can be used to reply to a webhook. So if a prompt ask to send a request back to a url this is the tool for that.';

    public function handle(
        Project $project,
        array $args = []): FunctionResponse
    {

        $url = data_get($args, 'url', null);

        $payload = data_get($args, 'payload', []);

        Log::info('WebhookReplyTool called', [
            'url' => $url,
            'payload' => $payload,
        ]);

        if (! $url) {
            Log::info('No url provided');

            return FunctionResponse::from([
                'content' => 'No message provided',
            ]);
        }

        $response = Http::post($url, $payload);

        $status = $response->status();

        return FunctionResponse::from([
            'content' => sprintf('Reply sent to %s with payload %s status %s', $url, json_encode($payload), $status),
        ]);
    }

    /**
     * @return PropertyDto[]
     */
    protected function getProperties(): array
    {
        return [
            new PropertyDto(
                name: 'url',
                description: 'The url to send the reply to',
                type: 'string',
                required: true,
            ),

            new PropertyDto(
                name: 'payload',
                description: 'The payload to send to that url',
                type: 'object',
                required: false,
            ),
        ];
    }
}

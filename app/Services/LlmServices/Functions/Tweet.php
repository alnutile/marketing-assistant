<?php

namespace App\Services\LlmServices\Functions;

use App\Models\Project;
use Illuminate\Support\Facades\Log;

class Tweet extends FunctionContract
{
    protected string $name = 'tweet';

    protected string $description = 'Send a twitter message.';

    public function handle(
        Project $project,
        array $args = []): FunctionResponse
    {
        Log::info('Tweet called');

        $message = data_get($args, 'message', null);

        if (! $message) {
            Log::info('No message provided');

            return FunctionResponse::from([
                'content' => 'No message provided',
            ]);
        }

        Log::info('Sending tweet', [
            'message' => $message,
        ]);

        //@TODO install pennant
        //setup Tweet tool using Httputa

        return FunctionResponse::from([
            'content' => json_encode($args),
        ]);
    }

    /**
     * @return PropertyDto[]
     */
    protected function getProperties(): array
    {
        return [
            new PropertyDto(
                name: 'message',
                description: 'The message that will be tweeted including the hashtag',
                type: 'string',
                required: true,
            ),
        ];
    }
}

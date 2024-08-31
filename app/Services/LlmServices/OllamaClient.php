<?php

namespace App\Services\LlmServices;

use App\Services\LlmServices\Requests\MessageInDto;
use App\Services\LlmServices\Responses\CompletionResponse;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Laravel\Pennant\Feature;

class OllamaClient extends BaseClient
{
    protected string $driver = 'ollama';

    /**
     * @param  MessageInDto[]  $messages
     *
     * @throws BindingResolutionException
     */
    public function chat(array $messages): CompletionResponse
    {
        Log::info('LlmDriver::OllamaClient::completion');

        $messages = $this->remapMessages($messages);

        $response = $this->getClient()->post('/chat', [
            'model' => $this->getConfig('ollama')['models']['completion_model'],
            'messages' => $messages,
            'stream' => false,
        ]);

        $results = $response->json()['message']['content'];

        return new CompletionResponse($results);
    }

    public function completion(string $prompt): CompletionResponse
    {
        Log::info('LlmDriver::Ollama::completion');

        $payload = [
            'model' => $this->getConfig('ollama')['models']['completion_model'],
            'prompt' => $prompt,
            'stream' => false,
        ];

        if ($this->format === 'json') {
            $payload['format'] = 'json';
        }

        $response = $this->getClient()->post('/generate', $payload);

        $results = $response->json()['response'];

        return new CompletionResponse($results);
    }

    protected function getClient()
    {
        $api_token = $this->getConfig('ollama')['api_key'];
        $baseUrl = $this->getConfig('ollama')['api_url'];
        if (! $api_token || ! $baseUrl) {
            throw new \Exception('Ollama API Base URL or Token not found');
        }

        return Http::withHeaders([
            'content-type' => 'application/json',
        ])
            ->timeout(120)
            ->baseUrl($baseUrl);
    }

    public function getFunctions(): array
    {
        $functions = LlmDriverFacade::getFunctions();

        return collect($functions)->map(function ($function) {
            $function = $function->toArray();
            $properties = [];
            $required = [];

            foreach (data_get($function, 'parameters.properties', []) as $property) {
                $name = data_get($property, 'name');

                if (data_get($property, 'required', false)) {
                    $required[] = $name;
                }

                $properties[$name] = [
                    'description' => data_get($property, 'description', null),
                    'type' => data_get($property, 'type', 'string'),
                    'default' => data_get($property, 'default', null),
                ];
            }

            return [
                'name' => data_get($function, 'name'),
                'description' => data_get($function, 'description'),
                'parameters' => $properties,
                'required' => $required,
            ];

        })->toArray();
    }

    public function isAsync(): bool
    {
        return false;
    }

    public function onQueue(): string
    {
        return 'ollama';
    }

    public function remapMessages(array $messages): array
    {
        $messages = collect($messages)->map(function ($message) {
            return $message->toArray();
        });

        return $messages->values()->toArray();
    }
}

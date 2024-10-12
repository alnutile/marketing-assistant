<?php

namespace App\Services\LlmServices;

use App\Services\LlmServices\Functions\FunctionDto;
use App\Services\LlmServices\Requests\MessageInDto;
use App\Services\LlmServices\Responses\CompletionResponse;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use OpenAI\Laravel\Facades\OpenAI;

class OpenAiClient extends BaseClient
{
    protected string $driver = 'openai';

    protected string $baseUrl = 'https://api.openai.com/v1';

    /**
     * @param  MessageInDto[]  $messages
     */
    public function chat(array|Collection $messages): CompletionResponse
    {

        $token = $this->getConfig('openai')['api_key'];
        $payload = [
            'model' => $this->getConfig('openai')['models']['chat_model'],
            'messages' => $this->messagesToArray($messages),
        ];

        $payload = $this->modifyPayload($payload);

        //put_fixture('openai_chat_payload_raw.json', $payload);

        $response = Http::withHeaders([
            'Content-type' => 'application/json',
        ])
            ->withToken($token)
            ->baseUrl($this->baseUrl)
            ->timeout(240)
            ->post('/chat/completions', $payload);

        if ($response->failed()) {
            Log::error('OpenAi API Error ', [
                'error' => $response->body(),
            ]);

            throw new \Exception('OpenAi API Error Chat');
        }

        put_fixture('openai_chat_response.json', $payload);

        return new CompletionResponse($response->json());
    }

    public function modifyPayload(array $payload, bool $noTools = false): array
    {
        Log::info('LlmDriver::OpenAi::modifyPayload', [
            'payload' => $payload,
        ]);

        $payload = $this->addJsonFormat($payload);

        $payload['tools'] = $this->getFunctions();

        return $payload;
    }

    public function getFunctions(): array
    {
        $functions = parent::getFunctions();

        return $this->remapFunctions($functions);
    }

    public function addJsonFormat(array $payload): array
    {
        if ($this->format === 'json') {
            $payload['response_format'] = [
                'type' => 'json_object',
            ];
        }

        return $payload;
    }

    /**
     * @param  FunctionDto[]  $functions
     */
    public function remapFunctions(array $functions): array
    {
        return collect($functions)->map(function ($function) {
            $properties = [];
            $required = [];

            $type = data_get($function, 'parameters.type', 'object');

            foreach (data_get($function, 'parameters.properties', []) as $property) {
                $name = data_get($property, 'name');

                if (data_get($property, 'required', false)) {
                    $required[] = $name;
                }

                $properties[$name] = [
                    'description' => data_get($property, 'description', null),
                    'type' => data_get($property, 'type', 'string'),
                ];
            }

            $itemsOrProperties = $properties;

            if ($type === 'array') {
                $itemsOrProperties = [
                    'results' => [
                        'type' => 'array',
                        'description' => 'The results of prompt',
                        'items' => [
                            'type' => 'object',
                            'properties' => $properties,
                        ],
                    ],
                ];
            } else {
                $itemsOrProperties = [
                    'results' => [
                        'type' => 'object',
                        'description' => 'The results of prompt',
                        'items' => [
                            'type' => 'object',
                            'properties' => $properties,
                        ],
                    ],
                ];
            }

            return [
                'type' => 'function',
                'function' => [
                    'name' => data_get($function, 'name'),
                    'description' => data_get($function, 'description'),
                    'parameters' => [
                        'type' => 'object',
                        'properties' => $itemsOrProperties,
                    ],
                ],
            ];
        })->toArray();
    }

    protected function getClient(): PendingRequest
    {
        $token = $this->getConfig('openai')['api_key'];

        if (! $token) {
            throw new \Exception('Missing token');
        }

        return Http::withHeaders([
            'content-type' => 'application/json',
            'Authorization' => 'Bearer '.$token,
        ])->withToken($token);
    }

    public function vision(string $prompt, string $base64Image, string $type = 'png'): CompletionResponse
    {

        $payload = [
            'model' => $this->getConfig('openai')['models']['vision'],
            'messages' => [
                [
                    'role' => 'user',
                    'content' => [
                        [
                            'type' => 'text',
                            'text' => $prompt,
                        ],
                        [
                            'type' => 'image_url',
                            'image_url' => [
                                'url' => sprintf('data:image/%s;base64,%s', $type, $base64Image),
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $response = $this->getClient()
            ->baseUrl($this->baseUrl)
            ->timeout(240)
            ->retry(3, function (int $attempt, \Exception $exception) {
                Log::info('OpenAi API Error going to retry', [
                    'attempt' => $attempt,
                    'error' => $exception->getMessage(),
                ]);

                return 60000;
            })
            ->post('/chat/completions', $payload);

        //put_fixture('image_results.json', $response->json());

        $results = null;

        if (! $response->successful()) {
            Log::error('Vision results', [
                'error' => $response->json()['error']['message'],
            ]);
            throw new \Exception('Vision API Error');
        }

        foreach ($response->json()['choices'] as $result) {
            $results = data_get($result, 'message.content');
            Log::info('Vision results', [
                'finish_reason' => data_get($result, 'finish_reason'),
            ]);
        }

        return new CompletionResponse($results);
    }

    public function completion(string $prompt,
        float $temperature = 0,
    ): CompletionResponse {
        $config = [
            'model' => $this->getConfig('openai')['models']['completion_model'],
            'messages' => [
                ['role' => 'user', 'content' => $prompt],
            ],
        ];

        if ($this->format === 'json') {
            $config['response_format'] = [
                'type' => 'json_object',
            ];
        }

        $response = OpenAI::chat()->create($config);

        $results = null;

        foreach ($response->choices as $result) {
            $results = $result->message->content;
        }

        return new CompletionResponse($results);
    }
}

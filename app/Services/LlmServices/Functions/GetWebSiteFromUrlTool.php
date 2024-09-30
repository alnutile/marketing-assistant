<?php

namespace App\Services\LlmServices\Functions;

use App\Models\Message;
use App\Models\Project;
use Facades\App\Services\LlmServices\Functions\Helpers\GetPage;
use Illuminate\Support\Facades\Log;

class GetWebSiteFromUrlTool extends \App\Services\LlmServices\Functions\FunctionContract
{

    protected string $name = 'get_web_site_from_url';

    protected string $description = 'If the prompt requires content from a url or urls and has a url then you can pass them one at a time here and it will return the markdown of that page ';

    public function handle(
        Project $project,
        array $args = []): FunctionResponse
    {
        Log::info('[LaraChain] GetWebSiteFromUrlTool called');

        $url = data_get($args, 'url', null);

        if (! $url) {
            throw new \Exception('No url found');
        }

        Log::info('[LaraChain] GetWebSiteFromUrlTool called', [
            'url' => $url,
        ]);

        $results = GetPage::handle($url);

        $results = <<<CONTENT
        URL: $url
        Body:
        $results
        CONTENT;

        return FunctionResponse::from([
            'content' => $results
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
                description: 'The URL To get',
                type: 'string',
                required: true,
            ),
        ];
    }

    public function runAsBatch(): bool
    {
        return false;
    }
}

<?php

namespace App\Services\LlmServices\Functions\Helpers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use League\HTMLToMarkdown\Converter\CodeConverter;
use League\HTMLToMarkdown\Converter\PreformattedConverter;
use League\HTMLToMarkdown\Converter\TableConverter;
use League\HTMLToMarkdown\Converter\TextConverter;
use League\HTMLToMarkdown\Environment;
use League\HTMLToMarkdown\HtmlConverter;
use Spatie\Browsershot\Browsershot;

class GetPage
{

    public function handle(string $url, bool $parseHtml = true): string
    {
        $results = Browsershot::url($url)
            ->userAgent('DailyAI Studio Browser 1.0, helping users automate workflows')
            ->dismissDialogs()
            ->fullPage();

        return $this->parseHtml($results->bodyHtml());
    }

    public function parseHtml(string $html): string
    {
        $environment = new Environment([
            'strip_tags' => true,
            'suppress_errors' => true,
            'hard_break' => true,
            'strip_placeholder_links' => true,
            'remove_nodes' => 'nav footer header script style meta',
        ]);
        $environment->addConverter(new TableConverter());
        $environment->addConverter(new CodeConverter());
        $environment->addConverter(new PreformattedConverter());
        $environment->addConverter(new TextConverter());

        $converter = new HtmlConverter($environment);

        $markdown = $converter->convert($html);

        return str($markdown)->trim()->toString();

    }
}

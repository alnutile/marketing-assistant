<?php

namespace App\Jobs;

use App\Services\LlmServices\LlmDriverFacade;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ReviewReportPageJob implements ShouldQueue
{
    use Batchable, Queueable;

    protected int $tries = 1;

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        if ($this->batch()->cancelled()) {
            // Determine if the batch has been cancelled...

            return;
        }

        //this could be a huge page lets assume not.
        try {
            $content = $this->reportPage->content;
            $originalPrompt = $this->reportPage->report->prompt;
            $prompt = <<<PROMPT
## System or Goal Prompt
$originalPrompt

## Format
The format is as follows so this one page can fit in the space of all the other pages as a final prompt to get a summary

1-3 highlights of good or not good areas of evaluation

## Example

**Good job at...**
the section "some info here..." did a good job at x

**there is a grammar issue**
The line "...." it might be better as "...."

**unclear term**
this term ... could be made more clear

## END EXAMPLE

* Since this is part of a bigger report we need to keep these page level reports short and concise

## Content For Prompt
$content

PROMPT;

            $results = LlmDriverFacade::driver('groq')
                ->completion($prompt);

            $review = $results->content;

            $prompt = <<<PROMPT
## Score Prompt
Consider the following results of a review prompt.
Return the score 1 - 5.
1 is low and poor
5 being the best. To rate how well this section did
0 is reserved for no results

The original prompt to helo with the score is below in the **original prompt** section

**format**
as integer no surrounding text

**example output**
1
2
5

**original prompt**
$originalPrompt

**JUST A NUMBER NOTHING ELSE**

## The Results Of the Review that you will consider in your scoring
$review

PROMPT;

            $results = LlmDriverFacade::driver('groq')
                ->completion($prompt);

            $this->reportPage->updateQuietly([
                'review' => $review,
                'score' => ! is_int($results->content) ?? 0,
                'status' => \App\Domains\Reports\StatusEnum::Completed,
            ]);

        } catch (\Exception $e) {
            $this->reportPage->updateQuietly([
                'status' => \App\Domains\Reports\StatusEnum::Failed,
            ]);
        }

    }
}

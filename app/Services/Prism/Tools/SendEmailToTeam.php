<?php

namespace App\Services\Prism\Tools;

use App\Models\Project;
use App\Models\Task;
use App\Notifications\DailyReport;
use EchoLabs\Prism\Tool;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class SendEmailToTeam extends Tool
{


    public function __construct(public Project $project)
    {
        $this
            ->as('send_email_to_team')
            ->for('Send an email to the team')
            ->withStringParameter(name:'message', description: "The message for the body of the email", required: true)
            ->using($this);
    }

    public function __invoke(string $message): string
    {
        Log::info('PrismOrchestrate::send_email_to_team called');

        $count = 0;

        foreach ($this->project->team->users as $user) {
            Notification::send($user, new DailyReport($message, $this->project));
            $count++;
        }

        return sprintf('Sent email to the %d members of the team', $count);
    }

}

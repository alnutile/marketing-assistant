<?php

namespace App\Services\LlmServices\Functions;

use App\Models\Project;
use App\Models\Task;
use App\Notifications\DailyReport;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class SendEmailToTeam extends FunctionContract
{
    protected string $name = 'send_email_to_team';

    protected string $description = 'Send an email to the team';

    public function handle(
        Project $project,
        array $args = []): FunctionResponse
    {
        Log::info('SendEmailToTeam called');

        $message = data_get($args, 'message', null);

        if(!$message) {
            Log::info('No message provided');
            return FunctionResponse::from([
                'content' => 'No message provided',
            ]);
        }

        foreach($project->team->users as $user) {
            Notification::send($user, new DailyReport($message, $project));
        }

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
                description: 'The message for the body of the email',
                type: 'string',
                required: true,
            ),
        ];
    }
}

<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Resources\ProjectResource;
use App\Jobs\SchedulerProjectJob;
use App\Models\Project;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewProject extends ViewRecord
{
    protected static string $resource = ProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\Action::make('chat with project')
                ->icon('heroicon-m-chat-bubble-bottom-center-text')
                ->action(function (Project $project) {
                    return to_route('projects.show', $project);
                }),
            Actions\Action::make('run scheduler')
                ->color('secondary')
                ->icon('heroicon-m-clock')
                ->action(function (Project $project) {
                    Notification::make()
                        ->title('Running scheduler')
                        ->success()
                        ->send();
                    SchedulerProjectJob::dispatchSync($project);
                    Notification::make()
                        ->title('Done')
                        ->success()
                        ->send();
                }),
        ];
    }
}

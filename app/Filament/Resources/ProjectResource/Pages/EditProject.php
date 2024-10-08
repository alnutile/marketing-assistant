<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Resources\ProjectResource;
use App\Jobs\SchedulerProjectJob;
use App\Models\Project;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditProject extends EditRecord
{
    protected static string $resource = ProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
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

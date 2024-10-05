<?php

namespace App\Filament\Resources\ScheduleLogResource\Pages;

use App\Filament\Resources\ScheduleLogResource;
use App\Jobs\SchedulerProjectJob;
use App\Models\Project;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;

class ListScheduleLogs extends ListRecords
{
    protected static string $resource = ScheduleLogResource::class;

    protected function getHeaderActions(): array
    {
        return [

            Actions\Action::make('run scheduler')
                ->color('secondary')
                ->icon('heroicon-o-bolt')
                ->action(function () {

                    $jobs = [];

                    Project::active()->get()->each(function ($project) use (&$jobs) {
                        $jobs[] = new SchedulerProjectJob($project);
                    });

                    Bus::batch($jobs)
                        ->name("Scheduler ran manually")
                        ->before(function(Batch $batch) {
                            Notification::make()
                                ->title('Running Scheduler')
                                ->success()
                                ->send();
                        })
                        ->finally(function(Batch $batch) {
                            Notification::make()
                                ->title('Done!')
                                ->success()
                                ->send();
                        })
                        ->onQueue("scheduler")
                        ->allowFailures()
                        ->dispatch();

                })
        ];
    }
}

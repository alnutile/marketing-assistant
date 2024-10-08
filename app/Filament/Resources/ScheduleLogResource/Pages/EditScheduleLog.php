<?php

namespace App\Filament\Resources\ScheduleLogResource\Pages;

use App\Filament\Resources\ScheduleLogResource;
use App\Jobs\SchedulerProjectJob;
use App\Models\Project;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditScheduleLog extends EditRecord
{
    protected static string $resource = ScheduleLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),

        ];
    }
}

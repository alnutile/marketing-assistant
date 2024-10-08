<?php

namespace App\Filament\Resources\ScheduleLogResource\Pages;

use App\Filament\Resources\ScheduleLogResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewScheduleLog extends ViewRecord
{
    protected static string $resource = ScheduleLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}

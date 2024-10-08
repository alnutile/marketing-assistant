<?php

namespace App\Filament\Resources\AutomationRunResource\Pages;

use App\Filament\Resources\AutomationRunResource;
use Filament\Resources\Pages\ViewRecord;

class ViewAutomationRun extends ViewRecord
{
    protected static string $resource = AutomationRunResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}

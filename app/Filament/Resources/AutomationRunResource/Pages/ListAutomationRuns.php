<?php

namespace App\Filament\Resources\AutomationRunResource\Pages;

use App\Filament\Resources\AutomationRunResource;
use Filament\Resources\Pages\ListRecords;

class ListAutomationRuns extends ListRecords
{
    protected static string $resource = AutomationRunResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}

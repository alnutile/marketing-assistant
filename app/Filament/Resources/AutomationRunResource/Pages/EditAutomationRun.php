<?php

namespace App\Filament\Resources\AutomationRunResource\Pages;

use App\Filament\Resources\AutomationRunResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAutomationRun extends EditRecord
{
    protected static string $resource = AutomationRunResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}

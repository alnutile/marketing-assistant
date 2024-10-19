<?php

namespace App\Filament\Resources\ReportPageResource\Pages;

use App\Filament\Resources\ReportPageResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditReportPage extends EditRecord
{
    protected static string $resource = ReportPageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}

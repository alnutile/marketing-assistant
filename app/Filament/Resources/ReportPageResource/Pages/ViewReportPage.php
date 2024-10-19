<?php

namespace App\Filament\Resources\ReportPageResource\Pages;

use App\Filament\Resources\ReportPageResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewReportPage extends ViewRecord
{
    protected static string $resource = ReportPageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}

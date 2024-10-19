<?php

namespace App\Filament\Resources\ReportPageResource\Pages;

use App\Filament\Resources\ReportPageResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListReportPages extends ListRecords
{
    protected static string $resource = ReportPageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

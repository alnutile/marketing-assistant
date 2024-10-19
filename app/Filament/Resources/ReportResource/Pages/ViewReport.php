<?php

namespace App\Filament\Resources\ReportResource\Pages;

use App\Filament\Resources\ReportResource;
use App\Models\Report;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewReport extends ViewRecord
{
    protected static string $resource = ReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->slideOver()
                ->form(Report::getForm()),
            Actions\Action::make('review')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Run Report?')
                ->modalDescription('This will remove previous results and take a few minutes depending on the size of the report')
                ->modalSubmitActionLabel('Yes, run report')
                ->icon('heroicon-o-document-text')
                ->action(function (Report $report) {

                    Notification::make()
                        ->title('Reviewing Report')
                        ->success()
                        ->send();

                    \Facades\App\Domains\Reports\CreateReport::handle($report);

                    Notification::make()
                        ->title('Report in the Queue will be done shortly')
                        ->success()
                        ->send();
                }),
        ];
    }
}

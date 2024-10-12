<?php

namespace App\Domains\Reports;

use Filament\Support\Contracts\HasLabel;

enum ReportTypes: string implements HasLabel
{
    case StandardsChecking = 'standards_checking';

    public function getLabel(): ?string
    {
        return str($this->name)->headline();
    }
}

<?php

namespace App\Domains\Reports;

use Filament\Support\Contracts\HasLabel;

enum StatusEnum: string implements HasLabel
{
    case Pending = 'pending';
    case Running = 'running';
    case Completed = 'completed';
    case Failed = 'failed';

    public function getLabel(): ?string
    {
        return str($this->value)->headline();
    }
}

<?php

namespace App\Domains\Campaigns;

use App\Helpers\EnumHelperTrait;

enum StatusEnum: string
{
    use EnumHelperTrait;

    case DRAFT = 'draft';
    case ACTIVE = 'active';
    case PAUSED = 'paused';
    case COMPLETED = 'completed';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::ACTIVE => 'Active',
            self::PAUSED => 'Paused',
            self::COMPLETED => 'Completed',
        };
    }
}

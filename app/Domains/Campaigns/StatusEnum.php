<?php

namespace App\Domains\Campaigns;

use App\Helpers\EnumHelperTrait;

enum StatusEnum: string
{
    use EnumHelperTrait;

    case Draft = 'draft';
    case Active = 'active';
    case Paused = 'paused';
    case Completed = 'completed';

}

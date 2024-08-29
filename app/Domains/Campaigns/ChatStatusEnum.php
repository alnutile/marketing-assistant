<?php

namespace App\Domains\Campaigns;

enum ChatStatusEnum: string
{
    case Complete = 'complete';
    case InProgress = 'in_progress';
    case Paused = 'paused';
    case Cancelled = 'cancelled';
    case Pending = 'pending';
}

<?php

namespace App\Http\Controllers;

use App\Jobs\AutomationRunnerJob;
use App\Models\Automation;
use Illuminate\Support\Facades\Bus;

class WebhooksController extends Controller
{
    public function trigger(Automation $automation)
    {
        Bus::batch([
            new AutomationRunnerJob($automation, json_encode(request()->all())),
        ])
            ->allowFailures()
            ->dispatch();

        return response()->json('ok');
    }
}

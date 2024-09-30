<?php

namespace App\Http\Controllers;

use App\Jobs\AutomationRunnerJob;
use App\Models\Automation;
use Illuminate\Support\Facades\Bus;

class WebhooksController extends Controller
{
    public function show(Automation $automation)
    {
        Bus::batch([
            new AutomationRunnerJob($automation),
        ])
            ->allowFailures()
            ->dispatch();

        return response()->json([
            'message' => 'Hello World',
        ]);
    }
}

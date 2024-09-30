<?php

namespace App\Http\Controllers;

use App\Jobs\AutomationRunnerJob;
use App\Models\Automation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;

class WebhooksController extends Controller
{

    public function show(Automation $automation)
    {
        Bus::dispatch(new AutomationRunnerJob())
            ->allowFailures()
            ->dispatch();

        return response()->json([
            'message' => 'Hello World',
        ]);
    }
}

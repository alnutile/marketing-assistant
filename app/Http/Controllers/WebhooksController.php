<?php

namespace App\Http\Controllers;

use App\Jobs\AutomationRunnerJob;
use App\Models\Automation;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;

class WebhooksController extends Controller
{
    public function trigger(Automation $automation)
    {
        Log::info('Webhook triggered', [
            'automation' => $automation->name,
            'payload' => request()->all(),
        ]);

        Bus::batch([
            new AutomationRunnerJob($automation, json_encode(request()->all())),
        ])
            ->name(sprintf('Automation Runner id: %d - %s', $automation->id, $automation->name))
            ->allowFailures()
            ->onQueue('automations')
            ->dispatch();

        return response()->json([
            'status' => 'ok',
        ], 200);
    }
}

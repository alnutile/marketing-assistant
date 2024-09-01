<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use Facades\App\Domains\Campaigns\DailyReportService;

class DailyReportSendController extends Controller
{
    public function __invoke(Campaign $campaign)
    {
        DailyReportService::sendReport($campaign);
        \request()->session()->flash('flash.banner', 'Sent!');

        return back();
    }
}

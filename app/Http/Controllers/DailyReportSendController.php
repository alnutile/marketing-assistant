<?php

namespace App\Http\Controllers;

use Facades\App\Domains\Campaigns\DailyReportService;
use App\Models\Campaign;
use Illuminate\Http\Request;

class DailyReportSendController extends Controller
{

    public function  __invoke(Campaign $campaign)
    {
        DailyReportService::sendReport($campaign);
        \request()->session()->flash('flash.banner', 'Sent!');
        return back();
    }
}

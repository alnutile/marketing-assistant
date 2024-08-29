<?php

namespace App\Http\Controllers;

use App\Domains\Campaigns\ChatStatusEnum;
use App\Domains\Campaigns\ProductServiceEnum;
use App\Domains\Campaigns\StatusEnum;
use App\Http\Resources\CampaignResource;
use App\Models\Campaign;

class CampaignController extends Controller
{
    public function index()
    {
        $campaigns = CampaignResource::collection(
            Campaign::user(auth()->user()->id)->paginate()
        );

        return inertia('Campaigns/Index', [
            'campaigns' => $campaigns,
        ]);
    }

    public function create()
    {
        return inertia('Campaigns/Create', [
            'statuses' => StatusEnum::selectOptions(),
            'productServices' => ProductServiceEnum::selectOptions(),
        ]);
    }

    public function store()
    {
        $validated = request()->validate([
            'name' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'status' => 'required',
            'content' => 'required',
            'product_or_service' => 'required',
            'target_audience' => 'required',
            'budget' => 'required',
        ]);

        $validated['chat_status'] = ChatStatusEnum::Pending->value;
        $campaign = Campaign::create($validated);

        return redirect()->route('campaigns.show', $campaign);
    }

    public function show(Campaign $campaign)
    {
        return inertia('Campaigns/Show', [
            'campaign' => new CampaignResource($campaign),
        ]);
    }

    public function edit(Campaign $campaign)
    {
        return inertia('Campaigns/Edit', [
            'campaign' => new CampaignResource($campaign),
        ]);
    }

    public function update(Campaign $campaign)
    {
        $validated = request()->validate([
            'name' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'status' => 'required',
            'content' => 'required',
            'product_or_service' => 'required',
            'target_audience' => 'required',
            'budget' => 'required',
        ]);

        $campaign->update($validated);

        return redirect()->route('campaigns.show', $campaign);
    }

    public function destroy(Campaign $campaign)
    {
        $campaign->delete();

        return redirect()->route('campaigns.index');
    }
}

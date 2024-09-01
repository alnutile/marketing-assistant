<?php

namespace App\Http\Controllers;

use App\Domains\Campaigns\ChatStatusEnum;
use App\Domains\Campaigns\ProductServiceEnum;
use App\Domains\Campaigns\StatusEnum;
use App\Http\Resources\CampaignResource;
use App\Http\Resources\CampaignResourceShow;
use App\Http\Resources\MessageResource;
use App\Models\Campaign;
use Facades\App\Domains\Campaigns\KickOffCampaign;

class CampaignController extends Controller
{
    public function index()
    {

        $campaigns = CampaignResource::collection(
            Campaign::whereIn(
                'team_id',
                auth()->user()
                    ->teams
                    ->pluck('id')
                    ->values()
                    ->toArray()
            )->paginate()
        );

        return inertia('Campaigns/Index', [
            'copy' => get_copy('campaigns.index'),
            'campaigns' => $campaigns,
        ]);
    }

    public function create()
    {
        $defaultContent = <<<'DEFAULT_CONTENT'
## Unique Selling Proposition (USP)
[What makes your product/service unique? Why should your target audience choose you over competitors?]

## Key Messages
- [Message 1]
- [Message 2]
- [Message 3]


## Success Metrics
- [Metric 1]: [Target]
- [Metric 2]: [Target]
- [Metric 3]: [Target]

## Social Media

  * Twitter
  * LinkedIn
  * Facebook
  * Medium

## Additional Notes
[Any other important information or considerations for this campaign]


DEFAULT_CONTENT;

        return inertia('Campaigns/Create', [
            'content_start' => $defaultContent,
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
        $validated['user_id'] = auth()->user()->id;
        $validated['team_id'] = auth()->user()->current_team_id;
        $campaign = Campaign::create($validated);

        return redirect()->route('campaigns.show', $campaign);
    }

    public function show(Campaign $campaign)
    {
        return inertia('Campaigns/Show', [
            'campaign' => new CampaignResourceShow($campaign),
            'messages' => MessageResource::collection($campaign->messages()
                ->notSystem()
                ->latest()->get()),
        ]);
    }

    public function edit(Campaign $campaign)
    {
        return inertia('Campaigns/Edit', [
            'statuses' => StatusEnum::selectOptions(),
            'productServices' => ProductServiceEnum::selectOptions(),
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

        request()->session()->flash('flash.banner', 'Updated');

        return back();
    }

    public function destroy(Campaign $campaign)
    {
        $campaign->delete();

        return redirect()->route('campaigns.index');
    }

    public function kickOff(Campaign $campaign)
    {
        KickOffCampaign::handle($campaign);
        request()->session()->flash('flash.banner', 'Done!');

        return back();
    }
}

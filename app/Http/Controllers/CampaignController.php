<?php

namespace App\Http\Controllers;

use App\Domains\Campaigns\ChatStatusEnum;
use App\Domains\Campaigns\ProductServiceEnum;
use App\Domains\Campaigns\StatusEnum;
use App\Http\Resources\MessageResource;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\ProjectResourceShow;
use App\Models\Project;
use Facades\App\Domains\Campaigns\KickOffCampaign;

class CampaignController extends Controller
{
    public function index()
    {

        $projects = ProjectResource::collection(
            Project::whereIn(
                'team_id',
                auth()->user()
                    ->teams
                    ->pluck('id')
                    ->values()
                    ->toArray()
            )->paginate()
        );

        return inertia('Campaigns/Index', [
            'copy' => get_copy('projects.index'),
            'projects' => $projects,
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
[Any other important information or considerations for this project]


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
            'system_prompt' => 'required',
            'scheduler_prompt' => 'required',
            'content' => 'required',
            'product_or_service' => 'nullable',
            'target_audience' => 'nullable',
            'budget' => 'nullable',
        ]);

        $validated['chat_status'] = ChatStatusEnum::Pending->value;
        $validated['user_id'] = auth()->user()->id;
        $validated['team_id'] = auth()->user()->current_team_id;
        $project = Project::create($validated);

        return redirect()->route('projects.show', $project);
    }

    public function show(Project $project)
    {
        return inertia('Campaigns/Show', [
            'project' => new ProjectResourceShow($project),
            'messages' => MessageResource::collection($project->messages()
                ->notSystem()
                ->notTool()
                ->latest()
                ->paginate(3)),
        ]);
    }

    public function edit(Project $project)
    {
        return inertia('Campaigns/Edit', [
            'statuses' => StatusEnum::selectOptions(),
            'productServices' => ProductServiceEnum::selectOptions(),
            'project' => new ProjectResource($project),
        ]);
    }

    public function update(Project $project)
    {
        $validated = request()->validate([
            'name' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'status' => 'required',
            'system_prompt' => 'required',
            'scheduler_prompt' => 'required',
            'content' => 'required',
            'product_or_service' => 'nullable',
            'target_audience' => 'nullable',
            'budget' => 'nullable',
        ]);

        $project->update($validated);

        request()->session()->flash('flash.banner', 'Updated');

        return back();
    }

    public function destroy(Project $project)
    {
        $project->delete();

        return redirect()->route('projects.index');
    }

    public function kickOff(Project $project)
    {
        KickOffCampaign::handle($project);
        request()->session()->flash('flash.banner', 'Done!');

        return back();
    }
}

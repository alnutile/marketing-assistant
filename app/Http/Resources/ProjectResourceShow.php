<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResourceShow extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'start_date' => $this->start_date?->format('Y-m-d'),
            'end_date' => $this->end_date?->format('Y-m-d'),
            'status' => $this->status->value,
            'status_formatted' => str($this->status->name)->headline(),
            'chat_status' => $this->chat_status->value,
            'chat_status_formatted' => str($this->chat_status->name)->headline(),
            'content' => $this->content,
            'product_or_service' => $this->product_or_service?->value,
            'target_audience' => $this->target_audience,
            'system_prompt' => $this->system_prompt,
            'scheduler_prompt' => $this->scheduler_prompt,
            'system_prompt_formatted' => str($this->system_prompt)->markdown(),
            'scheduler_prompt_formatted' => str($this->scheduler_prompt)->markdown(),
            'budget' => $this->budget,
            'users' => $this->team->allUsers(),
            'team' => TeamResource::make($this->team),
            'user' => ($this->user_id) ? new UserResource($this->user) : null,
        ];
    }
}

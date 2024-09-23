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
            'product_or_service' => $this->product_or_service->value,
            'target_audience' => $this->target_audience,
            'budget' => $this->budget,
            'user' => ($this->user_id) ? new UserResource($this->user) : null,
        ];
    }
}

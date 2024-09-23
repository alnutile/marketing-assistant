<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
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
            'content' => str($this->content)->markdown(),
            'content_raw' => $this->content,
            'updated_at' => $this->updated_at->diffForHumans(),
            'role' => $this->role->value,
            'user' => new UserResource($this->user),
            'project_id' => $this->project_id,
        ];
    }
}

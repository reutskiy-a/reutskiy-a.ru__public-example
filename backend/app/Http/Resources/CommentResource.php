<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
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
            'body' => $this->body,
            'date' => $this->created_at->locale('ru')->diffForHumans(),
            'user' => new UserResource($this->user),
            'owner' => $this->owner ?? false,
        ];
    }
}

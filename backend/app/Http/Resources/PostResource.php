<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $imagePath = $this->image->url ?? null;

        return [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,
            'image_path' => $imagePath,
            'date' => $this->date,
            'likes_count' => $this->likedUsers()->count(),
            'is_liked' => $this->isLiked ?? false,
            'comments_count' => $this->comments_count
        ];
    }
}

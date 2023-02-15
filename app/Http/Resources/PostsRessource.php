<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostsRessource extends JsonResource
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
            'title' => $this->title,
            'body' => $this->body,
            'created_at' => Carbon::parse($this->created_at)->diffForHumans(),
            'likes_count' => $this->likes->count(),
            'likes' => $this->likes,
            'user' => $this->user,
            'comments_count' => $this->comments->count(),
            'comments' => CommentsRessource::collection($this->comments),
            'images' => $this->images,
            //objectif is recuperate if user is like this post
            'is_like' => $this->likes->contains('user_id', auth()->user()->id),
        ];
    }
}

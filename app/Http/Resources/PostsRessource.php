<?php

namespace App\Http\Resources;

use App\Models\Post;
use App\Models\PostsLikes;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

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
            'likes_count' => $this->likes->count(),
            'likes' => $this->likes,
            'user' => $this->user,
            'comments_count' => $this->comments->count(),
            //get 100 last comments
            'comments' => CommentsRessource::collection($this->comments)->sortByDesc('created_at'),
            'images' => ImagesRessource::collection($this->images),
            'is_like' => PostsLikes::where('user_id', auth()->user()->id)->where('post_id', $this->id)->exists(),
            'created_at' => Carbon::parse($this->created_at)->diffForHumans(),
        ];
    }
}

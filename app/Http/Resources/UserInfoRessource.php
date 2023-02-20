<?php

namespace App\Http\Resources;

use App\Models\Friends;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserInfoRessource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'avatar' => $this->avatar,
            'username' => $this->username,
            'bio' => $this->bio,
            'is_admin' => $this->is_admin,
            'email_verified_at' => $this->email_verified_at,
            'name' => $this->name,
            'email' => $this->email,
            //friends array to object
            'friends' => FriendsRessource::collection($this->friends),
            //get post of user paginate
            'posts' => PostsRessource::collection($this->posts)->sortByDesc('created_at'),
        ];
    }
}

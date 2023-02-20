<?php

namespace App\Http\Resources;

use App\Models\Friends;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FriendsRessource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'friend_id' => $this->friend_id,
            'accepted' => $this->accepted,
            'user' => $this->friend,
        ];
    }
}

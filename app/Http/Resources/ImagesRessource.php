<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ImagesRessource extends JsonResource
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
            //return the image url from the storage/app/public/images folder
            'image' => asset('storage/images/' . $this->image),
            'post_id' => $this->post_id,
        ];
    }
}

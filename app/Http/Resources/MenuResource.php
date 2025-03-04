<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MenuResource extends JsonResource
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
            'description' => $this->description,
            'price' => $this->price,
            'stock' => $this->stock,
            'image' => url('/api/image/' . $this->encrypted_image_url),
            'type' => $this->type,
            'category' => new CategoryResource($this->whenLoaded('category')),
            'discount' => new DiscountResource($this->whenLoaded('discount'))
        ];
    }
}

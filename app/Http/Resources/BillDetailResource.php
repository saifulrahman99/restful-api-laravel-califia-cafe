<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BillDetailResource extends JsonResource
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
            'menu' => $this->menu ? [
                'id' => $this->menu->id,
                'name' => $this->menu->name,
                'image' => url('/api/images/' . $this->menu->encrypted_image_url),
                'deleted_at' => $this->menu->deleted_at,
            ] : null,
            'qty' => $this->qty,
            'price' => $this->price,
            'discount_price' => $this->discount_price,
            'total_price' => ($this->price - $this->discount_price) * $this->qty,
            'note' => $this->note,
            'bill_detail_toppings' => BillDetailToppingResource::collection($this->billDetailToppings),
        ];
    }
}

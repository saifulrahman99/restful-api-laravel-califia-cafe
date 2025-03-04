<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BillDetailToppingResource extends JsonResource
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
            'topping' => [
                'id'=> $this->topping->id,
                'name'=> $this->topping->name,
            ],
            'qty' => $this->qty,
            'price' => $this->price,
        ];
    }
}

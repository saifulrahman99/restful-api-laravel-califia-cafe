<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BillResource extends JsonResource
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
            'customer_name' => $this->customer_name,
            'trans_date' => $this->trans_date,
            'invoice_no' => $this->invoice_no,
            'table' => $this->table,
            'order_type' => $this->order_type,
            'status' => $this->status,
            'final_price' => $this->final_price,
            'bill_details' => BillDetailResource::collection($this->billDetails),
        ];
    }
}

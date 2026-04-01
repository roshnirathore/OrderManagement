<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
       
        return [
            'id'=>$this->id,
            'customer_email'=>$this->user->email,
            'customer_id'=>$this->user_id,
            'total_amount'=>(float) $this->total_amount,
            'status'=>$this->status,
            'items' => $this->items->map(function ($item) {
                return [
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => (float) $item->price,
                ];
            })
        ];
    }
}

<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'product_name' => $this->product->name,
            'product_price' => $this->product->price,
            'quantity' => $this->quantity,
            'subtotal' => $this->subtotal,
        ];
    }
}

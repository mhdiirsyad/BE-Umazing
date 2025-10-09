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
            'id' => $this->id,
            'user_id' => $this->user_id,
            'user' => $this->user->name,
            'total' => $this->total,
            'status' => $this->status,
            'address' => $this->address,
            'created_at' => $this->created_at->format('Y-m-d H:i'),
            'completed_at' => $this->completed_at ? $this->completed_at->format('Y-m-d H:i') : '-',
            'order_items' => OrderItemResource::collection($this->orderItems),
        ];
    }
}

<?php

namespace Modules\Order\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'user' => [
                'name' => $this->user->name,
                'email' => $this->user->email,
            ],
            'total' => $this->total,
            'status' => $this->status,
            'items' => OrderItemResource::collection($this->items),
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}

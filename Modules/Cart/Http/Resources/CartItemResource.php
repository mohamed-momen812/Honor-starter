<?php

namespace Modules\Cart\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'product' => [
                'name' => $this->product->name,
                'price' => $this->product->price,
                'sku' => $this->product->sku,
            ],
            'quantity' => $this->quantity,
            'subtotal' => $this->quantity * $this->product->price,
        ];
    }
}

<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductVariantResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'size' => $this->size,
            'color' => $this->color,
            'color_hex' => $this->color_hex,
            'sku' => $this->sku,
            'stock_quantity' => $this->stock_quantity,
            'is_in_stock' => $this->stock_quantity > 0,
            'effective_price' => $this->effective_price,
            'formatted_effective_price' => '₱' . number_format($this->effective_price, 2),
            'price_adjustment' => (float) $this->price_adjustment,
        ];
    }
}

<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'subtotal' => $this->subtotal,
            'formatted_subtotal' => '₱' . number_format($this->subtotal, 2),
            'total_items' => $this->total_items,
            'items' => $this->items->map(function ($item) {
                $variant = $item->variant;
                $product = $variant?->product;
                return [
                    'id' => $item->id,
                    'quantity' => $item->quantity,
                    'variant' => $variant ? [
                        'id' => $variant->id,
                        'size' => $variant->size,
                        'color' => $variant->color,
                        'color_hex' => $variant->color_hex,
                        'sku' => $variant->sku,
                        'stock_quantity' => $variant->stock_quantity,
                        'effective_price' => $variant->effective_price,
                        'formatted_price' => '₱' . number_format($variant->effective_price, 2),
                    ] : null,
                    'product' => $product ? [
                        'id' => $product->id,
                        'name' => $product->name,
                        'slug' => $product->slug,
                        'primary_image' => $product->primary_image,
                    ] : null,
                    'line_total' => $variant ? $variant->effective_price * $item->quantity : 0,
                ];
            }),
        ];
    }
}

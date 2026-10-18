<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'base_price' => (float) $this->base_price,
            'formatted_price' => '₱' . number_format($this->base_price, 2),
            'primary_image' => $this->primary_image,
            'images' => $this->images ?? [$this->primary_image],
            'status' => $this->status,
            'is_featured' => (bool) $this->is_featured,
            'is_bestseller' => (bool) $this->is_bestseller,
            'category' => [
                'id' => $this->category?->id,
                'name' => $this->category?->name,
                'slug' => $this->category?->slug,
            ],
            'total_stock' => $this->total_stock,
            'is_in_stock' => $this->total_stock > 0,
            'available_colors' => $this->available_colors,
            'available_sizes' => $this->available_sizes,
            'variants' => ProductVariantResource::collection($this->variants),
        ];
    }
}

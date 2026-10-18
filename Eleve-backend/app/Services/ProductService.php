<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductService
{
    public function createProduct(array $data): Product
    {
        return DB::transaction(function () use ($data) {
            $slug = Str::slug($data['name']);
            // Ensure unique slug
            $existing = Product::where('slug', $slug)->count();
            if ($existing > 0) {
                $slug = $slug . '-' . Str::random(4);
            }

            $product = Product::create([
                'category_id' => $data['category_id'],
                'name' => $data['name'],
                'slug' => $slug,
                'description' => $data['description'],
                'base_price' => $data['base_price'],
                'primary_image' => $data['primary_image'],
                'images' => $data['images'] ?? [$data['primary_image']],
                'status' => $data['status'] ?? 'active',
                'is_featured' => !empty($data['is_featured']),
                'is_bestseller' => !empty($data['is_bestseller']),
            ]);

            // Create initial variants if provided
            if (!empty($data['variants']) && is_array($data['variants'])) {
                foreach ($data['variants'] as $v) {
                    if (empty($v['size']) || empty($v['color'])) continue;
                    ProductVariant::create([
                        'product_id' => $product->id,
                        'size' => (string) $v['size'],
                        'color' => $v['color'],
                        'color_hex' => $v['color_hex'] ?? '#171717',
                        'sku' => $v['sku'] ?? ('ELE-' . strtoupper(Str::random(6))),
                        'stock_quantity' => max(0, (int) ($v['stock_quantity'] ?? 0)),
                        'price_adjustment' => (float) ($v['price_adjustment'] ?? 0.00),
                    ]);
                }
            }

            return $product->load(['category', 'variants']);
        });
    }

    public function updateProduct(Product $product, array $data): Product
    {
        return DB::transaction(function () use ($product, $data) {
            $updatePayload = [
                'category_id' => $data['category_id'] ?? $product->category_id,
                'name' => $data['name'] ?? $product->name,
                'description' => $data['description'] ?? $product->description,
                'base_price' => $data['base_price'] ?? $product->base_price,
                'status' => $data['status'] ?? $product->status,
                'is_featured' => isset($data['is_featured']) ? (bool) $data['is_featured'] : $product->is_featured,
                'is_bestseller' => isset($data['is_bestseller']) ? (bool) $data['is_bestseller'] : $product->is_bestseller,
            ];

            if (!empty($data['primary_image'])) {
                $updatePayload['primary_image'] = $data['primary_image'];
            }
            if (!empty($data['images'])) {
                $updatePayload['images'] = $data['images'];
            }

            $product->update($updatePayload);

            return $product->fresh(['category', 'variants']);
        });
    }
}

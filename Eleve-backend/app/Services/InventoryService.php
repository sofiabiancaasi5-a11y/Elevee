<?php

namespace App\Services;

use App\Models\ProductVariant;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    public function updateVariantStock(int $variantId, int $quantity): ProductVariant
    {
        return DB::transaction(function () use ($variantId, $quantity) {
            $variant = ProductVariant::lockForUpdate()->findOrFail($variantId);
            $variant->stock_quantity = max(0, $quantity);
            $variant->save();

            return $variant;
        });
    }

    public function getLowStockAlerts(int $threshold = 5): Collection
    {
        return ProductVariant::with(['product.category'])
            ->where('stock_quantity', '<=', $threshold)
            ->orderBy('stock_quantity', 'asc')
            ->get();
    }
}

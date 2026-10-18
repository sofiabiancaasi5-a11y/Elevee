<?php

namespace App\Repositories\Eloquent;

use App\Models\ProductVariant;
use App\Repositories\Contracts\InventoryRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class InventoryRepository implements InventoryRepositoryInterface
{
    public function getVariantsPaginated(array $filters, int $perPage = 20): LengthAwarePaginator
    {
        $query = ProductVariant::with(['product.category']);

        if (!empty($filters['search'])) {
            $term = '%' . trim($filters['search']) . '%';
            $query->where(function ($q) use ($term) {
                $q->where('sku', 'like', $term)
                  ->orWhere('color', 'like', $term)
                  ->orWhere('size', 'like', $term)
                  ->orWhereHas('product', function ($pq) use ($term) {
                      $pq->where('name', 'like', $term);
                  });
            });
        }

        if (!empty($filters['stock_status'])) {
            if ($filters['stock_status'] === 'low') {
                $query->where('stock_quantity', '<=', 5)->where('stock_quantity', '>', 0);
            } elseif ($filters['stock_status'] === 'out') {
                $query->where('stock_quantity', '<=', 0);
            } elseif ($filters['stock_status'] === 'in') {
                $query->where('stock_quantity', '>', 5);
            }
        }

        return $query->orderBy('stock_quantity', 'asc')->paginate($perPage);
    }

    public function getLowStockVariants(int $threshold = 5): Collection
    {
        return ProductVariant::with('product')
            ->where('stock_quantity', '<=', $threshold)
            ->orderBy('stock_quantity', 'asc')
            ->get();
    }

    public function findVariantById(int $variantId): ?ProductVariant
    {
        return ProductVariant::with('product')->find($variantId);
    }

    public function updateStock(int $variantId, int $newQuantity): bool
    {
        $variant = ProductVariant::find($variantId);
        if (!$variant) return false;
        return $variant->update(['stock_quantity' => max(0, $newQuantity)]);
    }

    public function adjustStock(int $variantId, int $delta): bool
    {
        // Atomic decrement/increment using PDO raw statement
        return (bool) DB::table('product_variants')
            ->where('id', $variantId)
            ->update([
                'stock_quantity' => DB::raw("GREATEST(0, stock_quantity + ({$delta}))"),
                'updated_at' => now(),
            ]);
    }
}

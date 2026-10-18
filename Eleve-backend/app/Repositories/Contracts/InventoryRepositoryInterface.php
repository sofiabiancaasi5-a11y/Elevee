<?php

namespace App\Repositories\Contracts;

use App\Models\ProductVariant;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface InventoryRepositoryInterface
{
    public function getVariantsPaginated(array $filters, int $perPage = 20): LengthAwarePaginator;

    public function getLowStockVariants(int $threshold = 5): Collection;

    public function findVariantById(int $variantId): ?ProductVariant;

    public function updateStock(int $variantId, int $newQuantity): bool;

    public function adjustStock(int $variantId, int $delta): bool;
}

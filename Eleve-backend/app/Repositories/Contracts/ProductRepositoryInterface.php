<?php

namespace App\Repositories\Contracts;

use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface ProductRepositoryInterface
{
    public function getFilteredPaginated(array $filters, int $perPage = 12): LengthAwarePaginator;

    public function getFeatured(int $limit = 6): Collection;

    public function getBestsellers(int $limit = 6): Collection;

    public function findByIdOrSlug(string|int $identifier): ?Product;

    public function create(array $data): Product;

    public function update(Product $product, array $data): Product;

    public function delete(Product $product): bool;
}

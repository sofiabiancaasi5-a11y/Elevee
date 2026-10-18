<?php

namespace App\Repositories\Eloquent;

use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ProductRepository implements ProductRepositoryInterface
{
    public function getFilteredPaginated(array $filters, int $perPage = 12): LengthAwarePaginator
    {
        $query = Product::with(['category', 'variants'])
            ->where('status', 'active');

        // Filter by category slug or id
        if (!empty($filters['category'])) {
            $cat = $filters['category'];
            $query->whereHas('category', function ($q) use ($cat) {
                if (is_numeric($cat)) {
                    $q->where('id', $cat);
                } else {
                    $q->where('slug', $cat);
                }
            });
        }

        // Search by keyword
        if (!empty($filters['search'])) {
            $search = '%' . trim($filters['search']) . '%';
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', $search)
                  ->orWhere('description', 'like', $search);
            });
        }

        // Filter by size
        if (!empty($filters['size'])) {
            $size = $filters['size'];
            $query->whereHas('variants', function ($q) use ($size) {
                $q->where('size', $size)
                  ->where('stock_quantity', '>', 0);
            });
        }

        // Filter by color
        if (!empty($filters['color'])) {
            $color = $filters['color'];
            $query->whereHas('variants', function ($q) use ($color) {
                $q->where('color', 'like', '%' . $color . '%')
                  ->where('stock_quantity', '>', 0);
            });
        }

        // Filter by price range
        if (isset($filters['min_price'])) {
            $query->where('base_price', '>=', (float) $filters['min_price']);
        }
        if (isset($filters['max_price'])) {
            $query->where('base_price', '<=', (float) $filters['max_price']);
        }

        // Sorting
        $sort = $filters['sort'] ?? 'newest';
        match ($sort) {
            'price_asc' => $query->orderBy('base_price', 'asc'),
            'price_desc' => $query->orderBy('base_price', 'desc'),
            'popular' => $query->orderBy('is_bestseller', 'desc')->orderBy('created_at', 'desc'),
            default => $query->orderBy('created_at', 'desc'),
        };

        return $query->paginate($perPage);
    }

    public function getFeatured(int $limit = 6): Collection
    {
        return Product::with(['category', 'variants'])
            ->where('status', 'active')
            ->where('is_featured', true)
            ->take($limit)
            ->get();
    }

    public function getBestsellers(int $limit = 6): Collection
    {
        return Product::with(['category', 'variants'])
            ->where('status', 'active')
            ->where('is_bestseller', true)
            ->take($limit)
            ->get();
    }

    public function findByIdOrSlug(string|int $identifier): ?Product
    {
        $query = Product::with(['category', 'variants']);
        if (is_numeric($identifier)) {
            return $query->where('id', $identifier)->first();
        }
        return $query->where('slug', $identifier)->first();
    }

    public function create(array $data): Product
    {
        return Product::create($data);
    }

    public function update(Product $product, array $data): Product
    {
        $product->update($data);
        return $product->fresh(['category', 'variants']);
    }

    public function delete(Product $product): bool
    {
        return (bool) $product->delete();
    }
}

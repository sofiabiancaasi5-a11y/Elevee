<?php

namespace App\Repositories\Eloquent;

use App\Models\Category;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use Illuminate\Support\Collection;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function getAllActive(): Collection
    {
        return Category::where('is_active', true)->withCount(['products' => function ($q) {
            $q->where('status', 'active');
        }])->get();
    }

    public function findBySlug(string $slug): ?Category
    {
        return Category::where('slug', $slug)->first();
    }

    public function findById(int $id): ?Category
    {
        return Category::find($id);
    }
}

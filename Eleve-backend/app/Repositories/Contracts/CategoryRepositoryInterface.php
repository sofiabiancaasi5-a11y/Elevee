<?php

namespace App\Repositories\Contracts;

use App\Models\Category;
use Illuminate\Support\Collection;

interface CategoryRepositoryInterface
{
    public function getAllActive(): Collection;

    public function findBySlug(string $slug): ?Category;

    public function findById(int $id): ?Category;
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected CategoryRepositoryInterface $categoryRepo
    ) {}

    public function index(): JsonResponse
    {
        $categories = $this->categoryRepo->getAllActive();
        return $this->successResponse(CategoryResource::collection($categories), 'Categories retrieved');
    }

    public function show(string $slug): JsonResponse
    {
        $category = $this->categoryRepo->findBySlug($slug);
        if (!$category) {
            return $this->errorResponse('Category not found', 404);
        }
        return $this->successResponse(new CategoryResource($category), 'Category retrieved');
    }
}

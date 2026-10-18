<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductDetailResource;
use App\Http\Resources\ProductResource;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected ProductRepositoryInterface $productRepo
    ) {}

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['category', 'search', 'size', 'color', 'min_price', 'max_price', 'sort']);
        $perPage = min(50, max(1, (int) $request->input('per_page', 12)));

        $paginator = $this->productRepo->getFilteredPaginated($filters, $perPage);

        return $this->successResponse(
            ProductResource::collection($paginator->items()),
            'Products retrieved successfully',
            200,
            [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ]
        );
    }

    public function featured(): JsonResponse
    {
        $products = $this->productRepo->getFeatured(6);
        return $this->successResponse(ProductResource::collection($products), 'Featured products retrieved');
    }

    public function bestsellers(): JsonResponse
    {
        $products = $this->productRepo->getBestsellers(6);
        return $this->successResponse(ProductResource::collection($products), 'Bestseller products retrieved');
    }

    public function show(string|int $identifier): JsonResponse
    {
        $product = $this->productRepo->findByIdOrSlug($identifier);

        if (!$product) {
            return $this->errorResponse('Product not found', 404);
        }

        return $this->successResponse(new ProductDetailResource($product), 'Product details retrieved');
    }
}

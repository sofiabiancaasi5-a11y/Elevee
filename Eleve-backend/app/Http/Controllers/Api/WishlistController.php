<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Wishlist;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $wishlists = Wishlist::with('product.category', 'product.variants')
            ->where('user_id', $user->id)
            ->get();

        $products = $wishlists->pluck('product')->filter();

        return $this->successResponse(ProductResource::collection($products), 'Wishlist retrieved');
    }

    public function toggle(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $user = $request->user();
        $existing = Wishlist::where('user_id', $user->id)
            ->where('product_id', $validated['product_id'])
            ->first();

        if ($existing) {
            $existing->delete();
            return $this->successResponse(['is_favorite' => false], 'Removed from wishlist');
        }

        Wishlist::create([
            'user_id' => $user->id,
            'product_id' => $validated['product_id'],
        ]);

        return $this->successResponse(['is_favorite' => true], 'Added to wishlist');
    }
}

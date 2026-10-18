<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CartResource;
use App\Services\CartService;
use App\Traits\ApiResponse;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected CartService $cartService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $sessionToken = $request->header('X-Session-Token');
        $cart = $this->cartService->getOrCreateCart($request->user(), $sessionToken);

        return $this->successResponse(new CartResource($cart), 'Cart retrieved');
    }

    public function addItem(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_variant_id' => 'required|exists:product_variants,id',
            'quantity' => 'nullable|integer|min:1',
        ]);

        $sessionToken = $request->header('X-Session-Token');
        $cart = $this->cartService->getOrCreateCart($request->user(), $sessionToken);

        try {
            $this->cartService->addItem($cart, (int) $validated['product_variant_id'], (int) ($validated['quantity'] ?? 1));
            return $this->successResponse(new CartResource($cart->fresh(['items.variant.product'])), 'Item added to bag');
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 422);
        }
    }

    public function updateItem(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:0',
        ]);

        $sessionToken = $request->header('X-Session-Token');
        $cart = $this->cartService->getOrCreateCart($request->user(), $sessionToken);

        try {
            $this->cartService->updateItem($cart, $id, (int) $validated['quantity']);
            return $this->successResponse(new CartResource($cart->fresh(['items.variant.product'])), 'Bag updated');
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 422);
        }
    }

    public function removeItem(Request $request, int $id): JsonResponse
    {
        $sessionToken = $request->header('X-Session-Token');
        $cart = $this->cartService->getOrCreateCart($request->user(), $sessionToken);

        $this->cartService->removeItem($cart, $id);
        return $this->successResponse(new CartResource($cart->fresh(['items.variant.product'])), 'Item removed from bag');
    }
}

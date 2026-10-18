<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductVariant;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;

class CartService
{
    public function getOrCreateCart(?User $user, ?string $sessionToken = null): Cart
    {
        if ($user) {
            $cart = Cart::firstOrCreate(['user_id' => $user->id]);
            // If there's also a session token, merge guest items
            if ($sessionToken) {
                $guestCart = Cart::where('session_token', $sessionToken)->whereNull('user_id')->first();
                if ($guestCart) {
                    $this->mergeCarts($guestCart, $cart);
                }
            }
            return $cart->load(['items.variant.product']);
        }

        if ($sessionToken) {
            return Cart::firstOrCreate(['session_token' => $sessionToken])
                ->load(['items.variant.product']);
        }

        return Cart::create()->load(['items.variant.product']);
    }

    public function addItem(Cart $cart, int $variantId, int $quantity = 1): CartItem
    {
        return DB::transaction(function () use ($cart, $variantId, $quantity) {
            $variant = ProductVariant::lockForUpdate()->findOrFail($variantId);

            $cartItem = CartItem::where('cart_id', $cart->id)
                ->where('product_variant_id', $variantId)
                ->first();

            $newQuantity = $cartItem ? $cartItem->quantity + $quantity : $quantity;

            if ($newQuantity > $variant->stock_quantity) {
                throw new Exception("Requested quantity exceeds available stock ({$variant->stock_quantity}).");
            }

            if ($cartItem) {
                $cartItem->quantity = $newQuantity;
                $cartItem->save();
            } else {
                $cartItem = CartItem::create([
                    'cart_id' => $cart->id,
                    'product_variant_id' => $variantId,
                    'quantity' => $newQuantity,
                ]);
            }

            return $cartItem->load('variant.product');
        });
    }

    public function updateItem(Cart $cart, int $itemId, int $quantity): ?CartItem
    {
        return DB::transaction(function () use ($cart, $itemId, $quantity) {
            $cartItem = CartItem::where('cart_id', $cart->id)->where('id', $itemId)->firstOrFail();

            if ($quantity <= 0) {
                $cartItem->delete();
                return null;
            }

            $variant = ProductVariant::lockForUpdate()->findOrFail($cartItem->product_variant_id);
            if ($quantity > $variant->stock_quantity) {
                throw new Exception("Requested quantity exceeds available stock ({$variant->stock_quantity}).");
            }

            $cartItem->quantity = $quantity;
            $cartItem->save();

            return $cartItem->load('variant.product');
        });
    }

    public function removeItem(Cart $cart, int $itemId): bool
    {
        return (bool) CartItem::where('cart_id', $cart->id)->where('id', $itemId)->delete();
    }

    private function mergeCarts(Cart $source, Cart $destination): void
    {
        foreach ($source->items as $item) {
            $existing = CartItem::where('cart_id', $destination->id)
                ->where('product_variant_id', $item->product_variant_id)
                ->first();

            if ($existing) {
                $existing->quantity += $item->quantity;
                $existing->save();
            } else {
                $item->cart_id = $destination->id;
                $item->save();
            }
        }

        $source->delete();
    }
}

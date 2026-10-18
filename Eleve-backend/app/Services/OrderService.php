<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\ProductVariant;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderService
{
    /**
     * Create an order atomically with PDO transaction and stock decrementing.
     *
     * @param User $user
     * @param array $data
     * @return Order
     * @throws Exception
     */
    public function placeOrder(User $user, array $data): Order
    {
        if (!$user || !$user->id) {
            throw new Exception("Authentication required. Please sign in to complete your order.");
        }

        // Begin atomic PDO transaction
        DB::beginTransaction();

        try {
            $itemsData = $data['items'] ?? [];
            if (empty($itemsData)) {
                throw new Exception("Cannot place order with empty items.");
            }

            $subtotal = 0;
            $orderItemsToInsert = [];

            // 1. Lock and validate stock for each variant using PDO 'forUpdate'
            foreach ($itemsData as $item) {
                $variantId = $item['product_variant_id'];
                $quantity = max(1, (int) $item['quantity']);

                /** @var ProductVariant|null $variant */
                $variant = ProductVariant::with('product')
                    ->lockForUpdate() // PDO SELECT ... FOR UPDATE ensures concurrency safety
                    ->find($variantId);

                if (!$variant) {
                    throw new Exception("Product variant with ID {$variantId} no longer exists.");
                }

                if ($variant->stock_quantity < $quantity) {
                    throw new Exception("Insufficient stock for {$variant->product->name} (Size: {$variant->size}, Color: {$variant->color}). Available: {$variant->stock_quantity}, Requested: {$quantity}.");
                }

                $effectivePrice = $variant->effective_price;
                $lineTotal = $effectivePrice * $quantity;
                $subtotal += $lineTotal;

                // 2. Decrement stock atomically
                $variant->stock_quantity -= $quantity;
                $variant->save();

                $orderItemsToInsert[] = [
                    'product_variant_id' => $variant->id,
                    'product_name' => $variant->product->name,
                    'size' => $variant->size,
                    'color' => $variant->color,
                    'sku' => $variant->sku,
                    'image_url' => $variant->product->primary_image,
                    'unit_price' => $effectivePrice,
                    'quantity' => $quantity,
                    'total_price' => $lineTotal,
                ];
            }

            // 2. Calculate shipping (Complimentary luxury shipping above ₱5,000, or flat ₱250)
            $shippingFee = $subtotal >= 5000 ? 0.00 : 250.00;
            $totalAmount = $subtotal + $shippingFee;

            // 3. Generate unique order number
            $orderNumber = 'ELE-' . now()->format('Ymd') . '-' . strtoupper(Str::random(5));

            // 4. Create Order Record (strictly linked to authenticated user)
            $order = Order::create([
                'order_number' => $orderNumber,
                'user_id' => $user->id,
                'shipping_address_id' => $data['shipping_address_id'] ?? null,
                'customer_name' => $data['customer_name'] ?? $user->name,
                'customer_email' => $data['customer_email'] ?? $user->email,
                'customer_phone' => $data['customer_phone'] ?? ($user->phone ?: '+63 900 000 0000'),
                'shipping_address_snapshot' => $data['shipping_address'] ?? [],
                'subtotal' => $subtotal,
                'shipping_fee' => $shippingFee,
                'total_amount' => $totalAmount,
                'status' => 'pending',
                'notes' => $data['notes'] ?? null,
            ]);

            // 5. Create Order Items
            foreach ($orderItemsToInsert as $item) {
                $item['order_id'] = $order->id;
                OrderItem::create($item);
            }

            // 6. Create Payment Record
            $paymentMethod = $data['payment_method'] ?? 'cod';
            $paymentStatus = in_array($paymentMethod, ['card', 'gcash']) ? 'completed' : 'pending';

            Payment::create([
                'order_id' => $order->id,
                'payment_method' => $paymentMethod,
                'transaction_reference' => 'TXN-' . strtoupper(Str::random(10)),
                'amount' => $totalAmount,
                'status' => $paymentStatus,
                'paid_at' => $paymentStatus === 'completed' ? now() : null,
            ]);

            // 7. Clear cart if user has one
            if ($user) {
                $cart = Cart::where('user_id', $user->id)->first();
                if ($cart) {
                    $cart->items()->delete();
                }
            }

            // 8. Create real-time notification records
            if ($user) {
                Notification::create([
                    'user_id' => $user->id,
                    'title' => "Order Placed: #{$orderNumber}",
                    'message' => 'Your bespoke footwear order has been received. Our atelier 3-second progression pipeline has begun.',
                    'type' => 'order_placed',
                    'data' => [
                        'order_id' => $order->id,
                        'order_number' => $orderNumber,
                        'status' => 'pending',
                        'amount' => $totalAmount,
                    ],
                    'is_read' => false,
                ]);
            }

            // Global / Admin broadcast notification
            Notification::create([
                'user_id' => null,
                'title' => "New Order #{$orderNumber}",
                'message' => "Order #{$orderNumber} placed for ₱" . number_format($totalAmount, 2),
                'type' => 'order_placed',
                'data' => [
                    'order_id' => $order->id,
                    'order_number' => $orderNumber,
                    'amount' => $totalAmount,
                ],
                'is_read' => false,
            ]);

            // Commit atomic transaction
            DB::commit();

            return $order->load(['items', 'payment']);
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Cancel an order if it is still pending, and atomically restore inventory stock.
     */
    public function cancelOrder(Order $order, string $reason = ''): Order
    {
        if ($order->status !== 'pending') {
            throw new Exception("Only orders with 'pending' status can be cancelled.");
        }

        DB::beginTransaction();

        try {
            // Restore stock for all items
            foreach ($order->items as $item) {
                if ($item->product_variant_id) {
                    ProductVariant::where('id', $item->product_variant_id)
                        ->increment('stock_quantity', $item->quantity);
                }
            }

            $order->update([
                'status' => 'cancelled',
                'notes' => $order->notes ? $order->notes . " | Cancellation reason: " . $reason : "Cancelled: " . $reason,
            ]);

            if ($order->payment && $order->payment->status === 'completed') {
                $order->payment->update(['status' => 'refunded']);
            }

            DB::commit();

            return $order->fresh(['items', 'payment']);
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}

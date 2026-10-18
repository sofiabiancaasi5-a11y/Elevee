<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::where('role', 'customer')->get();
        $variants = ProductVariant::with('product')->get();

        if ($users->isEmpty() || $variants->isEmpty()) {
            return;
        }

        $orderTemplates = [
            [
                'status' => 'pending',
                'payment_status' => 'pending',
                'method' => 'cod',
                'days_ago' => 0,
            ],
            [
                'status' => 'confirmed',
                'payment_status' => 'completed',
                'method' => 'card',
                'days_ago' => 1,
            ],
            [
                'status' => 'processing',
                'payment_status' => 'completed',
                'method' => 'gcash',
                'days_ago' => 2,
            ],
            [
                'status' => 'shipped',
                'payment_status' => 'completed',
                'method' => 'card',
                'days_ago' => 4,
            ],
            [
                'status' => 'delivered',
                'payment_status' => 'completed',
                'method' => 'bank_transfer',
                'days_ago' => 7,
            ],
            [
                'status' => 'delivered',
                'payment_status' => 'completed',
                'method' => 'card',
                'days_ago' => 12,
            ],
        ];

        foreach ($orderTemplates as $index => $tmpl) {
            $user = $users[$index % $users->count()];
            $address = $user->addresses()->first() ?? Address::first();

            $orderDate = now()->subDays($tmpl['days_ago'])->subHours(rand(1, 10));
            $orderNumber = 'ELE-' . $orderDate->format('Ymd') . '-' . strtoupper(Str::random(5));

            // Select 1 to 2 random variants
            $selectedVariants = $variants->random(rand(1, 2));
            $subtotal = 0;

            $order = Order::create([
                'order_number' => $orderNumber,
                'user_id' => $user->id,
                'shipping_address_id' => $address ? $address->id : null,
                'customer_name' => $user->name,
                'customer_email' => $user->email,
                'customer_phone' => $user->phone ?? '+63 917 000 0000',
                'shipping_address_snapshot' => [
                    'recipient_name' => $address ? $address->recipient_name : $user->name,
                    'phone_number' => $address ? $address->phone_number : ($user->phone ?? '+63 917 000 0000'),
                    'street_address' => $address ? $address->street_address : '128 Paseo de Roxas',
                    'apartment' => $address ? $address->apartment : 'Suite 10',
                    'city' => $address ? $address->city : 'Makati City',
                    'province_state' => $address ? $address->province_state : 'Metro Manila',
                    'postal_code' => $address ? $address->postal_code : '1229',
                    'country' => $address ? $address->country : 'Philippines',
                ],
                'subtotal' => 0, // updated below
                'shipping_fee' => 0.00, // Complimentary luxury shipping
                'total_amount' => 0, // updated below
                'status' => $tmpl['status'],
                'notes' => 'Complimentary luxury gift wrapping requested with satin ribbon.',
                'created_at' => $orderDate,
                'updated_at' => $orderDate,
            ]);

            foreach ($selectedVariants as $var) {
                $qty = 1;
                $price = $var->effective_price;
                $itemTotal = $price * $qty;
                $subtotal += $itemTotal;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_variant_id' => $var->id,
                    'product_name' => $var->product->name,
                    'size' => $var->size,
                    'color' => $var->color,
                    'sku' => $var->sku,
                    'image_url' => $var->product->primary_image,
                    'unit_price' => $price,
                    'quantity' => $qty,
                    'total_price' => $itemTotal,
                    'created_at' => $orderDate,
                    'updated_at' => $orderDate,
                ]);
            }

            $order->update([
                'subtotal' => $subtotal,
                'total_amount' => $subtotal,
            ]);

            Payment::create([
                'order_id' => $order->id,
                'payment_method' => $tmpl['method'],
                'transaction_reference' => 'TXN-' . strtoupper(Str::random(10)),
                'amount' => $subtotal,
                'status' => $tmpl['payment_status'],
                'paid_at' => $tmpl['payment_status'] === 'completed' ? $orderDate : null,
                'created_at' => $orderDate,
                'updated_at' => $orderDate,
            ]);

            // Seed real-time notification record for this customer order
            \App\Models\Notification::create([
                'user_id' => $user->id,
                'title' => match ($tmpl['status']) {
                    'delivered' => 'Delivered: Order ' . $order->order_number,
                    'shipped' => 'Dispatched: Order ' . $order->order_number,
                    'processing' => 'Atelier Handcrafting: Order ' . $order->order_number,
                    'confirmed' => 'Order Confirmed: ' . $order->order_number,
                    default => 'Order Placed: ' . $order->order_number,
                },
                'message' => match ($tmpl['status']) {
                    'delivered' => 'Your luxury Élevé footwear has arrived. Every step, elevated.',
                    'shipped' => 'Your bespoke parcel has been handed to our white-glove courier.',
                    'processing' => 'Artisans are crafting and inspecting your pair at our atelier.',
                    'confirmed' => 'Élevé atelier has confirmed your bespoke footwear order.',
                    default => 'Your order has been received and scheduled for atelier processing.',
                },
                'type' => 'order_status',
                'data' => [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'status' => $tmpl['status'],
                    'amount' => $subtotal,
                ],
                'is_read' => $index > 1, // keep first 2 unread to test badge
                'created_at' => $orderDate,
                'updated_at' => $orderDate,
            ]);
        }
    }
}

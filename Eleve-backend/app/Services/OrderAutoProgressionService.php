<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderAutoProgressionService
{
    /**
     * Map of sequential order status progression
     */
    protected const NEXT_STATUS = [
        'pending' => 'confirmed',
        'confirmed' => 'processing',
        'processing' => 'shipped',
        'shipped' => 'delivered',
    ];

    /**
     * Advance a single order to its next state in the 3-second pipeline.
     */
    public function advanceOrder(Order $order): ?Order
    {
        $currentStatus = $order->status;
        $nextStatus = self::NEXT_STATUS[$currentStatus] ?? null;

        if (!$nextStatus) {
            return $order; // Terminal state (delivered or cancelled)
        }

        return DB::transaction(function () use ($order, $nextStatus, $currentStatus) {
            // Lock order for update
            $lockedOrder = Order::where('id', $order->id)->lockForUpdate()->first();

            if (!$lockedOrder || $lockedOrder->status !== $currentStatus) {
                return $lockedOrder;
            }

            $lockedOrder->status = $nextStatus;
            $lockedOrder->updated_at = now();
            $lockedOrder->save();

            // If delivered, mark payment completed if COD
            if ($nextStatus === 'delivered') {
                $payment = Payment::where('order_id', $lockedOrder->id)->first();
                if ($payment && $payment->status !== 'completed') {
                    $payment->update([
                        'status' => 'completed',
                        'paid_at' => now(),
                    ]);
                }
            }

            // Create notification with bespoke luxury copywriting
            $notifMeta = $this->getNotificationCopy($nextStatus, $lockedOrder->order_number);

            Notification::create([
                'user_id' => $lockedOrder->user_id,
                'title' => $notifMeta['title'],
                'message' => $notifMeta['message'],
                'type' => 'order_status',
                'data' => [
                    'order_id' => $lockedOrder->id,
                    'order_number' => $lockedOrder->order_number,
                    'status' => $nextStatus,
                    'amount' => $lockedOrder->total_amount,
                ],
                'is_read' => false,
            ]);

            return $lockedOrder;
        });
    }

    /**
     * Process all orders in the pipeline that are ready for their 3-second progression step.
     */
    public function processPendingProgressions(int $intervalSeconds = 3): array
    {
        $cutoff = now()->subSeconds($intervalSeconds);

        // Find active orders whose last transition was at least $intervalSeconds ago
        $orders = Order::whereIn('status', array_keys(self::NEXT_STATUS))
            ->where('updated_at', '<=', $cutoff)
            ->get();

        $advanced = [];
        foreach ($orders as $order) {
            $updated = $this->advanceOrder($order);
            if ($updated) {
                $advanced[] = [
                    'order_number' => $updated->order_number,
                    'new_status' => $updated->status,
                ];
            }
        }

        return $advanced;
    }

    /**
     * Bespoke luxury notification titles and descriptions
     */
    protected function getNotificationCopy(string $status, string $orderNumber): array
    {
        return match ($status) {
            'confirmed' => [
                'title' => "Atelier Confirmed: #{$orderNumber}",
                'message' => 'Élevé atelier has verified and confirmed your bespoke footwear order.',
            ],
            'processing' => [
                'title' => "Artisans Crafting: #{$orderNumber}",
                'message' => 'Master shoemakers are assembling and inspecting your pair at our atelier.',
            ],
            'shipped' => [
                'title' => "Dispatched in Transit: #{$orderNumber}",
                'message' => 'Your luxury parcel is now in transit with our white-glove courier.',
            ],
            'delivered' => [
                'title' => "Delivered: #{$orderNumber}",
                'message' => 'Every step, elevated. Your handcrafted Élevé footwear has arrived.',
            ],
            default => [
                'title' => "Order Update: #{$orderNumber}",
                'message' => "Your order status is now {$status}.",
            ],
        };
    }
}

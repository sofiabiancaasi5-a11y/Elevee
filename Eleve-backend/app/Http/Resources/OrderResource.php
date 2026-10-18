<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_number' => $this->order_number,
            'customer_name' => $this->customer_name,
            'customer_email' => $this->customer_email,
            'customer_phone' => $this->customer_phone,
            'shipping_address' => $this->shipping_address_snapshot,
            'subtotal' => (float) $this->subtotal,
            'shipping_fee' => (float) $this->shipping_fee,
            'total_amount' => (float) $this->total_amount,
            'formatted_total' => '₱' . number_format($this->total_amount, 2),
            'status' => $this->status,
            'status_color' => $this->status_color,
            'notes' => $this->notes,
            'can_cancel' => $this->status === 'pending',
            'created_at' => $this->created_at?->toIso8601String(),
            'formatted_date' => $this->created_at?->format('M d, Y h:i A'),
            'payment' => $this->payment ? [
                'method' => $this->payment->payment_method,
                'status' => $this->payment->status,
                'reference' => $this->payment->transaction_reference,
            ] : null,
            'items' => $this->items->map(fn($item) => [
                'id' => $item->id,
                'product_variant_id' => $item->product_variant_id,
                'product_name' => $item->product_name,
                'size' => $item->size,
                'color' => $item->color,
                'sku' => $item->sku,
                'image_url' => $item->image_url,
                'unit_price' => (float) $item->unit_price,
                'formatted_unit_price' => '₱' . number_format($item->unit_price, 2),
                'quantity' => (int) $item->quantity,
                'total_price' => (float) $item->total_price,
                'formatted_total_price' => '₱' . number_format($item->total_price, 2),
            ]),
        ];
    }
}

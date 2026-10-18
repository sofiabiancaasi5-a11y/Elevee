<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

class OrderCreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:30',
            'shipping_address' => 'required|array',
            'shipping_address.street_address' => 'required|string|max:255',
            'shipping_address.city' => 'required|string|max:100',
            'shipping_address.province_state' => 'required|string|max:100',
            'shipping_address.postal_code' => 'required|string|max:20',
            'items' => 'required|array|min:1',
            'items.*.product_variant_id' => 'required|exists:product_variants,id',
            'items.*.quantity' => 'required|integer|min:1|max:10',
            'payment_method' => 'required|in:cod,card,gcash,bank_transfer',
            'notes' => 'nullable|string|max:500',
        ];
    }
}

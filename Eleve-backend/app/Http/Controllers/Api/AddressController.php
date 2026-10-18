<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $addresses = Address::where('user_id', $request->user()->id)->latest()->get();
        return $this->successResponse($addresses, 'Customer addresses retrieved');
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'recipient_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:30',
            'street_address' => 'required|string|max:255',
            'apartment' => 'nullable|string|max:100',
            'city' => 'required|string|max:100',
            'province_state' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'country' => 'nullable|string|max:100',
            'is_default' => 'nullable|boolean',
        ]);

        $user = $request->user();

        if (!empty($validated['is_default'])) {
            Address::where('user_id', $user->id)->update(['is_default' => false]);
        }

        $address = Address::create([
            ...$validated,
            'user_id' => $user->id,
            'country' => $validated['country'] ?? 'Philippines',
            'is_default' => !empty($validated['is_default']),
        ]);

        return $this->successResponse($address, 'Address saved successfully', 201);
    }
}

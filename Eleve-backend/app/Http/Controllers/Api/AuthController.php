<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    use ApiResponse;

    public function register(RegisterRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'customer',
            'phone' => $validated['phone'] ?? null,
        ]);

        $token = $user->createToken('eleve_mobile_token')->plainTextToken;

        return $this->successResponse([
            'user' => new UserResource($user),
            'token' => $token,
        ], 'Registration successful', 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->validated();

        $user = User::with('addresses')->where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return $this->errorResponse('Invalid email or password', 401);
        }

        $token = $user->createToken('eleve_mobile_token')->plainTextToken;

        return $this->successResponse([
            'user' => new UserResource($user),
            'token' => $token,
        ], 'Login successful');
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user()->load('addresses');
        return $this->successResponse(new UserResource($user), 'User profile retrieved');
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:30',
            'current_password' => 'nullable|string',
            'password' => 'nullable|string|min:6',
            'address' => 'nullable|array',
            'address.street_address' => 'required_with:address|string|max:255',
            'address.apartment' => 'nullable|string|max:255',
            'address.city' => 'required_with:address|string|max:100',
            'address.province_state' => 'required_with:address|string|max:100',
            'address.postal_code' => 'required_with:address|string|max:20',
        ]);

        if (!empty($validated['password'])) {
            if (empty($validated['current_password']) || !Hash::check($validated['current_password'], $user->password)) {
                return $this->errorResponse('Current password does not match our atelier records.', 422);
            }
            $user->password = Hash::make($validated['password']);
        }

        if (isset($validated['name'])) {
            $user->name = $validated['name'];
        }
        if (isset($validated['email'])) {
            $user->email = $validated['email'];
        }
        if (array_key_exists('phone', $validated)) {
            $user->phone = $validated['phone'];
        }
        $user->save();

        // Update or create default delivery address
        if (!empty($validated['address'])) {
            $addrData = $validated['address'];
            $defaultAddr = $user->addresses()->where('is_default', true)->first() ?: $user->addresses()->first();

            if ($defaultAddr) {
                $defaultAddr->update([
                    'recipient_name' => $user->name,
                    'phone_number' => $user->phone ?? $defaultAddr->phone_number,
                    'street_address' => $addrData['street_address'],
                    'apartment' => $addrData['apartment'] ?? null,
                    'city' => $addrData['city'],
                    'province_state' => $addrData['province_state'],
                    'postal_code' => $addrData['postal_code'],
                    'country' => 'Philippines',
                    'is_default' => true,
                ]);
            } else {
                $user->addresses()->create([
                    'recipient_name' => $user->name,
                    'phone_number' => $user->phone,
                    'street_address' => $addrData['street_address'],
                    'apartment' => $addrData['apartment'] ?? null,
                    'city' => $addrData['city'],
                    'province_state' => $addrData['province_state'],
                    'postal_code' => $addrData['postal_code'],
                    'country' => 'Philippines',
                    'is_default' => true,
                ]);
            }
        }

        return $this->successResponse(new UserResource($user->fresh()->load('addresses')), 'Atelier profile updated successfully');
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        return $this->successResponse(null, 'Successfully logged out');
    }
}

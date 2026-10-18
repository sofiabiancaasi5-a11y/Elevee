<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    public function __construct(
        protected UserRepositoryInterface $userRepo
    ) {}

    public function index(Request $request)
    {
        $query = User::where('role', 'customer')
            ->with(['orders' => function ($q) {
                $q->where('status', '!=', 'cancelled');
            }, 'addresses'])
            ->withCount('orders')
            ->latest();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $customers = $query->paginate(15)->withQueryString();

        return view('admin.customers.index', compact('customers'));
    }

    public function create()
    {
        return view('admin.customers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'phone' => 'nullable|string|max:30',
            'password' => 'required|string|min:6',
            'street_address' => 'nullable|string|max:255',
            'apartment' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'province_state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'role' => 'customer',
        ]);

        if (!empty($validated['street_address'])) {
            Address::create([
                'user_id' => $user->id,
                'recipient_name' => $user->name,
                'phone_number' => $user->phone ?? 'N/A',
                'street_address' => $validated['street_address'],
                'apartment' => $validated['apartment'] ?? null,
                'city' => $validated['city'] ?? 'Metro Manila',
                'province_state' => $validated['province_state'] ?? 'NCR',
                'postal_code' => $validated['postal_code'] ?? '1200',
                'country' => $validated['country'] ?? 'Philippines',
                'is_default' => true,
            ]);
        }

        return redirect()->route('admin.customers.index')->with('success', "Client {$user->name} created successfully.");
    }

    public function show(int $id)
    {
        $customer = User::with(['addresses', 'orders.items.variant.product'])
            ->where('role', 'customer')
            ->find($id);

        if (!$customer) {
            return redirect()->route('admin.customers.index')->with('error', 'Customer profile not found.');
        }

        $lifetimeSpend = $customer->orders->where('status', '!=', 'cancelled')->sum('total_amount');
        $ordersCount = $customer->orders->count();
        $recentOrders = $customer->orders()->latest()->take(10)->get();

        return view('admin.customers.show', compact('customer', 'lifetimeSpend', 'ordersCount', 'recentOrders'));
    }

    public function edit(int $id)
    {
        $customer = User::with('addresses')->find($id);
        if (!$customer) {
            return redirect()->route('admin.customers.index')->with('error', 'Customer not found.');
        }

        $defaultAddress = $customer->addresses->where('is_default', true)->first() ?? $customer->addresses->first();

        return view('admin.customers.edit', compact('customer', 'defaultAddress'));
    }

    public function update(Request $request, int $id)
    {
        $customer = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($customer->id)],
            'phone' => 'nullable|string|max:30',
            'password' => 'nullable|string|min:6',
            'street_address' => 'nullable|string|max:255',
            'apartment' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'province_state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
        ]);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $customer->update($updateData);

        // Address update or create
        if (!empty($validated['street_address'])) {
            $defaultAddress = $customer->addresses()->where('is_default', true)->first() ?? $customer->addresses()->first();

            $addressData = [
                'recipient_name' => $customer->name,
                'phone_number' => $customer->phone ?? 'N/A',
                'street_address' => $validated['street_address'],
                'apartment' => $validated['apartment'] ?? null,
                'city' => $validated['city'] ?? 'Metro Manila',
                'province_state' => $validated['province_state'] ?? 'NCR',
                'postal_code' => $validated['postal_code'] ?? '1200',
                'country' => $validated['country'] ?? 'Philippines',
                'is_default' => true,
            ];

            if ($defaultAddress) {
                $defaultAddress->update($addressData);
            } else {
                $addressData['user_id'] = $customer->id;
                Address::create($addressData);
            }
        }

        return redirect()->route('admin.customers.show', $customer->id)->with('success', "Client profile for {$customer->name} updated successfully.");
    }

    public function destroy(int $id)
    {
        $customer = User::findOrFail($id);

        if ($customer->role === 'admin') {
            return redirect()->route('admin.customers.index')->with('error', 'Administrator profiles cannot be removed from client directory.');
        }

        $activeOrders = $customer->orders()->whereIn('status', ['pending', 'confirmed', 'processing', 'shipped'])->count();
        if ($activeOrders > 0) {
            return redirect()->route('admin.customers.index')->with('error', "Cannot delete client {$customer->name} because they have {$activeOrders} active order(s) in fulfillment.");
        }

        $customer->addresses()->delete();
        $customer->delete();

        return redirect()->route('admin.customers.index')->with('success', "Client {$customer->name} was successfully removed.");
    }
}

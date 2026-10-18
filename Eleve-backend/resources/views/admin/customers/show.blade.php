@extends('layouts.admin')

@section('title', 'Client Profile — ' . $customer->name)
@section('header_title', 'Client Profile')

@section('content')
<div class="space-y-6">

    <!-- Header Actions -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.customers.index') }}" class="p-2 border border-stone-200 rounded-lg hover:bg-stone-50 text-stone-600 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <div>
                <h2 class="font-luxury-serif text-2xl font-bold text-stone-900">{{ $customer->name }}</h2>
                <p class="text-xs text-stone-500">Client since {{ $customer->created_at->format('F d, Y') }}</p>
            </div>
        </div>

        <div class="flex items-center space-x-2">
            <a href="{{ route('admin.customers.edit', $customer->id) }}" class="px-4 py-2 bg-stone-900 hover:bg-stone-800 text-white rounded-lg text-xs font-medium transition-colors shadow-sm">
                Edit Profile
            </a>
            <form action="{{ route('admin.customers.destroy', $customer->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this client?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 rounded-lg text-xs font-medium transition-colors">
                    Delete
                </button>
            </form>
        </div>
    </div>

    <!-- Client Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-5 rounded-xl border border-[#D8D0C4]/70 shadow-sm flex items-center space-x-4">
            <div class="w-12 h-12 rounded-xl bg-stone-100 flex items-center justify-center text-stone-800 font-luxury-serif font-bold text-xl">
                ₱
            </div>
            <div>
                <p class="text-xs text-stone-400 uppercase tracking-wider">Lifetime Spend</p>
                <h3 class="text-xl font-bold text-stone-900">₱{{ number_format($lifetimeSpend, 2) }}</h3>
            </div>
        </div>

        <div class="bg-white p-5 rounded-xl border border-[#D8D0C4]/70 shadow-sm flex items-center space-x-4">
            <div class="w-12 h-12 rounded-xl bg-stone-100 flex items-center justify-center text-stone-800 font-luxury-serif font-bold text-xl">
                🛍️
            </div>
            <div>
                <p class="text-xs text-stone-400 uppercase tracking-wider">Total Orders</p>
                <h3 class="text-xl font-bold text-stone-900">{{ $ordersCount }} orders</h3>
            </div>
        </div>

        <div class="bg-white p-5 rounded-xl border border-[#D8D0C4]/70 shadow-sm flex items-center space-x-4">
            <div class="w-12 h-12 rounded-xl bg-stone-100 flex items-center justify-center text-stone-800 font-luxury-serif font-bold text-xl">
                ⭐
            </div>
            <div>
                <p class="text-xs text-stone-400 uppercase tracking-wider">Client Tier</p>
                <h3 class="text-xl font-bold text-[#C5A880]">
                    @if($lifetimeSpend > 100000)
                        Haute Atelier VIP
                    @elseif($lifetimeSpend > 30000)
                        Preferred Patron
                    @else
                        Atelier Member
                    @endif
                </h3>
            </div>
        </div>
    </div>

    <!-- Client Details & Address -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Identity Card -->
        <div class="bg-white p-6 rounded-xl border border-[#D8D0C4]/70 shadow-sm space-y-4">
            <h3 class="font-luxury-serif text-sm font-semibold tracking-wider uppercase text-stone-800 border-b border-stone-100 pb-2">
                Client Profile & Contact
            </h3>
            <div class="space-y-3 text-xs">
                <div class="flex justify-between">
                    <span class="text-stone-400">Full Name</span>
                    <span class="font-semibold text-stone-900">{{ $customer->name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-stone-400">Email Address</span>
                    <span class="font-semibold text-stone-900">{{ $customer->email }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-stone-400">Phone Number</span>
                    <span class="font-semibold text-stone-900">{{ $customer->phone ?? 'Not provided' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-stone-400">Account Role</span>
                    <span class="px-2 py-0.5 bg-stone-100 rounded text-stone-700 font-medium capitalize">{{ $customer->role }}</span>
                </div>
            </div>
        </div>

        <!-- Address Card -->
        <div class="bg-white p-6 rounded-xl border border-[#D8D0C4]/70 shadow-sm space-y-4">
            <h3 class="font-luxury-serif text-sm font-semibold tracking-wider uppercase text-stone-800 border-b border-stone-100 pb-2">
                Primary Delivery Destination
            </h3>
            @php $addr = $customer->addresses->where('is_default', true)->first() ?? $customer->addresses->first(); @endphp
            @if($addr)
                <div class="space-y-2 text-xs">
                    <p class="font-semibold text-stone-900">{{ $addr->recipient_name }}</p>
                    <p class="text-stone-600">{{ $addr->street_address }} {{ $addr->apartment ? ', ' . $addr->apartment : '' }}</p>
                    <p class="text-stone-600">{{ $addr->city }}, {{ $addr->province_state }} {{ $addr->postal_code }}</p>
                    <p class="text-stone-600">{{ $addr->country }}</p>
                    <p class="text-stone-500 pt-1">Phone: {{ $addr->phone_number }}</p>
                </div>
            @else
                <div class="text-center py-6 text-stone-400 text-xs">
                    No delivery address recorded for this client.
                </div>
            @endif
        </div>
    </div>

    <!-- Order History -->
    <div class="bg-white rounded-xl border border-[#D8D0C4]/70 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-stone-200 flex items-center justify-between">
            <h3 class="font-luxury-serif text-base font-semibold text-stone-900">Purchase History</h3>
            <span class="text-xs text-stone-500">{{ $customer->orders->count() }} total orders placed</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-stone-50 text-[11px] uppercase tracking-wider text-stone-500 border-b border-stone-200">
                    <tr>
                        <th class="px-5 py-3.5 font-medium">Order Number</th>
                        <th class="px-5 py-3.5 font-medium">Date Placed</th>
                        <th class="px-5 py-3.5 font-medium">Status</th>
                        <th class="px-5 py-3.5 font-medium">Items</th>
                        <th class="px-5 py-3.5 font-medium">Total Amount</th>
                        <th class="px-5 py-3.5 font-medium text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse($recentOrders as $order)
                    <tr class="hover:bg-stone-50/70 transition-colors">
                        <td class="px-5 py-4 font-mono font-semibold text-xs text-stone-900">
                            {{ $order->order_number }}
                        </td>
                        <td class="px-5 py-4 text-xs text-stone-500">
                            {{ $order->created_at->format('M d, Y h:i A') }}
                        </td>
                        <td class="px-5 py-4 text-xs">
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-semibold uppercase tracking-wider
                                @if($order->status === 'delivered') bg-emerald-100 text-emerald-800
                                @elseif($order->status === 'shipped') bg-sky-100 text-sky-800
                                @elseif($order->status === 'processing') bg-indigo-100 text-indigo-800
                                @elseif($order->status === 'confirmed') bg-amber-100 text-amber-800
                                @elseif($order->status === 'cancelled') bg-rose-100 text-rose-800
                                @else bg-stone-100 text-stone-800 @endif">
                                {{ $order->status }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-xs text-stone-600">
                            {{ $order->items->sum('quantity') }} items
                        </td>
                        <td class="px-5 py-4 text-xs font-bold text-stone-900">
                            ₱{{ number_format($order->total_amount, 2) }}
                        </td>
                        <td class="px-5 py-4 text-xs text-right">
                            <a href="{{ route('admin.orders.show', $order->id) }}" class="px-3 py-1.5 bg-stone-100 hover:bg-stone-200 text-stone-800 rounded font-medium transition-colors">
                                View Order
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-8 text-center text-stone-400 text-xs">
                            No purchases recorded yet for this client.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@extends('layouts.admin')

@section('title', 'Client Orders')
@section('header_title', 'Client Orders Pipeline')

@section('content')
<div class="space-y-6">

    <!-- Status Tabs -->
    <div class="flex items-center space-x-2 border-b border-[#D8D0C4]/70 overflow-x-auto pb-1 text-xs font-medium">
        @foreach(['all' => 'All Orders', 'pending' => 'Pending', 'confirmed' => 'Confirmed', 'processing' => 'Processing', 'shipped' => 'Shipped', 'delivered' => 'Delivered', 'cancelled' => 'Cancelled'] as $stKey => $stLabel)
        <a href="{{ route('admin.orders.index', ['status' => $stKey, 'search' => request('search')]) }}" 
           class="px-4 py-2.5 rounded-t-lg transition-colors flex items-center space-x-2 whitespace-nowrap {{ ($status ?? 'all') === $stKey ? 'bg-white border-t-2 border-stone-900 text-stone-900 font-semibold shadow-sm' : 'text-stone-500 hover:text-stone-900' }}">
            <span>{{ $stLabel }}</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ ($status ?? 'all') === $stKey ? 'bg-stone-900 text-white' : 'bg-stone-200 text-stone-700' }}">
                {{ $statusCounts[$stKey] ?? 0 }}
            </span>
        </a>
        @endforeach
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white p-4 rounded-xl border border-[#D8D0C4]/70 shadow-sm flex items-center justify-between">
        <form action="{{ route('admin.orders.index') }}" method="GET" class="flex items-center space-x-3 flex-1 max-w-md">
            <input type="hidden" name="status" value="{{ $status ?? 'all' }}">
            <div class="relative w-full">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by ELE- order #, client name, or email..."
                       class="w-full pl-9 pr-4 py-2 bg-stone-50 border border-stone-200 rounded-lg text-xs text-stone-800 placeholder-stone-400 focus:outline-none focus:border-stone-500">
                <svg class="w-4 h-4 text-stone-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
            <button type="submit" class="px-4 py-2 bg-stone-900 text-white rounded-lg text-xs font-medium uppercase tracking-wider hover:bg-stone-800 transition-colors">
                Search
            </button>
            @if(request('search'))
                <a href="{{ route('admin.orders.index', ['status' => $status]) }}" class="text-xs text-stone-500 hover:text-stone-900 underline">Clear</a>
            @endif
        </form>
    </div>

    <!-- Orders Table -->
    <div class="bg-white rounded-xl border border-[#D8D0C4]/70 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-stone-50 text-[11px] uppercase tracking-wider text-stone-500 border-b border-stone-200">
                    <tr>
                        <th class="px-5 py-3.5 font-medium">Order Number</th>
                        <th class="px-5 py-3.5 font-medium">Client Details</th>
                        <th class="px-5 py-3.5 font-medium">Items</th>
                        <th class="px-5 py-3.5 font-medium">Total Amount</th>
                        <th class="px-5 py-3.5 font-medium">Payment</th>
                        <th class="px-5 py-3.5 font-medium">Fulfillment Status</th>
                        <th class="px-5 py-3.5 font-medium text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse($orders as $order)
                    <tr class="hover:bg-stone-50/70 transition-colors">
                        <!-- Order Number & Date -->
                        <td class="px-5 py-4">
                            <span class="font-mono text-xs font-bold text-stone-900 block">{{ $order->order_number }}</span>
                            <span class="text-[11px] text-stone-400">{{ $order->created_at->format('M d, Y h:i A') }}</span>
                        </td>

                        <!-- Client -->
                        <td class="px-5 py-4">
                            <h4 class="font-medium text-stone-900 text-xs">{{ $order->customer_name }}</h4>
                            <p class="text-[11px] text-stone-400">{{ $order->customer_email }}</p>
                        </td>

                        <!-- Items Summary -->
                        <td class="px-5 py-4">
                            <span class="text-xs font-semibold text-stone-800">{{ $order->items->count() }} pairs</span>
                            <div class="flex items-center -space-x-1.5 mt-1 overflow-hidden">
                                @foreach($order->items->take(3) as $it)
                                    <img src="{{ $it->image_url }}" title="{{ $it->product_name }} ({{ $it->size }})" class="w-6 h-6 rounded-full object-cover border border-white shadow-xs">
                                @endforeach
                            </div>
                        </td>

                        <!-- Total -->
                        <td class="px-5 py-4 font-bold text-stone-900">
                            ₱{{ number_format($order->total_amount, 2) }}
                        </td>

                        <!-- Payment -->
                        <td class="px-5 py-4">
                            <div class="text-xs">
                                <span class="uppercase text-[11px] font-semibold text-stone-700 block">{{ $order->payment?->payment_method ?? 'N/A' }}</span>
                                <span class="text-[10px] font-medium {{ $order->payment?->status === 'completed' ? 'text-emerald-600' : 'text-amber-600' }}">
                                    {{ ucfirst($order->payment?->status ?? 'pending') }}
                                </span>
                            </div>
                        </td>

                        <!-- Status Badge -->
                        <td class="px-5 py-4">
                            @php
                                $badgeStyles = match($order->status) {
                                    'pending' => 'bg-amber-100 text-amber-800 border-amber-200',
                                    'confirmed' => 'bg-blue-100 text-blue-800 border-blue-200',
                                    'processing' => 'bg-purple-100 text-purple-800 border-purple-200',
                                    'shipped' => 'bg-indigo-100 text-indigo-800 border-indigo-200',
                                    'delivered' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                    'cancelled' => 'bg-rose-100 text-rose-800 border-rose-200',
                                    default => 'bg-stone-100 text-stone-800'
                                };
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider border {{ $badgeStyles }}">
                                {{ $order->status }}
                            </span>
                        </td>

                        <!-- Action -->
                        <td class="px-5 py-4 text-right">
                            <a href="{{ route('admin.orders.show', $order->id) }}" class="inline-flex items-center px-3 py-1.5 bg-stone-100 hover:bg-stone-900 hover:text-white rounded-lg text-xs font-semibold uppercase tracking-wider border border-stone-200 transition-colors">
                                Review &rarr;
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-5 py-12 text-center text-stone-400 text-xs">
                            No orders found under this status.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($orders->hasPages())
        <div class="p-4 border-t border-stone-200 bg-stone-50">
            {{ $orders->links() }}
        </div>
        @endif
    </div>

</div>
@endsection

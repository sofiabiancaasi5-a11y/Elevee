@extends('layouts.admin')

@section('title', 'Dashboard')
@section('header_title', 'Executive Overview')

@section('content')
<div class="space-y-8">

    <!-- KPI Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-5">
        <!-- Revenue Card -->
        <div class="bg-white p-5 rounded-xl border border-[#D8D0C4]/70 shadow-sm relative overflow-hidden group">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs uppercase tracking-wider text-stone-500 font-medium">Gross Revenue</span>
                <span class="p-2 rounded-lg bg-stone-100 text-stone-800">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </span>
            </div>
            <div class="font-luxury-serif text-2xl font-bold text-stone-900">₱{{ number_format($total_sales, 2) }}</div>
            <p class="text-[11px] text-emerald-600 font-medium mt-1 flex items-center">
                <span>Completed & processing transactions</span>
            </p>
        </div>

        <!-- Orders Card -->
        <div class="bg-white p-5 rounded-xl border border-[#D8D0C4]/70 shadow-sm relative overflow-hidden group">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs uppercase tracking-wider text-stone-500 font-medium">Total Orders</span>
                <span class="p-2 rounded-lg bg-stone-100 text-stone-800">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                </span>
            </div>
            <div class="font-luxury-serif text-2xl font-bold text-stone-900">{{ $total_orders }}</div>
            <p class="text-[11px] text-stone-500 mt-1">
                <span class="font-semibold text-amber-700">{{ $pending_orders }}</span> pending confirmation
            </p>
        </div>

        <!-- Catalog Card -->
        <div class="bg-white p-5 rounded-xl border border-[#D8D0C4]/70 shadow-sm relative overflow-hidden group">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs uppercase tracking-wider text-stone-500 font-medium">Active Footwear</span>
                <span class="p-2 rounded-lg bg-stone-100 text-stone-800">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                </span>
            </div>
            <div class="font-luxury-serif text-2xl font-bold text-stone-900">{{ $total_products }}</div>
            <p class="text-[11px] text-stone-500 mt-1">Across 6 luxury categories</p>
        </div>

        <!-- Clients Card -->
        <div class="bg-white p-5 rounded-xl border border-[#D8D0C4]/70 shadow-sm relative overflow-hidden group">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs uppercase tracking-wider text-stone-500 font-medium">Registered Clients</span>
                <span class="p-2 rounded-lg bg-stone-100 text-stone-800">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </span>
            </div>
            <div class="font-luxury-serif text-2xl font-bold text-stone-900">{{ $total_customers }}</div>
            <p class="text-[11px] text-stone-500 mt-1">Shoppers with active accounts</p>
        </div>

        <!-- Low Stock Alert Card -->
        <div class="bg-white p-5 rounded-xl border {{ $low_stock_count > 0 ? 'border-amber-300 bg-amber-50/20' : 'border-[#D8D0C4]/70' }} shadow-sm relative overflow-hidden group">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs uppercase tracking-wider text-stone-500 font-medium">Low Stock Alerts</span>
                <span class="p-2 rounded-lg {{ $low_stock_count > 0 ? 'bg-amber-100 text-amber-800' : 'bg-stone-100 text-stone-800' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </span>
            </div>
            <div class="font-luxury-serif text-2xl font-bold {{ $low_stock_count > 0 ? 'text-amber-800' : 'text-stone-900' }}">{{ $low_stock_count }}</div>
            <p class="text-[11px] text-stone-500 mt-1">
                <a href="{{ route('admin.inventory.index', ['stock_status' => 'low']) }}" class="text-amber-700 underline font-medium">Review low variants &rarr;</a>
            </p>
        </div>
    </div>

    <!-- Middle Section: Recent Orders & Best Sellers -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Recent Orders Feed (2 cols) -->
        <div class="lg:col-span-2 bg-white rounded-xl border border-[#D8D0C4]/70 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-[#D8D0C4]/60 flex items-center justify-between">
                <div>
                    <h3 class="font-luxury-serif text-lg font-semibold text-stone-900">Recent Client Orders</h3>
                    <p class="text-xs text-stone-500">Live order queue requiring fulfillment</p>
                </div>
                <a href="{{ route('admin.orders.index') }}" class="text-xs text-stone-700 hover:text-black font-medium underline">View All Orders &rarr;</a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-stone-50 text-[11px] uppercase tracking-wider text-stone-500 border-b border-stone-200">
                        <tr>
                            <th class="px-5 py-3 font-medium">Order Number</th>
                            <th class="px-5 py-3 font-medium">Client</th>
                            <th class="px-5 py-3 font-medium">Items</th>
                            <th class="px-5 py-3 font-medium">Amount</th>
                            <th class="px-5 py-3 font-medium">Status</th>
                            <th class="px-5 py-3 font-medium text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        @forelse($recent_orders as $order)
                        <tr class="hover:bg-stone-50/60 transition-colors">
                            <td class="px-5 py-3.5 font-mono text-xs font-semibold text-stone-900">
                                {{ $order->order_number }}
                            </td>
                            <td class="px-5 py-3.5">
                                <p class="font-medium text-stone-800">{{ $order->customer_name }}</p>
                                <p class="text-[11px] text-stone-400">{{ $order->created_at->diffForHumans() }}</p>
                            </td>
                            <td class="px-5 py-3.5 text-xs text-stone-600">
                                {{ $order->items->count() }} pairs
                            </td>
                            <td class="px-5 py-3.5 font-medium text-stone-900">
                                ₱{{ number_format($order->total_amount, 2) }}
                            </td>
                            <td class="px-5 py-3.5">
                                @php
                                    $badgeStyles = match($order->status) {
                                        'pending' => 'bg-amber-100 text-amber-800 border-amber-200',
                                        'confirmed' => 'bg-blue-100 text-blue-800 border-blue-200',
                                        'processing' => 'bg-purple-100 text-purple-800 border-purple-200',
                                        'shipped' => 'bg-indigo-100 text-indigo-800 border-indigo-200',
                                        'delivered' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                        'cancelled' => 'bg-rose-100 text-rose-800 border-rose-200',
                                        default => 'bg-stone-100 text-stone-800 border-stone-200'
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold uppercase tracking-wider border {{ $badgeStyles }}">
                                    {{ $order->status }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="text-xs px-2.5 py-1 bg-stone-100 hover:bg-stone-900 hover:text-white rounded border border-stone-200 font-medium transition-colors">
                                    Manage
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-5 py-8 text-center text-stone-400 text-xs">
                                No orders received yet.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Best Sellers & Quick Stats (1 col) -->
        <div class="space-y-6">
            <!-- Top Selling Shoes -->
            <div class="bg-white rounded-xl border border-[#D8D0C4]/70 shadow-sm p-5">
                <h3 class="font-luxury-serif text-lg font-semibold text-stone-900 mb-1">Curated Bestsellers</h3>
                <p class="text-xs text-stone-500 mb-4">Highest volume footwear models</p>

                <div class="space-y-3.5">
                    @forelse($top_products as $top)
                    <div class="flex items-center space-x-3 p-2 rounded-lg hover:bg-stone-50 transition-colors">
                        <img src="{{ $top->image_url }}" alt="{{ $top->product_name }}" class="w-12 h-12 rounded object-cover border border-stone-200 flex-shrink-0">
                        <div class="flex-1 min-w-0">
                            <h4 class="text-xs font-semibold text-stone-900 truncate">{{ $top->product_name }}</h4>
                            <p class="text-[11px] text-stone-500">{{ $top->total_sold }} pairs sold</p>
                        </div>
                        <div class="text-right">
                            <span class="text-xs font-bold text-stone-900">₱{{ number_format($top->total_revenue, 2) }}</span>
                        </div>
                    </div>
                    @empty
                    <p class="text-xs text-stone-400 py-4 text-center">No sales recorded yet.</p>
                    @endforelse
                </div>
            </div>

            <!-- Revenue by Category -->
            <div class="bg-white rounded-xl border border-[#D8D0C4]/70 shadow-sm p-5">
                <h3 class="font-luxury-serif text-lg font-semibold text-stone-900 mb-1">Category Demand</h3>
                <p class="text-xs text-stone-500 mb-4">Revenue share across collections</p>

                <div class="space-y-3">
                    @forelse($sales_by_category as $cat)
                    <div>
                        <div class="flex justify-between text-xs mb-1">
                            <span class="font-medium text-stone-800">{{ $cat->category_name }}</span>
                            <span class="font-bold text-stone-900">₱{{ number_format($cat->revenue, 2) }}</span>
                        </div>
                        <div class="w-full bg-stone-100 rounded-full h-1.5 overflow-hidden">
                            @php
                                $percent = $total_sales > 0 ? min(100, round(($cat->revenue / $total_sales) * 100)) : 0;
                            @endphp
                            <div class="bg-stone-900 h-1.5 rounded-full" style="width: {{ $percent }}%"></div>
                        </div>
                    </div>
                    @empty
                    <p class="text-xs text-stone-400 py-3 text-center">Awaiting initial orders.</p>
                    @endforelse
                </div>
            </div>
        </div>

    </div>

</div>
@endsection

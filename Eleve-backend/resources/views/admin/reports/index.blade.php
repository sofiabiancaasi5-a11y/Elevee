@extends('layouts.admin')

@section('title', 'Financial Reports')
@section('header_title', 'Financial & Operational Reports')

@section('content')
<div class="space-y-8">

    <!-- KPI Metric Summary -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="bg-white p-5 rounded-xl border border-[#D8D0C4]/70 shadow-sm">
            <span class="text-xs uppercase tracking-wider text-stone-500 font-medium">Accumulated Sales</span>
            <div class="font-luxury-serif text-3xl font-bold text-stone-900 mt-2">₱{{ number_format($total_sales, 2) }}</div>
            <p class="text-[11px] text-emerald-600 font-medium mt-1">Excludes cancelled transactions</p>
        </div>

        <div class="bg-white p-5 rounded-xl border border-[#D8D0C4]/70 shadow-sm">
            <span class="text-xs uppercase tracking-wider text-stone-500 font-medium">Completed Deliveries</span>
            <div class="font-luxury-serif text-3xl font-bold text-stone-900 mt-2">{{ $delivered_orders }}</div>
            <p class="text-[11px] text-stone-500 mt-1">Successfully fulfilled to clients</p>
        </div>

        <div class="bg-white p-5 rounded-xl border border-[#D8D0C4]/70 shadow-sm">
            <span class="text-xs uppercase tracking-wider text-stone-500 font-medium">Average Order Value</span>
            @php $aov = $total_orders > 0 ? $total_sales / $total_orders : 0; @endphp
            <div class="font-luxury-serif text-3xl font-bold text-stone-900 mt-2">₱{{ number_format($aov, 2) }}</div>
            <p class="text-[11px] text-stone-500 mt-1">Per checkout basket</p>
        </div>

        <div class="bg-white p-5 rounded-xl border border-[#D8D0C4]/70 shadow-sm">
            <span class="text-xs uppercase tracking-wider text-stone-500 font-medium">Fulfillment Rate</span>
            @php $rate = $total_orders > 0 ? round(($delivered_orders / $total_orders) * 100) : 100; @endphp
            <div class="font-luxury-serif text-3xl font-bold text-stone-900 mt-2">{{ $rate }}%</div>
            <p class="text-[11px] text-stone-500 mt-1">Orders reaching completion</p>
        </div>
    </div>

    <!-- Status Breakdown Grid -->
    <div class="bg-white p-6 rounded-xl border border-[#D8D0C4]/70 shadow-sm space-y-4">
        <h3 class="font-luxury-serif text-lg font-semibold text-stone-900 pb-2 border-b border-stone-100">Order Status Distribution</h3>
        
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-3">
            @foreach($status_counts as $st => $cnt)
            <div class="p-3 bg-stone-50 border border-stone-200 rounded-lg text-center">
                <span class="text-[10px] uppercase tracking-wider font-semibold text-stone-500 block">{{ $st }}</span>
                <span class="font-luxury-serif text-2xl font-bold text-stone-900 mt-1 block">{{ $cnt }}</span>
            </div>
            @endforeach
        </div>
    </div>

    <!-- 2 Column Breakdown -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Category Revenue Breakdown -->
        <div class="bg-white p-6 rounded-xl border border-[#D8D0C4]/70 shadow-sm space-y-4">
            <h3 class="font-luxury-serif text-lg font-semibold text-stone-900 pb-2 border-b border-stone-100">Revenue Contribution by Category</h3>
            
            <div class="space-y-4">
                @foreach($sales_by_category as $cat)
                <div>
                    <div class="flex justify-between text-xs mb-1">
                        <span class="font-semibold text-stone-900">{{ $cat->category_name }}</span>
                        <span class="font-mono font-bold text-stone-900">₱{{ number_format($cat->revenue, 2) }} ({{ $cat->items_sold }} pairs)</span>
                    </div>
                    <div class="w-full bg-stone-100 rounded-full h-2 overflow-hidden">
                        @php $pct = $total_sales > 0 ? min(100, round(($cat->revenue / $total_sales) * 100)) : 0; @endphp
                        <div class="bg-stone-900 h-2 rounded-full" style="width: {{ $pct }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Best-Selling Footwear Models -->
        <div class="bg-white p-6 rounded-xl border border-[#D8D0C4]/70 shadow-sm space-y-4">
            <h3 class="font-luxury-serif text-lg font-semibold text-stone-900 pb-2 border-b border-stone-100">Top Performing Silhouettes</h3>

            <div class="space-y-3">
                @foreach($top_products as $prod)
                <div class="flex items-center space-x-3 p-3 bg-stone-50 border border-stone-200 rounded-lg">
                    <img src="{{ $prod->image_url }}" alt="" class="w-12 h-12 rounded object-cover border border-stone-200 flex-shrink-0">
                    <div class="flex-1 min-w-0">
                        <h4 class="text-xs font-semibold text-stone-900 truncate">{{ $prod->product_name }}</h4>
                        <p class="text-[11px] text-stone-500">{{ $prod->total_sold }} units purchased</p>
                    </div>
                    <div class="text-right">
                        <span class="text-xs font-bold text-stone-900 font-mono">₱{{ number_format($prod->total_revenue, 2) }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

</div>
@endsection

@extends('layouts.admin')

@section('title', 'Inventory Matrix')
@section('header_title', 'Inventory Control')

@section('content')
<div class="space-y-6">

    <!-- Filters & Quick Stats Bar -->
    <div class="bg-white p-5 rounded-xl border border-[#D8D0C4]/70 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <form action="{{ route('admin.inventory.index') }}" method="GET" class="flex flex-wrap items-center gap-3 flex-1">
            <div class="relative min-w-[220px]">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search SKU, shoe, or shade..."
                       class="w-full pl-9 pr-4 py-2 bg-stone-50 border border-stone-200 rounded-lg text-xs text-stone-800 placeholder-stone-400 focus:outline-none focus:border-stone-500">
                <svg class="w-4 h-4 text-stone-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>

            <select name="stock_status" class="py-2 px-3 bg-stone-50 border border-stone-200 rounded-lg text-xs text-stone-700 focus:outline-none focus:border-stone-500">
                <option value="">All Stock Levels</option>
                <option value="low" {{ request('stock_status') === 'low' ? 'selected' : '' }}>Low Stock (≤ 5 pairs)</option>
                <option value="out" {{ request('stock_status') === 'out' ? 'selected' : '' }}>Out of Stock (0 pairs)</option>
                <option value="in" {{ request('stock_status') === 'in' ? 'selected' : '' }}>Well Stocked (> 5 pairs)</option>
            </select>

            <button type="submit" class="px-4 py-2 bg-stone-900 text-white rounded-lg text-xs font-medium uppercase tracking-wider hover:bg-stone-800 transition-colors">
                Apply
            </button>
            @if(request()->hasAny(['search', 'stock_status']))
                <a href="{{ route('admin.inventory.index') }}" class="text-xs text-stone-500 hover:text-stone-900 underline self-center">Reset</a>
            @endif
        </form>

        <div class="flex items-center space-x-2">
            <span class="px-3 py-1.5 rounded-lg text-xs font-semibold {{ $lowStockCount > 0 ? 'bg-amber-100 text-amber-900 border border-amber-300' : 'bg-stone-100 text-stone-700' }}">
                {{ $lowStockCount }} Low Stock Alert{{ $lowStockCount === 1 ? '' : 's' }}
            </span>
        </div>
    </div>

    <!-- Inventory Matrix Table -->
    <div class="bg-white rounded-xl border border-[#D8D0C4]/70 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-stone-50 text-[11px] uppercase tracking-wider text-stone-500 border-b border-stone-200">
                    <tr>
                        <th class="px-5 py-3.5 font-medium">SKU Reference</th>
                        <th class="px-5 py-3.5 font-medium">Model</th>
                        <th class="px-5 py-3.5 font-medium">Size</th>
                        <th class="px-5 py-3.5 font-medium">Shade / Color</th>
                        <th class="px-5 py-3.5 font-medium">Stock Level</th>
                        <th class="px-5 py-3.5 font-medium text-right">Quick Stock Adjustment</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse($variants as $variant)
                    <tr class="hover:bg-stone-50/70 transition-colors">
                        <!-- SKU -->
                        <td class="px-5 py-4 font-mono text-xs font-semibold text-stone-900">
                            {{ $variant->sku }}
                        </td>

                        <!-- Model -->
                        <td class="px-5 py-4">
                            <div class="flex items-center space-x-3">
                                <img src="{{ $variant->product->primary_image }}" alt="" 
                                     class="w-10 h-10 rounded object-cover border border-stone-200 flex-shrink-0">
                                <div>
                                    <h4 class="font-medium text-stone-900 text-xs">{{ $variant->product->name }}</h4>
                                    <p class="text-[10px] text-stone-400">{{ $variant->product->category->name }}</p>
                                </div>
                            </div>
                        </td>

                        <!-- Size -->
                        <td class="px-5 py-4">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-stone-100 text-stone-800">
                                US {{ $variant->size }}
                            </span>
                        </td>

                        <!-- Shade / Color -->
                        <td class="px-5 py-4">
                            <div class="flex items-center space-x-2">
                                <span class="w-3.5 h-3.5 rounded-full border border-stone-300 shadow-sm" style="background-color: {{ $variant->color_hex }}"></span>
                                <span class="text-xs text-stone-700">{{ $variant->color }}</span>
                            </div>
                        </td>

                        <!-- Stock Level -->
                        <td class="px-5 py-4">
                            @if($variant->stock_quantity <= 0)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-rose-100 text-rose-800 border border-rose-200">
                                    Out of Stock
                                </span>
                            @elseif($variant->stock_quantity <= 5)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-amber-100 text-amber-800 border border-amber-200">
                                    Low ({{ $variant->stock_quantity }} pairs)
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-100 text-emerald-800 border border-emerald-200">
                                    {{ $variant->stock_quantity }} in stock
                                </span>
                            @endif
                        </td>

                        <!-- Quick Adjustment Form -->
                        <td class="px-5 py-4 text-right">
                            <form action="{{ route('admin.inventory.update') }}" method="POST" class="inline-flex items-center space-x-2">
                                @csrf
                                <input type="hidden" name="variant_id" value="{{ $variant->id }}">
                                <input type="number" name="stock_quantity" value="{{ $variant->stock_quantity }}" min="0" max="999"
                                       class="w-16 px-2 py-1 text-center font-mono text-xs bg-stone-50 border border-stone-300 rounded focus:outline-none focus:border-stone-900">
                                <button type="submit" class="px-2.5 py-1 bg-stone-900 hover:bg-stone-800 text-white rounded text-xs font-medium transition-colors">
                                    Set
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-12 text-center text-stone-400 text-xs">
                            No inventory items found matching the selected filter.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($variants->hasPages())
        <div class="p-4 border-t border-stone-200 bg-stone-50">
            {{ $variants->links() }}
        </div>
        @endif
    </div>

</div>
@endsection

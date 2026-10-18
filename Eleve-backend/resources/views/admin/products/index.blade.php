@extends('layouts.admin')

@section('title', 'Footwear Catalog')
@section('header_title', 'Footwear Collection')

@section('content')
<div class="space-y-6">

    <!-- Actions & Filter Bar -->
    <div class="bg-white p-5 rounded-xl border border-[#D8D0C4]/70 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <form action="{{ route('admin.products.index') }}" method="GET" class="flex flex-wrap items-center gap-3 flex-1">
            <!-- Search -->
            <div class="relative min-w-[240px]">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search shoe name..."
                       class="w-full pl-9 pr-4 py-2 bg-stone-50 border border-stone-200 rounded-lg text-xs text-stone-800 placeholder-stone-400 focus:outline-none focus:border-stone-500">
                <svg class="w-4 h-4 text-stone-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>

            <!-- Category Filter -->
            <select name="category" class="py-2 px-3 bg-stone-50 border border-stone-200 rounded-lg text-xs text-stone-700 focus:outline-none focus:border-stone-500">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>

            <!-- Status Filter -->
            <select name="status" class="py-2 px-3 bg-stone-50 border border-stone-200 rounded-lg text-xs text-stone-700 focus:outline-none focus:border-stone-500">
                <option value="">All Statuses</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Archived</option>
            </select>

            <button type="submit" class="px-4 py-2 bg-stone-900 text-white rounded-lg text-xs font-medium uppercase tracking-wider hover:bg-stone-800 transition-colors">
                Filter
            </button>
            @if(request()->hasAny(['search', 'category', 'status']))
                <a href="{{ route('admin.products.index') }}" class="text-xs text-stone-500 hover:text-stone-900 underline self-center">Clear</a>
            @endif
        </form>

        <a href="{{ route('admin.products.create') }}" class="inline-flex items-center px-4 py-2.5 bg-[#171717] hover:bg-stone-800 text-white text-xs font-semibold uppercase tracking-widest rounded-lg shadow-sm transition-colors">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            New Footwear
        </a>
    </div>

    <!-- Products Table -->
    <div class="bg-white rounded-xl border border-[#D8D0C4]/70 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-stone-50 text-[11px] uppercase tracking-wider text-stone-500 border-b border-stone-200">
                    <tr>
                        <th class="px-5 py-3.5 font-medium">Footwear</th>
                        <th class="px-5 py-3.5 font-medium">Category</th>
                        <th class="px-5 py-3.5 font-medium">Base Price</th>
                        <th class="px-5 py-3.5 font-medium">Variants & Stock</th>
                        <th class="px-5 py-3.5 font-medium">Badges</th>
                        <th class="px-5 py-3.5 font-medium">Status</th>
                        <th class="px-5 py-3.5 font-medium text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse($products as $product)
                    <tr class="hover:bg-stone-50/70 transition-colors">
                        <!-- Product Info -->
                        <td class="px-5 py-4">
                            <div class="flex items-center space-x-3">
                                <img src="{{ $product->primary_image }}" alt="{{ $product->name }}" 
                                     class="w-14 h-14 rounded-lg object-cover border border-stone-200 shadow-sm flex-shrink-0">
                                <div>
                                    <h4 class="font-luxury-serif font-semibold text-stone-900 text-sm">{{ $product->name }}</h4>
                                    <p class="text-[11px] text-stone-400 font-mono">{{ $product->slug }}</p>
                                </div>
                            </div>
                        </td>

                        <!-- Category -->
                        <td class="px-5 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-stone-100 text-stone-800 border border-stone-200">
                                {{ $product->category->name }}
                            </span>
                        </td>

                        <!-- Base Price -->
                        <td class="px-5 py-4 font-semibold text-stone-900">
                            ₱{{ number_format($product->base_price, 2) }}
                        </td>

                        <!-- Variants & Stock -->
                        <td class="px-5 py-4">
                            <div class="text-xs">
                                <span class="font-semibold text-stone-800">{{ $product->variants->count() }} variants</span>
                                <p class="text-[11px] {{ $product->total_stock <= 5 ? 'text-amber-600 font-bold' : 'text-stone-500' }}">
                                    Total {{ $product->total_stock }} pairs available
                                </p>
                            </div>
                        </td>

                        <!-- Badges -->
                        <td class="px-5 py-4">
                            <div class="flex flex-wrap gap-1">
                                @if($product->is_featured)
                                    <span class="px-2 py-0.5 rounded text-[10px] uppercase font-semibold bg-champagne/20 text-stone-800 border border-stone-300">Featured</span>
                                @endif
                                @if($product->is_bestseller)
                                    <span class="px-2 py-0.5 rounded text-[10px] uppercase font-semibold bg-stone-900 text-white">Bestseller</span>
                                @endif
                            </div>
                        </td>

                        <!-- Status -->
                        <td class="px-5 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-medium capitalize {{ $product->status === 'active' ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-stone-100 text-stone-600' }}">
                                {{ $product->status }}
                            </span>
                        </td>

                        <!-- Actions -->
                        <td class="px-5 py-4 text-right">
                            <div class="inline-flex items-center space-x-2">
                                <a href="{{ route('admin.products.edit', $product->id) }}" class="p-1.5 rounded bg-stone-100 hover:bg-stone-200 text-stone-700 transition-colors" title="Edit Product">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                    </svg>
                                </a>
                                <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" onsubmit="return confirm('Archive product {{ $product->name }}?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 rounded bg-rose-50 hover:bg-rose-100 text-rose-600 transition-colors" title="Delete Product">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-5 py-12 text-center text-stone-400 text-xs">
                            No footwear found matching the selected criteria.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($products->hasPages())
        <div class="p-4 border-t border-stone-200 bg-stone-50">
            {{ $products->links() }}
        </div>
        @endif
    </div>

</div>
@endsection

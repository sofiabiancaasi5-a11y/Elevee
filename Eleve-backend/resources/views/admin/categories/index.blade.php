@extends('layouts.admin')

@section('title', 'Categories Directory')
@section('header_title', 'Footwear Categories')

@section('content')
<div class="space-y-6">

    <!-- Actions & Filter Bar -->
    <div class="bg-white p-5 rounded-xl border border-[#D8D0C4]/70 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <form action="{{ route('admin.categories.index') }}" method="GET" class="flex flex-wrap items-center gap-3 flex-1">
            <div class="relative min-w-[240px]">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search category..."
                       class="w-full pl-9 pr-4 py-2 bg-stone-50 border border-stone-200 rounded-lg text-xs text-stone-800 placeholder-stone-400 focus:outline-none focus:border-stone-500">
                <svg class="w-4 h-4 text-stone-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>

            <select name="status" class="py-2 px-3 bg-stone-50 border border-stone-200 rounded-lg text-xs text-stone-700 focus:outline-none focus:border-stone-500">
                <option value="">All Statuses</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>

            <button type="submit" class="px-4 py-2 bg-stone-900 text-white rounded-lg text-xs font-medium uppercase tracking-wider hover:bg-stone-800 transition-colors">
                Filter
            </button>
            @if(request()->hasAny(['search', 'status']))
                <a href="{{ route('admin.categories.index') }}" class="text-xs text-stone-500 hover:text-stone-900 underline self-center">Clear</a>
            @endif
        </form>

        <a href="{{ route('admin.categories.create') }}" class="inline-flex items-center px-4 py-2.5 bg-[#171717] hover:bg-stone-800 text-white text-xs font-semibold uppercase tracking-widest rounded-lg shadow-sm transition-colors">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            New Category
        </a>
    </div>

    <!-- Categories Table -->
    <div class="bg-white rounded-xl border border-[#D8D0C4]/70 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-stone-50 text-[11px] uppercase tracking-wider text-stone-500 border-b border-stone-200">
                    <tr>
                        <th class="px-5 py-3.5 font-medium">Category Silhouette</th>
                        <th class="px-5 py-3.5 font-medium">Slug</th>
                        <th class="px-5 py-3.5 font-medium">Description</th>
                        <th class="px-5 py-3.5 font-medium">Product Count</th>
                        <th class="px-5 py-3.5 font-medium">Status</th>
                        <th class="px-5 py-3.5 font-medium text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse($categories as $cat)
                        <tr class="hover:bg-stone-50/70 transition-colors">
                            <td class="px-5 py-4">
                                <div class="flex items-center space-x-3.5">
                                    @if($cat->image_url)
                                        <img src="{{ $cat->image_url }}" alt="{{ $cat->name }}" class="w-12 h-12 rounded-lg object-cover bg-stone-100 border border-stone-200">
                                    @else
                                        <div class="w-12 h-12 rounded-lg bg-stone-100 border border-stone-200 flex items-center justify-center text-stone-400 font-bold font-serif">
                                            {{ substr($cat->name, 0, 1) }}
                                        </div>
                                    @endif
                                    <div>
                                        <div class="font-semibold text-stone-900">{{ $cat->name }}</div>
                                        <div class="text-[11px] text-stone-400">ID: #{{ $cat->id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <span class="font-mono text-xs text-stone-600 bg-stone-100 px-2 py-0.5 rounded">
                                    {{ $cat->slug }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-xs text-stone-500 max-w-xs truncate">
                                {{ $cat->description ?: '—' }}
                            </td>
                            <td class="px-5 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-amber-50 text-amber-900 border border-amber-200">
                                    {{ $cat->products_count }} silhouettes
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                @if($cat->is_active)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-emerald-50 text-emerald-800 border border-emerald-200">
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-stone-100 text-stone-600 border border-stone-200">
                                        Inactive
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-right">
                                <div class="flex items-center justify-end space-x-2">
                                    <a href="{{ route('admin.categories.edit', $cat->id) }}" class="p-1.5 text-stone-400 hover:text-stone-900 hover:bg-stone-100 rounded transition-colors" title="Edit Category">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>

                                    <form action="{{ route('admin.categories.destroy', $cat->id) }}" method="POST" onsubmit="return confirm('Are you certain you wish to delete this category?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-stone-400 hover:text-rose-600 hover:bg-rose-50 rounded transition-colors" title="Delete Category">
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
                            <td colspan="6" class="px-5 py-12 text-center text-stone-400">
                                No footwear categories found matching your search.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($categories->hasPages())
            <div class="px-5 py-3.5 border-t border-stone-200 bg-stone-50/50">
                {{ $categories->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

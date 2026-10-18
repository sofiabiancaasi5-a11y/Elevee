@extends('layouts.admin')

@section('title', 'Edit Footwear')
@section('header_title', 'Refine ' . $product->name)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <a href="{{ route('admin.products.index') }}" class="inline-flex items-center text-xs text-stone-600 hover:text-stone-900 font-medium">
            &larr; Back to Footwear Catalog
        </a>
    </div>

    @if ($errors->any())
        <div class="p-4 bg-rose-50 border border-rose-200 rounded-xl text-rose-800 text-xs space-y-1">
            <span class="font-bold">Please correct the following errors:</span>
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.products.update', $product->id) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Core Details Card -->
        <div class="bg-white p-6 rounded-xl border border-[#D8D0C4]/70 shadow-sm space-y-5">
            <h3 class="font-luxury-serif text-lg font-semibold text-stone-900 pb-2 border-b border-stone-100">Product Essence</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- Shoe Name -->
                <div>
                    <label class="block text-xs uppercase tracking-wider text-stone-600 font-semibold mb-1.5">Model Name *</label>
                    <input type="text" name="name" value="{{ old('name', $product->name) }}" required
                           class="w-full px-3.5 py-2.5 bg-stone-50 border border-stone-200 rounded-lg text-sm text-stone-900 focus:outline-none focus:border-stone-500">
                </div>

                <!-- Category -->
                <div>
                    <label class="block text-xs uppercase tracking-wider text-stone-600 font-semibold mb-1.5">Category *</label>
                    <select name="category_id" required
                            class="w-full px-3.5 py-2.5 bg-stone-50 border border-stone-200 rounded-lg text-sm text-stone-900 focus:outline-none focus:border-stone-500">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Base Price -->
                <div>
                    <label class="block text-xs uppercase tracking-wider text-stone-600 font-semibold mb-1.5">Base Price (PHP ₱) *</label>
                    <input type="number" step="0.01" name="base_price" value="{{ old('base_price', $product->base_price) }}" required
                           class="w-full px-3.5 py-2.5 bg-stone-50 border border-stone-200 rounded-lg text-sm text-stone-900 focus:outline-none focus:border-stone-500">
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-xs uppercase tracking-wider text-stone-600 font-semibold mb-1.5">Publication Status *</label>
                    <select name="status" required
                            class="w-full px-3.5 py-2.5 bg-stone-50 border border-stone-200 rounded-lg text-sm text-stone-900 focus:outline-none focus:border-stone-500">
                        <option value="active" {{ old('status', $product->status) === 'active' ? 'selected' : '' }}>Active (Available in Mobile App)</option>
                        <option value="draft" {{ old('status', $product->status) === 'draft' ? 'selected' : '' }}>Draft (Hidden)</option>
                        <option value="archived" {{ old('status', $product->status) === 'archived' ? 'selected' : '' }}>Archived</option>
                    </select>
                </div>
            </div>

            <!-- Description -->
            <div>
                <label class="block text-xs uppercase tracking-wider text-stone-600 font-semibold mb-1.5">Description & Craftsmanship *</label>
                <textarea name="description" rows="3" required
                          class="w-full px-3.5 py-2.5 bg-stone-50 border border-stone-200 rounded-lg text-sm text-stone-900 focus:outline-none focus:border-stone-500">{{ old('description', $product->description) }}</textarea>
            </div>

            <!-- Image URL with preview -->
            <div>
                <label class="block text-xs uppercase tracking-wider text-stone-600 font-semibold mb-1.5">Primary Photography URL *</label>
                <input type="url" name="primary_image" id="primary_image_input" 
                       value="{{ old('primary_image', $product->primary_image) }}" required
                       class="w-full px-3.5 py-2.5 bg-stone-50 border border-stone-200 rounded-lg text-sm text-stone-900 focus:outline-none focus:border-stone-500">
                
                <div class="mt-3 flex items-center space-x-4">
                    <img id="image_preview" src="{{ $product->primary_image }}" 
                         alt="Preview" class="w-20 h-20 rounded-lg object-cover border border-stone-200">
                    <p class="text-xs text-stone-500">Update image link to change the primary catalog visual.</p>
                </div>
            </div>

            <!-- Badges -->
            <div class="flex items-center space-x-6 pt-2">
                <label class="flex items-center space-x-2 text-xs font-medium text-stone-700 cursor-pointer">
                    <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $product->is_featured) ? 'checked' : '' }} class="rounded border-stone-300 text-stone-900 focus:ring-0">
                    <span>Feature on Mobile Home screen</span>
                </label>
                <label class="flex items-center space-x-2 text-xs font-medium text-stone-700 cursor-pointer">
                    <input type="checkbox" name="is_bestseller" value="1" {{ old('is_bestseller', $product->is_bestseller) ? 'checked' : '' }} class="rounded border-stone-300 text-stone-900 focus:ring-0">
                    <span>Mark as Atelier Bestseller</span>
                </label>
            </div>
        </div>

        <div class="flex items-center justify-end space-x-3 pt-2">
            <a href="{{ route('admin.products.index') }}" class="px-5 py-2.5 bg-stone-100 hover:bg-stone-200 text-stone-700 rounded-lg text-xs font-semibold uppercase tracking-wider transition-colors">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2.5 bg-[#171717] hover:bg-stone-800 text-white rounded-lg text-xs font-semibold uppercase tracking-widest shadow-md transition-colors">
                Save Changes
            </button>
        </div>
    </form>

    <!-- Interactive Variant Matrix Management -->
    <div class="bg-white p-6 rounded-xl border border-[#D8D0C4]/70 shadow-sm space-y-6">
        <div class="flex items-center justify-between pb-3 border-b border-stone-100">
            <div>
                <h3 class="font-luxury-serif text-lg font-semibold text-stone-900">Variant & Stock Matrix ({{ $product->variants->count() }})</h3>
                <p class="text-xs text-stone-500">Fine-tune individual sizes, shade hex codes, stock levels, and price adjustments.</p>
            </div>
            <a href="{{ route('admin.inventory.index', ['search' => $product->name]) }}" class="text-xs text-stone-800 underline font-medium">Bulk Inventory Matrix &rarr;</a>
        </div>

        <!-- Add New Variant Form -->
        <div class="p-4 bg-stone-50 border border-stone-200 rounded-xl space-y-3">
            <h4 class="text-xs font-bold uppercase tracking-wider text-stone-800 font-serif">Add New Variant Specification</h4>
            <form action="{{ route('admin.products.variants.store', $product->id) }}" method="POST" class="grid grid-cols-2 md:grid-cols-6 gap-3 items-end">
                @csrf
                <div>
                    <label class="block text-[10px] uppercase font-bold text-stone-500 mb-1">Size (US) *</label>
                    <input type="text" name="size" required placeholder="e.g. 9.5" class="w-full px-2.5 py-1.5 bg-white border border-stone-200 rounded text-xs">
                </div>
                <div>
                    <label class="block text-[10px] uppercase font-bold text-stone-500 mb-1">Color Shade *</label>
                    <input type="text" name="color" required placeholder="e.g. Ivory Blanc" class="w-full px-2.5 py-1.5 bg-white border border-stone-200 rounded text-xs">
                </div>
                <div>
                    <label class="block text-[10px] uppercase font-bold text-stone-500 mb-1">Hex Color *</label>
                    <div class="flex items-center space-x-1">
                        <input type="color" name="color_hex" value="#F7F5F0" class="w-7 h-7 p-0 border border-stone-300 rounded cursor-pointer">
                        <input type="text" name="color_hex" value="#F7F5F0" class="w-full px-2 py-1.5 bg-white border border-stone-200 rounded text-xs font-mono">
                    </div>
                </div>
                <div>
                    <label class="block text-[10px] uppercase font-bold text-stone-500 mb-1">Initial Stock *</label>
                    <input type="number" name="stock_quantity" min="0" value="10" required class="w-full px-2.5 py-1.5 bg-white border border-stone-200 rounded text-xs">
                </div>
                <div>
                    <label class="block text-[10px] uppercase font-bold text-stone-500 mb-1">Price Adjust (₱)</label>
                    <input type="number" step="0.01" name="price_adjustment" value="0.00" class="w-full px-2.5 py-1.5 bg-white border border-stone-200 rounded text-xs">
                </div>
                <div>
                    <button type="submit" class="w-full py-2 bg-stone-900 hover:bg-stone-800 text-white rounded text-xs font-semibold uppercase tracking-wider transition-colors">
                        + Add Variant
                    </button>
                </div>
            </form>
        </div>

        <!-- Variants Table -->
        <div class="overflow-x-auto border border-stone-200 rounded-lg">
            <table class="w-full text-left text-xs">
                <thead class="bg-stone-50 text-[10px] uppercase tracking-wider text-stone-500 border-b border-stone-200">
                    <tr>
                        <th class="px-4 py-3 font-medium">SKU</th>
                        <th class="px-4 py-3 font-medium">Specification</th>
                        <th class="px-4 py-3 font-medium">Stock Level</th>
                        <th class="px-4 py-3 font-medium">Price Adjustment</th>
                        <th class="px-4 py-3 font-medium">Effective Price</th>
                        <th class="px-4 py-3 font-medium text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse($product->variants as $variant)
                    <tr class="hover:bg-stone-50/70 transition-colors">
                        <td class="px-4 py-3 font-mono text-[11px] text-stone-600">
                            {{ $variant->sku }}
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center space-x-2">
                                <span class="w-3.5 h-3.5 rounded-full border border-stone-300 shadow-sm" style="background-color: {{ $variant->color_hex }}"></span>
                                <span class="font-bold text-stone-900">US {{ $variant->size }}</span>
                                <span class="text-stone-400">&bull;</span>
                                <span class="text-stone-600">{{ $variant->color }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <form action="{{ route('admin.products.variants.update', $variant->id) }}" method="POST" class="flex items-center space-x-1.5">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="sku" value="{{ $variant->sku }}">
                                <input type="hidden" name="price_adjustment" value="{{ $variant->price_adjustment }}">
                                <input type="number" name="stock_quantity" value="{{ $variant->stock_quantity }}" min="0" class="w-16 px-2 py-1 bg-stone-50 border border-stone-200 rounded text-xs text-center font-bold">
                                <button type="submit" class="px-2 py-1 bg-stone-200 hover:bg-stone-300 rounded text-[10px] font-medium text-stone-700">Save</button>
                            </form>
                        </td>
                        <td class="px-4 py-3 font-mono">
                            {{ $variant->price_adjustment >= 0 ? '+₱' . number_format($variant->price_adjustment, 2) : '-₱' . number_format(abs($variant->price_adjustment), 2) }}
                        </td>
                        <td class="px-4 py-3 font-bold text-stone-900">
                            ₱{{ number_format($variant->effective_price, 2) }}
                        </td>
                        <td class="px-4 py-3 text-right">
                            <form action="{{ route('admin.products.variants.destroy', $variant->id) }}" method="POST" onsubmit="return confirm('Remove variant {{ $variant->sku }}?');" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1 text-stone-400 hover:text-rose-600 rounded transition-colors" title="Delete Variant">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-6 text-center text-stone-400">
                            No variants created yet. Use the form above to add sizes and shades.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<script>
    const input = document.getElementById('primary_image_input');
    const preview = document.getElementById('image_preview');
    input.addEventListener('input', () => {
        if (input.value) {
            preview.src = input.value;
        }
    });
</script>
@endsection

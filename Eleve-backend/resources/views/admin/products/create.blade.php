@extends('layouts.admin')

@section('title', 'Add New Footwear')
@section('header_title', 'Craft New Footwear')

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

    <form action="{{ route('admin.products.store') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Core Details Card -->
        <div class="bg-white p-6 rounded-xl border border-[#D8D0C4]/70 shadow-sm space-y-5">
            <h3 class="font-luxury-serif text-lg font-semibold text-stone-900 pb-2 border-b border-stone-100">Product Essence</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- Shoe Name -->
                <div>
                    <label class="block text-xs uppercase tracking-wider text-stone-600 font-semibold mb-1.5">Model Name *</label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="e.g. The Aeris Minimalist Low-Top" required
                           class="w-full px-3.5 py-2.5 bg-stone-50 border border-stone-200 rounded-lg text-sm text-stone-900 focus:outline-none focus:border-stone-500">
                </div>

                <!-- Category -->
                <div>
                    <label class="block text-xs uppercase tracking-wider text-stone-600 font-semibold mb-1.5">Category *</label>
                    <select name="category_id" required
                            class="w-full px-3.5 py-2.5 bg-stone-50 border border-stone-200 rounded-lg text-sm text-stone-900 focus:outline-none focus:border-stone-500">
                        <option value="">Select Category</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Base Price -->
                <div>
                    <label class="block text-xs uppercase tracking-wider text-stone-600 font-semibold mb-1.5">Base Price (PHP ₱) *</label>
                    <input type="number" step="0.01" name="base_price" value="{{ old('base_price', '6850.00') }}" required
                           class="w-full px-3.5 py-2.5 bg-stone-50 border border-stone-200 rounded-lg text-sm text-stone-900 focus:outline-none focus:border-stone-500">
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-xs uppercase tracking-wider text-stone-600 font-semibold mb-1.5">Publication Status *</label>
                    <select name="status" required
                            class="w-full px-3.5 py-2.5 bg-stone-50 border border-stone-200 rounded-lg text-sm text-stone-900 focus:outline-none focus:border-stone-500">
                        <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active (Available in Mobile App)</option>
                        <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Draft (Hidden)</option>
                        <option value="archived" {{ old('status') === 'archived' ? 'selected' : '' }}>Archived</option>
                    </select>
                </div>
            </div>

            <!-- Description -->
            <div>
                <label class="block text-xs uppercase tracking-wider text-stone-600 font-semibold mb-1.5">Description & Craftsmanship *</label>
                <textarea name="description" rows="3" required placeholder="Describe materials, leather origin, sole construction, and aesthetic details..."
                          class="w-full px-3.5 py-2.5 bg-stone-50 border border-stone-200 rounded-lg text-sm text-stone-900 focus:outline-none focus:border-stone-500">{{ old('description') }}</textarea>
            </div>

            <!-- Image URL with preview -->
            <div>
                <label class="block text-xs uppercase tracking-wider text-stone-600 font-semibold mb-1.5">Primary Photography URL *</label>
                <input type="url" name="primary_image" id="primary_image_input" 
                       value="{{ old('primary_image', 'https://images.unsplash.com/photo-1595950653106-6c9ebd614d3a?auto=format&fit=crop&w=1000&q=85') }}" 
                       required placeholder="https://..."
                       class="w-full px-3.5 py-2.5 bg-stone-50 border border-stone-200 rounded-lg text-sm text-stone-900 focus:outline-none focus:border-stone-500">
                
                <div class="mt-3 flex items-center space-x-4">
                    <img id="image_preview" src="https://images.unsplash.com/photo-1595950653106-6c9ebd614d3a?auto=format&fit=crop&w=1000&q=85" 
                         alt="Preview" class="w-20 h-20 rounded-lg object-cover border border-stone-200">
                    <p class="text-xs text-stone-500">Provide high-resolution footwear photography for mobile display.</p>
                </div>
            </div>

            <!-- Badges -->
            <div class="flex items-center space-x-6 pt-2">
                <label class="flex items-center space-x-2 text-xs font-medium text-stone-700 cursor-pointer">
                    <input type="checkbox" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }} class="rounded border-stone-300 text-stone-900 focus:ring-0">
                    <span>Feature on Mobile Home screen</span>
                </label>
                <label class="flex items-center space-x-2 text-xs font-medium text-stone-700 cursor-pointer">
                    <input type="checkbox" name="is_bestseller" value="1" {{ old('is_bestseller') ? 'checked' : '' }} class="rounded border-stone-300 text-stone-900 focus:ring-0">
                    <span>Mark as Atelier Bestseller</span>
                </label>
            </div>
        </div>

        <!-- Initial Variant Generation Card -->
        <div class="bg-white p-6 rounded-xl border border-[#D8D0C4]/70 shadow-sm space-y-4">
            <h3 class="font-luxury-serif text-lg font-semibold text-stone-900 pb-2 border-b border-stone-100">Initial Variant Matrix</h3>
            <p class="text-xs text-stone-500">The system will generate size and color combinations with individual SKUs and tracking.</p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- Sizes Selection -->
                <div>
                    <label class="block text-xs uppercase tracking-wider text-stone-600 font-semibold mb-2">Available Sizes</label>
                    <div class="flex flex-wrap gap-2">
                        @foreach(['5', '6', '7', '8', '9', '10', '11', '12'] as $size)
                        <label class="px-3 py-1.5 border border-stone-200 rounded-lg text-xs font-semibold cursor-pointer has-[:checked]:bg-stone-900 has-[:checked]:text-white transition-colors">
                            <input type="checkbox" name="sizes[]" value="{{ $size }}" {{ in_array($size, ['7', '8', '9', '10']) ? 'checked' : '' }} class="hidden">
                            <span>US {{ $size }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                <!-- Initial Stock -->
                <div>
                    <label class="block text-xs uppercase tracking-wider text-stone-600 font-semibold mb-1.5">Initial Stock Quantity (per variant)</label>
                    <input type="number" name="stock_quantity" value="15" min="0" required
                           class="w-full px-3.5 py-2.5 bg-stone-50 border border-stone-200 rounded-lg text-sm text-stone-900 focus:outline-none focus:border-stone-500">
                    <p class="text-[11px] text-stone-400 mt-1">Each generated size/color will start with this inventory amount.</p>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end space-x-3 pt-2">
            <a href="{{ route('admin.products.index') }}" class="px-5 py-2.5 bg-stone-100 hover:bg-stone-200 text-stone-700 rounded-lg text-xs font-semibold uppercase tracking-wider transition-colors">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2.5 bg-[#171717] hover:bg-stone-800 text-white rounded-lg text-xs font-semibold uppercase tracking-widest shadow-md transition-colors">
                Publish Footwear
            </button>
        </div>
    </form>

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

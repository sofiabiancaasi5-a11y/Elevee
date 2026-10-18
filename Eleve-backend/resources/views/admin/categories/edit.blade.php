@extends('layouts.admin')

@section('title', 'Edit Category')
@section('header_title', 'Edit Category: ' . $category->name)

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <a href="{{ route('admin.categories.index') }}" class="inline-flex items-center text-xs font-semibold uppercase tracking-wider text-stone-500 hover:text-stone-900 transition-colors">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Categories
        </a>

        <div class="text-xs text-stone-400">
            Contains <span class="font-bold text-stone-700">{{ $category->products_count }}</span> active products
        </div>
    </div>

    <form action="{{ route('admin.categories.update', $category->id) }}" method="POST" class="bg-white rounded-xl border border-[#D8D0C4]/70 shadow-sm p-6 space-y-6">
        @csrf
        @method('PUT')

        <div class="border-b border-stone-100 pb-4">
            <h3 class="text-base font-semibold text-stone-900 font-serif">Modify Category Details</h3>
            <p class="text-xs text-stone-500 mt-1">Updates to category name or status will reflect across mobile navigation and filters.</p>
        </div>

        @if ($errors->any())
            <div class="p-4 bg-rose-50 border border-rose-200 rounded-lg text-xs text-rose-700">
                <ul class="list-disc pl-4 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="space-y-4">
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-stone-700 mb-1">Category Name *</label>
                <input type="text" name="name" value="{{ old('name', $category->name) }}" required
                       class="w-full px-3.5 py-2.5 bg-stone-50 border border-stone-200 rounded-lg text-sm text-stone-900 focus:outline-none focus:border-stone-500">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-stone-700 mb-1">URL Slug</label>
                <input type="text" name="slug" value="{{ old('slug', $category->slug) }}"
                       class="w-full px-3.5 py-2.5 bg-stone-50 border border-stone-200 rounded-lg text-sm text-stone-900 font-mono text-xs focus:outline-none focus:border-stone-500">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-stone-700 mb-1">Editorial Description</label>
                <textarea name="description" rows="3"
                          class="w-full px-3.5 py-2.5 bg-stone-50 border border-stone-200 rounded-lg text-sm text-stone-900 focus:outline-none focus:border-stone-500">{{ old('description', $category->description) }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-stone-700 mb-1">Banner / Thumbnail Image URL</label>
                <input type="url" name="image_url" value="{{ old('image_url', $category->image_url) }}"
                       class="w-full px-3.5 py-2.5 bg-stone-50 border border-stone-200 rounded-lg text-sm text-stone-900 focus:outline-none focus:border-stone-500">
                @if($category->image_url)
                    <div class="mt-2.5 flex items-center space-x-3">
                        <img src="{{ $category->image_url }}" alt="{{ $category->name }}" class="w-16 h-16 rounded-lg object-cover border border-stone-200">
                        <span class="text-xs text-stone-400">Current visual asset</span>
                    </div>
                @endif
            </div>

            <div class="pt-2">
                <label class="inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $category->is_active) ? 'checked' : '' }} class="rounded border-stone-300 text-stone-900 shadow-sm focus:ring-0">
                    <span class="ml-2.5 text-xs font-medium text-stone-800">Category is Active & Visible in Mobile App</span>
                </label>
            </div>
        </div>

        <div class="pt-4 border-t border-stone-100 flex items-center justify-end space-x-3">
            <a href="{{ route('admin.categories.index') }}" class="px-4 py-2 border border-stone-200 text-stone-600 rounded-lg text-xs font-medium hover:bg-stone-50">Cancel</a>
            <button type="submit" class="px-5 py-2 bg-[#171717] hover:bg-stone-800 text-white rounded-lg text-xs font-semibold uppercase tracking-wider shadow-sm transition-colors">
                Update Category
            </button>
        </div>
    </form>
</div>
@endsection

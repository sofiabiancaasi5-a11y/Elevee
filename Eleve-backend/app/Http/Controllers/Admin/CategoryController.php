<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Category::withCount('products')->latest();

        if ($request->filled('search')) {
            $term = '%' . trim($request->search) . '%';
            $query->where('name', 'like', $term)->orWhere('description', 'like', $term);
        }

        if ($request->filled('status')) {
            $isActive = $request->status === 'active';
            $query->where('is_active', $isActive);
        }

        $categories = $query->paginate(10)->withQueryString();

        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:categories,name',
            'slug' => 'nullable|string|max:120|unique:categories,slug',
            'description' => 'nullable|string',
            'image_url' => 'nullable|url',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['slug'] = !empty($validated['slug']) 
            ? Str::slug($validated['slug']) 
            : Str::slug($validated['name']);
        $validated['is_active'] = $request->has('is_active');

        $category = Category::create($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Footwear Category "' . $category->name . '" has been created.');
    }

    public function show(Category $category)
    {
        return redirect()->route('admin.categories.edit', $category);
    }

    public function edit(Category $category)
    {
        $category->loadCount('products');
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:categories,name,' . $category->id,
            'slug' => 'nullable|string|max:120|unique:categories,slug,' . $category->id,
            'description' => 'nullable|string',
            'image_url' => 'nullable|url',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['slug'] = !empty($validated['slug']) 
            ? Str::slug($validated['slug']) 
            : Str::slug($validated['name']);
        $validated['is_active'] = $request->has('is_active');

        $category->update($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category "' . $category->name . '" updated successfully.');
    }

    public function destroy(Category $category)
    {
        $productCount = $category->products()->count();

        if ($productCount > 0) {
            return redirect()->route('admin.categories.index')
                ->with('error', 'Cannot delete category "' . $category->name . '" because it currently contains ' . $productCount . ' footwear silhouette(s). Please reassign them first.');
        }

        $name = $category->name;
        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category "' . $name . '" has been permanently removed.');
    }
}

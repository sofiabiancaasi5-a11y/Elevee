<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function __construct(
        protected ProductService $productService
    ) {}

    public function index(Request $request)
    {
        $query = Product::with(['category', 'variants'])->latest();

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('search')) {
            $term = '%' . trim($request->search) . '%';
            $query->where('name', 'like', $term);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $products = $query->paginate(12)->withQueryString();
        $categories = Category::all();

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->get();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'base_price' => 'required|numeric|min:0',
            'primary_image' => 'required|url',
            'status' => 'required|in:active,draft,archived',
            'is_featured' => 'nullable|boolean',
            'is_bestseller' => 'nullable|boolean',
            'sizes' => 'nullable|array',
            'colors' => 'nullable|array',
            'stock_quantity' => 'nullable|integer|min:0',
        ]);

        $variants = [];
        $sizes = $request->input('sizes', ['7', '8', '9', '10']);
        $colors = $request->input('colors', [
            ['name' => 'Obsidian Black', 'hex' => '#171717'],
        ]);
        $stock = (int) ($validated['stock_quantity'] ?? 10);

        foreach ($colors as $color) {
            foreach ($sizes as $size) {
                $colorName = is_array($color) ? $color['name'] : $color;
                $colorHex = is_array($color) ? ($color['hex'] ?? '#171717') : '#171717';
                $colorPrefix = strtoupper(substr(str_replace(' ', '', $colorName), 0, 3));
                $sku = 'ELE-' . strtoupper(Str::random(3)) . '-' . $colorPrefix . '-SZ' . $size;

                $variants[] = [
                    'size' => $size,
                    'color' => $colorName,
                    'color_hex' => $colorHex,
                    'sku' => $sku,
                    'stock_quantity' => $stock,
                    'price_adjustment' => 0.00,
                ];
            }
        }

        $validated['variants'] = $variants;
        $this->productService->createProduct($validated);

        return redirect()->route('admin.products.index')->with('success', 'Product "' . $validated['name'] . '" created with ' . count($variants) . ' variants.');
    }

    public function edit(Product $product)
    {
        $product->load(['category', 'variants']);
        $categories = Category::all();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'base_price' => 'required|numeric|min:0',
            'primary_image' => 'required|url',
            'status' => 'required|in:active,draft,archived',
            'is_featured' => 'nullable|boolean',
            'is_bestseller' => 'nullable|boolean',
        ]);

        $this->productService->updateProduct($product, $validated);

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        $name = $product->name;
        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Product "' . $name . '" has been removed.');
    }

    public function addVariant(Request $request, Product $product)
    {
        $validated = $request->validate([
            'size' => 'required|string|max:10',
            'color' => 'required|string|max:50',
            'color_hex' => 'required|string|max:10',
            'sku' => 'nullable|string|max:50|unique:product_variants,sku',
            'stock_quantity' => 'required|integer|min:0',
            'price_adjustment' => 'nullable|numeric',
        ]);

        if (empty($validated['sku'])) {
            $colorPrefix = strtoupper(substr(str_replace(' ', '', $validated['color']), 0, 3));
            $validated['sku'] = 'ELE-' . strtoupper(Str::random(3)) . '-' . $colorPrefix . '-SZ' . $validated['size'];
        }
        $validated['price_adjustment'] = (float) ($validated['price_adjustment'] ?? 0);

        $product->variants()->create($validated);

        return redirect()->route('admin.products.edit', $product->id)
            ->with('success', 'New variant (Size ' . $validated['size'] . ' • ' . $validated['color'] . ') added successfully.');
    }

    public function updateVariant(Request $request, ProductVariant $variant)
    {
        $validated = $request->validate([
            'stock_quantity' => 'required|integer|min:0',
            'price_adjustment' => 'required|numeric',
            'sku' => 'required|string|max:50|unique:product_variants,sku,' . $variant->id,
        ]);

        $variant->update($validated);

        return redirect()->route('admin.products.edit', $variant->product_id)
            ->with('success', 'Variant SKU ' . $variant->sku . ' updated.');
    }

    public function deleteVariant(ProductVariant $variant)
    {
        $productId = $variant->product_id;
        $sku = $variant->sku;
        $variant->delete();

        return redirect()->route('admin.products.edit', $productId)
            ->with('success', 'Variant ' . $sku . ' removed.');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\InventoryRepositoryInterface;
use App\Services\InventoryService;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function __construct(
        protected InventoryRepositoryInterface $inventoryRepo,
        protected InventoryService $inventoryService
    ) {}

    public function index(Request $request)
    {
        $filters = $request->only(['search', 'stock_status']);
        $variants = $this->inventoryRepo->getVariantsPaginated($filters, 20)->withQueryString();
        $lowStockCount = $this->inventoryRepo->getLowStockVariants(5)->count();

        return view('admin.inventory.index', compact('variants', 'lowStockCount'));
    }

    public function updateStock(Request $request)
    {
        $validated = $request->validate([
            'variant_id' => 'required|exists:product_variants,id',
            'stock_quantity' => 'required|integer|min:0',
        ]);

        $this->inventoryService->updateVariantStock((int) $validated['variant_id'], (int) $validated['stock_quantity']);

        return back()->with('success', 'Variant stock updated successfully.');
    }
}

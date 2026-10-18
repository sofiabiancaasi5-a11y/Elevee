<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardReportService
{
    public function getDashboardMetrics(): array
    {
        $totalSales = (float) Order::where('status', '!=', 'cancelled')->sum('total_amount');
        $totalOrders = Order::count();
        $pendingOrders = Order::where('status', 'pending')->count();
        $deliveredOrders = Order::where('status', 'delivered')->count();
        $totalProducts = Product::where('status', 'active')->count();
        $totalCustomers = User::where('role', 'customer')->count();
        $lowStockCount = ProductVariant::where('stock_quantity', '<=', 5)->count();

        // Recent orders
        $recentOrders = Order::with(['user', 'items', 'payment'])
            ->latest()
            ->take(6)
            ->get();

        // Status counts
        $statusCounts = [
            'pending' => Order::where('status', 'pending')->count(),
            'confirmed' => Order::where('status', 'confirmed')->count(),
            'processing' => Order::where('status', 'processing')->count(),
            'shipped' => Order::where('status', 'shipped')->count(),
            'delivered' => $deliveredOrders,
            'cancelled' => Order::where('status', 'cancelled')->count(),
        ];

        // Sales by Category
        $salesByCategory = DB::table('order_items')
            ->join('product_variants', 'order_items.product_variant_id', '=', 'product_variants.id')
            ->join('products', 'product_variants.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', '!=', 'cancelled')
            ->select('categories.name as category_name', DB::raw('SUM(order_items.total_price) as revenue'), DB::raw('SUM(order_items.quantity) as items_sold'))
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('revenue')
            ->get();

        // Top 5 Best Selling Shoes
        $topProducts = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', '!=', 'cancelled')
            ->select(
                'order_items.product_name',
                'order_items.image_url',
                DB::raw('SUM(order_items.quantity) as total_sold'),
                DB::raw('SUM(order_items.total_price) as total_revenue')
            )
            ->groupBy('order_items.product_name', 'order_items.image_url')
            ->orderByDesc('total_sold')
            ->take(5)
            ->get();

        return [
            'total_sales' => $totalSales,
            'total_orders' => $totalOrders,
            'pending_orders' => $pendingOrders,
            'delivered_orders' => $deliveredOrders,
            'total_products' => $totalProducts,
            'total_customers' => $totalCustomers,
            'low_stock_count' => $lowStockCount,
            'recent_orders' => $recentOrders,
            'status_counts' => $statusCounts,
            'sales_by_category' => $salesByCategory,
            'top_products' => $topProducts,
        ];
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Services\OrderService;
use Exception;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(
        protected OrderRepositoryInterface $orderRepo,
        protected OrderService $orderService
    ) {}

    public function index(Request $request)
    {
        $status = $request->input('status', 'all');
        $search = $request->input('search');

        $orders = $this->orderRepo->getFilteredPaginated([
            'status' => $status,
            'search' => $search,
        ], 15)->withQueryString();

        $statusCounts = [
            'all' => Order::count(),
            'pending' => Order::where('status', 'pending')->count(),
            'confirmed' => Order::where('status', 'confirmed')->count(),
            'processing' => Order::where('status', 'processing')->count(),
            'shipped' => Order::where('status', 'shipped')->count(),
            'delivered' => Order::where('status', 'delivered')->count(),
            'cancelled' => Order::where('status', 'cancelled')->count(),
        ];

        return view('admin.orders.index', compact('orders', 'statusCounts', 'status', 'search'));
    }

    public function show(int $id)
    {
        $order = $this->orderRepo->findById($id);
        if (!$order) {
            return redirect()->route('admin.orders.index')->with('error', 'Order not found.');
        }

        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, int $id)
    {
        $order = $this->orderRepo->findById($id);
        if (!$order) {
            return redirect()->route('admin.orders.index')->with('error', 'Order not found.');
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,processing,shipped,delivered,cancelled',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($validated['status'] === 'cancelled' && $order->status !== 'cancelled') {
            try {
                $this->orderService->cancelOrder($order, $validated['notes'] ?? 'Cancelled by Admin');
                return back()->with('success', 'Order cancelled and stock restored to inventory.');
            } catch (Exception $e) {
                return back()->with('error', $e->getMessage());
            }
        }

        $order->status = $validated['status'];
        if (!empty($validated['notes'])) {
            $order->notes = ($order->notes ? $order->notes . "\n" : '') . '[' . now()->format('Y-m-d H:i') . '] ' . $validated['notes'];
        }
        $order->save();

        return back()->with('success', 'Order status updated to ' . ucfirst($validated['status']) . '.');
    }

    public function updateAddress(Request $request, int $id)
    {
        $order = $this->orderRepo->findById($id);
        if (!$order) {
            return redirect()->route('admin.orders.index')->with('error', 'Order not found.');
        }

        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:30',
            'street_address' => 'required|string|max:255',
            'apartment' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'province_state' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
        ]);

        $order->customer_name = $validated['customer_name'];
        $order->customer_email = $validated['customer_email'];
        $order->customer_phone = $validated['customer_phone'];

        $snapshot = $order->shipping_address_snapshot ?: [];
        $snapshot['recipient_name'] = $validated['customer_name'];
        $snapshot['phone_number'] = $validated['customer_phone'];
        $snapshot['street_address'] = $validated['street_address'];
        $snapshot['apartment'] = $validated['apartment'] ?? null;
        $snapshot['city'] = $validated['city'];
        $snapshot['province_state'] = $validated['province_state'];
        $snapshot['postal_code'] = $validated['postal_code'];
        $snapshot['country'] = 'Philippines';

        $order->shipping_address_snapshot = $snapshot;
        $order->save();

        return back()->with('success', 'Shipping destination updated successfully.');
    }

    public function packingSlip(int $id)
    {
        $order = $this->orderRepo->findById($id);
        if (!$order) {
            return redirect()->route('admin.orders.index')->with('error', 'Order not found.');
        }

        return view('admin.orders.packing-slip', compact('order'));
    }
}

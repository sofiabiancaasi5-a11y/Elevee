<?php

namespace App\Repositories\Eloquent;

use App\Models\Order;
use App\Repositories\Contracts\OrderRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class OrderRepository implements OrderRepositoryInterface
{
    public function getFilteredPaginated(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = Order::with(['user', 'items', 'payment'])
            ->latest();

        if (!empty($filters['status']) && $filters['status'] !== 'all') {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['search'])) {
            $term = '%' . trim($filters['search']) . '%';
            $query->where(function ($q) use ($term) {
                $q->where('order_number', 'like', $term)
                  ->orWhere('customer_name', 'like', $term)
                  ->orWhere('customer_email', 'like', $term);
            });
        }

        return $query->paginate($perPage);
    }

    public function getByUserId(int $userId, int $perPage = 10): LengthAwarePaginator
    {
        return Order::with(['items', 'payment'])
            ->where('user_id', $userId)
            ->latest()
            ->paginate($perPage);
    }

    public function findByOrderNumber(string $orderNumber): ?Order
    {
        return Order::with(['items', 'payment', 'user'])->where('order_number', $orderNumber)->first();
    }

    public function findById(int $id): ?Order
    {
        return Order::with(['items', 'payment', 'user'])->find($id);
    }

    public function updateStatus(Order $order, string $status): bool
    {
        return $order->update(['status' => $status]);
    }

    public function getRecent(int $limit = 5): Collection
    {
        return Order::with(['user', 'items', 'payment'])
            ->latest()
            ->take($limit)
            ->get();
    }
}

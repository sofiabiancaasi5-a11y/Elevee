<?php

namespace App\Repositories\Contracts;

use App\Models\Order;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface OrderRepositoryInterface
{
    public function getFilteredPaginated(array $filters, int $perPage = 15): LengthAwarePaginator;

    public function getByUserId(int $userId, int $perPage = 10): LengthAwarePaginator;

    public function findByOrderNumber(string $orderNumber): ?Order;

    public function findById(int $id): ?Order;

    public function updateStatus(Order $order, string $status): bool;

    public function getRecent(int $limit = 5): Collection;
}

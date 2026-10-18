<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class UserRepository implements UserRepositoryInterface
{
    public function getCustomersPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return User::where('role', 'customer')
            ->with(['addresses', 'orders'])
            ->withCount('orders')
            ->latest()
            ->paginate($perPage);
    }

    public function findById(int $id): ?User
    {
        return User::with(['addresses', 'orders.items', 'wishlists.product'])->find($id);
    }

    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }
}

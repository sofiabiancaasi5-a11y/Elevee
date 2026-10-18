<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\OrderCreateRequest;
use App\Http\Resources\OrderResource;
use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Services\OrderAutoProgressionService;
use App\Services\OrderService;
use App\Traits\ApiResponse;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected OrderService $orderService,
        protected OrderRepositoryInterface $orderRepo,
        protected OrderAutoProgressionService $progressionService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return $this->errorResponse('Unauthorized', 401);
        }

        $paginator = $this->orderRepo->getByUserId($user->id, 15);

        return $this->successResponse(
            OrderResource::collection($paginator->items()),
            'Order history retrieved',
            200,
            [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'total' => $paginator->total(),
            ]
        );
    }

    public function store(OrderCreateRequest $request): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return $this->errorResponse('Authentication required. Please sign in to place an order.', 401);
        }

        try {
            $order = $this->orderService->placeOrder($user, $request->validated());
            return $this->successResponse(new OrderResource($order), 'Order placed successfully', 201);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 422);
        }
    }

    public function show(Request $request, string $orderNumber): JsonResponse
    {
        $order = $this->orderRepo->findByOrderNumber($orderNumber);

        if (!$order) {
            return $this->errorResponse('Order not found', 404);
        }

        // Ensure user can only view their own order (or admin)
        $user = $request->user();
        if ($user && $order->user_id && $order->user_id !== $user->id && !$user->isAdmin()) {
            return $this->errorResponse('Unauthorized to view this order', 403);
        }

        return $this->successResponse(new OrderResource($order), 'Order details retrieved');
    }

    public function cancel(Request $request, string $orderNumber): JsonResponse
    {
        $order = $this->orderRepo->findByOrderNumber($orderNumber);

        if (!$order) {
            return $this->errorResponse('Order not found', 404);
        }

        $user = $request->user();
        if ($user && $order->user_id && $order->user_id !== $user->id && !$user->isAdmin()) {
            return $this->errorResponse('Unauthorized', 403);
        }

        try {
            $cancelledOrder = $this->orderService->cancelOrder($order, $request->input('reason', 'Customer requested cancellation'));
            return $this->successResponse(new OrderResource($cancelledOrder), 'Order cancelled successfully');
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 422);
        }
    }

    /**
     * Advance order to next status in 3-second progression pipeline.
     */
    public function autoProgress(Request $request, string $orderNumber): JsonResponse
    {
        $order = $this->orderRepo->findByOrderNumber($orderNumber);

        if (!$order) {
            return $this->errorResponse('Order not found', 404);
        }

        try {
            $updated = $this->progressionService->advanceOrder($order);
            return $this->successResponse(new OrderResource($updated), 'Order progressed to ' . $updated->status);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 422);
        }
    }
}

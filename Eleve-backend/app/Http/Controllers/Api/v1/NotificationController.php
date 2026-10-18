<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Order;
use App\Services\OrderAutoProgressionService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    use ApiResponse;

    protected OrderAutoProgressionService $progressionService;

    public function __construct(OrderAutoProgressionService $progressionService)
    {
        $this->progressionService = $progressionService;
    }

    /**
     * Get user's notifications list with unread counter.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $query = Notification::query()
            ->when($user, function ($q) use ($user) {
                $q->where(function ($sub) use ($user) {
                    $sub->where('user_id', $user->id)
                        ->orWhereNull('user_id'); // Global announcements
                });
            })
            ->latest();

        $unreadCount = (clone $query)->where('is_read', false)->count();
        $notifications = $query->paginate(20);

        return $this->successResponse([
            'unread_count' => $unreadCount,
            'notifications' => $notifications->items(),
            'pagination' => [
                'current_page' => $notifications->currentPage(),
                'last_page' => $notifications->lastPage(),
                'total' => $notifications->total(),
            ],
        ], 'Notifications retrieved');
    }

    /**
     * Lightweight delta polling endpoint for real-time app and admin sync.
     * Also automatically runs the 3-second progression pipeline on active orders.
     */
    public function poll(Request $request): JsonResponse
    {
        // Advance any pending 3-second orders across the system
        $this->progressionService->processPendingProgressions(3);

        $user = $request->user();
        $lastId = (int) $request->query('last_id', 0);
        $orderNumber = $request->query('order_number');

        // New notifications since last check
        $query = Notification::query()
            ->where('id', '>', $lastId)
            ->when($user, function ($q) use ($user) {
                $q->where(function ($sub) use ($user) {
                    $sub->where('user_id', $user->id)
                        ->orWhereNull('user_id');
                });
            })
            ->latest();

        $newNotifications = $query->get();

        $unreadCount = Notification::query()
            ->when($user, function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->where('is_read', false)
            ->count();

        // If polling for a specific order (e.g. while customer is on the Order Tracking screen)
        $orderData = null;
        if ($orderNumber) {
            $order = Order::where('order_number', $orderNumber)->first();
            if ($order) {
                $orderData = [
                    'order_number' => $order->order_number,
                    'status' => $order->status,
                    'updated_at' => $order->updated_at->toISOString(),
                ];
            }
        }

        return $this->successResponse([
            'new_notifications' => $newNotifications,
            'unread_count' => $unreadCount,
            'tracked_order' => $orderData,
            'timestamp' => now()->timestamp,
        ], 'Poll sync successful');
    }

    /**
     * Mark a single notification as read.
     */
    public function markAsRead(Request $request, $id): JsonResponse
    {
        $notification = Notification::findOrFail($id);

        if ($request->user() && $notification->user_id && $notification->user_id !== $request->user()->id) {
            return $this->errorResponse('Unauthorized', 403);
        }

        $notification->markAsRead();

        return $this->successResponse(null, 'Notification marked as read');
    }

    /**
     * Mark all notifications as read for current user.
     */
    public function markAllAsRead(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user) {
            Notification::where('user_id', $user->id)
                ->where('is_read', false)
                ->update([
                    'is_read' => true,
                    'read_at' => now(),
                ]);
        } else {
            Notification::whereNull('user_id')
                ->where('is_read', false)
                ->update([
                    'is_read' => true,
                    'read_at' => now(),
                ]);
        }

        return $this->successResponse(null, 'All notifications marked as read');
    }

    /**
     * Dismiss/delete a single notification.
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        $notification = Notification::findOrFail($id);
        $user = $request->user();

        if ($user && $notification->user_id && $notification->user_id !== $user->id) {
            return $this->errorResponse('Unauthorized', 403);
        }

        $notification->delete();

        return $this->successResponse(null, 'Notification dismissed successfully');
    }

    /**
     * Clear all notifications for user or current session.
     */
    public function clear(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user) {
            Notification::where('user_id', $user->id)->delete();
        } else {
            Notification::whereNull('user_id')->delete();
        }

        return $this->successResponse(null, 'All notifications cleared successfully');
    }
}

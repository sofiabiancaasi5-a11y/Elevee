<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Order;
use App\Services\OrderAutoProgressionService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationFeedController extends Controller
{
    public function __construct(
        protected OrderAutoProgressionService $progressionService
    ) {}

    /**
     * Dedicated administrative real-time feed endpoint.
     * Advances 3-second progressions and returns enriched notifications.
     */
    public function feed(Request $request): JsonResponse
    {
        // 1. Advance any pending 3-second order progressions across the boutique
        $this->progressionService->processPendingProgressions(3);

        $lastId = (int) $request->query('last_id', 0);
        $unreadOnly = $request->boolean('unread_only', false);
        $limit = min((int) $request->query('limit', 15), 50);

        // 2. Query notifications for admin feed
        $query = Notification::query()->latest();

        if ($unreadOnly) {
            $query->where('is_read', false);
        }

        $notifications = $query->take($limit)->get();

        $unreadCount = Notification::where('is_read', false)->count();
        $latestId = Notification::max('id') ?? 0;

        // Check for new notifications if lastId was provided
        $hasNew = $lastId > 0 && Notification::where('id', '>', $lastId)->exists();
        $newItems = $lastId > 0 
            ? Notification::where('id', '>', $lastId)->latest()->get()->map(fn($n) => $this->enrichNotification($n))
            : [];

        $formatted = $notifications->map(fn($n) => $this->enrichNotification($n));

        return response()->json([
            'success' => true,
            'notifications' => $formatted,
            'new_items' => $newItems,
            'has_new' => $hasNew,
            'unread_count' => $unreadCount,
            'latest_id' => $latestId,
            'timestamp' => now()->timestamp,
        ]);
    }

    /**
     * Mark all notifications as read across the admin feed.
     */
    public function markAllRead(Request $request): JsonResponse
    {
        Notification::where('is_read', false)->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'All atelier notifications marked as read.',
            'unread_count' => 0,
        ]);
    }

    /**
     * Mark a single notification as read.
     */
    public function markRead(Request $request, int $id): JsonResponse
    {
        $notification = Notification::findOrFail($id);
        $notification->markAsRead();

        $unreadCount = Notification::where('is_read', false)->count();

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read.',
            'unread_count' => $unreadCount,
        ]);
    }

    /**
     * Dismiss / delete an individual notification.
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $notification = Notification::findOrFail($id);
        $notification->delete();

        $unreadCount = Notification::where('is_read', false)->count();

        return response()->json([
            'success' => true,
            'message' => 'Notification dismissed.',
            'unread_count' => $unreadCount,
        ]);
    }

    /**
     * Clear all read notifications to keep the feed pristine.
     */
    public function clearRead(Request $request): JsonResponse
    {
        $deleted = Notification::where('is_read', true)->delete();

        return response()->json([
            'success' => true,
            'message' => "Cleared {$deleted} read notification(s).",
            'unread_count' => Notification::where('is_read', false)->count(),
        ]);
    }

    /**
     * Full-page Activity Feed & Audit Log.
     */
    public function index(Request $request)
    {
        $query = Notification::latest();

        if ($request->filled('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            if ($request->status === 'unread') {
                $query->where('is_read', false);
            } elseif ($request->status === 'read') {
                $query->where('is_read', true);
            }
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%")
                  ->orWhere('data', 'like', "%{$search}%");
            });
        }

        $notifications = $query->paginate(20)->withQueryString();

        // Key metrics
        $totalCount = Notification::count();
        $unreadCount = Notification::where('is_read', false)->count();
        $todayCount = Notification::whereDate('created_at', today())->count();

        return view('admin.notifications.index', compact(
            'notifications',
            'totalCount',
            'unreadCount',
            'todayCount'
        ));
    }

    /**
     * Helper to enrich notification data with URLs, styling, and relative time.
     */
    protected function enrichNotification(Notification $n): array
    {
        $data = $n->data ?? [];
        $orderId = $data['order_id'] ?? null;
        $orderNumber = $data['order_number'] ?? null;
        $status = $data['status'] ?? null;
        $amount = $data['amount'] ?? null;

        // If order_id isn't directly in data but order_number is, find the order
        if (!$orderId && $orderNumber) {
            $order = Order::where('order_number', $orderNumber)->first();
            if ($order) {
                $orderId = $order->id;
            }
        }

        $orderUrl = $orderId ? route('admin.orders.show', $orderId) : null;

        // Style matching by event type / order status
        $meta = $this->resolveStatusMeta($status, $n->type, $n->title);

        return [
            'id' => $n->id,
            'title' => $n->title,
            'message' => $n->message,
            'type' => $n->type,
            'is_read' => (bool) $n->is_read,
            'created_at' => $n->created_at->toISOString(),
            'formatted_time' => $n->created_at->format('g:i:s A'),
            'relative_time' => $this->formatRelativeTime($n->created_at),
            'order_id' => $orderId,
            'order_number' => $orderNumber,
            'order_url' => $orderUrl,
            'formatted_amount' => $amount ? '₱' . number_format((float) $amount, 2) : null,
            'status' => $status,
            'icon' => $meta['icon'],
            'badge_bg' => $meta['bg'],
            'badge_text' => $meta['text'],
            'badge_border' => $meta['border'],
        ];
    }

    protected function resolveStatusMeta(?string $status, string $type, string $title): array
    {
        $lowerTitle = strtolower($title);

        if ($status === 'delivered' || str_contains($lowerTitle, 'delivered')) {
            return [
                'icon' => 'check-circle',
                'bg' => 'bg-emerald-50',
                'text' => 'text-emerald-700',
                'border' => 'border-emerald-200',
            ];
        }

        if ($status === 'shipped' || str_contains($lowerTitle, 'transit') || str_contains($lowerTitle, 'dispatched')) {
            return [
                'icon' => 'truck',
                'bg' => 'bg-sky-50',
                'text' => 'text-sky-700',
                'border' => 'border-sky-200',
            ];
        }

        if ($status === 'processing' || str_contains($lowerTitle, 'crafting') || str_contains($lowerTitle, 'artisans')) {
            return [
                'icon' => 'sparkles',
                'bg' => 'bg-purple-50',
                'text' => 'text-purple-700',
                'border' => 'border-purple-200',
            ];
        }

        if ($status === 'confirmed' || str_contains($lowerTitle, 'confirmed')) {
            return [
                'icon' => 'shield-check',
                'bg' => 'bg-amber-50',
                'text' => 'text-amber-800',
                'border' => 'border-amber-200',
            ];
        }

        if ($type === 'order_placed' || str_contains($lowerTitle, 'placed') || str_contains($lowerTitle, 'new order')) {
            return [
                'icon' => 'shopping-bag',
                'bg' => 'bg-stone-100',
                'text' => 'text-stone-900',
                'border' => 'border-stone-300',
            ];
        }

        return [
            'icon' => 'bell',
            'bg' => 'bg-stone-50',
            'text' => 'text-stone-700',
            'border' => 'border-stone-200',
        ];
    }

    protected function formatRelativeTime(Carbon $date): string
    {
        $diffSeconds = $date->diffInSeconds(now());

        if ($diffSeconds < 10) {
            return 'Just now';
        }
        if ($diffSeconds < 60) {
            return "{$diffSeconds}s ago";
        }
        if ($diffSeconds < 3600) {
            $mins = floor($diffSeconds / 60);
            return "{$mins}m ago";
        }
        if ($diffSeconds < 86400) {
            $hours = floor($diffSeconds / 3600);
            return "{$hours}h ago";
        }

        return $date->diffForHumans();
    }
}

@extends('layouts.admin')

@section('title', 'Atelier Activity Feed & Audit Log')
@section('header_title', 'Activity Feed & Audit Log')

@section('content')
<div class="space-y-6">

    <!-- Top Metrics Overview -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-5 rounded-xl border border-[#D8D0C4]/70 shadow-sm flex items-center space-x-4">
            <div class="w-12 h-12 rounded-xl bg-stone-100 flex items-center justify-center text-stone-800">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                </svg>
            </div>
            <div>
                <p class="text-xs text-stone-400 uppercase tracking-wider font-semibold">Total Logged Activity</p>
                <h3 class="text-2xl font-bold text-stone-900">{{ number_format($totalCount) }}</h3>
            </div>
        </div>

        <div class="bg-white p-5 rounded-xl border border-[#D8D0C4]/70 shadow-sm flex items-center space-x-4">
            <div class="w-12 h-12 rounded-xl bg-amber-50 border border-amber-200 flex items-center justify-center text-amber-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
            <div>
                <p class="text-xs text-stone-400 uppercase tracking-wider font-semibold">Unread Notifications</p>
                <h3 class="text-2xl font-bold text-stone-900">{{ number_format($unreadCount) }}</h3>
            </div>
        </div>

        <div class="bg-white p-5 rounded-xl border border-[#D8D0C4]/70 shadow-sm flex items-center space-x-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center justify-center text-emerald-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
            </div>
            <div>
                <p class="text-xs text-stone-400 uppercase tracking-wider font-semibold">Today's Events</p>
                <h3 class="text-2xl font-bold text-stone-900">{{ number_format($todayCount) }}</h3>
            </div>
        </div>
    </div>

    <!-- Filter & Action Bar -->
    <div class="bg-white p-5 rounded-xl border border-[#D8D0C4]/70 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <form action="{{ route('admin.notifications.index') }}" method="GET" class="flex flex-wrap items-center gap-3 flex-1">
            <div class="relative min-w-[240px]">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by order #, title, or keyword..."
                       class="w-full pl-9 pr-4 py-2 bg-stone-50 border border-stone-200 rounded-lg text-xs text-stone-800 placeholder-stone-400 focus:outline-none focus:border-stone-500">
                <svg class="w-4 h-4 text-stone-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>

            <select name="type" class="py-2 px-3 bg-stone-50 border border-stone-200 rounded-lg text-xs text-stone-700 focus:outline-none focus:border-stone-500">
                <option value="all">All Event Types</option>
                <option value="order_status" {{ request('type') === 'order_status' ? 'selected' : '' }}>Order Status Transitions</option>
                <option value="order_placed" {{ request('type') === 'order_placed' ? 'selected' : '' }}>New Orders Placed</option>
                <option value="inventory_alert" {{ request('type') === 'inventory_alert' ? 'selected' : '' }}>Inventory Alerts</option>
            </select>

            <select name="status" class="py-2 px-3 bg-stone-50 border border-stone-200 rounded-lg text-xs text-stone-700 focus:outline-none focus:border-stone-500">
                <option value="">All Statuses</option>
                <option value="unread" {{ request('status') === 'unread' ? 'selected' : '' }}>Unread Only</option>
                <option value="read" {{ request('status') === 'read' ? 'selected' : '' }}>Read Only</option>
            </select>

            <button type="submit" class="px-4 py-2 bg-stone-900 text-white rounded-lg text-xs font-medium uppercase tracking-wider hover:bg-stone-800 transition-colors">
                Filter
            </button>
            @if(request()->hasAny(['search', 'type', 'status']))
                <a href="{{ route('admin.notifications.index') }}" class="text-xs text-stone-500 hover:text-stone-900 underline self-center">Clear</a>
            @endif
        </form>

        <div class="flex items-center space-x-2">
            <button onclick="markAllAdminRead(event)" class="px-3.5 py-2 bg-stone-100 hover:bg-stone-200 text-stone-800 rounded-lg text-xs font-medium transition-colors">
                Mark All Read
            </button>
            <button onclick="clearAllReadAdmin(event)" class="px-3.5 py-2 border border-stone-200 text-stone-600 hover:bg-stone-50 rounded-lg text-xs font-medium transition-colors">
                Clear Read
            </button>
        </div>
    </div>

    <!-- Activity Log Feed Table -->
    <div class="bg-white rounded-xl border border-[#D8D0C4]/70 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-stone-50 text-[11px] uppercase tracking-wider text-stone-500 border-b border-stone-200">
                    <tr>
                        <th class="px-5 py-3.5 font-medium">Event & Status</th>
                        <th class="px-5 py-3.5 font-medium">Activity Details</th>
                        <th class="px-5 py-3.5 font-medium">Order Reference</th>
                        <th class="px-5 py-3.5 font-medium">Amount</th>
                        <th class="px-5 py-3.5 font-medium">Timestamp</th>
                        <th class="px-5 py-3.5 font-medium text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse($notifications as $notif)
                        @php
                            $data = $notif->data ?? [];
                            $orderId = $data['order_id'] ?? null;
                            $orderNumber = $data['order_number'] ?? null;
                            $amount = $data['amount'] ?? null;
                            $isUnread = !$notif->is_read;

                            $lowerTitle = strtolower($notif->title);
                            $badgeColor = 'bg-stone-100 text-stone-800 border-stone-200';
                            if (str_contains($lowerTitle, 'delivered')) {
                                $badgeColor = 'bg-emerald-50 text-emerald-800 border-emerald-200';
                            } elseif (str_contains($lowerTitle, 'transit') || str_contains($lowerTitle, 'dispatched')) {
                                $badgeColor = 'bg-sky-50 text-sky-800 border-sky-200';
                            } elseif (str_contains($lowerTitle, 'crafting') || str_contains($lowerTitle, 'artisans')) {
                                $badgeColor = 'bg-purple-50 text-purple-800 border-purple-200';
                            } elseif (str_contains($lowerTitle, 'confirmed')) {
                                $badgeColor = 'bg-amber-50 text-amber-800 border-amber-200';
                            }
                        @endphp
                        <tr class="hover:bg-stone-50/70 transition-colors {{ $isUnread ? 'bg-amber-50/20' : '' }}">
                            <!-- Event Badge -->
                            <td class="px-5 py-4">
                                <div class="flex items-center space-x-2.5">
                                    @if($isUnread)
                                        <span class="w-2 h-2 rounded-full bg-[#C5A880] flex-shrink-0" title="Unread"></span>
                                    @else
                                        <span class="w-2 h-2 rounded-full bg-stone-300 flex-shrink-0 opacity-40"></span>
                                    @endif
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold border {{ $badgeColor }}">
                                        {{ $notif->type === 'order_placed' ? 'New Order' : 'Progression' }}
                                    </span>
                                </div>
                            </td>

                            <!-- Message Details -->
                            <td class="px-5 py-4">
                                <div class="font-semibold text-stone-900 text-xs">{{ $notif->title }}</div>
                                <div class="text-[11px] text-stone-500 mt-0.5 max-w-md">{{ $notif->message }}</div>
                            </td>

                            <!-- Order Reference -->
                            <td class="px-5 py-4">
                                @if($orderId)
                                    <a href="{{ route('admin.orders.show', $orderId) }}" class="font-mono text-xs font-semibold text-stone-900 hover:text-[#A67C52] hover:underline flex items-center">
                                        {{ $orderNumber ?: 'Order #' . $orderId }}
                                        <svg class="w-3 h-3 ml-1 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                    </a>
                                @elseif($orderNumber)
                                    <span class="font-mono text-xs text-stone-700">{{ $orderNumber }}</span>
                                @else
                                    <span class="text-xs text-stone-400">Atelier System</span>
                                @endif
                            </td>

                            <!-- Amount in PHP -->
                            <td class="px-5 py-4 text-xs font-bold text-stone-900">
                                {{ $amount ? '₱' . number_format((float) $amount, 2) : '—' }}
                            </td>

                            <!-- Timestamp -->
                            <td class="px-5 py-4 text-xs text-stone-500">
                                <div>{{ $notif->created_at->format('M d, Y') }}</div>
                                <div class="text-[10px] text-stone-400">{{ $notif->created_at->format('g:i:s A') }} ({{ $notif->created_at->diffForHumans() }})</div>
                            </td>

                            <!-- Actions -->
                            <td class="px-5 py-4 text-right text-xs space-x-2">
                                @if($orderId)
                                    <a href="{{ route('admin.orders.show', $orderId) }}" class="px-2.5 py-1.5 bg-stone-100 hover:bg-stone-200 text-stone-800 rounded font-medium transition-colors">
                                        View Order
                                    </a>
                                @endif

                                @if($isUnread)
                                    <button onclick="markSingleAdminRead(event, {{ $notif->id }})" class="px-2.5 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200 rounded font-medium transition-colors" title="Mark Read">
                                        ✓ Mark Read
                                    </button>
                                @endif

                                <button onclick="dismissAdminNotif(event, {{ $notif->id }})" class="px-2 py-1.5 text-stone-400 hover:text-rose-600 rounded transition-colors" title="Dismiss">
                                    ✕
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center text-stone-400 text-xs">
                                No activity log records match your filter criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($notifications->hasPages())
            <div class="px-5 py-3.5 border-t border-stone-200 bg-stone-50/50">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>

</div>
@endsection

@extends('layouts.admin')

@section('title', 'Order ' . $order->order_number)
@section('header_title', 'Order ' . $order->order_number)

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <a href="{{ route('admin.orders.index') }}" class="inline-flex items-center text-xs text-stone-600 hover:text-stone-900 font-medium">
            &larr; Back to Client Orders
        </a>

        @php
            $badgeStyles = match($order->status) {
                'pending' => 'bg-amber-100 text-amber-800 border-amber-300',
                'confirmed' => 'bg-blue-100 text-blue-800 border-blue-300',
                'processing' => 'bg-purple-100 text-purple-800 border-purple-300',
                'shipped' => 'bg-indigo-100 text-indigo-800 border-indigo-300',
                'delivered' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
                'cancelled' => 'bg-rose-100 text-rose-800 border-rose-300',
                default => 'bg-stone-100 text-stone-800'
            };
        @endphp
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.orders.packing-slip', $order->id) }}" target="_blank" class="inline-flex items-center px-3 py-1 bg-stone-100 hover:bg-stone-200 text-stone-800 rounded-lg text-xs font-semibold uppercase tracking-wider transition-colors border border-stone-300">
                <svg class="w-3.5 h-3.5 mr-1 text-stone-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                Print Packing Slip
            </a>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider border {{ $badgeStyles }}">
                Current Status: {{ $order->status }}
            </span>
        </div>
    </div>

    <!-- Visual Order Timeline Tracker -->
    <div class="bg-white p-6 rounded-xl border border-[#D8D0C4]/70 shadow-sm">
        <h4 class="text-xs uppercase tracking-wider text-stone-400 font-semibold mb-6">Fulfillment Milestone Pipeline</h4>
        
        @php
            $steps = ['pending' => 'Pending', 'confirmed' => 'Confirmed', 'processing' => 'Processing', 'shipped' => 'Shipped', 'delivered' => 'Delivered'];
            $statusOrder = ['pending' => 1, 'confirmed' => 2, 'processing' => 3, 'shipped' => 4, 'delivered' => 5, 'cancelled' => 0];
            $currentStepIndex = $statusOrder[$order->status] ?? 1;
        @endphp

        @if($order->status === 'cancelled')
            <div class="p-4 bg-rose-50 border border-rose-200 rounded-lg text-rose-800 text-xs flex items-center space-x-3">
                <svg class="w-5 h-5 text-rose-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>This order was cancelled. Reserved footwear items were automatically returned to inventory.</span>
            </div>
        @else
            <div class="grid grid-cols-5 gap-2 relative">
                @php $stepNum = 1; @endphp
                @foreach($steps as $key => $title)
                    @php 
                        $isCompleted = $currentStepIndex >= $stepNum;
                        $isCurrent = $currentStepIndex === $stepNum;
                    @endphp
                    <div class="text-center relative">
                        <div class="w-9 h-9 mx-auto rounded-full flex items-center justify-center text-xs font-bold transition-all
                            {{ $isCurrent ? 'bg-stone-900 text-white ring-4 ring-stone-200 shadow-md' : ($isCompleted ? 'bg-emerald-600 text-white' : 'bg-stone-100 text-stone-400 border border-stone-200') }}">
                            @if($isCompleted && !$isCurrent)
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                </svg>
                            @else
                                {{ $stepNum }}
                            @endif
                        </div>
                        <p class="text-[11px] font-semibold mt-2 {{ $isCurrent ? 'text-stone-900 font-bold' : ($isCompleted ? 'text-emerald-700' : 'text-stone-400') }}">
                            {{ $title }}
                        </p>
                    </div>
                    @php $stepNum++; @endphp
                @endforeach
            </div>
        @endif
    </div>

    <!-- 2 Column Order Details -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <!-- Ordered Items (2 cols) -->
        <div class="md:col-span-2 space-y-6">
            <div class="bg-white rounded-xl border border-[#D8D0C4]/70 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-[#D8D0C4]/60">
                    <h3 class="font-luxury-serif text-lg font-semibold text-stone-900">Footwear Items Ordered</h3>
                </div>

                <div class="divide-y divide-stone-100 p-5 space-y-4">
                    @foreach($order->items as $item)
                    <div class="flex items-center space-x-4 pt-4 first:pt-0">
                        <img src="{{ $item->image_url }}" alt="{{ $item->product_name }}" 
                             class="w-16 h-16 rounded-lg object-cover border border-stone-200 shadow-sm flex-shrink-0">
                        <div class="flex-1 min-w-0">
                            <h4 class="font-semibold text-stone-900 text-sm">{{ $item->product_name }}</h4>
                            <div class="flex flex-wrap items-center gap-2 mt-1 text-xs text-stone-600">
                                <span class="px-2 py-0.5 rounded bg-stone-100 border border-stone-200 font-medium">Size: US {{ $item->size }}</span>
                                <span class="px-2 py-0.5 rounded bg-stone-100 border border-stone-200 font-medium">Shade: {{ $item->color }}</span>
                                <span class="font-mono text-stone-400 text-[11px]">{{ $item->sku }}</span>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-stone-500">{{ $item->quantity }} &times; ₱{{ number_format($item->unit_price, 2) }}</p>
                            <p class="text-sm font-bold text-stone-900 mt-0.5">₱{{ number_format($item->total_price, 2) }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Financial Calculation -->
                <div class="bg-stone-50 p-5 border-t border-stone-200 space-y-2 text-xs">
                    <div class="flex justify-between text-stone-600">
                        <span>Items Subtotal</span>
                        <span>₱{{ number_format($order->subtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-stone-600">
                        <span>Complimentary White-Glove Shipping</span>
                        <span>₱{{ number_format($order->shipping_fee, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm font-bold text-stone-900 pt-2 border-t border-stone-200">
                        <span>Grand Total</span>
                        <span class="font-luxury-serif text-lg">₱{{ number_format($order->total_amount, 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- Client Notes & Instructions -->
            @if($order->notes)
            <div class="bg-white p-5 rounded-xl border border-[#D8D0C4]/70 shadow-sm">
                <h4 class="text-xs uppercase tracking-wider text-stone-500 font-semibold mb-2">Order Notes & History</h4>
                <div class="p-3 bg-stone-50 border border-stone-200 rounded-lg text-xs font-mono text-stone-700 whitespace-pre-wrap">
                    {{ $order->notes }}
                </div>
            </div>
            @endif
        </div>

        <!-- Order Control & Customer Info (1 col) -->
        <div class="space-y-6">

            <!-- Status Transition Action Card -->
            <div class="bg-white p-5 rounded-xl border border-[#D8D0C4]/70 shadow-sm space-y-4">
                <h3 class="font-luxury-serif text-base font-semibold text-stone-900 pb-2 border-b border-stone-100">Update Order Status</h3>

                <form action="{{ route('admin.orders.update-status', $order->id) }}" method="POST" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-xs uppercase tracking-wider text-stone-600 font-semibold mb-1.5">Change Pipeline Stage</label>
                        <select name="status" class="w-full px-3 py-2 bg-stone-50 border border-stone-300 rounded-lg text-xs font-medium text-stone-900 focus:outline-none focus:border-stone-500">
                            <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending (Received)</option>
                            <option value="confirmed" {{ $order->status === 'confirmed' ? 'selected' : '' }}>Confirmed (Verified)</option>
                            <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Processing (Atelier Packing)</option>
                            <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>Shipped (En Route)</option>
                            <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>Delivered (Complete)</option>
                            <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled (Return Stock)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs uppercase tracking-wider text-stone-600 font-semibold mb-1.5">Fulfillment Note (Optional)</label>
                        <textarea name="notes" rows="2" placeholder="e.g. Dispatched via courier tracking #DHL-88921"
                                  class="w-full px-3 py-2 bg-stone-50 border border-stone-300 rounded-lg text-xs text-stone-900 focus:outline-none focus:border-stone-500"></textarea>
                    </div>

                    <button type="submit" class="w-full py-2.5 bg-stone-900 hover:bg-stone-800 text-white rounded-lg text-xs font-semibold uppercase tracking-wider transition-colors shadow-sm">
                        Apply Status Update
                    </button>
                </form>

                @if(!in_array($order->status, ['delivered', 'cancelled']))
                <div class="pt-3 border-t border-stone-200">
                    <button type="button" onclick="stepAutoProgression()" id="stepProgBtn" class="w-full py-2 bg-gradient-to-r from-amber-600 to-amber-700 hover:from-amber-700 hover:to-amber-800 text-white rounded-lg text-xs font-semibold tracking-wide transition-all shadow-sm flex items-center justify-center space-x-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        <span>Trigger 3s Progression Step</span>
                    </button>
                    <p class="text-[10px] text-stone-400 text-center mt-1.5">Advances pipeline to next status with real-time customer notification</p>
                </div>
                <script>
                    async function stepAutoProgression() {
                        const btn = document.getElementById('stepProgBtn');
                        btn.disabled = true;
                        btn.innerHTML = 'Progressing...';
                        try {
                            const res = await fetch('/api/v1/orders/{{ $order->order_number }}/auto-progress', { method: 'POST' });
                            const json = await res.json();
                            if (json.success) {
                                window.location.reload();
                            } else {
                                alert(json.message || 'Could not advance order');
                                btn.disabled = false;
                            }
                        } catch (e) {
                            alert('Network error advancing order');
                            btn.disabled = false;
                        }
                    }
                </script>
                @endif
            </div>

            <!-- Client & Delivery Details -->
            <div class="bg-white p-5 rounded-xl border border-[#D8D0C4]/70 shadow-sm space-y-4">
                <div class="flex items-center justify-between pb-2 border-b border-stone-100">
                    <h3 class="font-luxury-serif text-base font-semibold text-stone-900">Delivery Destination</h3>
                    <button type="button" onclick="document.getElementById('editAddressModal').classList.toggle('hidden')" class="text-xs font-semibold text-stone-800 underline hover:text-stone-950">
                        Edit Address
                    </button>
                </div>

                <div class="text-xs space-y-2 text-stone-700">
                    <div>
                        <span class="text-stone-400 block text-[10px] uppercase tracking-wider">Recipient Name</span>
                        <span class="font-bold text-stone-900 text-sm">{{ $order->customer_name }}</span>
                    </div>
                    <div>
                        <span class="text-stone-400 block text-[10px] uppercase tracking-wider">Contact Email</span>
                        <a href="mailto:{{ $order->customer_email }}" class="text-stone-800 underline">{{ $order->customer_email }}</a>
                    </div>
                    <div>
                        <span class="text-stone-400 block text-[10px] uppercase tracking-wider">Contact Phone</span>
                        <span>{{ $order->customer_phone }}</span>
                    </div>
                    <div class="pt-2 border-t border-stone-100">
                        <span class="text-stone-400 block text-[10px] uppercase tracking-wider mb-1">Shipping Address Snapshot</span>
                        @php $addr = $order->shipping_address_snapshot; @endphp
                        <p class="font-medium text-stone-800 leading-relaxed">
                            {{ $addr['apartment'] ?? '' }} {{ $addr['street_address'] ?? '' }}<br>
                            {{ $addr['city'] ?? '' }}, {{ $addr['province_state'] ?? '' }} {{ $addr['postal_code'] ?? '' }}<br>
                            {{ $addr['country'] ?? 'Philippines' }}
                        </p>
                    </div>
                </div>

                <!-- Edit Address Modal -->
                <div id="editAddressModal" class="hidden pt-3 border-t border-stone-200">
                    <form action="{{ route('admin.orders.update-address', $order->id) }}" method="POST" class="space-y-3">
                        @csrf
                        <div>
                            <label class="block text-[10px] uppercase font-bold text-stone-500 mb-0.5">Recipient Name *</label>
                            <input type="text" name="customer_name" value="{{ old('customer_name', $order->customer_name) }}" required class="w-full px-2.5 py-1.5 bg-stone-50 border border-stone-200 rounded text-xs text-stone-900">
                        </div>
                        <div>
                            <label class="block text-[10px] uppercase font-bold text-stone-500 mb-0.5">Contact Email *</label>
                            <input type="email" name="customer_email" value="{{ old('customer_email', $order->customer_email) }}" required class="w-full px-2.5 py-1.5 bg-stone-50 border border-stone-200 rounded text-xs text-stone-900">
                        </div>
                        <div>
                            <label class="block text-[10px] uppercase font-bold text-stone-500 mb-0.5">Contact Phone *</label>
                            <input type="text" name="customer_phone" value="{{ old('customer_phone', $order->customer_phone) }}" required class="w-full px-2.5 py-1.5 bg-stone-50 border border-stone-200 rounded text-xs text-stone-900">
                        </div>
                        <div>
                            <label class="block text-[10px] uppercase font-bold text-stone-500 mb-0.5">Street Address *</label>
                            <input type="text" name="street_address" value="{{ old('street_address', $addr['street_address'] ?? '') }}" required class="w-full px-2.5 py-1.5 bg-stone-50 border border-stone-200 rounded text-xs text-stone-900">
                        </div>
                        <div>
                            <label class="block text-[10px] uppercase font-bold text-stone-500 mb-0.5">Suite / Apartment</label>
                            <input type="text" name="apartment" value="{{ old('apartment', $addr['apartment'] ?? '') }}" class="w-full px-2.5 py-1.5 bg-stone-50 border border-stone-200 rounded text-xs text-stone-900">
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-[10px] uppercase font-bold text-stone-500 mb-0.5">City *</label>
                                <input type="text" name="city" value="{{ old('city', $addr['city'] ?? '') }}" required class="w-full px-2.5 py-1.5 bg-stone-50 border border-stone-200 rounded text-xs text-stone-900">
                            </div>
                            <div>
                                <label class="block text-[10px] uppercase font-bold text-stone-500 mb-0.5">Postal Code *</label>
                                <input type="text" name="postal_code" value="{{ old('postal_code', $addr['postal_code'] ?? '') }}" required class="w-full px-2.5 py-1.5 bg-stone-50 border border-stone-200 rounded text-xs text-stone-900">
                            </div>
                        </div>
                        <div>
                            <label class="block text-[10px] uppercase font-bold text-stone-500 mb-0.5">Province / State *</label>
                            <input type="text" name="province_state" value="{{ old('province_state', $addr['province_state'] ?? '') }}" required class="w-full px-2.5 py-1.5 bg-stone-50 border border-stone-200 rounded text-xs text-stone-900">
                        </div>
                        <div class="flex justify-end space-x-2 pt-1">
                            <button type="button" onclick="document.getElementById('editAddressModal').classList.add('hidden')" class="px-3 py-1 bg-stone-100 text-stone-600 rounded text-xs">Cancel</button>
                            <button type="submit" class="px-3 py-1 bg-stone-900 hover:bg-stone-800 text-white rounded text-xs font-semibold">Save Address</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Payment Details Card -->
            <div class="bg-white p-5 rounded-xl border border-[#D8D0C4]/70 shadow-sm space-y-3">
                <h3 class="font-luxury-serif text-base font-semibold text-stone-900 pb-2 border-b border-stone-100">Payment Record</h3>

                <div class="text-xs space-y-2">
                    <div class="flex justify-between">
                        <span class="text-stone-500">Method</span>
                        <span class="font-semibold uppercase text-stone-900">{{ $order->payment?->payment_method ?? 'COD' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-stone-500">Payment Status</span>
                        <span class="font-bold {{ $order->payment?->status === 'completed' ? 'text-emerald-700' : 'text-amber-700' }}">
                            {{ ucfirst($order->payment?->status ?? 'pending') }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-stone-500">Reference</span>
                        <span class="font-mono text-stone-400 text-[10px]">{{ $order->payment?->transaction_reference ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection

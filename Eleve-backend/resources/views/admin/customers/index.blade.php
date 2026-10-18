@extends('layouts.admin')

@section('title', 'Client Directory')
@section('header_title', 'Client Directory')

@section('content')
<div class="space-y-6">

    <!-- Top Action & Search Bar -->
    <div class="bg-white p-5 rounded-xl border border-[#D8D0C4]/70 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h3 class="font-luxury-serif text-lg font-semibold text-stone-900">Registered Shoppers & Clients</h3>
            <p class="text-xs text-stone-500">Clients registered through the ÉLEVÉ Mobile App & Online Boutique</p>
        </div>
        <div class="flex items-center space-x-3">
            <form action="{{ route('admin.customers.index') }}" method="GET" class="flex items-center">
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search client, email, phone..." 
                           class="w-64 pl-9 pr-3 py-2 text-xs border border-stone-200 rounded-lg focus:outline-none focus:border-stone-900 bg-stone-50/50">
                    <svg class="w-4 h-4 text-stone-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                @if(request('search'))
                    <a href="{{ route('admin.customers.index') }}" class="ml-2 text-xs text-stone-400 hover:text-stone-700 underline">Clear</a>
                @endif
            </form>
            <a href="{{ route('admin.customers.create') }}" class="inline-flex items-center px-4 py-2 bg-stone-900 text-white rounded-lg text-xs font-medium hover:bg-stone-800 transition-colors shadow-sm">
                <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add Client
            </a>
        </div>
    </div>

    <!-- Customers Table -->
    <div class="bg-white rounded-xl border border-[#D8D0C4]/70 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-stone-50 text-[11px] uppercase tracking-wider text-stone-500 border-b border-stone-200">
                    <tr>
                        <th class="px-5 py-3.5 font-medium">Client Profile</th>
                        <th class="px-5 py-3.5 font-medium">Contact Number</th>
                        <th class="px-5 py-3.5 font-medium">Primary Address</th>
                        <th class="px-5 py-3.5 font-medium">Total Orders</th>
                        <th class="px-5 py-3.5 font-medium">Lifetime Spend</th>
                        <th class="px-5 py-3.5 font-medium text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse($customers as $customer)
                    <tr class="hover:bg-stone-50/70 transition-colors">
                        <!-- Profile -->
                        <td class="px-5 py-4">
                            <div class="flex items-center space-x-3">
                                @if($customer->avatar)
                                    <img src="{{ $customer->avatar }}" alt="" class="w-10 h-10 rounded-full object-cover border border-stone-200 flex-shrink-0">
                                @else
                                    <div class="w-10 h-10 rounded-full bg-stone-800 text-white flex items-center justify-center font-bold text-xs flex-shrink-0">
                                        {{ substr($customer->name, 0, 1) }}
                                    </div>
                                @endif
                                <div>
                                    <a href="{{ route('admin.customers.show', $customer->id) }}" class="font-semibold text-stone-900 text-xs hover:underline">{{ $customer->name }}</a>
                                    <p class="text-[11px] text-stone-400">{{ $customer->email }}</p>
                                </div>
                            </div>
                        </td>

                        <!-- Contact -->
                        <td class="px-5 py-4 text-xs text-stone-700">
                            {{ $customer->phone ?? 'N/A' }}
                        </td>

                        <!-- Primary Address -->
                        <td class="px-5 py-4 text-xs text-stone-600 max-w-xs truncate">
                            @php $defAddr = $customer->addresses->where('is_default', true)->first() ?? $customer->addresses->first(); @endphp
                            {{ $defAddr ? $defAddr->city . ', ' . $defAddr->province_state : 'None on file' }}
                        </td>

                        <!-- Total Orders -->
                        <td class="px-5 py-4 text-xs font-semibold text-stone-800">
                            {{ $customer->orders_count }} orders
                        </td>

                        <!-- Lifetime Spend -->
                        <td class="px-5 py-4 font-bold text-stone-900 text-sm">
                            ₱{{ number_format($customer->orders->sum('total_amount'), 2) }}
                        </td>

                        <!-- Actions -->
                        <td class="px-5 py-4 text-xs text-right space-x-2">
                            <a href="{{ route('admin.customers.show', $customer->id) }}" class="px-2.5 py-1.5 bg-stone-100 hover:bg-stone-200 text-stone-700 rounded text-[11px] font-medium transition-colors">
                                View
                            </a>
                            <a href="{{ route('admin.customers.edit', $customer->id) }}" class="px-2.5 py-1.5 bg-stone-100 hover:bg-stone-200 text-stone-700 rounded text-[11px] font-medium transition-colors">
                                Edit
                            </a>
                            <form action="{{ route('admin.customers.destroy', $customer->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to remove this client?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-2.5 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 rounded text-[11px] font-medium transition-colors">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-12 text-center text-stone-400 text-xs">
                            No clients found matching the search criteria.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($customers->hasPages())
        <div class="p-4 border-t border-stone-200 bg-stone-50">
            {{ $customers->links() }}
        </div>
        @endif
    </div>

</div>
@endsection

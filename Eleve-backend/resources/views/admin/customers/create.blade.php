@extends('layouts.admin')

@section('title', 'Add New Client Profile')
@section('header_title', 'Client Directory')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Header / Back -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="font-luxury-serif text-2xl font-bold text-stone-900">Add New Client</h2>
            <p class="text-xs text-stone-500">Register a new client profile with contact and delivery destination</p>
        </div>
        <a href="{{ route('admin.customers.index') }}" class="px-4 py-2 border border-stone-200 text-stone-600 rounded-lg text-xs font-medium hover:bg-stone-50 transition-colors">
            Back to Directory
        </a>
    </div>

    <form action="{{ route('admin.customers.store') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Account Credentials & Identity -->
        <div class="bg-white p-6 rounded-xl border border-[#D8D0C4]/70 shadow-sm space-y-4">
            <h3 class="font-luxury-serif text-sm font-semibold tracking-wider uppercase text-stone-800 border-b border-stone-100 pb-2">
                1. Account Identity & Credentials
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-stone-700 mb-1">Full Name *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required 
                           class="w-full px-3 py-2 text-sm border border-stone-200 rounded-lg focus:outline-none focus:border-stone-900 bg-stone-50/50" 
                           placeholder="e.g. Dominique Vance">
                    @error('name') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-stone-700 mb-1">Email Address *</label>
                    <input type="email" name="email" value="{{ old('email') }}" required 
                           class="w-full px-3 py-2 text-sm border border-stone-200 rounded-lg focus:outline-none focus:border-stone-900 bg-stone-50/50" 
                           placeholder="e.g. dominique@eleve.ph">
                    @error('email') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-stone-700 mb-1">Phone Number</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" 
                           class="w-full px-3 py-2 text-sm border border-stone-200 rounded-lg focus:outline-none focus:border-stone-900 bg-stone-50/50" 
                           placeholder="e.g. +63 917 123 4567">
                    @error('phone') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-stone-700 mb-1">Account Password *</label>
                    <input type="password" name="password" required 
                           class="w-full px-3 py-2 text-sm border border-stone-200 rounded-lg focus:outline-none focus:border-stone-900 bg-stone-50/50" 
                           placeholder="Minimum 6 characters">
                    @error('password') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <!-- Primary Delivery Destination -->
        <div class="bg-white p-6 rounded-xl border border-[#D8D0C4]/70 shadow-sm space-y-4">
            <h3 class="font-luxury-serif text-sm font-semibold tracking-wider uppercase text-stone-800 border-b border-stone-100 pb-2">
                2. Primary Delivery Destination (Optional)
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-stone-700 mb-1">Street Address</label>
                    <input type="text" name="street_address" value="{{ old('street_address') }}" 
                           class="w-full px-3 py-2 text-sm border border-stone-200 rounded-lg focus:outline-none focus:border-stone-900 bg-stone-50/50" 
                           placeholder="e.g. 5th Avenue corner 26th Street">
                </div>

                <div>
                    <label class="block text-xs font-medium text-stone-700 mb-1">Unit / Floor / Suite</label>
                    <input type="text" name="apartment" value="{{ old('apartment') }}" 
                           class="w-full px-3 py-2 text-sm border border-stone-200 rounded-lg focus:outline-none focus:border-stone-900 bg-stone-50/50" 
                           placeholder="e.g. Penthouse 42-B">
                </div>

                <div>
                    <label class="block text-xs font-medium text-stone-700 mb-1">City</label>
                    <input type="text" name="city" value="{{ old('city', 'Taguig') }}" 
                           class="w-full px-3 py-2 text-sm border border-stone-200 rounded-lg focus:outline-none focus:border-stone-900 bg-stone-50/50" 
                           placeholder="e.g. Taguig">
                </div>

                <div>
                    <label class="block text-xs font-medium text-stone-700 mb-1">Province / State</label>
                    <input type="text" name="province_state" value="{{ old('province_state', 'Metro Manila') }}" 
                           class="w-full px-3 py-2 text-sm border border-stone-200 rounded-lg focus:outline-none focus:border-stone-900 bg-stone-50/50" 
                           placeholder="e.g. Metro Manila">
                </div>

                <div>
                    <label class="block text-xs font-medium text-stone-700 mb-1">Postal / Zip Code</label>
                    <input type="text" name="postal_code" value="{{ old('postal_code', '1634') }}" 
                           class="w-full px-3 py-2 text-sm border border-stone-200 rounded-lg focus:outline-none focus:border-stone-900 bg-stone-50/50" 
                           placeholder="e.g. 1634">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-stone-700 mb-1">Country</label>
                    <input type="text" name="country" value="{{ old('country', 'Philippines') }}" 
                           class="w-full px-3 py-2 text-sm border border-stone-200 rounded-lg focus:outline-none focus:border-stone-900 bg-stone-50/50">
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end space-x-3">
            <a href="{{ route('admin.customers.index') }}" class="px-4 py-2 border border-stone-200 text-stone-600 rounded-lg text-xs font-medium hover:bg-stone-50 transition-colors">
                Cancel
            </a>
            <button type="submit" class="px-5 py-2.5 bg-stone-900 hover:bg-stone-800 text-white rounded-lg text-xs font-medium shadow-sm transition-colors">
                Save Client Profile
            </button>
        </div>
    </form>

</div>
@endsection

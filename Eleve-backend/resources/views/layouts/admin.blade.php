<!DOCTYPE html>
<html lang="en" class="h-full bg-[#F7F5F0]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — ÉLEVÉ Administrator</title>
    
    <!-- Google Fonts: Playfair Display + Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        obsidian: '#171717',
                        ivory: '#F7F5F0',
                        sand: '#D8D0C4',
                        champagne: '#C5A880',
                        charcoal: '#262626',
                    },
                    fontFamily: {
                        serif: ['"Playfair Display"', 'Georgia', 'serif'],
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <script>
        // Anti-flicker inline theme initialization
        if (localStorage.getItem('eleve_admin_theme') === 'dark' || (!localStorage.getItem('eleve_admin_theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #F7F5F0; }
        .font-luxury-serif { font-family: 'Playfair Display', Georgia, serif; }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #D8D0C4; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #171717; }

        /* Dark Mode Comprehensive Luxury Styling */
        .dark { color-scheme: dark; }
        .dark body { background-color: #0F0F0F !important; color: #F7F5F0 !important; }
        .dark header { background-color: rgba(23, 23, 23, 0.94) !important; border-color: rgba(216, 208, 196, 0.15) !important; }
        .dark main { background-color: #0F0F0F !important; }
        .dark .bg-white { background-color: #1A1A1A !important; border-color: rgba(216, 208, 196, 0.15) !important; color: #F7F5F0 !important; }
        .dark .bg-\[\#F7F5F0\] { background-color: #0F0F0F !important; }
        .dark .bg-stone-50 { background-color: #242424 !important; }
        .dark .bg-stone-100 { background-color: #262626 !important; color: #F7F5F0 !important; }
        .dark .text-stone-900 { color: #F7F5F0 !important; }
        .dark .text-stone-800 { color: #E8E2D9 !important; }
        .dark .text-stone-700 { color: #D8D0C4 !important; }
        .dark .text-stone-600 { color: #B3ADA3 !important; }
        .dark .text-stone-500 { color: #9E988F !important; }
        .dark .border-stone-200, 
        .dark .border-\[\#D8D0C4\]\/70, 
        .dark .border-\[\#D8D0C4\]\/60,
        .dark .border-\[\#D8D0C4\] { border-color: rgba(216, 208, 196, 0.15) !important; }
        .dark .divide-stone-100 > :not([hidden]) ~ :not([hidden]) { border-color: rgba(216, 208, 196, 0.1) !important; }
        .dark .divide-stone-200 > :not([hidden]) ~ :not([hidden]) { border-color: rgba(216, 208, 196, 0.15) !important; }
        .dark input:not([type="checkbox"]):not([type="radio"]), 
        .dark select, 
        .dark textarea {
            background-color: #242424 !important;
            border-color: rgba(216, 208, 196, 0.2) !important;
            color: #F7F5F0 !important;
        }
        .dark input::placeholder, 
        .dark textarea::placeholder { color: #737373 !important; }
        .dark table thead { background-color: #242424 !important; border-color: rgba(216, 208, 196, 0.15) !important; }
        .dark table thead th { color: #C5A880 !important; }
        .dark tbody tr:hover { background-color: rgba(255, 255, 255, 0.03) !important; }
    </style>
</head>
<body class="h-full flex flex-col md:flex-row text-stone-800 antialiased selection:bg-stone-900 selection:text-white">

    <!-- Sidebar -->
    <aside class="w-full md:w-64 bg-[#171717] text-white flex-shrink-0 flex flex-col justify-between border-r border-stone-800">
        <div>
            <!-- Brand Header -->
            <div class="px-6 py-8 border-b border-stone-800/80">
                <div class="flex items-center space-x-3">
                    <span class="w-8 h-8 rounded-full bg-stone-100 text-stone-900 flex items-center justify-center font-luxury-serif font-bold text-lg">É</span>
                    <div>
                        <h1 class="font-luxury-serif text-2xl tracking-[0.2em] font-semibold text-white">ÉLEVÉ</h1>
                        <p class="text-[10px] tracking-[0.3em] uppercase text-stone-400">Atelier Admin</p>
                    </div>
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="px-4 py-6 space-y-1.5 text-sm">
                <!-- Dashboard -->
                <a href="{{ route('admin.dashboard') }}" 
                   class="flex items-center px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-stone-800 text-white font-medium shadow-sm' : 'text-stone-400 hover:text-white hover:bg-stone-800/50' }}">
                    <svg class="w-5 h-5 mr-3 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    <span>Dashboard</span>
                </a>

                <!-- Products -->
                <a href="{{ route('admin.products.index') }}" 
                   class="flex items-center px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.products.*') ? 'bg-stone-800 text-white font-medium shadow-sm' : 'text-stone-400 hover:text-white hover:bg-stone-800/50' }}">
                    <svg class="w-5 h-5 mr-3 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                    <span>Footwear Catalog</span>
                </a>

                <!-- Categories -->
                <a href="{{ route('admin.categories.index') }}" 
                   class="flex items-center px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.categories.*') ? 'bg-stone-800 text-white font-medium shadow-sm' : 'text-stone-400 hover:text-white hover:bg-stone-800/50' }}">
                    <svg class="w-5 h-5 mr-3 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                    <span>Categories & Silhouettes</span>
                </a>

                <!-- Inventory -->
                <a href="{{ route('admin.inventory.index') }}" 
                   class="flex items-center px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.inventory.*') ? 'bg-stone-800 text-white font-medium shadow-sm' : 'text-stone-400 hover:text-white hover:bg-stone-800/50' }}">
                    <svg class="w-5 h-5 mr-3 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                    </svg>
                    <span>Inventory Matrix</span>
                </a>

                <!-- Orders -->
                <a href="{{ route('admin.orders.index') }}" 
                   class="flex items-center justify-between px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.orders.*') ? 'bg-stone-800 text-white font-medium shadow-sm' : 'text-stone-400 hover:text-white hover:bg-stone-800/50' }}">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span>Client Orders</span>
                    </div>
                </a>

                <!-- Customers -->
                <a href="{{ route('admin.customers.index') }}" 
                   class="flex items-center px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.customers.*') ? 'bg-stone-800 text-white font-medium shadow-sm' : 'text-stone-400 hover:text-white hover:bg-stone-800/50' }}">
                    <svg class="w-5 h-5 mr-3 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    <span>Client Directory</span>
                </a>

                <!-- Reports -->
                <a href="{{ route('admin.reports.index') }}" 
                   class="flex items-center px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.reports.*') ? 'bg-stone-800 text-white font-medium shadow-sm' : 'text-stone-400 hover:text-white hover:bg-stone-800/50' }}">
                    <svg class="w-5 h-5 mr-3 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    <span>Financial Reports</span>
                </a>

                <!-- Activity Feed -->
                <a href="{{ route('admin.notifications.index') }}" 
                   class="flex items-center justify-between px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.notifications.*') ? 'bg-stone-800 text-white font-medium shadow-sm' : 'text-stone-400 hover:text-white hover:bg-stone-800/50' }}">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                        <span>Activity Feed</span>
                    </div>
                    <span id="sidebarNotifBadge" class="hidden px-2 py-0.5 rounded-full bg-[#C5A880] text-stone-900 text-[10px] font-bold">0</span>
                </a>
            </nav>
        </div>

        <!-- Sidebar Footer / Admin Profile -->
        <div class="p-4 border-t border-stone-800/80 bg-stone-900/60">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3 overflow-hidden">
                    <div class="w-9 h-9 rounded-full bg-stone-700 border border-stone-600 flex items-center justify-center text-sm font-semibold text-white">
                        {{ substr(auth()->user()->name ?? 'A', 0, 1) }}
                    </div>
                    <div class="truncate">
                        <p class="text-xs font-medium text-white truncate">{{ auth()->user()->name ?? 'Director' }}</p>
                        <p class="text-[10px] text-stone-400 uppercase tracking-wider">Admin</p>
                    </div>
                </div>
                <form action="{{ route('admin.logout') }}" method="POST">
                    @csrf
                    <button type="submit" title="Sign Out" class="p-2 text-stone-400 hover:text-white hover:bg-stone-800 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-h-screen overflow-y-auto bg-[#F7F5F0]">
        
        <!-- Top Navbar -->
        <header class="bg-white/80 backdrop-blur-md border-b border-[#D8D0C4]/60 sticky top-0 z-30 px-6 py-4 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <h2 class="font-luxury-serif text-xl font-medium text-stone-900">@yield('header_title', 'Overview')</h2>
            </div>

            <div class="flex items-center space-x-4">
                <!-- 3s Auto-Progression Indicator -->
                <div class="flex items-center space-x-2 px-3 py-1 bg-stone-100 border border-[#D8D0C4]/50 rounded-full text-xs text-stone-600">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span class="font-medium text-[11px] tracking-wide">3s Auto-Progression Active</span>
                </div>

                <!-- Theme Toggle (Obsidian Dark / Ivory Light) -->
                <button id="adminThemeToggleBtn" onclick="toggleAdminTheme()" class="p-2 text-stone-600 hover:text-stone-900 bg-stone-100 hover:bg-stone-200 rounded-full transition-all" title="Toggle Obsidian Dark / Ivory Light Mode">
                    <!-- Moon icon (light mode) -->
                    <svg id="themeMoonIcon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                    </svg>
                    <!-- Sun icon (dark mode) -->
                    <svg id="themeSunIcon" class="w-4 h-4 hidden text-[#C5A880]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </button>

                <!-- Notification Bell with Dropdown -->
                <div class="relative" id="adminNotificationWrapper">
                    <button id="adminNotifBellBtn" onclick="toggleNotifDropdown()" class="relative p-2 text-stone-600 hover:text-stone-900 bg-stone-100 hover:bg-stone-200 rounded-full transition-colors" title="Atelier Activity Feed">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                        <span id="adminNotifBadge" class="absolute -top-1 -right-1 w-4 h-4 rounded-full bg-[#C5A880] text-stone-900 text-[10px] font-bold flex items-center justify-center hidden">0</span>
                    </button>

                    <!-- Dropdown Menu -->
                    <div id="adminNotifDropdown" class="hidden absolute right-0 mt-2 w-96 md:w-[410px] max-w-[calc(100vw-2rem)] bg-white rounded-xl shadow-2xl border border-[#D8D0C4] z-50 overflow-hidden">
                        <!-- Header -->
                        <div class="p-3.5 bg-[#171717] text-white">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                                    <span class="text-xs font-semibold tracking-wider uppercase font-luxury-serif">Atelier Real-Time Feed</span>
                                </div>
                                <button onclick="markAllAdminRead(event)" class="text-[11px] text-[#C5A880] hover:text-white transition-colors underline font-medium">Mark all read</button>
                            </div>
                            <div class="mt-2.5 flex items-center justify-between text-[11px] pt-2 border-t border-stone-800">
                                <div class="flex items-center space-x-1.5">
                                    <button id="feedFilterAll" onclick="setFeedFilter('all')" class="px-2.5 py-0.5 rounded-full bg-stone-800 text-white font-medium text-[10px]">All Activity</button>
                                    <button id="feedFilterUnread" onclick="setFeedFilter('unread')" class="px-2.5 py-0.5 rounded-full text-stone-400 hover:text-white font-medium text-[10px]">Unread</button>
                                </div>
                                <span id="feedUnreadCountHeader" class="text-[10px] text-stone-400">0 unread</span>
                            </div>
                        </div>

                        <!-- List Container -->
                        <div id="adminNotifList" class="max-h-80 overflow-y-auto divide-y divide-stone-100 text-xs">
                            <div class="p-8 text-center text-stone-400 text-xs">Synchronizing atelier activity...</div>
                        </div>

                        <!-- Footer -->
                        <div class="p-3 bg-stone-50 border-t border-stone-200/80 flex items-center justify-between text-[11px]">
                            <button onclick="clearAllReadAdmin(event)" class="text-stone-500 hover:text-stone-900 underline text-[11px]">Clear read</button>
                            <a href="{{ route('admin.notifications.index') }}" class="font-semibold text-stone-800 hover:text-[#C5A880] flex items-center">
                                <span>View Full Activity Log</span>
                                <svg class="w-3.5 h-3.5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </a>
                        </div>
                    </div>
                </div>

                <a href="{{ route('admin.products.create') }}" class="inline-flex items-center px-4 py-2 bg-[#171717] hover:bg-stone-800 text-white text-xs uppercase tracking-widest font-medium rounded-md shadow-sm transition-all duration-200">
                    <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add Footwear
                </a>
            </div>
        </header>

        <!-- Floating Luxury Admin Live Toast Alert -->
        <div id="adminLiveToast" class="fixed top-20 right-6 z-50 transform -translate-y-8 opacity-0 pointer-events-none transition-all duration-300 max-w-sm bg-[#171717] text-white p-4 rounded-xl shadow-2xl border border-stone-700 flex items-start space-x-3">
            <div id="adminLiveToastIcon" class="w-8 h-8 rounded-lg bg-[#C5A880]/20 text-[#C5A880] flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
            </div>
            <div class="flex-1 min-w-0">
                <h5 id="adminLiveToastTitle" class="text-xs font-bold text-white truncate"></h5>
                <p id="adminLiveToastMessage" class="text-[11px] text-stone-300 mt-0.5 line-clamp-2"></p>
                <a id="adminLiveToastLink" href="#" class="inline-block mt-2 text-[10px] font-semibold text-[#C5A880] hover:text-white uppercase tracking-wider">View Order &rarr;</a>
            </div>
            <button onclick="dismissLiveToast()" class="text-stone-400 hover:text-white p-1">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <!-- Flash Messages -->
        <div class="px-6 pt-4">
            @if(session('success'))
                <div class="mb-4 p-4 bg-emerald-50 border border-emerald-200 rounded-lg text-emerald-800 text-sm flex items-center justify-between shadow-sm">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-emerald-600 mr-2.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 p-4 bg-rose-50 border border-rose-200 rounded-lg text-rose-800 text-sm flex items-center justify-between shadow-sm">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-rose-600 mr-2.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>{{ session('error') }}</span>
                    </div>
                </div>
            @endif
        </div>

        <!-- Content -->
        <main class="flex-1 px-6 py-4 pb-12">
            @yield('content')
        </main>
    </div>

    <!-- Real-Time Notifications & Auto-Progression Client Script -->
    <script>
        let lastNotifId = 0;
        let currentFeedFilter = 'all';
        let audioContext = null;
        let toastTimeout = null;
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        function playLuxuryChime() {
            try {
                if (!audioContext) {
                    audioContext = new (window.AudioContext || window.webkitAudioContext)();
                }
                if (audioContext.state === 'suspended') {
                    audioContext.resume();
                }
                const osc = audioContext.createOscillator();
                const gain = audioContext.createGain();
                osc.type = 'sine';
                osc.frequency.setValueAtTime(880, audioContext.currentTime); // A5 note
                osc.frequency.exponentialRampToValueAtTime(1320, audioContext.currentTime + 0.12); // E6
                gain.gain.setValueAtTime(0.12, audioContext.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.001, audioContext.currentTime + 0.35);
                osc.connect(gain);
                gain.connect(audioContext.destination);
                osc.start();
                osc.stop(audioContext.currentTime + 0.4);
            } catch (e) {
                // Audio autoplay policy
            }
        }

        function toggleNotifDropdown() {
            const dd = document.getElementById('adminNotifDropdown');
            if (dd) {
                dd.classList.toggle('hidden');
                if (!dd.classList.contains('hidden')) {
                    pollNotifications();
                }
            }
        }

        function setFeedFilter(filter) {
            currentFeedFilter = filter;
            const btnAll = document.getElementById('feedFilterAll');
            const btnUnread = document.getElementById('feedFilterUnread');

            if (filter === 'all') {
                btnAll.className = 'px-2.5 py-0.5 rounded-full bg-stone-800 text-white font-medium text-[10px]';
                btnUnread.className = 'px-2.5 py-0.5 rounded-full text-stone-400 hover:text-white font-medium text-[10px]';
            } else {
                btnUnread.className = 'px-2.5 py-0.5 rounded-full bg-stone-800 text-white font-medium text-[10px]';
                btnAll.className = 'px-2.5 py-0.5 rounded-full text-stone-400 hover:text-white font-medium text-[10px]';
            }
            pollNotifications();
        }

        function renderIconSvg(icon) {
            switch (icon) {
                case 'check-circle':
                    return `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>`;
                case 'truck':
                    return `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h2m-6 0a1 1 0 001-1m6 1a1 1 0 001-1m-9 0H4"></path></svg>`;
                case 'sparkles':
                    return `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>`;
                case 'shield-check':
                    return `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>`;
                case 'shopping-bag':
                    return `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>`;
                default:
                    return `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>`;
            }
        }

        async function pollNotifications() {
            try {
                const unreadParam = currentFeedFilter === 'unread' ? '&unread_only=1' : '';
                const res = await fetch('/admin/notifications/feed?last_id=' + lastNotifId + unreadParam, {
                    headers: { 'Accept': 'application/json' }
                });
                const json = await res.json();
                if (json.success) {
                    const badge = document.getElementById('adminNotifBadge');
                    const sidebarBadge = document.getElementById('sidebarNotifBadge');
                    const headerUnread = document.getElementById('feedUnreadCountHeader');
                    const list = document.getElementById('adminNotifList');
                    const unread = json.unread_count;

                    // Update Top Bell Badge
                    if (badge) {
                        if (unread > 0) {
                            badge.textContent = unread > 99 ? '99+' : unread;
                            badge.classList.remove('hidden');
                        } else {
                            badge.classList.add('hidden');
                        }
                    }

                    // Update Sidebar Badge
                    if (sidebarBadge) {
                        if (unread > 0) {
                            sidebarBadge.textContent = unread > 99 ? '99+' : unread;
                            sidebarBadge.classList.remove('hidden');
                        } else {
                            sidebarBadge.classList.add('hidden');
                        }
                    }

                    if (headerUnread) {
                        headerUnread.textContent = unread + ' unread';
                    }

                    // Check for new notifications to trigger sound & toast
                    if (json.new_items && json.new_items.length > 0) {
                        if (lastNotifId > 0) {
                            playLuxuryChime();
                            showLiveToast(json.new_items[0]);
                        }
                    }
                    if (json.latest_id > 0) {
                        lastNotifId = Math.max(json.latest_id, lastNotifId);
                    }

                    // Render list
                    if (list) {
                        if (!json.notifications || json.notifications.length === 0) {
                            list.innerHTML = `
                                <div class="p-8 text-center text-stone-400">
                                    <svg class="w-8 h-8 mx-auto mb-2 text-stone-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                    </svg>
                                    <p class="text-xs">No ${currentFeedFilter === 'unread' ? 'unread ' : ''}activity records.</p>
                                </div>
                            `;
                            return;
                        }

                        let html = '';
                        json.notifications.forEach(n => {
                            const isUnread = !n.is_read;
                            html += `
                                <div class="group relative p-3.5 hover:bg-stone-50/90 transition-all cursor-pointer ${isUnread ? 'bg-amber-50/40 border-l-2 border-l-[#C5A880]' : ''}" 
                                     onclick="handleCardClick(${n.id}, '${n.order_url || ''}')">
                                    <div class="flex items-start space-x-3">
                                        <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 ${n.badge_bg} ${n.badge_border} border ${n.badge_text}">
                                            ${renderIconSvg(n.icon)}
                                        </div>
                                        <div class="flex-1 min-w-0 pr-6">
                                            <div class="flex items-center justify-between">
                                                <h4 class="font-semibold text-stone-900 text-xs truncate ${isUnread ? 'font-bold' : ''}">
                                                    ${n.title}
                                                </h4>
                                                ${n.formatted_amount ? `<span class="ml-2 font-mono font-bold text-stone-900 text-[11px] flex-shrink-0">${n.formatted_amount}</span>` : ''}
                                            </div>
                                            <p class="text-[11px] text-stone-600 mt-0.5 leading-snug line-clamp-2">${n.message}</p>
                                            <div class="flex items-center space-x-2 mt-1.5 text-[10px] text-stone-400">
                                                <span class="font-medium text-stone-500">${n.relative_time}</span>
                                                <span>&bull;</span>
                                                <span>${n.formatted_time}</span>
                                                ${n.order_url ? `<span class="text-[#A67C52] font-semibold group-hover:underline flex items-center ml-auto">View Order &rarr;</span>` : ''}
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Quick Actions -->
                                    <div class="absolute top-2.5 right-2.5 flex items-center space-x-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                        ${isUnread ? `
                                            <button onclick="markSingleAdminRead(event, ${n.id})" title="Mark read" class="p-1 hover:bg-stone-200 rounded text-stone-500 hover:text-stone-900">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            </button>` : ''}
                                        <button onclick="dismissAdminNotif(event, ${n.id})" title="Dismiss" class="p-1 hover:bg-rose-100 rounded text-stone-400 hover:text-rose-600">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </button>
                                    </div>
                                </div>
                            `;
                        });
                        list.innerHTML = html;
                    }
                }
            } catch (e) {
                // Background poll silent failover
            }
        }

        async function handleCardClick(id, orderUrl) {
            try {
                await fetch('/admin/notifications/' + id + '/mark-read', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });
            } catch (e) {}

            if (orderUrl) {
                window.location.href = orderUrl;
            } else {
                pollNotifications();
            }
        }

        async function markAllAdminRead(e) {
            if (e) e.stopPropagation();
            try {
                // Optimistic UI update
                const badge = document.getElementById('adminNotifBadge');
                const sidebarBadge = document.getElementById('sidebarNotifBadge');
                if (badge) badge.classList.add('hidden');
                if (sidebarBadge) sidebarBadge.classList.add('hidden');

                const res = await fetch('/admin/notifications/mark-all-read', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });
                await res.json();
                pollNotifications();
            } catch (e) {}
        }

        async function markSingleAdminRead(e, id) {
            if (e) e.stopPropagation();
            try {
                await fetch('/admin/notifications/' + id + '/mark-read', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });
                pollNotifications();
            } catch (e) {}
        }

        async function dismissAdminNotif(e, id) {
            if (e) e.stopPropagation();
            try {
                await fetch('/admin/notifications/' + id, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });
                pollNotifications();
            } catch (e) {}
        }

        async function clearAllReadAdmin(e) {
            if (e) e.stopPropagation();
            if (!confirm('Clear all read notifications from the feed?')) return;
            try {
                await fetch('/admin/notifications/clear-read', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });
                pollNotifications();
            } catch (e) {}
        }

        function showLiveToast(n) {
            const toast = document.getElementById('adminLiveToast');
            const titleEl = document.getElementById('adminLiveToastTitle');
            const msgEl = document.getElementById('adminLiveToastMessage');
            const linkEl = document.getElementById('adminLiveToastLink');

            if (!toast || !titleEl) return;

            titleEl.textContent = n.title;
            msgEl.textContent = n.message;
            if (n.order_url) {
                linkEl.href = n.order_url;
                linkEl.classList.remove('hidden');
            } else {
                linkEl.classList.add('hidden');
            }

            toast.classList.remove('opacity-0', 'pointer-events-none', '-translate-y-8');
            toast.classList.add('opacity-100', 'translate-y-0');

            if (toastTimeout) clearTimeout(toastTimeout);
            toastTimeout = setTimeout(() => {
                dismissLiveToast();
            }, 6000);
        }

        function dismissLiveToast() {
            const toast = document.getElementById('adminLiveToast');
            if (toast) {
                toast.classList.add('opacity-0', 'pointer-events-none', '-translate-y-8');
                toast.classList.remove('opacity-100', 'translate-y-0');
            }
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            const wrapper = document.getElementById('adminNotificationWrapper');
            const dd = document.getElementById('adminNotifDropdown');
            if (wrapper && dd && !wrapper.contains(e.target)) {
                dd.classList.add('hidden');
            }
        });

        // Theme Toggle Logic
        function updateThemeIcon() {
            const isDark = document.documentElement.classList.contains('dark');
            const moonIcon = document.getElementById('themeMoonIcon');
            const sunIcon = document.getElementById('themeSunIcon');
            if (moonIcon && sunIcon) {
                if (isDark) {
                    moonIcon.classList.add('hidden');
                    sunIcon.classList.remove('hidden');
                } else {
                    sunIcon.classList.add('hidden');
                    moonIcon.classList.remove('hidden');
                }
            }
        }

        function toggleAdminTheme() {
            const isDark = document.documentElement.classList.toggle('dark');
            localStorage.setItem('eleve_admin_theme', isDark ? 'dark' : 'light');
            updateThemeIcon();
        }

        // Initialize Theme Icon State
        updateThemeIcon();

        // Run immediate poll and loop every 3 seconds
        pollNotifications();
        setInterval(pollNotifications, 3000);
    </script>
</body>
</html>

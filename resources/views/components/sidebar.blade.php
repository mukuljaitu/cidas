<aside class="sidebar bg-white border-r border-gray-200 flex flex-col h-screen sticky top-0">
    <!-- Logo -->
    <div class="p-6 flex items-center gap-3 logo-container">
        <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
            </svg>
        </div>
        <span class="text-xl font-bold text-gray-900 tracking-tight logo-text">CIDAS <span class="text-xs font-semibold text-blue-600 align-top">PRO</span></span>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 px-4 space-y-8 mt-4">
        <div>

            <h3 class="px-2 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4 sidebar-text">Create</h3>
            <div class="space-y-1">
                <a href="{{ route('tours.create') }}" class="nav-link flex items-center gap-3 px-3 py-2 rounded-lg {{ Request::is('create-tour*') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50' }} transition-colors">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span class="font-medium sidebar-text">Tour</span>
                </a>
                <a href="{{ route('orders.index') }}" class="nav-link flex items-center gap-3 px-3 py-2 rounded-lg {{ Request::is('orders*') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50' }} transition-colors">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    <span class="font-medium sidebar-text">Order</span>
                </a>
            </div>
        </div>
        <div>
            <h3 class="px-2 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4 sidebar-text">Analyse</h3>
            <div class="space-y-1">
                <a href="{{ route('tours.index') }}" class="nav-link flex items-center gap-3 px-3 py-2 rounded-lg {{ Request::is('view-tours*') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50' }} transition-colors">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    <span class="font-medium sidebar-text">Tours</span>
                </a>
            </div>
        </div>
        <div>
            <h3 class="px-2 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4 sidebar-text">Library</h3>
            <div class="space-y-1">
                <a href="#" class="nav-link flex items-center justify-between gap-3 px-3 py-2 rounded-lg text-gray-400 cursor-not-allowed">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        <span class="font-medium sidebar-text">All Members</span>
                    </div>
                    <span class="text-xs font-bold bg-gray-100 text-gray-400 px-2 py-0.5 rounded-full sidebar-text">Coming soon</span>
                </a>
                <a href="#" class="nav-link flex items-center justify-between gap-3 px-3 py-2 rounded-lg text-gray-400 cursor-not-allowed">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                        <span class="font-medium sidebar-text">Departments</span>
                    </div>
                    <span class="text-xs font-bold bg-gray-100 text-gray-400 px-2 py-0.5 rounded-full sidebar-text">Coming soon</span>
                </a>
                <a href="#" class="nav-link flex items-center justify-between gap-3 px-3 py-2 rounded-lg text-gray-400 cursor-not-allowed">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="font-medium sidebar-text">Activity</span>
                    </div>
                    <span class="text-xs font-bold bg-gray-100 text-gray-400 px-2 py-0.5 rounded-full sidebar-text">Coming soon</span>
                </a>
            </div>
        </div>
        <div>
            <h3 class="px-2 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4 sidebar-text">Financials</h3>
            <div class="space-y-1">
                <a href="#" class="nav-link flex items-center justify-between gap-3 px-3 py-2 rounded-lg text-gray-400 cursor-not-allowed">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m4 0h1m-7 4h12a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span class="font-medium sidebar-text">Banking</span>
                    </div>
                    <span class="text-xs font-bold bg-gray-100 text-gray-400 px-2 py-0.5 rounded-full sidebar-text">Coming soon</span>
                </a>
            </div>
        </div>
        <div>
            <h3 class="px-2 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4 sidebar-text">Assets</h3>
            <div class="space-y-1">
                <a href="/products" class="nav-link flex items-center gap-3 px-3 py-2 rounded-lg {{ Request::is('products*') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50' }} transition-colors">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-14L4 7m8 4v10m0 0l-8-4V7"></path>
                    </svg>
                    <span class="font-medium sidebar-text">Products</span>
                </a>
                <a href="/variants" class="nav-link flex items-center gap-3 px-3 py-2 rounded-lg {{ Request::is('variants*') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50' }} transition-colors">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h10M7 12h10M7 17h10"></path>
                    </svg>
                    <span class="font-medium sidebar-text">Variants</span>
                </a>
                <a href="/cities" class="nav-link flex items-center gap-3 px-3 py-2 rounded-lg {{ Request::is('cities*') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50' }} transition-colors">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span class="font-medium sidebar-text">Cities</span>
                </a>
            </div>
        </div>
    </nav>

    <!-- User Profile & Toggle -->
    <div class="p-4 border-t border-gray-100 bg-gray-50/50">
        <div class="flex items-center gap-3 user-profile-main">
            @php
            $user = Auth::user();
            $userName = $user?->name ?: 'Admin User';
            $userRole = 'System Architect';
            $initial = strtoupper(substr($userName, 0, 1));
            @endphp
            <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 font-bold border-2 border-white shadow-sm flex-shrink-0">
                {{ $initial }}
            </div>
            <div class="flex-1 min-w-0 user-details">
                <p class="text-sm font-semibold text-gray-900 truncate">{{ $userName }}</p>
                <p class="text-xs text-gray-500 truncate">{{ $userRole }}</p>
            </div>
            <button class="p-1.5 text-gray-400 hover:text-gray-600 transition-colors sidebar-toggle" onclick="toggleSidebar()">
                <svg class="w-5 h-5 arrow-open" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path>
                </svg>
                <svg class="w-5 h-5 arrow-closed" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path>
                </svg>
            </button>
        </div>
    </div>
</aside>

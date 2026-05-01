<!DOCTYPE html>
<html lang="en" class="transition-colors duration-300">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Management Dashboard</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Configure Tailwind to use class-based dark mode -->
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['-apple-system', 'BlinkMacSystemFont', '"Segoe UI"', 'Roboto', 'Helvetica', 'Arial', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in-up {
            animation: fadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        /* Smooth transitions for background colors */
        body {
            transition: background-color 0.3s ease, color 0.3s ease;
        }
    </style>
</head>

<body class="min-h-screen bg-[#F2F2F7] dark:bg-black font-sans text-gray-900 dark:text-white">

    <!-- Top Navigation Bar - Glassmorphism effect -->
    <nav class="sticky top-0 z-50 w-full bg-white/70 dark:bg-[#1C1C1E]/70 backdrop-blur-xl border-b border-gray-200/50 dark:border-white/10 transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">

                <!-- Logo & Menu -->
                <div class="flex items-center gap-4">
                    <span class="text-xl font-semibold tracking-tight text-gray-900 dark:text-white">
                        CIDAS for <span class="text-green-600">CCCPL</span>
                    </span>
                </div>

                <!-- Right Actions -->
                <div class="flex items-center gap-2">
                    <button class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-800 dark:text-gray-200 transition-colors">
                        <i data-lucide="bell" class="w-5 h-5"></i>
                    </button>
                    @php
                    $user = Auth::user();
                    $userName = $user?->name ?: 'Guest';
                    $initial = strtoupper(substr($userName, 0, 1));
                    $profileUrl = $user ? url('/profile') : (Route::has('login') ? route('login') : '#');
                    @endphp
                    <a href="{{ $profileUrl }}" class="h-8 w-8 ml-2 rounded-full bg-gradient-to-tr from-blue-500 to-purple-500 flex items-center justify-center text-white font-medium shadow-sm">
                        {{ $initial }}
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- Header Section -->
        <div class="mb-10 animate-fade-in-up">
            <h1 class="text-4xl font-bold tracking-tight text-gray-900 dark:text-white mb-2">
                Management Overview
            </h1>
            <p id="current-date" class="text-gray-500 dark:text-gray-400 font-medium">
                Loading date...
            </p>
        </div>

        <!-- Categories Grid -->
        <div class="space-y-10">

            <!-- Category 1 -->
            <div class="animate-fade-in-up" style="animation-delay: 0ms;">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4 ml-2">Create</h2>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">

                    <a href="{{ route('tours.create') }}" class="group relative flex flex-col items-center justify-center p-6 bg-white dark:bg-[#1C1C1E] rounded-[28px] shadow-[0_2px_10px_-4px_rgba(0,0,0,0.05)] dark:shadow-none border border-gray-100 dark:border-white/5 hover:shadow-lg dark:hover:bg-[#2C2C2E] transition-all duration-300 ease-out transform hover:-translate-y-1 active:scale-95 overflow-hidden">
                        <div class="w-16 h-16 rounded-[20px] flex items-center justify-center mb-4 bg-blue-500 text-white shadow-inner group-hover:scale-110 transition-transform duration-300 ease-out">
                            <i data-lucide="map" class="w-[30px] h-[30px]"></i>
                        </div>
                        <span class="text-sm font-semibold text-gray-800 dark:text-gray-200 text-center leading-tight">Tour</span>
                        <div class="absolute bottom-3 opacity-0 group-hover:opacity-100 transform translate-y-2 group-hover:translate-y-0 transition-all duration-300">
                            <i data-lucide="chevron-right" class="w-4 h-4 text-gray-400"></i>
                        </div>
                    </a>

                    <a href="{{ route('orders.index') }}" class="group relative flex flex-col items-center justify-center p-6 bg-white dark:bg-[#1C1C1E] rounded-[28px] shadow-[0_2px_10px_-4px_rgba(0,0,0,0.05)] dark:shadow-none border border-gray-100 dark:border-white/5 hover:shadow-lg dark:hover:bg-[#2C2C2E] transition-all duration-300 ease-out transform hover:-translate-y-1 active:scale-95 overflow-hidden">
                        <div class="w-16 h-16 rounded-[20px] flex items-center justify-center mb-4 bg-emerald-500 text-white shadow-inner group-hover:scale-110 transition-transform duration-300 ease-out">
                            <i data-lucide="book-text" class="w-[30px] h-[30px]"></i>
                        </div>
                        <span class="text-sm font-semibold text-gray-800 dark:text-gray-200 text-center leading-tight">Order</span>
                        <div class="absolute bottom-3 opacity-0 group-hover:opacity-100 transform translate-y-2 group-hover:translate-y-0 transition-all duration-300">
                            <i data-lucide="chevron-right" class="w-4 h-4 text-gray-400"></i>
                        </div>
                    </a>

                </div>
            </div>

            <!-- Category 2 -->
            <div class="animate-fade-in-up" style="animation-delay: 100ms;">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4 ml-2">Analyse</h2>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">

                    <a href="{{ route('tours.index') }}" class="group relative flex flex-col items-center justify-center p-6 bg-white dark:bg-[#1C1C1E] rounded-[28px] shadow-[0_2px_10px_-4px_rgba(0,0,0,0.05)] dark:shadow-none border border-gray-100 dark:border-white/5 hover:shadow-lg dark:hover:bg-[#2C2C2E] transition-all duration-300 ease-out transform hover:-translate-y-1 active:scale-95 overflow-hidden">
                        <div class="w-16 h-16 rounded-[20px] flex items-center justify-center mb-4 bg-indigo-500 text-white shadow-inner group-hover:scale-110 transition-transform duration-300 ease-out">
                            <i data-lucide="monitor" class="w-[30px] h-[30px]"></i>
                        </div>
                        <span class="text-sm font-semibold text-gray-800 dark:text-gray-200 text-center leading-tight">Tours</span>
                        <div class="absolute bottom-3 opacity-0 group-hover:opacity-100 transform translate-y-2 group-hover:translate-y-0 transition-all duration-300">
                            <i data-lucide="chevron-right" class="w-4 h-4 text-gray-400"></i>
                        </div>
                    </a>

                    <a href="{{ route('employees.analyze') }}" class="group relative flex flex-col items-center justify-center p-6 bg-white dark:bg-[#1C1C1E] rounded-[28px] shadow-[0_2px_10px_-4px_rgba(0,0,0,0.05)] dark:shadow-none border border-gray-100 dark:border-white/5 hover:shadow-lg dark:hover:bg-[#2C2C2E] transition-all duration-300 ease-out transform hover:-translate-y-1 active:scale-95 overflow-hidden">
                        <div class="w-16 h-16 rounded-[20px] flex items-center justify-center mb-4 bg-blue-600 text-white shadow-inner group-hover:scale-110 transition-transform duration-300 ease-out">
                            <i data-lucide="users" class="w-[30px] h-[30px]"></i>
                        </div>
                        <span class="text-sm font-semibold text-gray-800 dark:text-gray-200 text-center leading-tight">Employees</span>
                        <div class="absolute bottom-3 opacity-0 group-hover:opacity-100 transform translate-y-2 group-hover:translate-y-0 transition-all duration-300">
                            <i data-lucide="chevron-right" class="w-4 h-4 text-gray-400"></i>
                        </div>
                    </a>

                    <a href="{{ route('orders.analyze') }}" class="group relative flex flex-col items-center justify-center p-6 bg-white dark:bg-[#1C1C1E] rounded-[28px] shadow-[0_2px_10px_-4px_rgba(0,0,0,0.05)] dark:shadow-none border border-gray-100 dark:border-white/5 hover:shadow-lg dark:hover:bg-[#2C2C2E] transition-all duration-300 ease-out transform hover:-translate-y-1 active:scale-95 overflow-hidden">
                        <div class="w-16 h-16 rounded-[20px] flex items-center justify-center mb-4 bg-emerald-500 text-white shadow-inner group-hover:scale-110 transition-transform duration-300 ease-out">
                            <i data-lucide="line-chart" class="w-[30px] h-[30px]"></i>
                        </div>
                        <span class="text-sm font-semibold text-gray-800 dark:text-gray-200 text-center leading-tight">Orders</span>
                        <div class="absolute bottom-3 opacity-0 group-hover:opacity-100 transform translate-y-2 group-hover:translate-y-0 transition-all duration-300">
                            <i data-lucide="chevron-right" class="w-4 h-4 text-gray-400"></i>
                        </div>
                    </a>

                    <a href="{{ route('cities.analyze') }}" class="group relative flex flex-col items-center justify-center p-6 bg-white dark:bg-[#1C1C1E] rounded-[28px] shadow-[0_2px_10px_-4px_rgba(0,0,0,0.05)] dark:shadow-none border border-gray-100 dark:border-white/5 hover:shadow-lg dark:hover:bg-[#2C2C2E] transition-all duration-300 ease-out transform hover:-translate-y-1 active:scale-95 overflow-hidden">
                        <div class="w-16 h-16 rounded-[20px] flex items-center justify-center mb-4 bg-purple-600 text-white shadow-inner group-hover:scale-110 transition-transform duration-300 ease-out">
                            <i data-lucide="map-pin" class="w-[30px] h-[30px]"></i>
                        </div>
                        <span class="text-sm font-semibold text-gray-800 dark:text-gray-200 text-center leading-tight">City Intelligence</span>
                        <div class="absolute bottom-3 opacity-0 group-hover:opacity-100 transform translate-y-2 group-hover:translate-y-0 transition-all duration-300">
                            <i data-lucide="chevron-right" class="w-4 h-4 text-gray-400"></i>
                        </div>
                    </a>

                </div>
            </div>

            <!-- Category 3 -->
            <div class="animate-fade-in-up" style="animation-delay: 200ms;">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4 ml-2">Inventory & Logistics <span class="text-xs font-semibold text-gray-400 dark:text-gray-500 ml-2">Coming soon</span></h2>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">

                    <div class="relative flex flex-col items-center justify-center p-6 bg-white dark:bg-[#1C1C1E] rounded-[28px] shadow-[0_2px_10px_-4px_rgba(0,0,0,0.05)] dark:shadow-none border border-gray-100 dark:border-white/5 opacity-60 cursor-not-allowed overflow-hidden">
                        <div class="w-16 h-16 rounded-[20px] flex items-center justify-center mb-4 bg-pink-500 text-white shadow-inner">
                            <i data-lucide="hash" class="w-[30px] h-[30px]"></i>
                        </div>
                        <span class="text-sm font-semibold text-gray-800 dark:text-gray-200 text-center leading-tight">Batch Table</span>
                        <span class="text-[10px] font-bold text-gray-400 mt-1 uppercase tracking-wider">Coming soon</span>
                    </div>

                    <div class="relative flex flex-col items-center justify-center p-6 bg-white dark:bg-[#1C1C1E] rounded-[28px] shadow-[0_2px_10px_-4px_rgba(0,0,0,0.05)] dark:shadow-none border border-gray-100 dark:border-white/5 opacity-60 cursor-not-allowed overflow-hidden">
                        <div class="w-16 h-16 rounded-[20px] flex items-center justify-center mb-4 bg-yellow-500 text-white shadow-inner">
                            <i data-lucide="clipboard-list" class="w-[30px] h-[30px]"></i>
                        </div>
                        <span class="text-sm font-semibold text-gray-800 dark:text-gray-200 text-center leading-tight">Stock Ledger</span>
                        <span class="text-[10px] font-bold text-gray-400 mt-1 uppercase tracking-wider">Coming soon</span>
                    </div>

                    <div class="relative flex flex-col items-center justify-center p-6 bg-white dark:bg-[#1C1C1E] rounded-[28px] shadow-[0_2px_10px_-4px_rgba(0,0,0,0.05)] dark:shadow-none border border-gray-100 dark:border-white/5 opacity-60 cursor-not-allowed overflow-hidden">
                        <div class="w-16 h-16 rounded-[20px] flex items-center justify-center mb-4 bg-cyan-500 text-white shadow-inner">
                            <i data-lucide="send" class="w-[30px] h-[30px]"></i>
                        </div>
                        <span class="text-sm font-semibold text-gray-800 dark:text-gray-200 text-center leading-tight">Dispatches</span>
                        <span class="text-[10px] font-bold text-gray-400 mt-1 uppercase tracking-wider">Coming soon</span>
                    </div>

                </div>
            </div>

            <div class="animate-fade-in-up" style="animation-delay: 300ms;">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4 ml-2">Finance & Administration <span class="text-xs font-semibold text-gray-400 dark:text-gray-500 ml-2">Coming soon</span></h2>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">

                    <div class="relative flex flex-col items-center justify-center p-6 bg-white dark:bg-[#1C1C1E] rounded-[28px] shadow-[0_2px_10px_-4px_rgba(0,0,0,0.05)] dark:shadow-none border border-gray-100 dark:border-white/5 opacity-60 cursor-not-allowed overflow-hidden">
                        <div class="w-16 h-16 rounded-[20px] flex items-center justify-center mb-4 bg-emerald-500 text-white shadow-inner">
                            <i data-lucide="book-text" class="w-[30px] h-[30px]"></i>
                        </div>
                        <span class="text-sm font-semibold text-gray-800 dark:text-gray-200 text-center leading-tight">Order Book</span>
                        <span class="text-[10px] font-bold text-gray-400 mt-1 uppercase tracking-wider">Coming soon</span>
                    </div>

                    <div class="relative flex flex-col items-center justify-center p-6 bg-white dark:bg-[#1C1C1E] rounded-[28px] shadow-[0_2px_10px_-4px_rgba(0,0,0,0.05)] dark:shadow-none border border-gray-100 dark:border-white/5 opacity-60 cursor-not-allowed overflow-hidden">
                        <div class="w-16 h-16 rounded-[20px] flex items-center justify-center mb-4 bg-teal-600 text-white shadow-inner">
                            <i data-lucide="landmark" class="w-[30px] h-[30px]"></i>
                        </div>
                        <span class="text-sm font-semibold text-gray-800 dark:text-gray-200 text-center leading-tight">Banks</span>
                        <span class="text-[10px] font-bold text-gray-400 mt-1 uppercase tracking-wider">Coming soon</span>
                    </div>

                    <div class="relative flex flex-col items-center justify-center p-6 bg-white dark:bg-[#1C1C1E] rounded-[28px] shadow-[0_2px_10px_-4px_rgba(0,0,0,0.05)] dark:shadow-none border border-gray-100 dark:border-white/5 opacity-60 cursor-not-allowed overflow-hidden">
                        <div class="w-16 h-16 rounded-[20px] flex items-center justify-center mb-4 bg-blue-600 text-white shadow-inner">
                            <i data-lucide="scale" class="w-[30px] h-[30px]"></i>
                        </div>
                        <span class="text-sm font-semibold text-gray-800 dark:text-gray-200 text-center leading-tight">Salesman Vs Payment</span>
                        <span class="text-[10px] font-bold text-gray-400 mt-1 uppercase tracking-wider">Coming soon</span>
                    </div>

                    <div class="relative flex flex-col items-center justify-center p-6 bg-white dark:bg-[#1C1C1E] rounded-[28px] shadow-[0_2px_10px_-4px_rgba(0,0,0,0.05)] dark:shadow-none border border-gray-100 dark:border-white/5 opacity-60 cursor-not-allowed overflow-hidden">
                        <div class="w-16 h-16 rounded-[20px] flex items-center justify-center mb-4 bg-slate-600 text-white shadow-inner">
                            <i data-lucide="building" class="w-[30px] h-[30px]"></i>
                        </div>
                        <span class="text-sm font-semibold text-gray-800 dark:text-gray-200 text-center leading-tight">Firms</span>
                        <span class="text-[10px] font-bold text-gray-400 mt-1 uppercase tracking-wider">Coming soon</span>
                    </div>

                </div>
            </div>
            <div class="animate-fade-in-up" style="animation-delay: 400ms;">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4 ml-2">Assets</h2>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                    <a href="/employees" class="group relative flex flex-col items-center justify-center p-6 bg-white dark:bg-[#1C1C1E] rounded-[28px] shadow-[0_2px_10px_-4px_rgba(0,0,0,0.05)] dark:shadow-none border border-gray-100 dark:border-white/5 hover:shadow-lg dark:hover:bg-[#2C2C2E] transition-all duration-300 ease-out transform hover:-translate-y-1 active:scale-95 overflow-hidden">
                        <div class="w-16 h-16 rounded-[20px] flex items-center justify-center mb-4 bg-indigo-400 text-white shadow-inner group-hover:scale-110 transition-transform duration-300 ease-out">
                            <i data-lucide="users" class="w-[30px] h-[30px]"></i>
                        </div>
                        <span class="text-sm font-semibold text-gray-800 dark:text-gray-200 text-center leading-tight">Employees</span>
                        <div class="absolute bottom-3 opacity-0 group-hover:opacity-100 transform translate-y-2 group-hover:translate-y-0 transition-all duration-300">
                            <i data-lucide="chevron-right" class="w-4 h-4 text-gray-400"></i>
                        </div>
                    </a>

                    <a href="/parties" class="group relative flex flex-col items-center justify-center p-6 bg-white dark:bg-[#1C1C1E] rounded-[28px] shadow-[0_2px_10px_-4px_rgba(0,0,0,0.05)] dark:shadow-none border border-gray-100 dark:border-white/5 hover:shadow-lg dark:hover:bg-[#2C2C2E] transition-all duration-300 ease-out transform hover:-translate-y-1 active:scale-95 overflow-hidden">
                        <div class="w-16 h-16 rounded-[20px] flex items-center justify-center mb-4 bg-slate-600 text-white shadow-inner group-hover:scale-110 transition-transform duration-300 ease-out">
                            <i data-lucide="building" class="w-[30px] h-[30px]"></i>
                        </div>
                        <span class="text-sm font-semibold text-gray-800 dark:text-gray-200 text-center leading-tight">Parties</span>
                        <div class="absolute bottom-3 opacity-0 group-hover:opacity-100 transform translate-y-2 group-hover:translate-y-0 transition-all duration-300">
                            <i data-lucide="chevron-right" class="w-4 h-4 text-gray-400"></i>
                        </div>
                    </a>

                    <a href="/products" class="group relative flex flex-col items-center justify-center p-6 bg-white dark:bg-[#1C1C1E] rounded-[28px] shadow-[0_2px_10px_-4px_rgba(0,0,0,0.05)] dark:shadow-none border border-gray-100 dark:border-white/5 hover:shadow-lg dark:hover:bg-[#2C2C2E] transition-all duration-300 ease-out transform hover:-translate-y-1 active:scale-95 overflow-hidden">
                        <div class="w-16 h-16 rounded-[20px] flex items-center justify-center mb-4 bg-orange-500 text-white shadow-inner group-hover:scale-110 transition-transform duration-300 ease-out">
                            <i data-lucide="package" class="w-[30px] h-[30px]"></i>
                        </div>
                        <span class="text-sm font-semibold text-gray-800 dark:text-gray-200 text-center leading-tight">Products</span>
                        <div class="absolute bottom-3 opacity-0 group-hover:opacity-100 transform translate-y-2 group-hover:translate-y-0 transition-all duration-300">
                            <i data-lucide="chevron-right" class="w-4 h-4 text-gray-400"></i>
                        </div>
                    </a>

                    <a href="/cities" class="group relative flex flex-col items-center justify-center p-6 bg-white dark:bg-[#1C1C1E] rounded-[28px] shadow-[0_2px_10px_-4px_rgba(0,0,0,0.05)] dark:shadow-none border border-gray-100 dark:border-white/5 hover:shadow-lg dark:hover:bg-[#2C2C2E] transition-all duration-300 ease-out transform hover:-translate-y-1 active:scale-95 overflow-hidden">
                        <div class="w-16 h-16 rounded-[20px] flex items-center justify-center mb-4 bg-cyan-500 text-white shadow-inner group-hover:scale-110 transition-transform duration-300 ease-out">
                            <i data-lucide="map-pin" class="w-[30px] h-[30px]"></i>
                        </div>
                        <span class="text-sm font-semibold text-gray-800 dark:text-gray-200 text-center leading-tight">Cities</span>
                        <div class="absolute bottom-3 opacity-0 group-hover:opacity-100 transform translate-y-2 group-hover:translate-y-0 transition-all duration-300">
                            <i data-lucide="chevron-right" class="w-4 h-4 text-gray-400"></i>
                        </div>
                    </a>

                    <a href="/transports" class="group relative flex flex-col items-center justify-center p-6 bg-white dark:bg-[#1C1C1E] rounded-[28px] shadow-[0_2px_10px_-4px_rgba(0,0,0,0.05)] dark:shadow-none border border-gray-100 dark:border-white/5 hover:shadow-lg dark:hover:bg-[#2C2C2E] transition-all duration-300 ease-out transform hover:-translate-y-1 active:scale-95 overflow-hidden">
                        <div class="w-16 h-16 rounded-[20px] flex items-center justify-center mb-4 bg-green-500 text-white shadow-inner group-hover:scale-110 transition-transform duration-300 ease-out">
                            <i data-lucide="truck" class="w-[30px] h-[30px]"></i>
                        </div>
                        <span class="text-sm font-semibold text-gray-800 dark:text-gray-200 text-center leading-tight">Transports</span>
                        <div class="absolute bottom-3 opacity-0 group-hover:opacity-100 transform translate-y-2 group-hover:translate-y-0 transition-all duration-300">
                            <i data-lucide="chevron-right" class="w-4 h-4 text-gray-400"></i>
                        </div>
                    </a>
                </div>
            </div>

        </div>

    </main>

    <!-- Vanilla JavaScript for interactivity -->
    <script>
        // Initialize Lucide Icons
        lucide.createIcons();

        // Handle Date/Time
        function updateDate() {
            const dateElement = document.getElementById('current-date');
            const now = new Date();
            const options = {
                weekday: 'long',
                month: 'long',
                day: 'numeric'
            };
            dateElement.textContent = now.toLocaleDateString('en-US', options);
        }
        updateDate();
        setInterval(updateDate, 60000); // Update every minute just in case
    </script>
</body>

</html>
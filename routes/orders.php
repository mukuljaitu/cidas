<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Book</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f5f5f7;
            color: #1d1d1f;
        }

        .glass-panel {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-incomplete {
            background: #fff4e5;
            color: #b76e00;
        }

        .status-complete {
            background: #e6f4ea;
            color: #1e7e34;
        }

        .status-okay {
            background: #f3e8ff;
            /* Light purple shade */
            color: #7e22ce;
            /* Darker purple text */
        }

        .side-drawer {
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            transform: translateX(100%);
        }

        .side-drawer.open {
            transform: translateX(0);
        }

        .order-row:hover {
            background-color: rgba(0, 102, 204, 0.04);
            cursor: pointer;
        }

        .type-toggle {
            display: flex;
            background: #f1f1f1;
            padding: 2px;
            border-radius: 10px;
        }

        .type-toggle button {
            flex: 1;
            padding: 6px;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 600;
            transition: all 0.2s;
            color: #6b7280;
        }

        .type-toggle button.active {
            background: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            color: #2563eb;
        }

        @keyframes slideUp {
            from {
                transform: translateY(20px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .animate-slide-up {
            animation: slideUp 0.3s ease-out forwards;
        }

        .filter-card.active-filter {
            border-color: #2563eb !important;
            background-color: #f8faff;
            box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.1);
        }

        .cat-filter-btn {
            color: #64748b;
            background: transparent;
        }

        .cat-filter-btn:hover {
            background: #f8fafc;
            color: #1e293b;
        }

        .cat-filter-btn.active-cat {
            background: var(--active-bg, #2563eb);
            color: var(--active-fg, white);
            box-shadow: var(--active-shadow, 0 4px 12px rgba(37, 99, 235, 0.2));
        }

        .cat-filter-btn.active-cat i {
            color: var(--active-icon, var(--active-fg, white)) !important;
        }

        body.orders-focus #ordersSection {
            padding: 12px !important;
        }

        body.orders-focus #ordersKpiRow,
        body.orders-focus #ordersCategoryRow {
            display: none !important;
        }

        /* Sidebar icon active animation */
        .sidebar-btn {
            transition: all 0.25s ease;
        }

        .sidebar-btn.active {
            background-color: #eff6ff;
            /* blue-50 */
            color: #2563eb;
            /* blue-600 */
            transform: scale(1.08);
        }

        /* Smooth content switch */
        .view-section {
            transition: opacity 0.25s ease, transform 0.25s ease;
        }

        .view-hidden {
            opacity: 0;
            transform: translateY(8px);
            pointer-events: none;
        }

        .view-visible {
            opacity: 1;
            transform: translateY(0);
        }

        .chart-type-btn {
            transition: all 0.2s ease;
        }

        .chart-type-btn.active {
            background: #2563eb;
            color: white;
        }

        /* Matching Bank System Image Uploader */
        #imageContainer {
            position: relative;
            margin-top: 10px;
            border: 2px dashed #e5e7eb;
            border-radius: 16px;
            overflow: hidden;
            background: #f8f9fa;
            min-height: 150px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        #imageContainer:hover {
            border-color: #2563eb;
            background: #f1f5f9;
        }

        #imagePreview {
            max-width: 100%;
            max-height: 300px;
            display: none;
            border-radius: 12px;
        }

        #imageActions {
            display: none;
            position: absolute;
            top: 10px;
            right: 10px;
            background: white;
            padding: 5px;
            border-radius: 50px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            gap: 5px;
        }

        .img-btn {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: none;
            background: transparent;
            cursor: pointer;
        }

        .img-btn:hover {
            background: #f3f4f6;
        }

        /* Lightbox Overlay */
        #lightboxOverlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.85);
            backdrop-filter: blur(10px);
            z-index: 3000;
            align-items: center;
            justify-content: center;
            cursor: zoom-out;
        }

        #lightboxOverlay.active {
            display: flex;
            animation: fadeIn 0.2s ease-out;
        }

        #lightboxImage {
            max-width: 90%;
            max-height: 90vh;
            border-radius: 8px;
            box-shadow: 0 0 40px rgba(0, 0, 0, 0.5);
            transform: scale(0.9);
            transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        #lightboxOverlay.active #lightboxImage {
            transform: scale(1);
        }

        .lightbox-close-hint {
            position: absolute;
            top: 20px;
            color: white;
            font-size: 14px;
            font-weight: 500;
            background: rgba(255, 255, 255, 0.1);
            padding: 8px 16px;
            border-radius: 50px;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        /* Table View Styles */
        .entries-table-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            border: 1px solid #e5e7eb;
            overflow: hidden;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .entries-toolbar {
            padding: 16px 24px;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            gap: 12px;
            align-items: center;
            flex-wrap: wrap;
            background: white;
        }

        .filter-chip {
            display: inline-flex;
            align-items: center;
            height: 36px;
            padding: 0 16px;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 20px;
            font-size: 14px;
            color: #4b5563;
            cursor: pointer;
            transition: all 0.2s;
            position: relative;
        }

        .filter-chip:hover {
            background: #f9fafb;
            border-color: #d1d5db;
        }

        .filter-chip.active {
            background: #eff6ff;
            border-color: #2563eb;
            color: #2563eb;
        }

        .entries-table-container {
            flex: 1;
            overflow: auto;
        }

        .entries-table {
            width: 100%;
            border-collapse: collapse;
        }

        .entries-table th {
            text-align: left;
            padding: 16px 24px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            color: #6b7280;
            background: #f9fafb;
            border-bottom: 1px solid #e5e7eb;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .entries-table td {
            padding: 16px 24px;
            font-size: 14px;
            color: #1f2937;
            border-bottom: 1px solid #f3f4f6;
        }

        .entries-table tr:hover {
            background: #f9fafb;
        }

        /* Popover Styles */
        .popover {
            display: none;
            position: absolute;
            top: 45px;
            left: 0;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            border: 1px solid #e5e7eb;
            z-index: 100;
            min-width: 280px;
            padding: 8px 0;
        }

        .popover.show {
            display: block;
        }

        .popover-header {
            padding: 12px 16px;
            font-size: 11px;
            font-weight: 700;
            color: #6b7280;
            text-transform: uppercase;
            border-bottom: 1px solid #f3f4f6;
        }

        .popover-content {
            padding: 16px;
        }

        .popover-option {
            padding: 8px 12px;
            cursor: pointer;
            border-radius: 6px;
            font-size: 14px;
        }

        .popover-option:hover {
            background: #f3f4f6;
        }

        /* Apple Style Spinner */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(5px);
            z-index: 9999;
            display: flex;
            justify-content: center;
            align-items: center;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }

        .loading-overlay.visible {
            opacity: 1;
            pointer-events: all;
        }

        .spinner {
            width: 40px;
            height: 40px;
            position: relative;
        }

        .spinner-blade {
            position: absolute;
            left: 44.5%;
            top: 37%;
            width: 10%;
            height: 25%;
            border-radius: 50%/20%;
            animation: spinner-fade 1s linear infinite;
            background-color: #8e8e93;
        }

        @keyframes spinner-fade {
            0% {
                opacity: 1;
            }

            100% {
                opacity: 0.25;
            }
        }
    </style>
</head>

<body class="overflow-hidden h-screen flex flex-col">
    <!-- Loading Wheel -->
    <div id="pageLoader" class="loading-overlay">
        <div class="spinner">
            <div class="spinner-blade" style="transform: rotate(0deg) translate(0, -150%); animation-delay: -1s;"></div>
            <div class="spinner-blade" style="transform: rotate(30deg) translate(0, -150%); animation-delay: -0.9167s;"></div>
            <div class="spinner-blade" style="transform: rotate(60deg) translate(0, -150%); animation-delay: -0.8333s;"></div>
            <div class="spinner-blade" style="transform: rotate(90deg) translate(0, -150%); animation-delay: -0.75s;"></div>
            <div class="spinner-blade" style="transform: rotate(120deg) translate(0, -150%); animation-delay: -0.6667s;"></div>
            <div class="spinner-blade" style="transform: rotate(150deg) translate(0, -150%); animation-delay: -0.5833s;"></div>
            <div class="spinner-blade" style="transform: rotate(180deg) translate(0, -150%); animation-delay: -0.5s;"></div>
            <div class="spinner-blade" style="transform: rotate(210deg) translate(0, -150%); animation-delay: -0.4167s;"></div>
            <div class="spinner-blade" style="transform: rotate(240deg) translate(0, -150%); animation-delay: -0.3333s;"></div>
            <div class="spinner-blade" style="transform: rotate(270deg) translate(0, -150%); animation-delay: -0.25s;"></div>
            <div class="spinner-blade" style="transform: rotate(300deg) translate(0, -150%); animation-delay: -0.1667s;"></div>
            <div class="spinner-blade" style="transform: rotate(330deg) translate(0, -150%); animation-delay: -0.0833s;"></div>
        </div>
    </div>

    <header class="h-16 glass-panel border-b flex items-center justify-between px-8 z-30">
        <div class="flex items-center gap-4">
            <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center text-white"><img src="logo.png" width="60px" /></div>
            <h1 class="text-xl font-semibold tracking-tight">Order Book</h1>
        </div>
        <div class="flex items-center gap-6">
            <div class="relative">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" id="searchInput" placeholder="Search Party, Salesman, Product, or Bill No..." class="pl-10 pr-4 py-2 bg-gray-100 border-none rounded-full text-sm focus:ring-2 focus:ring-blue-500 transition-all w-64">
            </div>
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-[10px] font-bold text-blue-600">PS</div>
                <span class="text-sm font-medium">Pankaj Singla</span>
            </div>
        </div>
    </header>

    <main class="flex-1 flex overflow-hidden">
        <aside class="w-20 border-r bg-white flex flex-col items-center py-8 gap-8">
            <button
                id="ordersBtn"
                class="sidebar-btn w-12 h-12 rounded-xl text-gray-400 hover:bg-gray-50 flex items-center justify-center active"
                onclick="showOrders()">
                <i class="fas fa-list-ul"></i>
            </button>


            <button
                id="chartsBtn"
                class="sidebar-btn w-12 h-12 rounded-xl text-gray-400 hover:bg-gray-50 flex items-center justify-center"
                onclick="showCharts()">
                <i class="fas fa-chart-line"></i>
            </button>

            <button
                id="entriesBtn"
                class="sidebar-btn w-12 h-12 rounded-xl text-gray-400 hover:bg-gray-50 flex items-center justify-center"
                onclick="showEntries()">
                <i class="fas fa-table"></i>
            </button>



        </aside>

        <section class="flex-1 relative p-8 overflow-y-auto">
            <div id="chartsSection"
                class="view-section absolute inset-0 p-8 view-hidden"
                style="display:none;">

                <!-- Header -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold">Analytics Overview</h2>
                    <p class="text-sm text-gray-500">Live insights from order book</p>
                </div>

                <!-- KPI Cards -->
                <div class="grid grid-cols-4 gap-6 mb-10">
                    <div class="bg-white rounded-2xl p-6 shadow-sm border">
                        <p class="text-xs text-gray-500 uppercase font-semibold">Total Orders</p>
                        <p class="text-3xl font-bold mt-2" id="kpiTotal">0</p>
                    </div>
                    <div class="bg-white rounded-2xl p-6 shadow-sm border">
                        <p class="text-xs text-amber-600 uppercase font-semibold">Pending</p>
                        <p class="text-3xl font-bold mt-2" id="kpiPending">0</p>
                    </div>
                    <div class="bg-white rounded-2xl p-6 shadow-sm border">
                        <p class="text-xs text-blue-600 uppercase font-semibold">Completed</p>
                        <p class="text-3xl font-bold mt-2" id="kpiCompleted">0</p>
                    </div>
                    <div class="bg-white rounded-2xl p-6 shadow-sm border">
                        <p class="text-xs text-red-600 uppercase font-semibold">Cancelled</p>
                        <p class="text-3xl font-bold mt-2" id="kpiCancelled">0</p>
                    </div>
                </div>
                <div class="mb-6 flex gap-3">
                    <button onclick="setChartType('All', this)"
                        class="chart-type-btn px-5 py-2 rounded-xl bg-white border font-semibold active">
                        All
                    </button>
                    <button onclick="setChartType('Fer', this)"
                        class="chart-type-btn px-5 py-2 rounded-xl bg-white border font-semibold">
                        Fertilizers
                    </button>
                    <button onclick="setChartType('Pes', this)"
                        class="chart-type-btn px-5 py-2 rounded-xl bg-white border font-semibold">
                        Pesticides
                    </button>
                </div>

                <!-- Charts -->
                <div class="grid grid-cols-2 gap-8">
                    <div class="bg-white rounded-3xl p-6 shadow-sm border">
                        <h3 class="font-semibold mb-4">Orders by Status</h3>
                        <canvas id="statusChart"></canvas>
                    </div>

                    <div class="bg-white rounded-3xl p-6 shadow-sm border">
                        <h3 class="font-semibold mb-4">Orders Timeline</h3>
                        <canvas id="timelineChart"></canvas>
                    </div>
                </div>

                <!-- Salesman Leaderboard -->
                <div class="bg-white rounded-3xl p-6 shadow-sm border mt-10">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="font-semibold text-lg">🏆 Salesman Leaderboard</h3>
                        <span class="text-xs text-gray-400">Based on current filters</span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-gray-400 text-xs uppercase border-b">
                                    <th class="text-left py-3">Salesman</th>
                                    <th class="text-center py-3">Total</th>
                                    <th class="text-center py-3 text-blue-600">Completed</th>
                                    <th class="text-center py-3 text-amber-600">Pending</th>
                                    <th class="text-center py-3 text-red-600">Cancelled</th>
                                </tr>
                            </thead>
                            <tbody id="salesmanLeaderboard"></tbody>
                        </table>
                    </div>
                </div>

                <!-- Product Insights -->
                <div class="grid grid-cols-2 gap-8 mt-10">

                    <!-- Top Selling Products -->
                    <div class="bg-white rounded-3xl p-6 shadow-sm border">
                        <h3 class="font-semibold mb-4">📦 Top Selling Products</h3>
                        <canvas id="topProductsChart"></canvas>
                    </div>
                    <!-- Popular Pack Sizes -->
                    <div class="bg-white rounded-3xl p-6 shadow-sm border">
                        <h3 class="font-semibold mb-4">📏 Popular Pack Sizes</h3>
                        <canvas id="packSizeChart"></canvas>
                    </div>


                </div>


                <!-- Pending Products -->
                <div class="bg-white rounded-3xl p-6 shadow-sm border mt-8">
                    <h3 class="font-semibold mb-4">⏳ Pending Products (Billing Risk)</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-gray-400 text-xs uppercase border-b">
                                    <th class="text-left py-3">Product</th>
                                    <th class="text-center py-3">Pending Qty</th>
                                </tr>
                            </thead>
                            <tbody id="pendingProductsTable"></tbody>
                        </table>
                    </div>
                </div>

            </div>


            <div id="ordersSection"
                class="view-section absolute inset-0 p-8 view-visible"
                style="display:block;">
                <div class="flex flex-col h-full overflow-hidden">


                    <div id="ordersKpiRow" class="grid grid-cols-7 gap-6 mb-8">
                        <div onclick="filterByStatus('All', this)" class="filter-card active-filter bg-white p-6 rounded-2xl shadow-sm border-2 border-transparent cursor-pointer hover:border-blue-500 transition-all active:scale-95">
                            <p class="text-gray-500 text-xs font-semibold uppercase">Total Orders</p>
                            <p class="text-3xl font-bold mt-1" id="statTotal">0</p>
                        </div>
                        <div onclick="filterByStatus('Incomplete', this)" class="filter-card bg-white p-6 rounded-2xl shadow-sm border-2 border-transparent cursor-pointer hover:border-amber-500 transition-all active:scale-95">
                            <p class="text-amber-600 text-xs font-semibold uppercase">Pending Billing</p>
                            <p class="text-3xl font-bold mt-1" id="statIncomplete">0</p>
                        </div>
                        <div onclick="filterByStatus('Finalized', this)" class="filter-card bg-white p-6 rounded-2xl shadow-sm border-2 border-transparent cursor-pointer hover:border-blue-600 transition-all active:scale-95">
                            <p class="text-blue-600 text-xs font-semibold uppercase">Completed</p>
                            <p class="text-3xl font-bold mt-1" id="statComplete">0</p>
                        </div>
                        <div onclick="filterByStatus('Received', this)" class="filter-card bg-white p-6 rounded-2xl shadow-sm border-2 border-transparent cursor-pointer hover:border-green-600 transition-all active:scale-95">
                            <p class="text-green-600 text-xs font-semibold uppercase">Recieving</p>
                            <p class="text-3xl font-bold mt-1" id="statReceived">0</p>
                        </div>
                        <div onclick="filterByStatus('Okay', this)" class="filter-card bg-white p-6 rounded-2xl shadow-sm border-2 border-transparent cursor-pointer hover:border-purple-600 transition-all active:scale-95">
                            <p class="text-purple-600 text-xs font-semibold uppercase">Okay</p>
                            <p class="text-3xl font-bold mt-1" id="statOkay">0</p>
                        </div>
                        <div onclick="filterByStatus('Cancelled', this)" class="filter-card bg-white p-6 rounded-2xl shadow-sm border-2 border-transparent cursor-pointer hover:border-red-500 transition-all active:scale-95">
                            <p class="text-red-600 text-xs font-semibold uppercase">Cancelled</p>
                            <p class="text-3xl font-bold mt-1" id="statCancelled">0</p>
                        </div>
                        <div class="bg-white p-6 rounded-2xl border-2 border-dashed border-gray-200 flex items-center justify-center group hover:border-blue-500 transition-colors cursor-pointer" onclick="toggleModal('orderModal')">
                            <div class="text-center">
                                <i class="fas fa-plus text-gray-400 group-hover:text-blue-500 mb-2"></i>
                                <p class="text-sm font-medium text-gray-500 group-hover:text-blue-500">New Entry</p>
                            </div>
                        </div>
                    </div>
                    <div id="ordersCategoryRow" class="mb-6 flex justify-center">
                        <div class="bg-white px-6 py-3 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4">
                            <div class="flex items-center gap-2">
                                <button data-filter-group="category" style="--active-bg:#1d4ed8;--active-fg:#ffffff;--active-shadow:0 4px 12px rgba(29,78,216,0.25);" onclick="filterByCategory('All', this); filterByBillType('All')" class="cat-filter-btn active-cat px-6 py-2 rounded-xl text-sm font-bold transition-all">
                                    All Orders
                                </button>
                                <button data-filter-group="category" style="--active-bg:#16a34a;--active-fg:#ffffff;--active-shadow:0 4px 12px rgba(22,163,74,0.25);" onclick="filterByCategory('Fer', this)" class="cat-filter-btn px-6 py-2 rounded-xl text-sm font-bold transition-all flex items-center gap-2">
                                    <i class="fas fa-seedling text-green-500"></i> Fertilizers
                                </button>
                                <button data-filter-group="category" style="--active-bg:#0ea5e9;--active-fg:#ffffff;--active-shadow:0 4px 12px rgba(14,165,233,0.25);" onclick="filterByCategory('Pes', this)" class="cat-filter-btn px-6 py-2 rounded-xl text-sm font-bold transition-all flex items-center gap-2">
                                    <i class="fas fa-flask text-blue-500"></i> Pesticides
                                </button>
                                <button data-filter-group="billType" style="--active-bg:#f59e0b;--active-fg:#111827;--active-icon:#111827;--active-shadow:0 4px 12px rgba(245,158,11,0.25);" onclick="filterByBillType('A', this)" class="cat-filter-btn px-6 py-2 rounded-xl text-sm font-bold transition-all flex items-center gap-2">
                                    Type A
                                </button>
                                <button data-filter-group="billType" style="--active-bg:#7c3aed;--active-fg:#ffffff;--active-shadow:0 4px 12px rgba(124,58,237,0.25);" onclick="filterByBillType('B', this)" class="cat-filter-btn px-6 py-2 rounded-xl text-sm font-bold transition-all flex items-center gap-2">
                                    Type B
                                </button>
                                <button data-filter-group="category" style="--active-bg:#e11d48;--active-fg:#ffffff;--active-shadow:0 4px 12px rgba(225,29,72,0.25);" id="missingFilesBtn" onclick="filterByCategory('MissingFiles', this)" class="cat-filter-btn px-6 py-2 rounded-xl text-sm font-bold transition-all flex items-center gap-2 text-red-600 hover:bg-red-50">
                                    <i class="fas fa-exclamation-triangle"></i> Missing Files
                                </button>
                                <button data-filter-group="sort" style="--active-bg:#334155;--active-fg:#ffffff;--active-shadow:0 4px 12px rgba(51,65,85,0.25);" id="billNoSortBtn" onclick="toggleBillNoSort()" class="cat-filter-btn px-6 py-2 rounded-xl text-sm font-bold transition-all flex items-center gap-2">
                                    <i class="fas fa-sort"></i> Bill No
                                </button>
                            </div>
                        </div>
                    </div>
                    <div id="ordersTableCard" class="flex-1 bg-white rounded-3xl shadow-sm border border-gray-100 flex flex-col overflow-hidden min-h-0">
                        <div class="px-6 py-4 border-b flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <h2 class="font-bold text-lg">Order Records</h2>
                                <button id="ordersFocusBtn" onclick="toggleOrdersFocus()" class="w-9 h-9 rounded-lg bg-gray-50 hover:bg-gray-100 border border-gray-200 text-gray-600 flex items-center justify-center">
                                    <i class="fas fa-expand"></i>
                                </button>
                            </div>
                            <div class="flex items-center gap-3">
                                <!-- Party Filter -->
                                <div class="relative">
                                    <button onclick="togglePartyDropdown()" id="partyFilterBtn" class="flex items-center justify-between min-w-[180px] max-w-[240px] px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 cursor-pointer shadow-sm">
                                        <span class="truncate mr-2" id="partyFilterLabel">All Parties</span>
                                        <i class="fas fa-chevron-down text-xs text-gray-500"></i>
                                    </button>
                                    <div id="partyDropdown" class="hidden absolute top-full left-0 mt-1 w-64 bg-white border border-gray-200 rounded-xl shadow-lg z-50 flex flex-col max-h-[300px]">
                                        <div class="p-2 border-b">
                                            <input type="text" id="partySearchInput" placeholder="Search party..." onkeyup="filterPartyList()" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        </div>
                                        <div id="partyListContainer" class="overflow-y-auto flex-1 p-1 space-y-0.5">
                                            <!-- List populated via JS -->
                                        </div>
                                    </div>
                                </div>

                                <div class="relative">
                                    <select id="salesmanFilter" onchange="filterBySalesman(this.value)" class="appearance-none pl-3 pr-8 py-2 bg-white border border-gray-200 rounded-lg text-sm font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 hover:bg-gray-50 cursor-pointer shadow-sm">
                                        <option value="All">All Salesmen</option>
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-500">
                                        <i class="fas fa-chevron-down text-xs"></i>
                                    </div>
                                </div>
                                <button onclick="exportToCSV()" class="flex items-center gap-2 px-3 py-1.5 bg-indigo-50 text-indigo-700 border border-indigo-200 rounded-lg text-sm font-bold hover:bg-indigo-100 transition-colors">
                                    <i class="fas fa-file-csv"></i> CSV
                                </button>
                                <button onclick="exportToExcel()" class="flex items-center gap-2 px-3 py-1.5 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-lg text-sm font-bold hover:bg-emerald-100 transition-colors">
                                    <i class="fas fa-file-excel"></i> Excel
                                </button>
                                <button onclick="exportToPDF()" class="flex items-center gap-2 px-3 py-1.5 bg-rose-50 text-rose-700 border border-rose-200 rounded-lg text-sm font-bold hover:bg-rose-100 transition-colors">
                                    <i class="fas fa-file-pdf"></i> PDF
                                </button>
                            </div>
                        </div>
                        <div class="overflow-y-auto flex-1">
                            <table class="w-full text-left border-collapse">
                                <thead class="sticky top-0 bg-white/95 backdrop-blur-sm z-10 border-b">
                                    <tr class="text-gray-400 text-xs font-bold uppercase tracking-wider">
                                        <th class="px-6 py-4">Order Details</th>
                                        <th class="px-6 py-4">Party / Salesman</th>
                                        <th class="px-6 py-4">Items</th>
                                        <th class="px-6 py-4">Billing Info</th>
                                        <th class="px-6 py-4">Status</th>
                                        <th class="px-6 py-4 text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="orderTableBody"></tbody>
                            </table>
                        </div>
                        <div class="px-6 py-3 border-t bg-white flex items-center justify-between gap-4">
                            <div class="text-xs font-medium text-gray-500" id="ordersPaginationInfo">Showing 0-0 of 0</div>
                            <div class="flex items-center gap-3">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-semibold text-gray-500">Rows</span>
                                    <select id="ordersPageSize" onchange="setOrdersPageSize(this.value)" class="px-2 py-1 border border-gray-200 rounded-lg text-xs font-semibold text-gray-700 bg-white hover:bg-gray-50">
                                        <option value="25">25</option>
                                        <option value="50" selected>50</option>
                                        <option value="100">100</option>
                                        <option value="200">200</option>
                                    </select>
                                </div>
                                <span class="text-xs font-semibold text-gray-600" id="ordersPageIndicator">Page 1/1</span>
                                <button id="ordersPrevBtn" onclick="goToOrdersPage(currentOrdersPage - 1)" class="px-3 py-1.5 rounded-lg border border-gray-200 text-xs font-bold text-gray-700 hover:bg-gray-50 disabled:opacity-40 disabled:cursor-not-allowed">Prev</button>
                                <button id="ordersNextBtn" onclick="goToOrdersPage(currentOrdersPage + 1)" class="px-3 py-1.5 rounded-lg border border-gray-200 text-xs font-bold text-gray-700 hover:bg-gray-50 disabled:opacity-40 disabled:cursor-not-allowed">Next</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="entriesSection"
                class="view-section absolute inset-0 p-8 view-hidden"
                style="display:none;">
                <div class="entries-table-card">
                    <div class="entries-toolbar">
                        <!-- Filter Chips -->
                        <div style="position: relative; display: flex; gap: 12px;">
                            <!-- Status Filter -->
                            <div style="position: relative;">
                                <div id="chip-status-entries" class="filter-chip" onclick="toggleEntriesPopover('popover-status-entries')">
                                    <span id="label-status-entries">Status: All</span>
                                    <i class="fas fa-chevron-down ml-2 text-xs"></i>
                                </div>
                                <div id="popover-status-entries" class="popover">
                                    <div class="popover-header">Filter by Status</div>
                                    <div class="popover-content">
                                        <div class="popover-option" onclick="filterEntriesByStatus('All')">All Statuses</div>
                                        <div class="popover-option" onclick="filterEntriesByStatus('Incomplete')">Pending Billing</div>
                                        <div class="popover-option" onclick="filterEntriesByStatus('Finalized')">Completed</div>
                                        <div class="popover-option" onclick="filterEntriesByStatus('Cancelled')">Cancelled</div>
                                        <div class="popover-option" onclick="filterEntriesByStatus('Received')">Received</div>
                                        <div class="popover-option" onclick="filterEntriesByStatus('Okay')">Okay</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Salesman Filter -->
                            <div style="position: relative;">
                                <div id="chip-salesman-entries" class="filter-chip" onclick="toggleEntriesPopover('popover-salesman-entries')">
                                    <span id="label-salesman-entries">Salesman: All</span>
                                    <i class="fas fa-chevron-down ml-2 text-xs"></i>
                                </div>
                                <div id="popover-salesman-entries" class="popover">
                                    <div class="popover-header">Filter by Salesman</div>
                                    <div class="p-2 border-b">
                                        <input type="text" placeholder="Search Salesman..." class="w-full px-2 py-1 border rounded text-sm focus:outline-none focus:ring-1 focus:ring-blue-500" onkeyup="filterPopoverList(this, 'list-salesman-entries')">
                                    </div>
                                    <div id="list-salesman-entries" class="popover-content max-h-48 overflow-y-auto">
                                        <!-- Populated by JS -->
                                    </div>
                                </div>
                            </div>

                            <!-- Party Filter -->
                            <div style="position: relative;">
                                <div id="chip-party-entries" class="filter-chip" onclick="toggleEntriesPopover('popover-party-entries')">
                                    <span id="label-party-entries">Party: All</span>
                                    <i class="fas fa-chevron-down ml-2 text-xs"></i>
                                </div>
                                <div id="popover-party-entries" class="popover">
                                    <div class="popover-header">Filter by Party</div>
                                    <div class="p-2 border-b">
                                        <input type="text" placeholder="Search Party..." class="w-full px-2 py-1 border rounded text-sm focus:outline-none focus:ring-1 focus:ring-blue-500" onkeyup="filterPopoverList(this, 'list-party-entries')">
                                    </div>
                                    <div id="list-party-entries" class="popover-content max-h-48 overflow-y-auto">
                                        <!-- Populated by JS -->
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Search -->
                        <div class="ml-auto relative">
                            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input type="text" id="entriesSearch" placeholder="Search entries..." class="pl-10 pr-4 py-2 bg-gray-50 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 w-64" onkeyup="searchEntries()">
                        </div>
                    </div>
                    <div class="entries-table-container">
                        <table class="entries-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Bill Number</th>
                                    <th>Party Name</th>
                                    <th>Salesman</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="entriesTableBody">
                                <!-- Rows will be populated by JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <div id="itemsModal" class="fixed inset-0 bg-black/60 z-[100] hidden flex items-center justify-center backdrop-blur-sm" onclick="closeItemsModal()">
        <div class="bg-white w-full max-w-2xl rounded-3xl shadow-2xl overflow-hidden animate-slide-up" onclick="event.stopPropagation()">
            <div class="p-6 border-b flex justify-between items-center bg-white">
                <div>
                    <h3 class="font-bold text-xl text-gray-900">Manage Order Items</h3>
                    <p class="text-xs text-gray-400 uppercase font-semibold tracking-wider" id="itemsModalOrderId">Order #---</p>
                </div>
                <button onclick="closeItemsModal()" class="w-10 h-10 rounded-full hover:bg-gray-100 flex items-center justify-center"><i class="fas fa-times text-gray-400"></i></button>
            </div>

            <div class="p-4 bg-blue-50/50 border-b space-y-3">
                <p class="text-[10px] font-bold text-blue-600 uppercase px-2">Quick Add Item</p>
                <div class="grid grid-cols-12 gap-2">
                    <div class="col-span-4">
                        <select id="quickAddName" class="w-full px-3 py-2 bg-white border rounded-lg text-sm" onchange="loadQuickAddPackings(this)">
                            <option value="">Select Product...</option>
                        </select>
                    </div>
                    <div class="col-span-2">
                        <select id="quickAddPacking" class="w-full px-3 py-2 bg-white border rounded-lg text-sm">
                            <option>Case</option>
                            <option>Bag</option>
                            <option>Drum</option>
                        </select>
                    </div>
                    <div class="col-span-2">
                        <select id="quickAddSize" class="w-full px-3 py-2 bg-white border rounded-lg text-sm">
                            <option value="">Select Size...</option>
                        </select>
                    </div>
                    <div class="col-span-2">
                        <input type="number" id="quickAddQty" class="w-full px-3 py-2 bg-white border rounded-lg text-sm text-center" value="1">
                    </div>
                    <div class="col-span-2">
                        <button onclick="addNewItemToOrder()" class="w-full h-full bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"><i class="fas fa-plus"></i></button>
                    </div>
                </div>
            </div>
            <div id="itemsModalList" class="p-6 space-y-3 max-h-[50vh] overflow-y-auto"></div>
        </div>
    </div>

    <div id="sideDrawerOverlay" class="fixed inset-0 bg-black/20 z-40 hidden backdrop-blur-sm" onclick="closeDrawer()"></div>
    <div id="sideDrawer" class="side-drawer fixed top-0 right-0 h-full w-[450px] bg-white shadow-2xl z-50 flex flex-col">
        <div class="p-6 border-b flex items-center justify-between">
            <div>
                <h3 class="text-xl font-bold">Billing & Transport</h3>
                <p class="text-sm text-gray-500" id="drawerOrderId">#---</p>
            </div>
            <button onclick="closeDrawer()" class="w-10 h-10 rounded-full hover:bg-gray-100 flex items-center justify-center"><i class="fas fa-times text-gray-400"></i></button>
        </div>
        <div class="flex-1 overflow-y-auto p-8 space-y-8">
            <input type="hidden" id="currentEditId">
            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase mb-3">Bill Type</label>
                <div class="type-toggle w-32"><button type="button" id="editBtnA" onclick="setEditBillType('A')">A</button><button type="button" id="editBtnB" onclick="setEditBillType('B')">B</button></div>
                <input type="hidden" id="editBillType" value="A">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div><label class="block text-xs font-bold text-gray-400 uppercase mb-2">Bill Date</label><input type="date" id="editBillDate" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl outline-none"></div>
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Bill Number</label>
                    <input type="text" id="editBillNo" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl outline-none">
                    <p id="editBillNoError" class="text-red-600 text-xs mt-1 hidden">Bill already exists</p>
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Transport / Vehicle</label>
                <select id="editTransport" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl outline-none">
                    <option value="">Loading Transporters...</option>
                </select>
            </div>
            <div class="mt-4 p-4 bg-blue-50 rounded-xl space-y-2">
                <div class="flex justify-between">
                    <span class="text-[10px] font-bold text-gray-400 uppercase">Vehicle Type:</span>
                    <span id="displayVehicle" class="text-xs font-semibold text-blue-700">---</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-[10px] font-bold text-gray-400 uppercase">Vehicle No:</span>
                    <span id="displayVehicleNo" class="text-xs font-semibold text-blue-700">---</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-[10px] font-bold text-gray-400 uppercase">Contact:</span>
                    <span id="displayContact" class="text-xs font-semibold text-blue-700">---</span>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Status</label>
                <select id="editStatus" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl outline-none">
                    <option value="Incomplete">Pending Billing</option>
                    <option value="Finalized">Completed</option>
                    <option value="Cancelled">Cancelled</option>
                    <option value="Received">Receiving Done ✅</option>
                    <option value="Okay">Okay</option>
                </select>
                </select>
            </div>
            <div id="receivingUploadSection" class="hidden animate-slide-up">
                <label class="block text-xs font-bold text-gray-400 uppercase mb-2 mt-6">Receiving Proofs (Multiple)</label>

                <div id="imageGrid" class="grid grid-cols-3 gap-2 mb-2"></div>

                <div id="imageContainer" onclick="triggerOrderFileInput()">
                    <div id="imagePlaceholder" class="text-center">
                        <i class="fas fa-images text-3xl text-gray-300"></i>
                        <p class="text-[10px] text-gray-500 font-bold uppercase mt-2">Add Images</p>
                    </div>
                </div>

                <input type="file" id="orderFileInput" name="receiving_images[]" accept="image/*,.pdf,.xls,.xlsx,.csv,application/pdf,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" class="hidden" multiple onchange="handleMultipleFiles(this)">
                <input type="hidden" id="existing_images_json" value="[]">
            </div>
        </div>
        <div class="p-6 border-t bg-gray-50 flex gap-3">
            <button onclick="deleteOrder()" class="px-6 py-3 bg-red-50 text-red-600 rounded-xl font-semibold hover:bg-red-100 transition-colors">
                <i class="fas fa-trash-alt mr-2"></i>Delete
            </button><button onclick="updateOrderDetails()" class="flex-1 py-3 bg-blue-600 text-white rounded-xl font-semibold shadow-lg">Update Record</button>
        </div>
    </div>

    <div id="orderModal" class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center opacity-0 pointer-events-none transition-opacity duration-200">
        <div class="bg-white w-[800px] max-h-[90vh] rounded-3xl overflow-hidden shadow-2xl flex flex-col">
            <div class="p-8 border-b">
                <h2 class="text-2xl font-bold">New Order Entry</h2>
            </div>
            <form id="newOrderForm" class="flex flex-col flex-1 overflow-hidden">
                <div class="p-8 overflow-y-auto space-y-6">
                    <div class="grid grid-cols-2 gap-6">
                        <div><label class="block text-xs font-bold text-gray-400 uppercase mb-2">Default Bill Type</label>
                            <div class="type-toggle w-32"><button type="button" id="btnA" class="active" onclick="setBillType('A')">A</button><button type="button" id="btnB" onclick="setBillType('B')">B</button></div><input type="hidden" id="newBillType" value="A">
                        </div>
                        <div><label class="block text-xs font-bold text-gray-400 uppercase mb-2">Bill Products</label>
                            <div class="type-toggle w-64"><button type="button" id="btnFer" class="active" onclick="setBillTypeP('Fer')">Fertilizer</button><button type="button" id="btnPes" onclick="setBillTypeP('Pes')">Pesticide</button></div><input type="hidden" id="newBillTypeP" value="Fer">
                        </div>
                    </div>
                    <div class="grid grid-cols-3 gap-6">
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Order Date</label>
                            <input type="date" id="newOrderDate" required class="w-full px-4 py-3 bg-gray-100 border-none rounded-xl">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Salesman</label>
                            <select id="newSalesman" onchange="updatePartyDropdown(this.value)" required class="w-full px-4 py-3 bg-gray-100 border-none rounded-xl">
                                <option value="">Select Salesman...</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Party Name</label>
                            <select id="newParty" required class="w-full px-4 py-3 bg-gray-100 border-none rounded-xl">
                                <option value="">Select Party...</option>
                            </select>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between border-b pb-2"><label class="text-xs font-bold text-gray-400 uppercase">Items</label><button type="button" onclick="addItemRow()" class="text-blue-600 text-xs font-bold hover:text-blue-800"><i class="fas fa-plus mr-1"></i>Add Item</button></div>
                        <div id="itemRowsContainer" class="space-y-3">
                            <div class="grid grid-cols-12 gap-3 item-row">
                                <div class="col-span-4 relative">
                                    <div class="flex items-center gap-2">
                                        <select class="prod-name w-full px-4 py-2 bg-gray-50 border rounded-lg text-sm" onchange="loadPackingsForItem(this)" required>
                                            <option value="">Select Product...</option>
                                        </select>
                                        <button type="button" onclick="openProductSearch(this)" class="flex items-center gap-2 px-3 py-2 bg-blue-50 text-blue-700 rounded-lg border border-blue-100 hover:bg-blue-100">
                                            <i class="fas fa-search text-xs"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-span-2">
                                    <select class="prod-unit w-full px-4 py-2 bg-gray-50 border rounded-lg text-sm">
                                        <option>Case</option>
                                        <option>Bag</option>
                                        <option>Drum</option>
                                    </select>
                                </div>
                                <div class="col-span-2">
                                    <select class="prod-packing w-full px-4 py-2 bg-gray-50 border rounded-lg text-sm">
                                        <option value="">Select Size...</option>
                                    </select>
                                </div>
                                <div class="col-span-3">
                                    <input type="number" class="prod-qty w-full px-4 py-2 bg-gray-50 border rounded-lg text-sm text-center" value="1" min="1" required>
                                </div>
                                <div class="col-span-1 flex items-center justify-center">
                                    <div class="w-8"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="p-8 bg-gray-50 border-t flex justify-end gap-4"><button type="button" onclick="toggleModal('orderModal')" class="px-6 py-2 text-gray-500">Cancel</button><button type="submit" class="px-10 py-3 bg-blue-600 text-white rounded-xl font-semibold">Save Order</button></div>
            </form>
        </div>
    </div>

    <div id="toast" class="fixed bottom-8 left-1/2 -translate-x-1/2 bg-gray-900 text-white px-6 py-3 rounded-full text-sm font-medium shadow-2xl translate-y-20 transition-transform duration-300 z-[100] flex items-center gap-3">
        <i class="fas fa-check-circle text-green-400"></i><span id="toastMessage">Success</span>
    </div>

    <script>
        let statusChart, timelineChart;
        let currentChartType = 'All';
        // Add this near your other global variables (around line 520)
        let currentFilterBillType = 'All';
        let ordersFocusMode = false;

        function setActiveButtonInGroup(group, element) {
            document.querySelectorAll(`.cat-filter-btn[data-filter-group="${group}"]`).forEach(btn => btn.classList.remove('active-cat'));
            if (element) element.classList.add('active-cat');
        }

        function resetOrdersPage() {
            currentOrdersPage = 1;
        }

        function updateOrdersFocusButton() {
            const btn = document.getElementById('ordersFocusBtn');
            if (!btn) return;
            const iconClass = ordersFocusMode ? 'fa-compress' : 'fa-expand';
            btn.innerHTML = `<i class="fas ${iconClass}"></i>`;
            btn.title = ordersFocusMode ? 'Collapse table' : 'Expand table';
        }

        function toggleOrdersFocus() {
            ordersFocusMode = !ordersFocusMode;
            document.body.classList.toggle('orders-focus', ordersFocusMode);
            try {
                localStorage.setItem('ordersFocusMode', ordersFocusMode ? '1' : '0');
            } catch (e) {}
            updateOrdersFocusButton();
        }

        function goToOrdersPage(page) {
            const p = parseInt(page, 10);
            currentOrdersPage = Number.isFinite(p) && p > 0 ? p : 1;
            renderTable();
        }

        function setOrdersPageSize(size) {
            const s = parseInt(size, 10);
            if (!Number.isFinite(s) || s <= 0) return;
            ordersPageSize = s;
            currentOrdersPage = 1;
            renderTable();
        }

        function updateOrdersPaginationUI(total) {
            const maxPage = Math.max(1, Math.ceil((total || 0) / (ordersPageSize || 1)));
            if (currentOrdersPage > maxPage) currentOrdersPage = maxPage;

            const start = total ? ((currentOrdersPage - 1) * ordersPageSize + 1) : 0;
            const end = total ? Math.min(total, currentOrdersPage * ordersPageSize) : 0;

            const info = document.getElementById('ordersPaginationInfo');
            if (info) info.innerText = `Showing ${start}-${end} of ${total || 0}`;

            const indicator = document.getElementById('ordersPageIndicator');
            if (indicator) indicator.innerText = `Page ${currentOrdersPage}/${maxPage}`;

            const prevBtn = document.getElementById('ordersPrevBtn');
            if (prevBtn) prevBtn.disabled = currentOrdersPage <= 1;

            const nextBtn = document.getElementById('ordersNextBtn');
            if (nextBtn) nextBtn.disabled = currentOrdersPage >= maxPage;

            const pageSizeSelect = document.getElementById('ordersPageSize');
            if (pageSizeSelect && String(pageSizeSelect.value) !== String(ordersPageSize)) {
                pageSizeSelect.value = String(ordersPageSize);
            }
        }

        function filterByBillType(billType, element) {
            currentFilterBillType = billType;
            if (billType === 'All') {
                setActiveButtonInGroup('billType', null);
                resetOrdersPage();
                renderTable();
                return;
            }
            setActiveButtonInGroup('billType', element);
            resetOrdersPage();
            renderTable();
        }

        function setChartType(type, btn) {
            currentChartType = type;

            document.querySelectorAll('.chart-type-btn')
                .forEach(b => b.classList.remove('active'));

            btn.classList.add('active');

            buildCharts(); // 👈 THIS was missing effect earlier
        }



        function buildCharts() {
            let filteredOrders = currentChartType === 'All' ?
                ordersData :
                ordersData.filter(o => o.type === currentChartType);


            // KPI
            document.getElementById('kpiTotal').innerText = filteredOrders.length;
            document.getElementById('kpiPending').innerText = filteredOrders.filter(o => o.status === 'Incomplete').length;
            document.getElementById('kpiCompleted').innerText = filteredOrders.filter(o => o.status === 'Finalized').length;
            document.getElementById('kpiCancelled').innerText = ordersData.filter(o => o.status === 'Cancelled').length;

            // STATUS CHART
            const statusCounts = {
                Pending: filteredOrders.filter(o => o.status === 'Incomplete').length,
                Completed: filteredOrders.filter(o => o.status === 'Finalized').length,
                Received: filteredOrders.filter(o => o.status === 'Received').length,
                Cancelled: filteredOrders.filter(o => o.status === 'Cancelled').length
            };


            if (statusChart) statusChart.destroy();
            statusChart = new Chart(document.getElementById('statusChart'), {
                type: 'bar',
                data: {
                    labels: Object.keys(statusCounts),
                    datasets: [{
                        data: Object.values(statusCounts),
                        backgroundColor: ['#f59e0b', '#2563eb', '#16a34a', '#dc2626']
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });

            // TIMELINE CHART
            const dateMap = {};
            filteredOrders.forEach(o => {
                dateMap[o.order_date] = (dateMap[o.order_date] || 0) + 1;
            });


            const rawDates = Object.keys(dateMap);
            const sortedRawDates = rawDates.sort((a, b) => new Date(a) - new Date(b));
            const labels = sortedRawDates.map(d => formatIndianDate(d));
            const counts = sortedRawDates.map(d => dateMap[d]);

            if (timelineChart) timelineChart.destroy();
            timelineChart = new Chart(document.getElementById('timelineChart'), {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        data: counts,
                        borderColor: '#2563eb',
                        backgroundColor: 'rgba(37,99,235,0.15)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
            buildSalesmanLeaderboard(filteredOrders);
            buildTopProductsChart(filteredOrders);
            buildPackSizeChart(filteredOrders);
            buildPendingProducts(filteredOrders);



        }

        let packSizeChart;

        async function buildPackSizeChart(filteredOrders) {
            const orderIds = filteredOrders.map(o => o.id);
            await getItemsForOrders(orderIds);

            const sizeMap = {};

            orderIds.forEach(id => {
                orderItemsCache[id].forEach(item => {
                    const size = item.Size || item.size || 'Unknown';
                    sizeMap[size] = (sizeMap[size] || 0) + Number(item.quantity);
                });
            });

            const labels = Object.keys(sizeMap);
            const data = Object.values(sizeMap);

            if (packSizeChart) packSizeChart.destroy();

            packSizeChart = new Chart(
                document.getElementById('packSizeChart'), {
                    type: 'bar',
                    data: {
                        labels,
                        datasets: [{
                            data,
                            backgroundColor: '#16a34a',
                            borderRadius: 8
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            x: {
                                beginAtZero: true
                            }
                        }
                    }
                }
            );
        }


        async function buildPendingProducts(filteredOrders) {
            const pendingOrders = filteredOrders.filter(o => o.status === 'Incomplete');
            const orderIds = pendingOrders.map(o => o.id);

            await getItemsForOrders(orderIds);

            const pendingMap = {};

            orderIds.forEach(id => {
                orderItemsCache[id].forEach(item => {
                    pendingMap[item.product] =
                        (pendingMap[item.product] || 0) + Number(item.quantity);
                });
            });

            const tbody = document.getElementById('pendingProductsTable');

            const sorted = Object.entries(pendingMap)
                .sort((a, b) => b[1] - a[1]);

            if (sorted.length === 0) {
                tbody.innerHTML = `
            <tr>
                <td colspan="2" class="text-center text-gray-400 py-6">
                    No pending products 🎉
                </td>
            </tr>`;
                return;
            }

            tbody.innerHTML = sorted.map(([product, qty]) => `
        <tr class="border-b hover:bg-gray-50">
            <td class="py-3 font-semibold">${product}</td>
            <td class="py-3 text-center font-bold text-amber-600">${qty}</td>
        </tr>
    `).join('');
        }


        const orderItemsCache = {};

        async function getItemsForOrders(orderIds) {
            const missing = orderIds.filter(id => !orderItemsCache[id]);

            if (missing.length === 0) return;

            const batchSize = 80;
            for (let i = 0; i < missing.length; i += batchSize) {
                const batch = missing.slice(i, i + batchSize);
                const res = await fetch('api.php?action=get_order_items_bulk', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        ids: batch
                    })
                });
                const byOrder = await res.json();
                batch.forEach(id => {
                    const key = String(id);
                    const items = byOrder && typeof byOrder === 'object' ? byOrder[key] : null;
                    orderItemsCache[id] = Array.isArray(items) ? items : [];
                });
            }
        }


        let topProductsChart;

        async function buildTopProductsChart(filteredOrders) {
            const orderIds = filteredOrders.map(o => o.id);
            await getItemsForOrders(orderIds);

            const productMap = {};

            orderIds.forEach(id => {
                orderItemsCache[id].forEach(item => {
                    productMap[item.product] =
                        (productMap[item.product] || 0) + Number(item.quantity);
                });
            });

            const sorted = Object.entries(productMap)
                .sort((a, b) => b[1] - a[1])
                .slice(0, 8);

            if (sorted.length === 0) return; // graceful empty

            const labels = sorted.map(p => p[0]);
            const data = sorted.map(p => p[1]);

            if (topProductsChart) topProductsChart.destroy();

            topProductsChart = new Chart(
                document.getElementById('topProductsChart'), {
                    type: 'bar',
                    data: {
                        labels,
                        datasets: [{
                            data,
                            backgroundColor: '#2563eb',
                            borderRadius: 8
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                }
            );
        }



        function buildSalesmanLeaderboard(filteredOrders) {
            const leaderboard = {};

            filteredOrders.forEach(o => {
                if (!leaderboard[o.salesman]) {
                    leaderboard[o.salesman] = {
                        total: 0,
                        completed: 0,
                        pending: 0,
                        cancelled: 0
                    };
                }

                leaderboard[o.salesman].total++;

                if (o.status === 'Finalized') leaderboard[o.salesman].completed++;
                else if (o.status === 'Incomplete') leaderboard[o.salesman].pending++;
                else if (o.status === 'Cancelled') leaderboard[o.salesman].cancelled++;
            });

            // Convert to array & sort by completed desc
            const sorted = Object.entries(leaderboard)
                .sort((a, b) => b[1].completed - a[1].completed);

            const tbody = document.getElementById('salesmanLeaderboard');
            tbody.innerHTML = sorted.map(([name, stats], index) => `
        <tr class="
    border-b transition
    ${index === 0 
        ? 'bg-yellow-50 border-l-4 border-yellow-400 hover:bg-yellow-100' 
        : index === 1 
        ? 'bg-gray-50 border-l-4 border-gray-400 hover:bg-gray-100' 
        : index === 2 
        ? 'bg-orange-50 border-l-4 border-orange-400 hover:bg-orange-100' 
        : 'hover:bg-gray-50'
    }
">
    <td class="py-3 font-semibold flex items-center gap-2">
        <span class="text-lg">
            ${index === 0 ? '🥇' : index === 1 ? '🥈' : index === 2 ? '🥉' : ''}
        </span>
        ${name}
    </td>
    <td class="text-center font-bold">${stats.total}</td>
    <td class="text-center font-bold text-blue-600">${stats.completed}</td>
    <td class="text-center font-bold text-amber-600">${stats.pending}</td>
    <td class="text-center font-bold text-red-600">${stats.cancelled}</td>
</tr>

    `).join('');
        }


        function showCharts() {
            const orders = document.getElementById('ordersSection');
            const charts = document.getElementById('chartsSection');

            // Animate orders out
            orders.classList.remove('view-visible');
            orders.classList.add('view-hidden');

            setTimeout(() => {
                orders.style.display = 'none';

                charts.style.display = 'block';
                charts.classList.remove('view-hidden');
                charts.classList.add('view-visible');

                buildCharts(); // 👈 ADD THIS
            }, 250);

            document.getElementById('chartsBtn').classList.add('active');
            document.getElementById('ordersBtn').classList.remove('active');
        }

        function showOrders() {
            const orders = document.getElementById('ordersSection');
            const charts = document.getElementById('chartsSection');

            // Animate charts out
            charts.classList.remove('view-visible');
            charts.classList.add('view-hidden');

            setTimeout(() => {
                charts.style.display = 'none';

                orders.style.display = 'block';
                orders.classList.remove('view-hidden');
                orders.classList.add('view-visible');
            }, 250);

            document.getElementById('ordersBtn').classList.add('active');
            document.getElementById('chartsBtn').classList.remove('active');
        }

        const productMasterList = ["DAP 50kg", "Urea 45kg", "Potash", "Super Phosphate", "Liquid Fertilizer 1L", "Zinc Sulfate", "Pesticide Spray A", "Herbicide Mix B"];
        let ordersData = [];
        let filteredOrders = [];
        let orderSerialMap = {};
        let currentFilterStatus = 'All';
        let currentFilterCategory = 'All'; // New variable
        let currentFilterSalesman = 'All'; // New variable
        let currentFilterParty = 'All'; // New variable
        let billNoSortDirection = null;
        let currentOrdersPage = 1;
        let ordersPageSize = 50;
        let itemsPrefetchToken = 0;
        let lastItemsPrefetchSearch = '';
        let currentViewOrderId = null;

        let salesmanRegistry = [];
        let currentTypeProducts = [];

        function formatIndianDate(dateStr) {
            if (!dateStr) return '';
            const d = new Date(dateStr);
            if (isNaN(d)) {
                const s = String(dateStr).split(/[T\s]/)[0];
                const m1 = s.match(/^(\d{4})-(\d{2})-(\d{2})$/);
                if (m1) return `${m1[3]}/${m1[2]}/${m1[1]}`;
                const m2 = s.match(/^(\d{2})[\/\-](\d{2})[\/\-](\d{4})$/);
                if (m2) return `${m2[1]}/${m2[2]}/${m2[3]}`;
                return s;
            }
            const day = String(d.getDate()).padStart(2, '0');
            const month = String(d.getMonth() + 1).padStart(2, '0');
            const year = d.getFullYear();
            return `${day}/${month}/${year}`;
        }

        function filterByCategory(category, element) {
            currentFilterCategory = category;
            setActiveButtonInGroup('category', element);
            resetOrdersPage();
            renderTable();
        }

        function toggleBillNoSort() {
            billNoSortDirection = (billNoSortDirection === 'asc') ? 'desc' : 'asc';
            updateBillNoSortButton();
            resetOrdersPage();
            renderTable();
        }

        function updateBillNoSortButton() {
            const btn = document.getElementById('billNoSortBtn');
            if (!btn) return;
            if (!billNoSortDirection) {
                btn.innerHTML = '<i class="fas fa-sort"></i> Bill No';
                return;
            }
            setActiveButtonInGroup('sort', btn);
            const arrow = billNoSortDirection === 'asc' ? '↑' : '↓';
            btn.innerHTML = `<i class="fas fa-sort"></i> Bill No ${arrow}`;
        }

        function getBillNoSortKey(billNo) {
            const raw = String(billNo || '').trim();
            if (!raw || raw === '-') return {
                year: Number.POSITIVE_INFINITY,
                seq: Number.POSITIVE_INFINITY,
                raw: raw.toLowerCase()
            };
            const parts = raw.split('/');
            const seqPart = parts.length >= 3 ? parts[2] : '';
            const yearPart = parts.length >= 4 ? parts[3] : '';
            const seq = parseInt(String(seqPart).replace(/[^0-9]/g, ''), 10);
            const year = parseInt(String(yearPart).replace(/[^0-9]/g, ''), 10);
            return {
                year: Number.isFinite(year) ? year : 0,
                seq: Number.isFinite(seq) ? seq : Number.POSITIVE_INFINITY,
                raw: raw.toLowerCase()
            };
        }

        function filterBySalesman(salesman) {
            currentFilterSalesman = salesman;
            resetOrdersPage();
            renderTable();
        }

        // --- Party Filter Logic ---
        function togglePartyDropdown() {
            const dropdown = document.getElementById('partyDropdown');
            dropdown.classList.toggle('hidden');
            if (!dropdown.classList.contains('hidden')) {
                document.getElementById('partySearchInput').focus();
            }
        }

        function filterPartyList() {
            const input = document.getElementById('partySearchInput');
            const filter = input.value.toLowerCase();
            const container = document.getElementById('partyListContainer');
            const items = container.getElementsByTagName('div');

            for (let i = 0; i < items.length; i++) {
                const txtValue = items[i].innerText || items[i].textContent;
                if (txtValue.toLowerCase().indexOf(filter) > -1) {
                    items[i].style.display = "";
                } else {
                    items[i].style.display = "none";
                }
            }
        }

        function selectParty(party) {
            currentFilterParty = party;
            const label = party === 'All' ? 'All Parties' : party;
            document.getElementById('partyFilterLabel').innerText = label;
            document.getElementById('partyDropdown').classList.add('hidden');
            resetOrdersPage();
            renderTable();
        }

        function populatePartyFilter() {
            const container = document.getElementById('partyListContainer');
            if (!container) return;

            // Get unique parties
            const parties = [...new Set(ordersData.map(o => o.party))].filter(Boolean).sort();

            let html = `<div onclick="selectParty('All')" class="px-3 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded-lg cursor-pointer">All Parties</div>`;

            parties.forEach(p => {
                const isSelected = p === currentFilterParty;
                const bgClass = isSelected ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600';
                html += `<div onclick="selectParty('${p.replace(/'/g, "\\'")}')" class="px-3 py-2 text-sm ${bgClass} rounded-lg cursor-pointer">${p}</div>`;
            });

            container.innerHTML = html;
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('partyDropdown');
            const button = document.getElementById('partyFilterBtn');
            if (!dropdown.classList.contains('hidden') && !dropdown.contains(event.target) && !button.contains(event.target)) {
                dropdown.classList.add('hidden');
            }
        });
        // --------------------------

        function populateSalesmanFilter() {
            const select = document.getElementById('salesmanFilter');
            if (!select) return;
            // Only populate if it has only the default option or is empty, OR re-populate but keep selection
            const currentVal = select.value;
            const salesmen = [...new Set(ordersData.map(o => o.salesman))].filter(Boolean).sort();

            select.innerHTML = '<option value="All">All Salesmen</option>';
            salesmen.forEach(s => {
                const opt = document.createElement('option');
                opt.value = s;
                opt.innerText = s;
                if (s === currentVal) opt.selected = true;
                select.appendChild(opt);
            });
        }

        async function loadSalesmenList() {
            try {
                const res = await fetch('api.php?action=get_salesmen');
                salesmanRegistry = await res.json();

                const salesmanSelect = document.getElementById('newSalesman');
                salesmanSelect.innerHTML = '<option value="">Select Salesman...</option>';

                salesmanRegistry.forEach(emp => {
                    const opt = document.createElement('option');
                    opt.value = emp.salesman_name;
                    opt.innerText = emp.salesman_name;
                    salesmanSelect.appendChild(opt);
                });
            } catch (err) {
                console.error("Error loading employees", err);
            }
        }

        function updatePartyDropdown(selectedSalesman) {
            const partySelect = document.getElementById('newParty');
            partySelect.innerHTML = '<option value="">Select Party...</option>';

            // Find the employee record
            const employee = salesmanRegistry.find(e => e.salesman_name === selectedSalesman);

            if (employee && employee.party_list) {
                // Split the 'cities' column by comma
                const parties = employee.party_list.split(',');

                parties.forEach(party => {
                    const trimmedParty = party.trim();
                    if (trimmedParty) {
                        const opt = document.createElement('option');
                        opt.value = trimmedParty;
                        opt.innerText = trimmedParty;
                        partySelect.appendChild(opt);
                    }
                });
            }
        }

        async function fetchOrders() {
            try {
                const res = await fetch('api.php?action=list');
                ordersData = await res.json();
                if (!Array.isArray(ordersData)) ordersData = [];

                ordersData = ordersData.filter(o => !(Number(o.is_deleted || 0) === 1));
                orderSerialMap = {};
                const total = ordersData.length;
                ordersData.forEach((o, idx) => {
                    orderSerialMap[o.id] = total - idx;
                });

                renderTable();
                updateStats();
                populateSalesmanFilter();
                populatePartyFilter(); // Added this

                // 🔥 ADD THIS
                if (document.getElementById('chartsSection').style.display === 'block') {
                    buildCharts();
                }

            } catch (err) {
                console.error("Fetch error", err);
            }
        }


        function filterByStatus(status, element) {
            currentFilterStatus = status;
            document.querySelectorAll('.filter-card').forEach(card => card.classList.remove('active-filter'));
            element.classList.add('active-filter');
            resetOrdersPage();
            renderTable();
        }

        async function renderTable() {
            const tbody = document.getElementById('orderTableBody');
            const rawSearch = document.getElementById('searchInput').value || '';
            const search = rawSearch.toLowerCase().trim();
            const digitsOnly = search.replace(/[^0-9]/g, '');
            const numericOnlySearch = /^[0-9]+$/.test(search) && digitsOnly.length > 0;

            if (search.length === 0) {
                itemsPrefetchToken++;
                lastItemsPrefetchSearch = '';
            } else if (!numericOnlySearch && search.length >= 2 && lastItemsPrefetchSearch !== search) {
                lastItemsPrefetchSearch = search;
                const token = ++itemsPrefetchToken;
                const ids = ordersData.map(o => o.id);
                const batchSize = 120;
                (async () => {
                    for (let i = 0; i < ids.length; i += batchSize) {
                        if (itemsPrefetchToken !== token) return;
                        const currentSearch = (document.getElementById('searchInput').value || '').toLowerCase().trim();
                        if (currentSearch !== search) return;
                        await getItemsForOrders(ids.slice(i, i + batchSize));
                        if (itemsPrefetchToken !== token) return;
                        const currentSearchAfter = (document.getElementById('searchInput').value || '').toLowerCase().trim();
                        if (currentSearchAfter !== search) return;
                        if (i === 0 || ((i / batchSize) % 4) === 0) renderTable();
                    }
                    if (itemsPrefetchToken !== token) return;
                    const currentSearchEnd = (document.getElementById('searchInput').value || '').toLowerCase().trim();
                    if (currentSearchEnd !== search) return;
                    renderTable();
                })();
            }

            filteredOrders = ordersData.filter(o => {
                const partyMatch = (o.party || '').toLowerCase().includes(search);
                const salesmanMatch = (o.salesman || '').toLowerCase().includes(search);
                let productMatch = false;
                if (search && orderItemsCache[o.id]) {
                    productMatch = orderItemsCache[o.id].some(it => ((it.product || '')).toLowerCase().includes(search));
                }
                const billNoStr = ((o.bill_no || '') + '').toLowerCase();
                const parts = billNoStr.split('/');
                const billMiddleRaw = parts.length >= 3 ? parts[2] : '';
                const billMiddleDigits = (billMiddleRaw || '').replace(/[^0-9]/g, '');
                const billNoMatch =
                    search === '' ?
                    false :
                    (numericOnlySearch ?
                        (billMiddleDigits === digitsOnly) :
                        billNoStr.includes(search));
                const matchesSearch = search === '' ? true : (partyMatch || salesmanMatch || productMatch || billNoMatch);
                const matchesStatus = (currentFilterStatus === 'All' || o.status === currentFilterStatus);

                let matchesCategory = true;
                if (currentFilterCategory === 'MissingFiles') {
                    // Check if status is Received AND no files uploaded
                    const hasFiles = o.receiving_image_path && o.receiving_image_path !== '[]' && o.receiving_image_path !== '' && o.receiving_image_path !== 'null';
                    matchesCategory = (o.status === 'Received' && !hasFiles);
                } else {
                    matchesCategory = (currentFilterCategory === 'All' || o.type === currentFilterCategory);
                }

                const matchesBillType = (numericOnlySearch && billNoMatch) ? true : (currentFilterBillType === 'All' || o.bill_type === currentFilterBillType);
                const matchesSalesman = (currentFilterSalesman === 'All' || o.salesman === currentFilterSalesman);
                const matchesParty = (currentFilterParty === 'All' || o.party === currentFilterParty);

                return matchesSearch && matchesStatus && matchesCategory && matchesBillType && matchesSalesman && matchesParty;
            });

            if (billNoSortDirection) {
                const dir = billNoSortDirection === 'asc' ? 1 : -1;
                filteredOrders.sort((a, b) => {
                    const ka = getBillNoSortKey(a.bill_no);
                    const kb = getBillNoSortKey(b.bill_no);
                    if (ka.year !== kb.year) return (ka.year - kb.year) * dir;
                    if (ka.seq !== kb.seq) return (ka.seq - kb.seq) * dir;
                    return ka.raw.localeCompare(kb.raw) * dir;
                });
            }

            const total = filteredOrders.length;
            updateOrdersPaginationUI(total);
            const maxPage = Math.max(1, Math.ceil((total || 0) / (ordersPageSize || 1)));
            if (currentOrdersPage > maxPage) currentOrdersPage = maxPage;
            const startIndex = (currentOrdersPage - 1) * ordersPageSize;
            const pageOrders = filteredOrders.slice(startIndex, startIndex + ordersPageSize);

            tbody.innerHTML = pageOrders.map(o => {
                // Uniform Naming and Color Logic
                let statusText = o.status;
                let badgeClass = "";

                // Locate this section inside renderTable()
                if (o.status === 'Finalized') {
                    statusText = "Completed";
                    badgeClass = "bg-blue-100 text-blue-700";
                } else if (o.status === 'Incomplete') {
                    statusText = "Pending";
                    badgeClass = "status-incomplete";
                } else if (o.status === 'Received') {
                    badgeClass = "bg-green-100 text-green-700";
                } else if (o.status === 'Cancelled') {
                    badgeClass = "bg-red-100 text-red-600";
                } else if (o.status === 'Okay') { // Add this logic
                    statusText = "Okay";
                    badgeClass = "status-okay";
                }

                return `
            <tr class="order-row border-b last:border-0" onclick="openDrawer('${o.id}')">
                <td class="px-6 py-4"><p class="font-bold text-sm">#${orderSerialMap[o.id] || ''}</p><p class="text-xs text-gray-500">${formatIndianDate(o.order_date)}</p></td>
                <td class="px-6 py-4"><p class="font-semibold">${o.party}</p><p class="text-xs text-blue-600 font-medium">${o.salesman}</p></td>
                <td class="px-6 py-4"><button onclick="viewProductList(event, '${o.id}')" class="flex items-center gap-2 px-3 py-1.5 bg-blue-50 text-blue-700 rounded-lg border border-blue-100 hover:bg-blue-100"><i class="fas fa-boxes text-xs"></i><span class="text-xs font-bold">View Items</span></button></td>
                <td class="px-6 py-4"><div class="flex items-center gap-2"><span class="text-[10px] px-1.5 py-0.5 bg-gray-100 rounded font-bold">${o.bill_type}</span><p class="text-xs font-mono">${o.bill_no || '---'}</p></div></td>
                <td class="px-6 py-4"><span class="status-badge ${badgeClass}">${statusText}</span></td>
                <td class="px-6 py-4 text-right"><i class="fas fa-chevron-right text-gray-300"></i></td>
            </tr>
        `;
            }).join('');
        }

        window.viewProductList = async (event, orderId) => {
            if (event) event.stopPropagation();
            currentViewOrderId = orderId;
            document.getElementById('itemsModalOrderId').innerText = `Order #${orderSerialMap[orderId] || ''}`;

            try {
                const res = await fetch(`api.php?action=get_details&id=${orderId}`);
                const data = await res.json();
                renderItemsModalList(data.items);
                const prodRes = await fetch(`api.php?action=get_products_by_type&type=${data.type}`);
                const products = await prodRes.json();

                const quickSelect = document.getElementById('quickAddName');
                quickSelect.innerHTML = '<option value="">Select Product...</option>';
                products.forEach(pName => {
                    const opt = document.createElement('option');
                    opt.value = pName;
                    opt.innerText = pName;
                    quickSelect.appendChild(opt);
                });
                document.getElementById('itemsModal').classList.remove('hidden');
            } catch (err) {
                showToast("Error loading items");
            }
        };

        function renderItemsModalList(items) {
            const listContainer = document.getElementById('itemsModalList');
            if (items.length === 0) {
                listContainer.innerHTML = '<p class="text-center text-gray-400 py-4">No items added yet.</p>';
                return;
            }
            listContainer.innerHTML = items.map(item => `
                <div class="flex items-center gap-3 p-3 bg-white border border-gray-100 rounded-xl hover:shadow-sm transition-shadow">
                    <div class="flex-1"><p class="font-bold text-gray-900 text-sm">${item.product}</p><p class="text-[10px] text-gray-500 uppercase font-semibold">${item.packing} | ${item.Size || 'No Size'}</p></div>
                    <div class="flex items-center gap-2">
                        <input type="number" onchange="updateItemQuantity(${item.id}, this.value)" value="${item.quantity}" class="w-16 px-2 py-1 border rounded-lg text-center font-bold text-blue-600">
                        <button onclick="removeItemFromOrder(${item.id})" class="w-8 h-8 flex items-center justify-center text-red-400 hover:bg-red-50 rounded-full"><i class="fas fa-trash-alt text-xs"></i></button>
                    </div>
                </div>
            `).join('');
        }
        async function loadTransporterDropdown() {
            try {
                const res = await fetch('api.php?action=get_transporters');
                const transporterNames = await res.json();

                const select = document.getElementById('editTransport');
                // Clear and add the default option
                select.innerHTML = '<option value="">Select Transport...</option>';

                // Map names from the database into the dropdown
                transporterNames.forEach(name => {
                    const opt = document.createElement('option');
                    opt.value = name;
                    opt.innerText = name;
                    select.appendChild(opt);
                });
                return true;
            } catch (err) {
                console.error("Failed to load transporters", err);
            }
        }
        async function updateTransporterDisplay(name) {
            if (!name) {
                document.getElementById('displayVehicle').innerText = "---";
                document.getElementById('displayVehicleNo').innerText = "---";
                document.getElementById('displayContact').innerText = "---";
                return;
            }

            try {
                const res = await fetch(`api.php?action=get_transporter_details&name=${encodeURIComponent(name)}`);
                const details = await res.json();

                if (details) {
                    document.getElementById('displayVehicle').innerText = details.vehicle || "---";
                    document.getElementById('displayVehicleNo').innerText = details.vehicle_number || "---";
                    document.getElementById('displayContact').innerText = details.contact || "---";
                }
            } catch (err) {
                console.error("Error fetching details", err);
            }
        }
        async function loadQuickAddPackings(selectElement) {
            const productName = selectElement.value;
            const sizeSelect = document.getElementById('quickAddSize');

            if (!productName) {
                sizeSelect.innerHTML = '<option value="">Select Size...</option>';
                return;
            }

            try {
                const res = await fetch(`api.php?action=get_product_packings&name=${encodeURIComponent(productName)}`);
                const data = await res.json();

                sizeSelect.innerHTML = '<option value="">Select Size...</option>';
                if (data && data.packing) {
                    const sizes = data.packing.split(',');
                    sizes.forEach(size => {
                        const opt = document.createElement('option');
                        opt.value = size.trim();
                        opt.innerText = size.trim();
                        sizeSelect.appendChild(opt);
                    });
                }
            } catch (err) {
                console.error("Error loading sizes", err);
            }
        }
        async function updateProductList() {
            const type = document.getElementById('newBillTypeP').value; // 'Fer' or 'Pes'
            try {
                const res = await fetch(`api.php?action=get_products_by_type&type=${type}`);
                const products = await res.json();
                currentTypeProducts = products;

                // Update all product dropdowns in the form, preserving current selection
                const selects = document.querySelectorAll('.prod-name');
                selects.forEach(select => {
                    const currentVal = select.value;
                    select.innerHTML = '<option value="">Select Product...</option>';
                    products.forEach(pName => {
                        const opt = document.createElement('option');
                        opt.value = pName;
                        opt.innerText = pName;
                        if (pName === currentVal) opt.selected = true;
                        select.appendChild(opt);
                    });
                });
            } catch (err) {
                console.error("Error fetching products", err);
            }
        }

        function openProductSearch(btn) {
            const container = btn.closest('.col-span-4') || btn.parentElement;
            let panel = container.querySelector('.prod-search-panel');
            if (panel) {
                panel.classList.toggle('hidden');
                return;
            }
            panel = document.createElement('div');
            panel.className = 'prod-search-panel absolute left-0 right-0 mt-1 z-50 bg-white border border-gray-200 rounded-lg shadow-xl';
            const input = document.createElement('input');
            input.type = 'text';
            input.placeholder = 'Search product';
            input.className = 'w-full px-3 py-2 border-b';
            const list = document.createElement('div');
            list.className = 'max-h-48 overflow-y-auto';
            panel.appendChild(input);
            panel.appendChild(list);
            container.appendChild(panel);
            const select = container.querySelector('.prod-name');

            function render(items) {
                list.innerHTML = items.map(p => `<div class="px-4 py-2 hover:bg-blue-50 cursor-pointer text-sm transition-colors border-b last:border-0" data-name="${p}">${p}</div>`).join('');
                list.querySelectorAll('div[data-name]').forEach(el => {
                    el.onclick = () => {
                        select.value = el.getAttribute('data-name');
                        panel.classList.add('hidden');
                        loadPackingsForItem(select);
                    };
                });
            }
            render(currentTypeProducts || []);
            input.addEventListener('input', () => {
                const v = input.value.toLowerCase();
                const filtered = (currentTypeProducts || []).filter(p => p.toLowerCase().includes(v));
                render(filtered);
            });

            function onDocClick(e) {
                if (!panel.contains(e.target) && e.target !== btn) {
                    panel.remove();
                    document.removeEventListener('click', onDocClick);
                }
            }
            document.addEventListener('click', onDocClick);
        }

        async function loadPackingsForItem(selectElement) {
            const productName = selectElement.value;
            const row = selectElement.closest('.item-row');
            const packingSelect = row.querySelector('.prod-packing');

            if (!productName) {
                packingSelect.innerHTML = '<option value="">Select Packing...</option>';
                return;
            }

            try {
                const res = await fetch(`api.php?action=get_product_packings&name=${encodeURIComponent(productName)}`);
                const data = await res.json(); // This returns the 'packing' column string

                packingSelect.innerHTML = '<option value="">Select Packing...</option>';

                // Since your DB stores packing as "250ml, 500ml", we split it
                if (data && data.packing) {
                    const packingOptions = data.packing.split(',');
                    packingOptions.forEach(size => {
                        const opt = document.createElement('option');
                        opt.value = size.trim();
                        opt.innerText = size.trim();
                        packingSelect.appendChild(opt);
                    });
                }
            } catch (err) {
                console.error("Error loading packings", err);
            }
        }

        // 2. Event Listener for Dropdown Change
        document.getElementById('editTransport').addEventListener('change', function() {
            updateTransporterDisplay(this.value);
        });
        // --- Add this to the script section in orders.php ---

        // Function to handle selection change
        document.getElementById('editTransport').addEventListener('change', async function() {
            const selectedName = this.value;
            if (!selectedName) return;

            try {
                const res = await fetch(`api.php?action=get_transporter_details&name=${encodeURIComponent(selectedName)}`);
                const details = await res.json();

                if (details) {
                    // Logically link fields if you have inputs for them in the drawer
                    // For example, if you want to show the number/vehicle in specific labels or inputs:
                    console.log("Auto-filled details:", details);

                    // If you add input fields for these in your side drawer, you can set them here:
                    // document.getElementById('editVehicleNo').value = details.vehicle_number;
                    // document.getElementById('editContact').value = details.contact;
                }
            } catch (err) {
                console.error("Error fetching transporter details", err);
            }
        });
        window.addNewItemToOrder = async () => {
            const payload = {
                order_id: currentViewOrderId,
                product: document.getElementById('quickAddName').value,
                packing: document.getElementById('quickAddPacking').value, // Unit: Case/Bag
                size: document.getElementById('quickAddSize').value, // Size: 250ml
                quantity: document.getElementById('quickAddQty').value
            };

            if (!payload.product || !payload.size) {
                showToast("Select product and size");
                return;
            }

            const res = await fetch('api.php?action=add_items', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(payload)
            });

            const result = await res.json();
            if (result.success) {
                showToast("Item added");
                viewProductList(null, currentViewOrderId); // Refresh list
                fetchOrders(); // Refresh background stats
            }
        };

        window.updateItemQuantity = async (itemId, newQty) => {
            const res = await fetch('api.php?action=update_item_qty', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    item_id: itemId,
                    quantity: newQty
                })
            });
            if (res.ok) {
                showToast("Quantity updated");
                fetchOrders();
            }
        };

        window.removeItemFromOrder = async (itemId) => {
            if (!confirm("Remove this item?")) return;
            const res = await fetch('api.php?action=delete_item', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    item_id: itemId
                })
            });
            const result = await res.json();
            if (result.success) {
                showToast("Item removed");
                viewProductList(null, currentViewOrderId);
                fetchOrders();
            }
        };

        // UI Helpers
        window.closeItemsModal = () => document.getElementById('itemsModal').classList.add('hidden');
        window.toggleModal = (id) => {
            const m = document.getElementById(id);
            m.classList.toggle('opacity-0');
            m.classList.toggle('pointer-events-none');
        };
        window.closeDrawer = () => {
            document.getElementById('sideDrawer').classList.remove('open');
            document.getElementById('sideDrawerOverlay').classList.add('hidden');
        };
        window.showToast = (msg) => {
            const t = document.getElementById('toast');
            document.getElementById('toastMessage').innerText = msg;
            t.classList.remove('translate-y-20');
            setTimeout(() => t.classList.add('translate-y-20'), 3000);
        };

        window.showProductSuggestions = (input) => {
            const val = input.value.toLowerCase();
            const container = input.parentElement;
            let existing = container.querySelector('.suggestions-box');
            if (existing) existing.remove();
            if (!val) return;

            const matches = currentTypeProducts.filter(p => p.toLowerCase().includes(val));
            if (matches.length === 0) return;

            const list = document.createElement('div');
            list.className = "suggestions-box absolute left-0 right-0 z-50 bg-white border border-gray-200 rounded-lg shadow-xl mt-1 max-h-40 overflow-y-auto";

            matches.forEach(match => {
                const item = document.createElement('div');
                item.className = "px-4 py-2 hover:bg-blue-50 cursor-pointer text-sm transition-colors border-b last:border-0";
                item.innerText = match;
                item.onclick = () => {
                    input.value = match;
                    list.remove();
                    // Trigger loading packings for this specific product
                    loadPackingsForItem(input, match);
                };
                list.appendChild(item);
            });
            container.style.position = 'relative';
            container.appendChild(list);
        };

        // Form Submission Logic
        document.getElementById('newOrderForm').onsubmit = async (e) => {
            e.preventDefault();
            const items = Array.from(document.querySelectorAll('.item-row')).map(row => ({
                product: row.querySelector('.prod-name').value,
                unit: row.querySelector('.prod-unit').value, // Map 'Case/Bag' to 'unit'
                packing: row.querySelector('.prod-packing').value, // Map '250ml/1L' to 'packing' (which PHP then saves as 'size')
                quantity: row.querySelector('.prod-qty').value
            }));
            const payload = {
                orderDate: document.getElementById('newOrderDate').value,
                salesman: document.getElementById('newSalesman').value,
                party: document.getElementById('newParty').value,
                billType: document.getElementById('newBillType').value,
                type: document.getElementById('newBillTypeP').value,
                billNo: document.getElementById('newBillType').value === 'B' ? '-' : (document.getElementById('newBillTypeP').value === 'Fer' ? 'CCC/F/' : 'CCC/P/'),
                status: 'Incomplete',
                items: items
            };
            const res = await fetch('api.php?action=create', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(payload)
            });
            const result = await res.json();
            if (result.success) {
                toggleModal('orderModal');
                document.getElementById('newOrderForm').reset();
                fetchOrders();
                showToast("Order saved successfully!");
            }
        };

        window.addItemRow = () => {
            const container = document.getElementById('itemRowsContainer');
            const div = document.createElement('div');
            div.className = "grid grid-cols-12 gap-3 item-row mt-3 animate-slide-up";
            div.innerHTML = `
        <div class="col-span-4 relative">
            <div class="flex items-center gap-2">
                <select class="prod-name w-full px-4 py-2 bg-gray-50 border rounded-lg text-sm" onchange="loadPackingsForItem(this)" required>
                    <option value="">Select Product...</option>
                </select>
                <button type="button" onclick="openProductSearch(this)" class="flex items-center gap-2 px-3 py-2 bg-blue-50 text-blue-700 rounded-lg border border-blue-100 hover:bg-blue-100">
                    <i class="fas fa-search text-xs"></i>
                </button>
            </div>
        </div>
        <div class="col-span-2">
            <select class="prod-unit w-full px-4 py-2 bg-gray-50 border rounded-lg text-sm">
                <option>Case</option><option>Bag</option><option>Drum</option>
            </select>
        </div>
        <div class="col-span-2">
            <select class="prod-packing w-full px-4 py-2 bg-gray-50 border rounded-lg text-sm">
                <option value="">Select Size...</option>
            </select>
        </div>
        <div class="col-span-3">
            <input type="number" class="prod-qty w-full px-3 py-2 bg-gray-50 border rounded-lg text-sm text-center" value="1" min="1" required>
        </div>
        <div class="col-span-1 flex items-center justify-center">
            <button type="button" onclick="this.parentElement.parentElement.remove()" class="w-8 h-8 text-red-400"><i class="fas fa-times"></i></button>
        </div>`;
            container.appendChild(div);
            updateProductList();
        };

        window.setBillType = (t) => {
            document.getElementById('newBillType').value = t;
            document.getElementById('btnA').className = t === 'A' ? 'active' : '';
            document.getElementById('btnB').className = t === 'B' ? 'active' : '';
        };
        window.setBillTypeP = (t) => {
            document.getElementById('newBillTypeP').value = t;
            document.getElementById('btnFer').className = t === 'Fer' ? 'active' : '';
            document.getElementById('btnPes').className = t === 'Pes' ? 'active' : '';
            // Fetch the new list of products for this category
            updateProductList();
        };
        window.setEditBillType = (t) => {
            // 1. Update the hidden input value
            document.getElementById('editBillType').value = t;

            // 2. Update the visual state of the buttons
            const btnA = document.getElementById('editBtnA');
            const btnB = document.getElementById('editBtnB');

            if (t === 'A') {
                btnA.classList.add('active');
                btnB.classList.remove('active');
            } else {
                btnA.classList.remove('active');
                btnB.classList.add('active');
            }

            // 3. Handle the Bill Number field logic
            const bInput = document.getElementById('editBillNo');
            if (t === 'B') {
                bInput.value = '-';
                bInput.disabled = true;
                bInput.classList.add('bg-gray-100'); // Optional: make it look disabled
            } else {
                bInput.disabled = false;
                bInput.classList.remove('bg-gray-100');

                // Restore prefix if empty or just a dash
                if (bInput.value === '' || bInput.value === '-') {
                    const isPesticide = document.getElementById('newBillTypeP').value === 'Pes';
                    bInput.value = isPesticide ? 'CCC/P/' : 'CCC/F/';
                }
            }
            if (typeof validateEditBillNoUnique === 'function') validateEditBillNoUnique();
        };

        function validateEditBillNoUnique() {
            const type = document.getElementById('editBillType').value;
            const input = document.getElementById('editBillNo');
            const msg = document.getElementById('editBillNoError');
            if (!input || !msg) return true;
            if (type !== 'A') {
                input.classList.remove('border-red-500');
                input.classList.add('border-gray-200');
                msg.classList.add('hidden');
                return true;
            }
            const val = (input.value || '').trim().toLowerCase();
            const currentId = String(document.getElementById('currentEditId').value || '');
            const duplicate = Array.isArray(ordersData) && ordersData.some(o => {
                const bn = ((o.bill_no || '') + '').trim().toLowerCase();
                return (o.bill_type === 'A') && bn !== '' && bn === val && String(o.id) !== currentId;
            });
            if (duplicate && val !== '') {
                input.classList.remove('border-gray-200');
                input.classList.add('border-red-500');
                msg.classList.remove('hidden');
                return false;
            } else {
                input.classList.remove('border-red-500');
                input.classList.add('border-gray-200');
                msg.classList.add('hidden');
                return true;
            }
        }
        (function() {
            const el = document.getElementById('editBillNo');
            if (el) {
                el.addEventListener('input', validateEditBillNoUnique);
                el.addEventListener('blur', validateEditBillNoUnique);
            }
        })();
        let currentImages = [];

        function handleMultipleFiles(input) {
            if (input.files) {
                Array.from(input.files).forEach(file => {
                    const isImage = file.type.startsWith('image/');
                    if (isImage) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            renderPreview(e.target.result, true, file.name, file.type);
                        };
                        reader.readAsDataURL(file);
                    } else {
                        renderPreview(null, true, file.name, file.type);
                    }
                });
            }
        }

        function renderPreview(src, isNew = false, fileName = '', fileType = '') {
            const grid = document.getElementById('imageGrid');
            const div = document.createElement('div');
            div.className = "relative group aspect-square rounded-lg overflow-hidden border bg-gray-100";
            const isImage = (fileType && fileType.startsWith('image/')) || (src && (src.startsWith('data:image/') || src.match(/\.(jpe?g|png|gif|webp)(\?|$)/i)));
            const safeSrc = (src || '').replace(/'/g, "\\'");
            if (isImage && src) {
                div.innerHTML = `
        <img src="${safeSrc}" class="w-full h-full object-cover cursor-pointer" onclick="openLightbox('${safeSrc}')">
        <button type="button" onclick="removeSpecificImage(this, '${safeSrc}', ${isNew})" class="absolute top-1 right-1 bg-red-500 text-white w-5 h-5 rounded-full text-[10px] flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
            <i class="fas fa-times"></i>
        </button>
    `;
            } else {
                const ext = (fileName || '').split('.').pop().toLowerCase();
                let iconClass = 'fa-file';
                if (ext === 'pdf') iconClass = 'fa-file-pdf';
                else if (['xls', 'xlsx', 'csv'].includes(ext)) iconClass = 'fa-file-excel';
                const showActions = !!(safeSrc && !isNew && !safeSrc.startsWith('data:'));
                const actionsHtml = showActions ? `
        <div class="absolute bottom-1 left-1 right-1 flex gap-2 justify-center opacity-0 group-hover:opacity-100 transition-opacity">
            <a href="${safeSrc}" target="_blank" rel="noopener" class="px-2 py-1 text-[10px] bg-white rounded border border-gray-300 shadow-sm">View</a>
            <a href="${safeSrc}" download class="px-2 py-1 text-[10px] bg-white rounded border border-gray-300 shadow-sm">Download</a>
        </div>` : '';
                div.innerHTML = `
        <div class="w-full h-full flex flex-col items-center justify-center p-2 text-center">
            <i class="fas ${iconClass} text-3xl text-gray-500 mb-1"></i>
            <span class="text-[10px] font-semibold text-gray-600 truncate w-full">${(fileName || 'File')}</span>
        </div>
        <button type="button" onclick="removeSpecificImage(this, '${safeSrc}', ${isNew})" class="absolute top-1 right-1 bg-red-500 text-white w-5 h-5 rounded-full text-[10px] flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
            <i class="fas fa-times"></i>
        </button>
        ${actionsHtml}
    `;
            }
            grid.appendChild(div);
        }

        function removeSpecificImage(btn, src, isNew) {
            btn.parentElement.remove();
            if (!isNew) {
                // If it was an existing image, remove it from our tracking array
                currentImages = currentImages.filter(img => img !== src);
                document.getElementById('existing_images_json').value = JSON.stringify(currentImages);
            }
        }
        window.openDrawer = async (id) => {
            const res = await fetch(`api.php?action=get_details&id=${id}`);
            const o = await res.json();

            document.getElementById('currentEditId').value = o.id;
            document.getElementById('drawerOrderId').innerText = `#${orderSerialMap[o.id] || ''}`;
            document.getElementById('editBillDate').value = o.bill_date || '';
            document.getElementById('editBillNo').value = o.bill_no || '';
            document.getElementById('editTransport').value = o.transport || '';
            document.getElementById('editStatus').value = o.status;

            await loadTransporterDropdown();
            const savedTransport = o.transport || '';
            // 2. NOW set the value automatically based on what is saved in the database
            // 'o.transport' contains the name saved in your 'orders' table
            document.getElementById('editTransport').value = savedTransport;
            updateTransporterDisplay(savedTransport);

            // FIX: This triggers the visual toggle and the A/B logic upon opening
            setEditBillType(o.bill_type || 'A');
            validateEditBillNoUnique();

            document.getElementById('sideDrawer').classList.add('open');
            document.getElementById('sideDrawerOverlay').classList.remove('hidden');
        };
        window.updateOrderDetails = async () => {
            if (!validateEditBillNoUnique()) {
                showToast("Bill already exists");
                return;
            }
            const data = {
                id: document.getElementById('currentEditId').value,
                billType: document.getElementById('editBillType').value,
                billDate: document.getElementById('editBillDate').value,
                billNo: document.getElementById('editBillNo').value,
                transport: document.getElementById('editTransport').value,
                status: document.getElementById('editStatus').value
            };
            const res = await fetch('api.php?action=update', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            });
            if (res.ok) {
                showToast("Record Updated Successfully");
                closeDrawer();
                fetchOrders();
            }
        };

        function updateStats() {
            document.getElementById('statTotal').innerText = ordersData.length;
            document.getElementById('statIncomplete').innerText = ordersData.filter(o => o.status === 'Incomplete').length;
            document.getElementById('statComplete').innerText = ordersData.filter(o => o.status === 'Finalized').length;
            document.getElementById('statReceived').innerText = ordersData.filter(o => o.status === 'Received').length;
            const okCount = ordersData.filter(o => o.status === 'Okay').length;
            const okElem = document.getElementById('statOkay');
            if (okElem) okElem.innerText = okCount;
            document.getElementById('statCancelled').innerText = ordersData.filter(o => o.status === 'Cancelled').length;
        }

        function debounce(fn, waitMs) {
            let t = null;
            return function(...args) {
                if (t) clearTimeout(t);
                t = setTimeout(() => fn.apply(this, args), waitMs);
            };
        }

        const searchEl = document.getElementById('searchInput');
        if (searchEl) searchEl.addEventListener('input', debounce(() => {
            resetOrdersPage();
            renderTable();
        }, 180));
        window.onload = () => {
            fetchOrders();
            loadTransporterDropdown();
            loadSalesmenList(); // New function call
            updateProductList();
            try {
                ordersFocusMode = localStorage.getItem('ordersFocusMode') === '1';
            } catch (e) {
                ordersFocusMode = false;
            }
            document.body.classList.toggle('orders-focus', ordersFocusMode);
            updateOrdersFocusButton();
        };
        window.deleteOrder = async () => {
            const id = document.getElementById('currentEditId').value;
            if (!id) return;

            if (confirm("Are you sure you want to permanently delete this order? This action cannot be undone.")) {
                try {
                    const res = await fetch(`api.php?action=delete_order&id=${id}`, {
                        method: 'POST'
                    });
                    const result = await res.json();

                    if (result.success) {
                        showToast("Order deleted successfully");
                        closeDrawer();
                        fetchOrders();
                    } else {
                        showToast("Error deleting order");
                    }
                } catch (err) {
                    console.error("Delete error", err);
                    showToast("Server error occurred");
                }
            }
        };

        function triggerOrderFileInput() {
            document.getElementById('orderFileInput').click();
        }

        function previewOrderImg(input) {
            const preview = document.getElementById('imagePreview');
            const placeholder = document.getElementById('imagePlaceholder');
            const actions = document.getElementById('imageActions');

            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                    placeholder.style.display = 'none';
                    actions.style.display = 'flex';
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        function removeOrderImage() {
            document.getElementById('imagePreview').src = '';
            document.getElementById('imagePreview').style.display = 'none';
            document.getElementById('imagePlaceholder').style.display = 'block';
            document.getElementById('imageActions').style.display = 'none';
            document.getElementById('orderFileInput').value = '';
            document.getElementById('existing_receiving_image').value = '';
        }

        // Logic to show/hide uploader based on status
        document.getElementById('editStatus').addEventListener('change', function() {
            const section = document.getElementById('receivingUploadSection');
            if (this.value === 'Received') {
                section.classList.remove('hidden');
            } else {
                section.classList.add('hidden');
            }
        });

        const originalOpenDrawer = window.openDrawer;
        window.openDrawer = async (id) => {
            await originalOpenDrawer(id);
            const order = ordersData.find(o => o.id == id);
            const grid = document.getElementById('imageGrid');
            grid.innerHTML = ''; // Clear previous

            try {
                // Parse existing images
                currentImages = JSON.parse(order.receiving_image_path || '[]');
            } catch (e) {
                // Handle legacy single-string paths if any
                currentImages = order.receiving_image_path ? [order.receiving_image_path] : [];
            }

            document.getElementById('existing_images_json').value = JSON.stringify(currentImages);
            currentImages.forEach(src => renderPreview(src, false));

            const section = document.getElementById('receivingUploadSection');
            section.classList.toggle('hidden', order.status !== 'Received');
        };

        window.updateOrderDetails = async () => {
            if (!validateEditBillNoUnique()) {
                showToast("Bill already exists");
                return;
            }
            const formData = new FormData();
            formData.append('id', document.getElementById('currentEditId').value);
            formData.append('billType', document.getElementById('editBillType').value);
            formData.append('billDate', document.getElementById('editBillDate').value);
            formData.append('billNo', document.getElementById('editBillNo').value);
            formData.append('transport', document.getElementById('editTransport').value);
            formData.append('status', document.getElementById('editStatus').value);

            // Pass the remaining existing images as a JSON string
            formData.append('existing_images', document.getElementById('existing_images_json').value);

            const fileInput = document.getElementById('orderFileInput');
            for (let i = 0; i < fileInput.files.length; i++) {
                formData.append('receiving_images[]', fileInput.files[i]);
            }

            const res = await fetch('api.php?action=update', {
                method: 'POST',
                body: formData
            });

            if (res.ok) {
                showToast("Order Updated with " + (currentImages.length + fileInput.files.length) + " images");
                closeDrawer();
                fetchOrders();
            }
        };

        function openLightbox(src) {
            const lbImg = document.getElementById('lightboxImage');
            const lbOverlay = document.getElementById('lightboxOverlay');

            // If no src is provided, try to get it from the drawer's preview
            const finalSrc = src || document.getElementById('imagePreview').src;

            if (finalSrc && finalSrc !== "" && !finalSrc.endsWith('#')) {
                lbImg.src = finalSrc;
                lbOverlay.classList.add('active');
                document.body.style.overflow = 'hidden';
            }
        }

        function closeLightbox() {
            document.getElementById('lightboxOverlay').classList.remove('active');
            document.body.style.overflow = 'auto';
        }

        // Close with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === "Escape") closeLightbox();
        });

        async function exportToCSV() {
            if (!filteredOrders || filteredOrders.length === 0) {
                showToast("No data to export");
                return;
            }

            // We need to fetch item details for all orders to make a useful export
            const orderIds = filteredOrders.map(o => o.id);
            await getItemsForOrders(orderIds);

            const rows = [];
            filteredOrders.forEach(o => {
                const items = orderItemsCache[o.id] || [];
                if (items.length === 0) {
                    rows.push({
                        "Order Date": formatIndianDate(o.order_date),
                        "Bill No": o.bill_no || '',
                        "Party": o.party,
                        "Salesman": o.salesman,
                        "Status": o.status,
                        "Transport": o.transport || '',
                        "Product": "",
                        "Packing": "",
                        "Quantity": "",
                        "Unit": ""
                    });
                } else {
                    items.forEach(item => {
                        rows.push({
                            "Order Date": formatIndianDate(o.order_date),
                            "Bill No": o.bill_no || '',
                            "Party": o.party,
                            "Salesman": o.salesman,
                            "Status": o.status,
                            "Transport": o.transport || '',
                            "Product": item.product,
                            "Packing": item.packing + (item.Size ? ` (${item.Size})` : ''),
                            "Quantity": item.quantity,
                            "Unit": item.unit || ''
                        });
                    });
                }
            });

            const ws = XLSX.utils.json_to_sheet(rows);
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, "Orders");
            XLSX.writeFile(wb, "Orders_Export.csv");
        }

        async function exportToExcel() {
            if (!filteredOrders || filteredOrders.length === 0) {
                showToast("No data to export");
                return;
            }

            const orderIds = filteredOrders.map(o => o.id);
            await getItemsForOrders(orderIds);

            const rows = [];
            filteredOrders.forEach(o => {
                const items = orderItemsCache[o.id] || [];
                if (items.length === 0) {
                    rows.push({
                        "Order Date": formatIndianDate(o.order_date),
                        "Bill No": o.bill_no || '',
                        "Party": o.party,
                        "Salesman": o.salesman,
                        "Status": o.status,
                        "Transport": o.transport || '',
                        "Product": "",
                        "Packing": "",
                        "Quantity": "",
                        "Unit": ""
                    });
                } else {
                    items.forEach(item => {
                        rows.push({
                            "Order Date": formatIndianDate(o.order_date),
                            "Bill No": o.bill_no || '',
                            "Party": o.party,
                            "Salesman": o.salesman,
                            "Status": o.status,
                            "Transport": o.transport || '',
                            "Product": item.product,
                            "Packing": item.packing + (item.Size ? ` (${item.Size})` : ''),
                            "Quantity": item.quantity,
                            "Unit": item.unit || ''
                        });
                    });
                }
            });

            const ws = XLSX.utils.json_to_sheet(rows);
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, "Orders");
            XLSX.writeFile(wb, "Orders_Export.xlsx");
        }

        async function exportToPDF() {
            if (!filteredOrders || filteredOrders.length === 0) {
                showToast("No data to export");
                return;
            }

            const {
                jsPDF
            } = window.jspdf;
            const doc = new jsPDF();

            doc.setFontSize(18);
            doc.text("Order Book Report", 14, 22);
            doc.setFontSize(10);
            doc.text(`Generated on: ${new Date().toLocaleDateString()}`, 14, 30);

            const tableData = filteredOrders.map(o => [
                formatIndianDate(o.order_date),
                o.bill_no || '-',
                o.party,
                o.salesman,
                o.status,
                o.transport || '-'
            ]);

            doc.autoTable({
                head: [
                    ['Date', 'Bill No', 'Party', 'Salesman', 'Status', 'Transport']
                ],
                body: tableData,
                startY: 35,
                theme: 'grid',
                styles: {
                    fontSize: 8
                },
                headStyles: {
                    fillColor: [37, 99, 235]
                }
            });

            doc.save("Orders_Report.pdf");
        }
        // ==========================
        // ENTRIES SECTION LOGIC
        // ==========================

        function withLoader(callback) {
            const loader = document.getElementById('pageLoader');
            if (loader) loader.classList.add('visible');

            setTimeout(() => {
                callback();
                setTimeout(() => {
                    if (loader) loader.classList.remove('visible');
                }, 300);
            }, 500);
        }

        let currentEntryStatus = 'All';
        let currentEntrySalesman = 'All';
        let currentEntryParty = 'All';

        function showEntries() {
            withLoader(() => {
                // Hide other sections
                const ordersSection = document.getElementById('ordersSection');
                if (ordersSection) {
                    ordersSection.classList.add('view-hidden');
                    ordersSection.classList.remove('view-visible');
                    ordersSection.style.display = 'none';
                }

                const chartsSection = document.getElementById('chartsSection');
                if (chartsSection) {
                    chartsSection.classList.add('view-hidden');
                    chartsSection.classList.remove('view-visible');
                    chartsSection.style.display = 'none';
                }

                // Show entries section
                const entriesSection = document.getElementById('entriesSection');
                if (entriesSection) {
                    entriesSection.classList.remove('view-hidden');
                    entriesSection.classList.add('view-visible');
                    entriesSection.style.display = 'block';
                }

                // Update sidebar buttons
                document.querySelectorAll('.sidebar-btn').forEach(b => b.classList.remove('active', 'bg-blue-50', 'text-blue-600'));
                const btn = document.getElementById('entriesBtn');
                if (btn) {
                    btn.classList.add('active', 'bg-blue-50', 'text-blue-600');
                }

                // Populate filter lists if not already done (or refresh them)
                populateEntriesFilters();

                renderEntriesTable();
            });
        }

        function populateEntriesFilters() {
            if (typeof ordersData === 'undefined') return;

            // --- Salesman ---
            const salesmen = [...new Set(ordersData.map(o => o.salesman).filter(Boolean))].sort();
            const salesmanList = document.getElementById('list-salesman-entries');
            if (salesmanList) {
                let html = `<div class="popover-option" onclick="filterEntriesBySalesman('All')">All Salesmen</div>`;
                salesmen.forEach(s => {
                    html += `<div class="popover-option" onclick="filterEntriesBySalesman('${s.replace(/'/g, "\\'")}')">${s}</div>`;
                });
                salesmanList.innerHTML = html;
            }

            // --- Party ---
            const parties = [...new Set(ordersData.map(o => o.party).filter(Boolean))].sort();
            const partyList = document.getElementById('list-party-entries');
            if (partyList) {
                let html = `<div class="popover-option" onclick="filterEntriesByParty('All')">All Parties</div>`;
                parties.forEach(p => {
                    html += `<div class="popover-option" onclick="filterEntriesByParty('${p.replace(/'/g, "\\'")}')">${p}</div>`;
                });
                partyList.innerHTML = html;
            }
        }

        function filterPopoverList(input, listId) {
            const filter = input.value.toLowerCase();
            const list = document.getElementById(listId);
            if (!list) return;
            const options = list.getElementsByClassName('popover-option');
            for (let i = 0; i < options.length; i++) {
                const txt = options[i].textContent || options[i].innerText;
                if (txt.toLowerCase().indexOf(filter) > -1) {
                    options[i].style.display = "";
                } else {
                    options[i].style.display = "none";
                }
            }
        }

        // Override showOrders and showCharts to hide entriesSection
        showOrders = function() {
            withLoader(() => {
                const entriesSection = document.getElementById('entriesSection');
                if (entriesSection) {
                    entriesSection.style.display = 'none';
                    entriesSection.classList.add('view-hidden');
                    entriesSection.classList.remove('view-visible');
                }

                const chartsSection = document.getElementById('chartsSection');
                if (chartsSection) {
                    chartsSection.style.display = 'none';
                    chartsSection.classList.add('view-hidden');
                    chartsSection.classList.remove('view-visible');
                }

                const ordersSection = document.getElementById('ordersSection');
                if (ordersSection) {
                    ordersSection.style.display = 'block';
                    ordersSection.classList.remove('view-hidden');
                    ordersSection.classList.add('view-visible');
                }

                document.querySelectorAll('.sidebar-btn').forEach(b => b.classList.remove('active', 'bg-blue-50', 'text-blue-600'));
                const btn = document.getElementById('ordersBtn');
                if (btn) {
                    btn.classList.add('active', 'bg-blue-50', 'text-blue-600');
                }
            });
        }

        showCharts = function() {
            withLoader(() => {
                const entriesSection = document.getElementById('entriesSection');
                if (entriesSection) {
                    entriesSection.style.display = 'none';
                    entriesSection.classList.add('view-hidden');
                    entriesSection.classList.remove('view-visible');
                }

                const ordersSection = document.getElementById('ordersSection');
                if (ordersSection) {
                    ordersSection.style.display = 'none';
                    ordersSection.classList.add('view-hidden');
                    ordersSection.classList.remove('view-visible');
                }

                const chartsSection = document.getElementById('chartsSection');
                if (chartsSection) {
                    chartsSection.style.display = 'block';
                    chartsSection.classList.remove('view-hidden');
                    chartsSection.classList.add('view-visible');
                }

                document.querySelectorAll('.sidebar-btn').forEach(b => b.classList.remove('active', 'bg-blue-50', 'text-blue-600'));
                const btn = document.getElementById('chartsBtn');
                if (btn) {
                    btn.classList.add('active', 'bg-blue-50', 'text-blue-600');
                }

                if (typeof buildCharts === 'function') buildCharts();
            });
        }


        function renderEntriesTable() {
            const tbody = document.getElementById('entriesTableBody');
            if (!tbody) return;
            tbody.innerHTML = '';

            const searchInput = document.getElementById('entriesSearch');
            const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';

            // Ensure ordersData exists
            if (typeof ordersData === 'undefined') {
                console.error('ordersData is undefined');
                return;
            }

            const filtered = ordersData.filter(o => {
                // Status Filter
                if (currentEntryStatus !== 'All' && o.status !== currentEntryStatus) return false;

                // Salesman Filter
                if (currentEntrySalesman !== 'All' && o.salesman !== currentEntrySalesman) return false;

                // Party Filter
                if (currentEntryParty !== 'All' && o.party !== currentEntryParty) return false;

                // Search Filter
                if (searchTerm) {
                    const searchStr = `${o.bill_no || ''} ${o.party || ''} ${o.salesman || ''} ${o.status || ''}`.toLowerCase();
                    if (!searchStr.includes(searchTerm)) return false;
                }

                return true;
            });

            if (filtered.length === 0) {
                tbody.innerHTML = `<tr><td colspan="5" class="text-center py-8 text-gray-400">No entries found</td></tr>`;
                return;
            }

            filtered.forEach(o => {
                const tr = document.createElement('tr');
                tr.className = 'hover:bg-blue-50 transition-colors border-b border-gray-100 odd:bg-white even:bg-gray-50 cursor-pointer';
                tr.onclick = () => openInvoiceModal(o.id);

                let statusBadge = '';
                switch (o.status) {
                    case 'Finalized':
                        statusBadge = '<span class="px-2 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-700">Completed</span>';
                        break;
                    case 'Incomplete':
                        statusBadge = '<span class="px-2 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-700">Pending</span>';
                        break;
                    case 'Cancelled':
                        statusBadge = '<span class="px-2 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700">Cancelled</span>';
                        break;
                    case 'Received':
                        statusBadge = '<span class="px-2 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700">Received</span>';
                        break;
                    case 'Okay':
                        statusBadge = '<span class="px-2 py-1 rounded-full text-xs font-bold bg-purple-100 text-purple-700">Okay</span>';
                        break;
                    default:
                        statusBadge = `<span class="px-2 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-700">${o.status}</span>`;
                }

                tr.innerHTML = `
                    <td class="px-6 py-4 text-sm text-gray-600">${o.bill_date || '-'}</td>
                    <td class="px-6 py-4 text-sm font-semibold text-gray-900">${o.bill_no || '-'}</td>
                    <td class="px-6 py-4 text-sm font-medium text-gray-900">${o.party}</td>
                    <td class="px-6 py-4 text-sm text-gray-500">${o.salesman}</td>
                    <td class="px-6 py-4">${statusBadge}</td>
                `;
                tbody.appendChild(tr);
            });
        }

        function filterEntriesByStatus(status) {
            currentEntryStatus = status;
            const label = document.getElementById('label-status-entries');
            if (label) label.innerText = status === 'All' ? 'Status: All' : `Status: ${status}`;

            // Close popover
            const popover = document.getElementById('popover-status-entries');
            if (popover) popover.classList.remove('show');

            renderEntriesTable();
        }

        function filterEntriesBySalesman(salesman) {
            currentEntrySalesman = salesman;
            const label = document.getElementById('label-salesman-entries');
            if (label) label.innerText = salesman === 'All' ? 'Salesman: All' : `Salesman: ${salesman}`;

            // Close popover
            const popover = document.getElementById('popover-salesman-entries');
            if (popover) popover.classList.remove('show');

            renderEntriesTable();
        }

        function filterEntriesByParty(party) {
            currentEntryParty = party;
            const label = document.getElementById('label-party-entries');
            if (label) label.innerText = party === 'All' ? 'Party: All' : `Party: ${party}`;

            // Close popover
            const popover = document.getElementById('popover-party-entries');
            if (popover) popover.classList.remove('show');

            renderEntriesTable();
        }

        function toggleEntriesPopover(id) {
            const popover = document.getElementById(id);
            if (!popover) return;
            const isVisible = popover.classList.contains('show');
            // Close all other popovers if any
            document.querySelectorAll('.popover').forEach(p => p.classList.remove('show'));

            if (!isVisible) {
                popover.classList.add('show');
            }
        }

        // Close popovers when clicking outside
        document.addEventListener('click', function(event) {
            if (!event.target.closest('.filter-chip') && !event.target.closest('.popover')) {
                document.querySelectorAll('.popover').forEach(p => p.classList.remove('show'));
            }
        });

        function searchEntries() {
            renderEntriesTable();
        }

        async function openInvoiceModal(id) {
            withLoader(async () => {
                // Fetch details if items are missing
                if (!orderItemsCache[id]) {
                    await getItemsForOrders([id]);
                }

                // Get order from ordersData
                const o = ordersData.find(order => order.id == id);
                if (!o) return;

                const items = orderItemsCache[id] || [];

                // Populate Modal
                document.getElementById('invoiceId').innerText = `Order #${(typeof orderSerialMap !== 'undefined' ? orderSerialMap[o.id] : o.id) || o.id}`;
                document.getElementById('invoiceDate').innerText = formatIndianDate(o.bill_date || o.order_date);
                document.getElementById('invoiceStatus').innerText = o.status;
                document.getElementById('invoiceParty').innerText = o.party;
                document.getElementById('invoiceSalesman').innerText = o.salesman;
                document.getElementById('invoiceTransport').innerText = o.transport || '-';
                document.getElementById('invoiceBillNo').innerText = o.bill_no || '-';

                // Render Items
                const tbody = document.getElementById('invoiceItemsBody');
                tbody.innerHTML = '';

                if (items.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="4" class="text-center py-8 text-gray-400">No items found</td></tr>`;
                } else {
                    items.forEach(item => {
                        const tr = document.createElement('tr');
                        tr.className = 'hover:bg-gray-50 transition-colors';
                        tr.innerHTML = `
                            <td class="py-4 px-6 text-sm font-medium text-gray-900">${item.product}</td>
                            <td class="py-4 px-6 text-sm text-gray-600 text-center">${item.packing}</td>
                            <td class="py-4 px-6 text-sm font-bold text-gray-900 text-center">${item.quantity}</td>
                            <td class="py-4 px-6 text-sm text-gray-500 text-center">${item.Size ? `${item.Size}` : ''}</td> 

                        `;
                        tbody.appendChild(tr);
                    });
                }

                // Show Modal
                const modal = document.getElementById('invoiceModal');
                const content = document.getElementById('invoiceContent');
                modal.classList.remove('hidden');
                // Trigger reflow
                void modal.offsetWidth;
                content.classList.remove('scale-95', 'opacity-0');
                content.classList.add('scale-100', 'opacity-100');
            });
        }

        function closeInvoiceModal() {
            const modal = document.getElementById('invoiceModal');
            const content = document.getElementById('invoiceContent');

            content.classList.remove('scale-100', 'opacity-100');
            content.classList.add('scale-95', 'opacity-0');

            setTimeout(() => {
                modal.classList.add('hidden');
            }, 200);
        }

        function printInvoice() {
            const printContent = document.getElementById('invoiceContent').innerHTML;
            const originalContents = document.body.innerHTML;

            // Create a temporary print container
            const printDiv = document.createElement('div');
            printDiv.innerHTML = printContent;
            // Remove buttons for print
            const buttons = printDiv.querySelectorAll('button');
            buttons.forEach(btn => btn.remove());

            // Simple print style
            const style = document.createElement('style');
            style.innerHTML = `
                @media print {
                    body { padding: 20px; }
                    .no-print { display: none !important; }
                }
             `;
            printDiv.appendChild(style);

            // Open new window for clean print
            const printWindow = window.open('', '', 'height=600,width=800');
            printWindow.document.write('<html><head><title>Invoice Print</title>');
            // Copy Tailwind CDN if needed or basic styles
            printWindow.document.write('<script src="https://cdn.tailwindcss.com"><\/script>');
            printWindow.document.write('</head><body>');
            printWindow.document.write(printDiv.innerHTML);
            printWindow.document.write('</body></html>');
            printWindow.document.close();
            printWindow.print();
        }
    </script>
    <div id="lightboxOverlay" onclick="closeLightbox()">
        <div class="lightbox-close-hint">Click anywhere to close</div>
        <img id="lightboxImage" src="" alt="Receiving Proof Preview">
    </div>
    <div id="invoiceModal" class="fixed inset-0 bg-black/60 z-[100] hidden flex items-center justify-center backdrop-blur-sm" onclick="closeInvoiceModal()">
        <div class="bg-white w-full max-w-4xl h-[90vh] rounded-2xl shadow-2xl overflow-hidden flex flex-col transform transition-all scale-95 opacity-0 duration-200" id="invoiceContent" onclick="event.stopPropagation()">
            <!-- Header -->
            <div class="p-8 border-b bg-gray-50 flex justify-between items-start">
                <div>
                    <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">INVOICE</h1>
                    <p class="text-gray-500 mt-2 font-mono text-sm" id="invoiceId">#---</p>
                </div>
                <div class="text-right">
                    <h2 class="text-xl font-bold text-gray-800">CCCPL</h2>
                    <div class="mt-2 space-y-1">
                        <p class="text-sm text-gray-600"><span class="font-medium">Date:</span> <span id="invoiceDate" class="font-mono">---</span></p>
                        <p class="text-sm text-gray-600"><span class="font-medium">Status:</span> <span id="invoiceStatus" class="font-mono">---</span></p>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="p-8 overflow-y-auto flex-1 bg-white">
                <!-- Info Grid -->
                <div class="grid grid-cols-2 gap-12 mb-10">
                    <div>
                        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">Bill To</h3>
                        <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                            <p class="text-lg font-bold text-gray-900" id="invoiceParty">---</p>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">Order Details</h3>
                        <div class="bg-gray-50 p-4 rounded-xl border border-gray-100 space-y-2">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-500 font-medium">Salesman</span>
                                <span class="text-sm text-gray-900 font-semibold" id="invoiceSalesman">---</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-500 font-medium">Transport</span>
                                <span class="text-sm text-gray-900 font-semibold" id="invoiceTransport">---</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-500 font-medium">Bill No</span>
                                <span class="text-sm text-gray-900 font-semibold font-mono" id="invoiceBillNo">---</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Items Table -->
                <div class="rounded-xl border border-gray-200 overflow-hidden">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="text-left py-4 px-6 text-xs font-bold text-gray-500 uppercase tracking-wider">Product</th>
                                <th class="text-center py-4 px-6 text-xs font-bold text-gray-500 uppercase tracking-wider">Packing</th>
                                <th class="text-center py-4 px-6 text-xs font-bold text-gray-500 uppercase tracking-wider">Qty</th>
                                <th class="text-center py-4 px-6 text-xs font-bold text-gray-500 uppercase tracking-wider">Unit</th>
                            </tr>
                        </thead>
                        <tbody id="invoiceItemsBody" class="divide-y divide-gray-100">
                            <!-- Items -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Footer -->
            <div class="p-6 border-t bg-gray-50 flex justify-end gap-3">
                <button onclick="closeInvoiceModal()" class="px-6 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 font-semibold transition-colors shadow-sm">Close</button>
                <button onclick="printInvoice()" class="px-6 py-2.5 bg-blue-600 text-white rounded-xl hover:bg-blue-700 font-semibold transition-colors shadow-lg shadow-blue-200 flex items-center gap-2">
                    <i class="fas fa-print"></i> Print
                </button>
            </div>
        </div>
    </div>
</body>

</html>
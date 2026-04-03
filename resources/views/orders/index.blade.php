@extends('layouts.admin')

@section('title', 'Order Book')

@push('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<style>
    body {
        background-color: #f5f5f7;
        color: #1d1d1f;
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

    .status-okay {
        background: #f3e8ff;
        color: #7e22ce;
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
</style>
@endpush

@section('content')
<div class="flex flex-col h-full gap-6">
    <div class="flex items-center justify-end">
        <div class="relative w-full max-w-md">
            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
            <input id="searchInput" type="text" placeholder="Search Party, Salesman, Prod" class="w-full pl-11 pr-4 py-2.5 bg-white border border-gray-200 rounded-2xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm">
        </div>
    </div>

    <div id="ordersKpiRow" class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-4">
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
            <p class="text-green-600 text-xs font-semibold uppercase">Receiving</p>
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

    <div class="flex justify-center">
        <div class="bg-white px-6 py-3 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-3 flex-wrap">
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

    <div class="flex-1 bg-white rounded-3xl shadow-sm border border-gray-100 flex flex-col overflow-hidden min-h-0">
        <div class="px-6 py-4 border-b flex items-center justify-between gap-3 flex-wrap">
            <div class="flex items-center gap-3">
                <h2 class="font-bold text-lg">Order Records</h2>
            </div>
            <div class="flex items-center gap-3 flex-wrap">
                <div class="relative">
                    <button onclick="togglePartyDropdown()" id="partyFilterBtn" class="flex items-center justify-between min-w-[180px] max-w-[240px] px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 cursor-pointer shadow-sm">
                        <span class="truncate mr-2" id="partyFilterLabel">All Parties</span>
                        <i class="fas fa-chevron-down text-xs text-gray-500"></i>
                    </button>
                    <div id="partyDropdown" class="hidden absolute top-full left-0 mt-1 w-64 bg-white border border-gray-200 rounded-xl shadow-lg z-50 flex flex-col max-h-[300px]">
                        <div class="p-2 border-b">
                            <input type="text" id="partySearchInput" placeholder="Search party..." onkeyup="filterPartyList()" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div id="partyListContainer" class="overflow-y-auto flex-1 p-1 space-y-0.5"></div>
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

        <div class="px-6 py-3 border-t bg-white flex items-center justify-between gap-4 flex-wrap">
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

<div id="orderModal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/40 backdrop-blur-sm">
    <div class="bg-white w-full max-w-3xl rounded-3xl shadow-2xl overflow-hidden">
        <div class="flex items-center justify-between p-6 border-b">
            <h2 class="text-xl font-bold">New Order Entry</h2>
            <button onclick="toggleModal('orderModal')" class="w-10 h-10 rounded-full hover:bg-gray-100 flex items-center justify-center">
                <i class="fas fa-times text-gray-400"></i>
            </button>
        </div>

        <div class="p-8 space-y-6">
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Default Bill Type</div>
                    <div class="flex bg-gray-100 p-1 rounded-xl w-40">
                        <button type="button" id="newBillTypeA" onclick="setNewBillType('A')" class="flex-1 py-2 rounded-lg text-xs font-bold transition-all bg-white shadow-sm text-blue-600">A</button>
                        <button type="button" id="newBillTypeB" onclick="setNewBillType('B')" class="flex-1 py-2 rounded-lg text-xs font-bold transition-all text-gray-400">B</button>
                    </div>
                </div>

                <div>
                    <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Bill Products</div>
                    <div class="flex bg-gray-100 p-1 rounded-xl w-60">
                        <button type="button" id="btnTypeFer" onclick="setNewOrderType('Fer')" class="flex-1 py-2 rounded-lg text-xs font-bold transition-all bg-white shadow-sm text-blue-600">Fertilizer</button>
                        <button type="button" id="btnTypePes" onclick="setNewOrderType('Pes')" class="flex-1 py-2 rounded-lg text-xs font-bold transition-all text-gray-400">Pesticide</button>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Order Date</div>
                    <input id="newOrderDate" type="date" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500/20">
                </div>
                <div>
                    <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Salesman</div>
                    <select id="newSalesman" onchange="onNewSalesmanChange(this.value)" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium focus:ring-2 focus:ring-blue-500/20">
                        <option value="">Select Salesman...</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Party Name</div>
                    <select id="newParty" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium focus:ring-2 focus:ring-blue-500/20">
                        <option value="">Select Party...</option>
                    </select>
                </div>
                <div class="flex items-end justify-end">
                    <button type="button" onclick="addNewOrderItemFromQuickRow()" class="text-sm font-bold text-blue-600 hover:text-blue-700">
                        + Add Item
                    </button>
                </div>
            </div>

            <div class="space-y-2">
                <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Items</div>
                <div class="grid grid-cols-12 gap-2">
                    <div class="col-span-5">
                        <select id="newProduct" onchange="onNewProductChange()" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm">
                            <option value="">Select Product...</option>
                        </select>
                    </div>
                    <div class="col-span-3">
                        <select id="newPacking" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm">
                            <option value="Case">Case</option>
                        </select>
                    </div>
                    <div class="col-span-2">
                        <select id="newSize" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm">
                            <option value="">Select S</option>
                        </select>
                    </div>
                    <div class="col-span-2">
                        <input id="newQty" type="number" value="1" min="1" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm text-center">
                    </div>
                </div>
                <div id="newOrderItemsList" class="space-y-2"></div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4">
                <button type="button" onclick="toggleModal('orderModal')" class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl">Cancel</button>
                <button type="button" onclick="saveNewOrder()" class="px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg shadow-blue-200">Save Order</button>
            </div>
        </div>
    </div>
</div>

<div id="itemsModal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/40 backdrop-blur-sm">
    <div class="bg-white w-full max-w-3xl rounded-3xl shadow-2xl overflow-hidden">
        <div class="flex items-center justify-between p-6 border-b">
            <div>
                <h3 class="font-bold text-xl">Manage Order Items</h3>
                <div class="text-[10px] font-bold text-blue-600 uppercase tracking-widest mt-1" id="itemsModalOrderLabel">Order #00000</div>
            </div>
            <button onclick="toggleModal('itemsModal')" class="w-10 h-10 rounded-full hover:bg-gray-100 flex items-center justify-center">
                <i class="fas fa-times text-gray-400"></i>
            </button>
        </div>

        <div class="p-4 bg-blue-50/50 border-b">
            <div class="grid grid-cols-12 gap-2">
                <div class="col-span-5">
                    <select id="quickItemProduct" onchange="onQuickItemProductChange()" class="w-full px-3 py-2.5 bg-white border border-gray-200 rounded-lg text-sm">
                        <option value="">Select Product...</option>
                    </select>
                </div>
                <div class="col-span-3">
                    <select id="quickItemPacking" class="w-full px-3 py-2.5 bg-white border border-gray-200 rounded-lg text-sm">
                        <option value="Case">Case</option>
                    </select>
                </div>
                <div class="col-span-2">
                    <select id="quickItemSize" class="w-full px-3 py-2.5 bg-white border border-gray-200 rounded-lg text-sm">
                        <option value="">Select S</option>
                    </select>
                </div>
                <div class="col-span-1">
                    <input id="quickItemQty" type="number" min="1" value="1" class="w-full px-3 py-2.5 bg-white border border-gray-200 rounded-lg text-sm text-center">
                </div>
                <div class="col-span-1">
                    <button onclick="addItemToExistingOrder()" class="w-full h-full bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>
        </div>

        <div id="itemsList" class="p-6 space-y-3 max-h-[60vh] overflow-y-auto"></div>
    </div>
</div>

<div id="drawerOverlay" class="fixed inset-0 bg-black/30 hidden z-[90]" onclick="closeDrawer()"></div>
<div id="editDrawer" class="side-drawer fixed inset-y-0 right-0 w-[480px] bg-white shadow-2xl border-l border-gray-200 z-[95] overflow-y-auto">
    <div class="p-8">
        <div class="flex items-center justify-between mb-8">
            <div>
                <div class="text-2xl font-bold">Billing & Transport</div>
                <div class="text-xs text-gray-400 font-bold uppercase tracking-widest mt-1" id="drawerOrderId">#00000</div>
            </div>
            <button type="button" onclick="closeDrawer()" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-50 rounded-lg transition-all">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="editForm" class="space-y-6">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <div>
                    <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Bill Type</div>
                    <div class="flex bg-gray-100 p-1 rounded-xl w-32">
                        <button type="button" onclick="setDrawerBillType('A')" id="btnBillA" class="flex-1 py-1.5 rounded-lg text-xs font-bold transition-all">A</button>
                        <button type="button" onclick="setDrawerBillType('B')" id="btnBillB" class="flex-1 py-1.5 rounded-lg text-xs font-bold transition-all">B</button>
                    </div>
                    <input type="hidden" name="bill_type" id="field_bill_type" value="A">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Bill Date</div>
                        <input type="date" name="bill_date" id="field_bill_date" class="block w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500/20">
                    </div>
                    <div>
                        <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Bill Number</div>
                        <input type="text" name="bill_no" id="field_bill_no" class="block w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500/20">
                    </div>
                </div>

                <div>
                    <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Transport / Vehicle</div>
                    <select name="transport_id" id="field_transport_id" onchange="onDrawerTransportChange(this.value)" class="block w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500/20">
                        <option value="">Select Transport...</option>
                    </select>
                    <div class="mt-3 rounded-2xl bg-blue-50 p-4 border border-blue-100">
                        <div class="grid grid-cols-2 gap-2 text-xs text-gray-600 font-semibold">
                            <div>Vehicle Type:</div>
                            <div class="text-right" id="transportVehicle">---</div>
                            <div>Vehicle No:</div>
                            <div class="text-right" id="transportVehicleNo">---</div>
                            <div>Contact:</div>
                            <div class="text-right" id="transportContact">---</div>
                        </div>
                    </div>
                </div>

                <div>
                    <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Status</div>
                    <select name="status" id="field_status" class="block w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium focus:ring-2 focus:ring-blue-500/20">
                        <option value="Incomplete">Pending Billing</option>
                        <option value="Finalized">Completed</option>
                        <option value="Received">Receiving Done</option>
                        <option value="Okay">Okay</option>
                        <option value="Cancelled">Cancelled</option>
                    </select>
                </div>

                <div class="pt-4 border-t border-gray-100">
                    <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Receiving Proofs</div>
                    <div id="imageGrid" class="grid grid-cols-3 gap-2 mb-3"></div>
                    <input type="file" name="receiving_images[]" multiple class="block w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    <input type="hidden" name="existing_images" id="field_existing_images" value="[]">
                </div>
            </div>

            <div class="pt-6 flex items-center gap-3">
                <button type="button" onclick="deleteCurrentOrder()" class="px-6 py-3 bg-red-50 hover:bg-red-100 text-red-600 font-bold rounded-xl transition-all flex items-center gap-2">
                    <i class="fas fa-trash"></i> Delete
                </button>
                <button type="button" onclick="updateCurrentOrder()" class="flex-1 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl transition-all shadow-lg shadow-blue-200">Update Record</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>
<script>
    const ORDER_BOOK = {
        csrf: @json(csrf_token()),
        endpoints: {
            list: @json(route('orders.api.list')),
            detailsBase: @json(url('/orders/api/details')),
            itemsBulk: @json(route('orders.api.items.bulk')),
            salesmen: @json(route('orders.api.salesmen')),
            parties: @json(route('orders.api.parties')),
            transports: @json(route('orders.api.transports')),
            transportDetailsBase: @json(url('/orders/api/transports')),
            products: @json(route('orders.api.products')),
            productPackings: @json(route('orders.api.product-packings')),
            create: @json(route('orders.store'))
        }
    };

    let ordersData = [];
    let filteredOrders = [];
    let currentFilterStatus = 'All';
    let currentFilterCategory = 'All';
    let currentFilterBillType = 'All';
    let currentFilterSalesman = 'All';
    let currentFilterParty = 'All';
    let billNoSortDirection = null;
    let currentOrdersPage = 1;
    let ordersPageSize = 50;
    let orderItemsCache = {};
    let currentDrawerOrderId = null;
    let currentItemsOrderId = null;
    let itemsPrefetchToken = 0;
    let lastItemsPrefetchSearch = '';

    let newOrderBillType = 'A';
    let newOrderType = 'Fer';
    let newOrderItems = [];

    function showModal(id) {
        const el = document.getElementById(id);
        if (!el) return;
        el.classList.remove('hidden');
        el.classList.add('flex');
    }

    function hideModal(id) {
        const el = document.getElementById(id);
        if (!el) return;
        el.classList.add('hidden');
        el.classList.remove('flex');
    }

    function toggleModal(id) {
        const el = document.getElementById(id);
        if (!el) return;
        if (el.classList.contains('hidden')) showModal(id);
        else hideModal(id);
    }

    function formatIndianDate(input) {
        if (!input) return '';
        const d = new Date(input);
        if (Number.isNaN(d.getTime())) return '';
        return d.toLocaleDateString('en-IN', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        });
    }

    function normalizeImages(v) {
        if (!v) return [];
        if (Array.isArray(v)) return v.filter(Boolean);
        if (typeof v === 'string') {
            try {
                const parsed = JSON.parse(v);
                if (Array.isArray(parsed)) return parsed.filter(Boolean);
            } catch (e) {}
        }
        return [];
    }

    function hasReceivingFiles(order) {
        const imgs = normalizeImages(order.receiving_image_path);
        return imgs.length > 0;
    }

    async function fetchOrders() {
        const res = await fetch(ORDER_BOOK.endpoints.list, {
            headers: {
                'Accept': 'application/json'
            }
        });
        const data = await res.json();
        ordersData = Array.isArray(data) ? data : [];
        renderTable();
        updateStats();
        populateSalesmanFilterFromOrders();
        populatePartyFilterFromOrders();
    }

    function updateStats() {
        const total = ordersData.length;
        const countBy = (status) => ordersData.filter(o => o.status === status).length;
        document.getElementById('statTotal').innerText = total;
        document.getElementById('statIncomplete').innerText = countBy('Incomplete');
        document.getElementById('statComplete').innerText = countBy('Finalized');
        document.getElementById('statReceived').innerText = countBy('Received');
        document.getElementById('statOkay').innerText = countBy('Okay');
        document.getElementById('statCancelled').innerText = countBy('Cancelled');
    }

    function setActiveButton(group, btnEl) {
        document.querySelectorAll(`[data-filter-group="${group}"]`).forEach(b => b.classList.remove('active-cat'));
        if (btnEl) btnEl.classList.add('active-cat');
    }

    function filterByStatus(status, element) {
        currentFilterStatus = status;
        document.querySelectorAll('.filter-card').forEach(card => card.classList.remove('active-filter'));
        if (element) element.classList.add('active-filter');
        resetOrdersPage();
        renderTable();
    }

    function filterByCategory(type, btn) {
        currentFilterCategory = type;
        setActiveButton('category', btn);
        resetOrdersPage();
        renderTable();
    }

    function filterByBillType(type, btn) {
        currentFilterBillType = type;
        setActiveButton('billType', btn);
        resetOrdersPage();
        renderTable();
    }

    function filterBySalesman(val) {
        currentFilterSalesman = val || 'All';
        resetOrdersPage();
        renderTable();
    }

    function setPartyFilter(val, label) {
        currentFilterParty = val;
        document.getElementById('partyFilterLabel').innerText = label || 'All Parties';
        document.getElementById('partyDropdown').classList.add('hidden');
        resetOrdersPage();
        renderTable();
    }

    function togglePartyDropdown() {
        const dd = document.getElementById('partyDropdown');
        if (!dd) return;
        dd.classList.toggle('hidden');
        if (!dd.classList.contains('hidden')) {
            document.getElementById('partySearchInput').focus();
        }
    }

    function filterPartyList() {
        const term = (document.getElementById('partySearchInput').value || '').toLowerCase();
        document.querySelectorAll('#partyListContainer [data-party-item]').forEach(el => {
            const text = (el.getAttribute('data-party-label') || '').toLowerCase();
            el.style.display = text.includes(term) ? 'flex' : 'none';
        });
    }

    function populateSalesmanFilterFromOrders() {
        const select = document.getElementById('salesmanFilter');
        const currentVal = select.value;
        const salesmen = [...new Set(ordersData.map(o => o.salesman).filter(Boolean))].sort();
        select.innerHTML = '<option value="All">All Salesmen</option>';
        salesmen.forEach(name => {
            const opt = document.createElement('option');
            opt.value = name;
            opt.innerText = name;
            if (name === currentVal) opt.selected = true;
            select.appendChild(opt);
        });
    }

    function populatePartyFilterFromOrders() {
        const parties = [...new Set(ordersData.map(o => o.party).filter(Boolean))].sort();
        const container = document.getElementById('partyListContainer');
        container.innerHTML = '';
        const all = document.createElement('button');
        all.type = 'button';
        all.className = 'w-full flex items-center justify-between px-3 py-2 rounded-lg text-sm hover:bg-gray-50';
        all.setAttribute('data-party-item', '1');
        all.setAttribute('data-party-label', 'All Parties');
        all.onclick = () => setPartyFilter('All', 'All Parties');
        all.innerHTML = '<span>All Parties</span><span class="text-xs text-gray-400 font-bold">ALL</span>';
        container.appendChild(all);
        parties.forEach(p => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'w-full flex items-center px-3 py-2 rounded-lg text-sm hover:bg-gray-50';
            btn.setAttribute('data-party-item', '1');
            btn.setAttribute('data-party-label', p);
            btn.onclick = () => setPartyFilter(p, p);
            btn.innerText = p;
            container.appendChild(btn);
        });
    }

    function toggleBillNoSort() {
        if (!billNoSortDirection) billNoSortDirection = 'asc';
        else if (billNoSortDirection === 'asc') billNoSortDirection = 'desc';
        else billNoSortDirection = null;
        resetOrdersPage();
        renderTable();
    }

    function getBillNoSortKey(billNo) {
        const raw = (billNo || '').toString();
        const parts = raw.split('/');
        const yearRaw = parts.length ? parts[parts.length - 1] : '';
        const yearDigits = (yearRaw || '').replace(/[^0-9]/g, '');
        const year = yearDigits ? parseInt(yearDigits, 10) : 0;
        const midRaw = parts.length >= 3 ? parts[2] : '';
        const midDigits = (midRaw || '').replace(/[^0-9]/g, '');
        const seq = midDigits ? parseInt(midDigits, 10) : 0;
        return {
            year,
            seq,
            raw
        };
    }

    function resetOrdersPage() {
        currentOrdersPage = 1;
    }

    function setOrdersPageSize(n) {
        ordersPageSize = parseInt(n, 10) || 50;
        resetOrdersPage();
        renderTable();
    }

    function goToOrdersPage(n) {
        currentOrdersPage = Math.max(1, n);
        renderTable();
    }

    async function getItemsForOrders(orderIds) {
        const missing = orderIds.filter(id => !orderItemsCache[id]);
        if (missing.length === 0) return;
        const res = await fetch(ORDER_BOOK.endpoints.itemsBulk, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': ORDER_BOOK.csrf,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                ids: missing
            })
        });
        const byOrder = await res.json();
        missing.forEach(id => {
            const key = String(id);
            const items = byOrder && typeof byOrder === 'object' ? byOrder[key] : null;
            orderItemsCache[id] = Array.isArray(items) ? items : [];
        });
    }

    async function renderTable() {
        const tbody = document.getElementById('orderTableBody');
        const rawSearch = document.getElementById('searchInput').value || '';
        const search = rawSearch.toLowerCase().trim();

        if (search.length === 0) {
            itemsPrefetchToken++;
            lastItemsPrefetchSearch = '';
        } else if (search.length >= 2 && lastItemsPrefetchSearch !== search) {
            lastItemsPrefetchSearch = search;
            const token = ++itemsPrefetchToken;
            const ids = ordersData.map(o => o.id);
            const batchSize = 200;
            (async () => {
                for (let i = 0; i < ids.length; i += batchSize) {
                    if (itemsPrefetchToken !== token) return;
                    const currentSearch = (document.getElementById('searchInput').value || '').toLowerCase().trim();
                    if (currentSearch !== search) return;
                    await getItemsForOrders(ids.slice(i, i + batchSize));
                    if (itemsPrefetchToken !== token) return;
                    const currentSearchAfter = (document.getElementById('searchInput').value || '').toLowerCase().trim();
                    if (currentSearchAfter !== search) return;
                    if (i === 0 || ((i / batchSize) % 3) === 0) renderTable();
                }
                if (itemsPrefetchToken !== token) return;
                const currentSearchEnd = (document.getElementById('searchInput').value || '').toLowerCase().trim();
                if (currentSearchEnd !== search) return;
                renderTable();
            })();
        }

        filteredOrders = ordersData.filter(o => {
            const matchesStatus = currentFilterStatus === 'All' || o.status === currentFilterStatus;
            let matchesCategory = true;
            if (currentFilterCategory === 'MissingFiles') {
                matchesCategory = (o.status === 'Received' || o.status === 'Okay') && !hasReceivingFiles(o);
            } else {
                matchesCategory = currentFilterCategory === 'All' || o.type === currentFilterCategory;
            }
            const matchesBillType = currentFilterBillType === 'All' || o.bill_type === currentFilterBillType;
            const matchesSalesman = currentFilterSalesman === 'All' || o.salesman === currentFilterSalesman;
            const matchesParty = currentFilterParty === 'All' || o.party === currentFilterParty;

            let productMatch = false;
            if (search && orderItemsCache[o.id]) {
                productMatch = orderItemsCache[o.id].some(it => ((it.product || '') + '').toLowerCase().includes(search));
            }

            const matchesSearch = search === '' ? true : (
                ((o.party || '') + '').toLowerCase().includes(search) ||
                ((o.salesman || '') + '').toLowerCase().includes(search) ||
                ((o.bill_no || '') + '').toLowerCase().includes(search) ||
                productMatch
            );

            return matchesStatus && matchesCategory && matchesBillType && matchesSalesman && matchesParty && matchesSearch;
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
        const maxPage = Math.max(1, Math.ceil(total / ordersPageSize));
        if (currentOrdersPage > maxPage) currentOrdersPage = maxPage;
        const startIndex = (currentOrdersPage - 1) * ordersPageSize;
        const pageOrders = filteredOrders.slice(startIndex, startIndex + ordersPageSize);

        document.getElementById('ordersPaginationInfo').innerText = total === 0 ?
            'Showing 0-0 of 0' :
            `Showing ${startIndex + 1}-${Math.min(startIndex + ordersPageSize, total)} of ${total}`;
        document.getElementById('ordersPageIndicator').innerText = `Page ${currentOrdersPage}/${maxPage}`;
        document.getElementById('ordersPrevBtn').disabled = currentOrdersPage <= 1;
        document.getElementById('ordersNextBtn').disabled = currentOrdersPage >= maxPage;

        tbody.innerHTML = pageOrders.map(o => {
            let statusText = o.status;
            let badgeClass = '';
            if (o.status === 'Finalized') {
                statusText = 'Completed';
                badgeClass = 'bg-blue-100 text-blue-700';
            } else if (o.status === 'Incomplete') {
                statusText = 'Pending';
                badgeClass = 'status-incomplete';
            } else if (o.status === 'Received') {
                badgeClass = 'bg-green-100 text-green-700';
            } else if (o.status === 'Cancelled') {
                badgeClass = 'bg-red-100 text-red-600';
            } else if (o.status === 'Okay') {
                statusText = 'Okay';
                badgeClass = 'status-okay';
            } else {
                badgeClass = 'bg-gray-100 text-gray-700';
            }

            return `
                <tr class="order-row border-b last:border-0" onclick="openDrawer(${o.id})">
                    <td class="px-6 py-4">
                        <p class="font-bold text-sm">#${String(o.id).padStart(5, '0')}</p>
                        <p class="text-xs text-gray-500">${formatIndianDate(o.order_date)}</p>
                    </td>
                    <td class="px-6 py-4">
                        <p class="font-semibold">${o.party || ''}</p>
                        <p class="text-xs text-blue-600 font-medium">${o.salesman || ''}</p>
                    </td>
                    <td class="px-6 py-4">
                        <button onclick="openItems(${o.id}); event.stopPropagation();" class="flex items-center gap-2 px-3 py-1.5 bg-blue-50 text-blue-700 rounded-lg border border-blue-100 hover:bg-blue-100">
                            <i class="fas fa-boxes text-xs"></i>
                            <span class="text-xs font-bold">${o.items_count || 0}</span>
                        </button>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] px-1.5 py-0.5 bg-gray-100 rounded font-bold">${o.bill_type || ''}</span>
                            <p class="text-xs font-mono">${o.bill_no || '---'}</p>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="status-badge ${badgeClass}">${statusText}</span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <i class="fas fa-chevron-right text-gray-300"></i>
                    </td>
                </tr>
            `;
        }).join('');
    }

    async function openDrawer(orderId) {
        currentDrawerOrderId = orderId;
        const overlay = document.getElementById('drawerOverlay');
        overlay.classList.remove('hidden');
        const drawer = document.getElementById('editDrawer');
        drawer.classList.add('open');
        document.getElementById('drawerOrderId').innerText = `#${String(orderId).padStart(5, '0')}`;

        const res = await fetch(`${ORDER_BOOK.endpoints.detailsBase}/${orderId}`, {
            headers: {
                'Accept': 'application/json'
            }
        });
        const data = await res.json();
        const order = data && data.order ? data.order : null;
        if (!order) return;

        setDrawerBillType(order.bill_type || 'A');
        document.getElementById('field_bill_date').value = order.bill_date ? order.bill_date.split('T')[0] : '';
        document.getElementById('field_bill_no').value = order.bill_no || '';
        document.getElementById('field_status').value = order.status || 'Incomplete';

        document.getElementById('field_transport_id').value = order.transport_id || '';
        if (order.transport_id) {
            await onDrawerTransportChange(order.transport_id);
        } else {
            setTransportPreview(null);
        }

        const imgs = normalizeImages(order.receiving_image_path);
        document.getElementById('field_existing_images').value = JSON.stringify(imgs);
        renderImageGrid(imgs);
    }

    function closeDrawer() {
        document.getElementById('drawerOverlay').classList.add('hidden');
        document.getElementById('editDrawer').classList.remove('open');
        currentDrawerOrderId = null;
    }

    function setDrawerBillType(type) {
        document.getElementById('field_bill_type').value = type;
        const btnA = document.getElementById('btnBillA');
        const btnB = document.getElementById('btnBillB');
        if (type === 'A') {
            btnA.className = 'flex-1 py-1.5 rounded-lg text-xs font-bold bg-white shadow-sm text-blue-600';
            btnB.className = 'flex-1 py-1.5 rounded-lg text-xs font-bold text-gray-400';
        } else {
            btnB.className = 'flex-1 py-1.5 rounded-lg text-xs font-bold bg-white shadow-sm text-purple-600';
            btnA.className = 'flex-1 py-1.5 rounded-lg text-xs font-bold text-gray-400';
        }
    }

    function renderImageGrid(images) {
        const grid = document.getElementById('imageGrid');
        grid.innerHTML = '';
        images.forEach((path, index) => {
            const div = document.createElement('div');
            div.className = 'relative aspect-square rounded-lg border border-gray-200 overflow-hidden group';
            div.innerHTML = `
                <img src="/${path}" class="w-full h-full object-cover">
                <button type="button" onclick="removeExistingImage(${index})" class="absolute inset-0 bg-red-600/80 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                    <i class="fas fa-trash text-white"></i>
                </button>
            `;
            grid.appendChild(div);
        });
    }

    function removeExistingImage(index) {
        const images = JSON.parse(document.getElementById('field_existing_images').value || '[]');
        images.splice(index, 1);
        document.getElementById('field_existing_images').value = JSON.stringify(images);
        renderImageGrid(images);
    }

    async function updateCurrentOrder() {
        if (!currentDrawerOrderId) return;
        const formData = new FormData(document.getElementById('editForm'));
        try {
            const response = await fetch(`/orders/${currentDrawerOrderId}`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            const data = await response.json();
            if (data && data.success) {
                closeDrawer();
                await fetchOrders();
            } else {
                alert(data && data.error ? data.error : 'Update failed');
            }
        } catch (e) {
            alert('Update failed');
        }
    }

    async function deleteCurrentOrder() {
        if (!currentDrawerOrderId) return;
        if (!confirm('Are you sure you want to delete this order?')) return;
        try {
            const response = await fetch(`/orders/${currentDrawerOrderId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': ORDER_BOOK.csrf,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            const data = await response.json();
            if (data && data.success) {
                closeDrawer();
                await fetchOrders();
            } else {
                alert('Delete failed');
            }
        } catch (e) {
            alert('Delete failed');
        }
    }

    async function openItems(orderId) {
        currentItemsOrderId = orderId;
        showModal('itemsModal');
        document.getElementById('itemsModalOrderLabel').innerText = `ORDER #${String(orderId).padStart(5, '0')}`;
        await refreshItemsModal(orderId);
        await ensureQuickProductsLoaded();
    }

    async function refreshItemsModal(orderId) {
        if (!orderId) return;
        const res = await fetch(`${ORDER_BOOK.endpoints.detailsBase}/${orderId}`, {
            headers: {
                'Accept': 'application/json'
            }
        });
        const data = await res.json();
        const order = data && data.order ? data.order : null;
        const items = order && Array.isArray(order.items) ? order.items : [];
        renderItemsList(items);
    }

    function renderItemsList(items) {
        const list = document.getElementById('itemsList');
        list.innerHTML = '';
        items.forEach(item => {
            const div = document.createElement('div');
            div.className = 'flex items-center justify-between p-3 bg-gray-50 rounded-xl border border-gray-100';
            div.innerHTML = `
                <div>
                    <div class="text-sm font-bold text-gray-900">${item.product}</div>
                    <div class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">${item.packing || 'Case'} ${item.size || ''}</div>
                </div>
                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-2">
                        <button onclick="updateItemQty(${item.id}, ${item.quantity - 1})" class="w-6 h-6 rounded bg-white border border-gray-200 flex items-center justify-center text-gray-400 hover:text-gray-600">-</button>
                        <span class="text-sm font-bold w-6 text-center">${item.quantity}</span>
                        <button onclick="updateItemQty(${item.id}, ${item.quantity + 1})" class="w-6 h-6 rounded bg-white border border-gray-200 flex items-center justify-center text-gray-400 hover:text-gray-600">+</button>
                    </div>
                    <button onclick="deleteItem(${item.id})" class="text-red-400 hover:text-red-600"><i class="fas fa-times"></i></button>
                </div>
            `;
            list.appendChild(div);
        });
    }

    async function updateItemQty(itemId, newQty) {
        if (newQty < 1) return;
        await fetch(`/order-items/${itemId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': ORDER_BOOK.csrf,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                quantity: newQty
            })
        });
        await refreshItemsModal(currentItemsOrderId);
        await fetchOrders();
    }

    async function deleteItem(itemId) {
        if (!confirm('Delete this item?')) return;
        await fetch(`/order-items/${itemId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': ORDER_BOOK.csrf,
                'Accept': 'application/json'
            }
        });
        await refreshItemsModal(currentItemsOrderId);
        await fetchOrders();
    }

                                async function loadSalesmenForCreate() {
                                    const res = await fetch(ORDER_BOOK.endpoints.salesmen, {
                                        headers: {
                                            'Accept': 'application/json'
                                        }
                                    });
                                    const salesmen = await res.json();
                                    const select = document.getElementById('newSalesman');
                                    select.innerHTML = '<option value="">Select Salesman...</option>';
                                    (Array.isArray(salesmen) ? salesmen : []).forEach(s => {
                                        const opt = document.createElement('option');
                                        opt.value = s.id;
                                        opt.innerText = s.name;
                                        select.appendChild(opt);
                                    });
                                }

                                async function onNewSalesmanChange(salesmanId) {
                                    const partySelect = document.getElementById('newParty');
                                    partySelect.innerHTML = '<option value="">Select Party...</option>';
                                    if (!salesmanId) return;
                                    const res = await fetch(`${ORDER_BOOK.endpoints.parties}?salesman_id=${encodeURIComponent(salesmanId)}`, {
                                        headers: {
                                            'Accept': 'application/json'
                                        }
                                    });
                                    const parties = await res.json();
                                    (Array.isArray(parties) ? parties : []).forEach(p => {
                                        const opt = document.createElement('option');
                                        opt.value = p.id;
                                        opt.innerText = p.name;
                                        partySelect.appendChild(opt);
                                    });
                                }

                                function setNewBillType(type) {
                                    newOrderBillType = type;
                                    const btnA = document.getElementById('newBillTypeA');
                                    const btnB = document.getElementById('newBillTypeB');
                                    if (type === 'A') {
                                        btnA.className = 'flex-1 py-2 rounded-lg text-xs font-bold transition-all bg-white shadow-sm text-blue-600';
                                        btnB.className = 'flex-1 py-2 rounded-lg text-xs font-bold transition-all text-gray-400';
                                    } else {
                                        btnB.className = 'flex-1 py-2 rounded-lg text-xs font-bold transition-all bg-white shadow-sm text-purple-600';
                                        btnA.className = 'flex-1 py-2 rounded-lg text-xs font-bold transition-all text-gray-400';
                                    }
                                }

                                async function setNewOrderType(type) {
                                    newOrderType = type;
                                    const btnFer = document.getElementById('btnTypeFer');
                                    const btnPes = document.getElementById('btnTypePes');
                                    if (type === 'Fer') {
                                        btnFer.className = 'flex-1 py-2 rounded-lg text-xs font-bold transition-all bg-white shadow-sm text-blue-600';
                                        btnPes.className = 'flex-1 py-2 rounded-lg text-xs font-bold transition-all text-gray-400';
                                    } else {
                                        btnPes.className = 'flex-1 py-2 rounded-lg text-xs font-bold transition-all bg-white shadow-sm text-blue-600';
                                        btnFer.className = 'flex-1 py-2 rounded-lg text-xs font-bold transition-all text-gray-400';
                                    }
                                    await loadProductsForType(type);
                                }

                                async function loadProductsForType(type) {
                                    const res = await fetch(`${ORDER_BOOK.endpoints.products}?type=${encodeURIComponent(type)}`, {
                                        headers: {
                                            'Accept': 'application/json'
                                        }
                                    });
                                    const products = await res.json();
                                    const select = document.getElementById('newProduct');
                                    select.innerHTML = '<option value="">Select Product...</option>';
                                    (Array.isArray(products) ? products : []).forEach(p => {
                                        const opt = document.createElement('option');
                                        opt.value = p.id;
                                        opt.innerText = p.name;
                                        select.appendChild(opt);
                                    });
                                    const quick = document.getElementById('quickItemProduct');
                                    quick.innerHTML = '<option value="">Select Product...</option>';
                                    (Array.isArray(products) ? products : []).forEach(p => {
                                        const opt = document.createElement('option');
                                        opt.value = p.id;
                                        opt.innerText = p.name;
                                        quick.appendChild(opt);
                                    });
                                }

                                async function onNewProductChange() {
                                    const productId = document.getElementById('newProduct').value;
                                    await loadPackingsAndSizes(productId, 'newPacking', 'newSize');
                                }

                                async function onQuickItemProductChange() {
                                    const productId = document.getElementById('quickItemProduct').value;
                                    await loadPackingsAndSizes(productId, 'quickItemPacking', 'quickItemSize');
                                }

                                async function loadPackingsAndSizes(productId, packingElId, sizeElId) {
                                    const packingEl = document.getElementById(packingElId);
                                    const sizeEl = document.getElementById(sizeElId);
                                    packingEl.innerHTML = '<option value="Case">Case</option>';
                                    sizeEl.innerHTML = '<option value="">Select S</option>';
                                    if (!productId) return;
                                    const res = await fetch(`${ORDER_BOOK.endpoints.productPackings}?product_id=${encodeURIComponent(productId)}`, {
                                        headers: {
                                            'Accept': 'application/json'
                                        }
                                    });
                                    const data = await res.json();
                                    const packings = data && Array.isArray(data.packings) ? data.packings : [];
                                    const sizes = data && Array.isArray(data.sizes) ? data.sizes : [];
                                    packingEl.innerHTML = '<option value="Case">Case</option>';
                                    packings.forEach(p => {
                                        const opt = document.createElement('option');
                                        opt.value = p;
                                        opt.innerText = p;
                                        packingEl.appendChild(opt);
                                    });
                                    sizeEl.innerHTML = '<option value="">Select S</option>';
                                    sizes.forEach(s => {
                                        const opt = document.createElement('option');
                                        opt.value = s;
                                        opt.innerText = s;
                                        sizeEl.appendChild(opt);
                                    });
                                }

                                function renderNewOrderItems() {
                                    const list = document.getElementById('newOrderItemsList');
                                    list.innerHTML = '';
                                    newOrderItems.forEach((it, idx) => {
                                        const row = document.createElement('div');
                                        row.className = 'flex items-center justify-between p-3 bg-gray-50 rounded-xl border border-gray-100';
                                        row.innerHTML = `
                <div>
                    <div class="text-sm font-bold text-gray-900">${it.product_name}</div>
                    <div class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">${it.packing || 'Case'} ${it.size || ''}</div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="text-sm font-bold">${it.quantity}</div>
                    <button onclick="removeNewOrderItem(${idx})" class="text-red-400 hover:text-red-600"><i class="fas fa-trash"></i></button>
                </div>
            `;
                                        list.appendChild(row);
                                    });
                                }

                                function removeNewOrderItem(idx) {
                                    newOrderItems.splice(idx, 1);
                                    renderNewOrderItems();
                                }

                                async function addNewOrderItemFromQuickRow() {
                                    const productId = document.getElementById('newProduct').value;
                                    const productName = document.getElementById('newProduct').selectedOptions[0]?.textContent || '';
                                    const packing = document.getElementById('newPacking').value;
                                    const size = document.getElementById('newSize').value;
                                    const qty = parseInt(document.getElementById('newQty').value, 10) || 1;
                                    if (!productId) return alert('Select product');
                                    newOrderItems.push({
                                        product_id: parseInt(productId, 10),
                                        product_name: productName,
                                        packing,
                                        size,
                                        quantity: qty
                                    });
                                    renderNewOrderItems();
                                    document.getElementById('newProduct').value = '';
                                    document.getElementById('newPacking').innerHTML = '<option value="Case">Case</option>';
                                    document.getElementById('newSize').innerHTML = '<option value="">Select S</option>';
                                    document.getElementById('newQty').value = 1;
                                }

                                async function saveNewOrder() {
                                    const orderDate = document.getElementById('newOrderDate').value;
                                    const salesmanId = document.getElementById('newSalesman').value;
                                    const partyId = document.getElementById('newParty').value;
                                    if (!orderDate) return alert('Select order date');
                                    if (!salesmanId) return alert('Select salesman');
                                    if (!partyId) return alert('Select party');
                                    if (newOrderItems.length === 0) {
                                        const productId = document.getElementById('newProduct').value;
                                        if (productId) {
                                            const productName = document.getElementById('newProduct').selectedOptions[0]?.textContent || '';
                                            const packing = document.getElementById('newPacking').value;
                                            const size = document.getElementById('newSize').value;
                                            const qty = parseInt(document.getElementById('newQty').value, 10) || 1;
                                            newOrderItems.push({
                                                product_id: parseInt(productId, 10),
                                                product_name: productName,
                                                packing,
                                                size,
                                                quantity: qty
                                            });
                                            renderNewOrderItems();
                                        }
                                    }

                                    if (newOrderItems.length === 0) return alert('Add at least 1 item');

                                    const payload = {
                                        order_date: orderDate,
                                        type: newOrderType,
                                        bill_type: newOrderBillType,
                                        status: 'Incomplete',
                                        salesman_id: parseInt(salesmanId, 10),
                                        party_id: parseInt(partyId, 10),
                                        items: newOrderItems.map(it => ({
                                            product_id: it.product_id,
                                            packing: it.packing || null,
                                            size: it.size || null,
                                            quantity: it.quantity
                                        }))
                                    };

                                    const res = await fetch(ORDER_BOOK.endpoints.create, {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': ORDER_BOOK.csrf,
                                            'Accept': 'application/json'
                                        },
                                        body: JSON.stringify(payload)
                                    });
                                    const data = await res.json();
                                    if (data && data.success) {
                                        toggleModal('orderModal');
                                        newOrderItems = [];
                                        renderNewOrderItems();
                                        await fetchOrders();
                                    } else {
                                        alert(data && data.error ? data.error : 'Failed to save order');
                                    }
                                }

                                async function ensureQuickProductsLoaded() {
                                    const hasOptions = document.getElementById('quickItemProduct').options.length > 1;
                                    if (hasOptions) return;
                                    await loadProductsForType(newOrderType);
                                }

                                async function addItemToExistingOrder() {
                                    if (!currentItemsOrderId) return;
                                    const productId = document.getElementById('quickItemProduct').value;
                                    const packing = document.getElementById('quickItemPacking').value;
                                    const size = document.getElementById('quickItemSize').value;
                                    const qty = parseInt(document.getElementById('quickItemQty').value, 10) || 1;
                                    if (!productId) return alert('Select product');
                                    const res = await fetch(`/orders/${currentItemsOrderId}/items`, {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': ORDER_BOOK.csrf,
                                            'Accept': 'application/json'
                                        },
                                        body: JSON.stringify({
                                            product_id: parseInt(productId, 10),
                                            packing: packing || null,
                                            size: size || null,
                                            quantity: qty
                                        })
                                    });
                                    const data = await res.json();
                                    if (data && data.success) {
                                        await openItems(currentItemsOrderId);
                                        await fetchOrders();
                                    } else {
                                        alert(data && data.error ? data.error : 'Failed to add item');
                                    }
                                }

                                async function loadTransports() {
                                    const res = await fetch(ORDER_BOOK.endpoints.transports, {
                                        headers: {
                                            'Accept': 'application/json'
                                        }
                                    });
                                    const transports = await res.json();
                                    const select = document.getElementById('field_transport_id');
                                    const currentVal = select.value;
                                    select.innerHTML = '<option value="">Select Transport...</option>';
                                    (Array.isArray(transports) ? transports : []).forEach(t => {
                                        const opt = document.createElement('option');
                                        opt.value = t.id;
                                        opt.innerText = t.name;
                                        if (String(t.id) === String(currentVal)) opt.selected = true;
                                        select.appendChild(opt);
                                    });
                                }

                                function setTransportPreview(t) {
                                    document.getElementById('transportVehicle').innerText = t && t.vehicle ? t.vehicle : '---';
                                    document.getElementById('transportVehicleNo').innerText = t && t.vehicle_number ? t.vehicle_number : '---';
                                    document.getElementById('transportContact').innerText = t && t.contact ? t.contact : '---';
                                }

                                async function onDrawerTransportChange(transportId) {
                                    if (!transportId) {
                                        setTransportPreview(null);
                                        return;
                                    }
                                    const res = await fetch(`${ORDER_BOOK.endpoints.transportDetailsBase}/${encodeURIComponent(transportId)}`, {
                                        headers: {
                                            'Accept': 'application/json'
                                        }
                                    });
                                    const data = await res.json();
                                    setTransportPreview(data);
                                }

                                function exportToCSV() {
                                    const rows = filteredOrders.map(o => ({
                                        OrderID: o.id,
                                        OrderDate: formatIndianDate(o.order_date),
                                        Salesman: o.salesman || '',
                                        Party: o.party || '',
                                        Type: o.type || '',
                                        BillType: o.bill_type || '',
                                        BillNo: o.bill_no || '',
                                        Status: o.status || '',
                                        Items: o.items_count || 0
                                    }));
                                    const header = Object.keys(rows[0] || {}).join(',');
                                    const lines = rows.map(r => Object.values(r).map(v => `"${String(v).replaceAll('"', '""')}"`).join(','));
                                    const csv = [header, ...lines].join('\n');
                                    const blob = new Blob([csv], {
                                        type: 'text/csv;charset=utf-8;'
                                    });
                                    const url = URL.createObjectURL(blob);
                                    const a = document.createElement('a');
                                    a.href = url;
                                    a.download = 'orders.csv';
                                    document.body.appendChild(a);
                                    a.click();
                                    a.remove();
                                    URL.revokeObjectURL(url);
                                }

                                function exportToExcel() {
                                    const rows = filteredOrders.map(o => ({
                                        OrderID: o.id,
                                        OrderDate: formatIndianDate(o.order_date),
                                        Salesman: o.salesman || '',
                                        Party: o.party || '',
                                        Type: o.type || '',
                                        BillType: o.bill_type || '',
                                        BillNo: o.bill_no || '',
                                        Status: o.status || '',
                                        Items: o.items_count || 0
                                    }));
                                    const ws = XLSX.utils.json_to_sheet(rows);
                                    const wb = XLSX.utils.book_new();
                                    XLSX.utils.book_append_sheet(wb, ws, 'Orders');
                                    XLSX.writeFile(wb, 'orders.xlsx');
                                }

                                function exportToPDF() {
                                    const {
                                        jsPDF
                                    } = window.jspdf;
                                    const doc = new jsPDF({
                                        orientation: 'landscape'
                                    });
                                    const head = [
                                        ['ID', 'Date', 'Salesman', 'Party', 'Type', 'Bill', 'Status', 'Items']
                                    ];
                                    const body = filteredOrders.map(o => ([
                                        String(o.id).padStart(5, '0'),
                                        formatIndianDate(o.order_date),
                                        o.salesman || '',
                                        o.party || '',
                                        o.type || '',
                                        `${o.bill_type || ''} ${o.bill_no || ''}`.trim(),
                                        o.status || '',
                                        String(o.items_count || 0)
                                    ]));
                                    doc.autoTable({
                                        head,
                                        body,
                                        styles: {
                                            fontSize: 8
                                        }
                                    });
                                    doc.save('orders.pdf');
                                }

                                document.addEventListener('click', (e) => {
                                    const dd = document.getElementById('partyDropdown');
                                    if (!dd || dd.classList.contains('hidden')) return;
                                    const inBtn = e.target.closest('#partyFilterBtn');
                                    const inDd = e.target.closest('#partyDropdown');
                                    if (!inBtn && !inDd) dd.classList.add('hidden');
                                });

                                document.getElementById('searchInput').addEventListener('input', () => {
                                    resetOrdersPage();
                                    renderTable();
                                });

                                document.addEventListener('keydown', (e) => {
                                    if (e.key === 'Escape') {
                                        document.getElementById('partyDropdown')?.classList.add('hidden');
                                        if (!document.getElementById('orderModal').classList.contains('hidden')) toggleModal('orderModal');
                                        if (!document.getElementById('itemsModal').classList.contains('hidden')) toggleModal('itemsModal');
                                        if (document.getElementById('editDrawer').classList.contains('open')) closeDrawer();
                                    }
                                });

                                (async () => {
                                    const today = new Date().toISOString().slice(0, 10);
                                    document.getElementById('newOrderDate').value = today;
                                    await loadSalesmenForCreate();
                                    await loadProductsForType('Fer');
                                    await loadTransports();
                                    await fetchOrders();
                                })();
</script>
@endpush

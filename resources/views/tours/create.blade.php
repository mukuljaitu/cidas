@extends('layouts.admin')

@section('title', 'Create Tour - ' . $state)

@section('content')
@php
$statuses = [
['key' => 'Field Visit', 'icon' => 'M5.5 2a.5.5 0 0 0 0 1h.278l.72 7.22A2 2 0 0 0 8.49 12h1.02a2 2 0 0 0 1.992-1.78L12.222 3H12.5a.5.5 0 0 0 0-1H5.5Z'],
['key' => 'Office Visit', 'icon' => 'M2 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2Zm2-1a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H4Z'],
['key' => 'Leave', 'icon' => 'M6 2a2 2 0 0 1 4 0v2h1a1 1 0 0 1 1 1v9a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V5a1 1 0 0 1 1-1h1V2Zm1 2h2V2a1 1 0 0 0-2 0v2Z'],
['key' => 'Holiday', 'icon' => 'M4 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H4Zm2 4.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5ZM6 8a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3A.5.5 0 0 1 6 8Z'],
['key' => 'No Station', 'icon' => 'M8 0a5 5 0 0 1 5 5c0 3-3 7-5 9-2-2-5-6-5-9a5 5 0 0 1 5-5Zm0 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4Z'],
];
@endphp

@push('styles')
<style>
    .emp-card.active {
        border-color: #3B82F6 !important;
        background-color: #EFF6FF !important;
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.1);
    }

    .date-pill {
        min-width: 65px;
        height: 75px;
        background: white;
        border: 1px solid #E2E8F0;
        border-radius: 12px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        flex-shrink: 0;
        transition: all 0.2s ease;
    }

    .date-pill.active {
        background: #3B82F6;
        color: white;
        border-color: #3B82F6;
        box-shadow: 0 8px 16px rgba(59, 130, 246, 0.2);
    }

    .date-pill.disabled {
        opacity: 0.4;
        cursor: not-allowed;
    }

    .control-item {
        background: white;
        padding: 14px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        gap: 12px;
        cursor: pointer;
        border: 1px solid #E2E8F0;
        margin-bottom: 10px;
        font-size: 0.9rem;
        font-weight: 600;
        color: #475569;
        transition: all 0.2s ease;
    }

    .control-item:hover {
        border-color: #3B82F6;
        background: #F8FAFC;
    }

    .control-item.active {
        border-color: #3B82F6;
        color: #1D4ED8;
        background: #EFF6FF;
    }

    .node {
        background: white;
        border-radius: 10px;
        padding: 12px;
        text-align: center;
        font-size: 0.85rem;
        font-weight: 600;
        border: 1px solid #E2E8F0;
        cursor: pointer;
        transition: all 0.2s ease;
        color: #475569;
    }

    .node:hover {
        border-color: #3B82F6;
    }

    .node.active {
        border-color: #10B981;
        color: #065F46;
        background: #ECFDF5;
    }

    .switch {
        position: relative;
        display: inline-block;
        width: 44px;
        height: 24px;
    }

    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #CBD5E1;
        transition: .4s;
        border-radius: 24px;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 18px;
        width: 18px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
    }

    input:checked+.slider {
        background-color: #3B82F6;
    }

    input:checked+.slider:before {
        transform: translateX(20px);
    }

    .month-picker {
        position: absolute;
        z-index: 50;
        right: 0;
        top: 100%;
        margin-top: 8px;
        width: 280px;
        background: #fff;
        border: 1px solid #E2E8F0;
        border-radius: 16px;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        display: none;
    }

    .month-picker.active {
        display: block;
    }

    .month-item {
        padding: 10px;
        text-align: center;
        border: 1px solid #F1F5F9;
        border-radius: 10px;
        cursor: pointer;
        font-weight: 600;
        font-size: 0.9rem;
        background: white;
        transition: all 0.2s;
    }

    .month-item:hover:not(.disabled) {
        border-color: #3B82F6;
        color: #3B82F6;
        background: #EFF6FF;
    }

    .month-item.disabled {
        opacity: 0.3;
        cursor: not-allowed;
    }

    #cityContainer.disabled {
        opacity: 0.5;
        pointer-events: none;
    }
</style>
@endpush

<div class="flex flex-col gap-8">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-baseline gap-3">
            <h1 class="text-3xl font-bold text-gray-900 tracking-tight uppercase">{{ $state }}</h1>
            <span class="text-xs font-bold text-gray-400 uppercase tracking-[0.2em]">Tours</span>
        </div>
        <div class="flex items-center gap-4">
            <div class="flex items-center gap-2 bg-white border border-gray-200 rounded-xl p-1 shadow-sm">
                <a href="{{ route('tours.create', ['state' => 'Punjab']) }}" class="px-4 py-2 text-sm font-bold rounded-lg {{ $state === 'Punjab' ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-gray-50' }}">Punjab</a>
                <a href="{{ route('tours.create', ['state' => 'Rajasthan']) }}" class="px-4 py-2 text-sm font-bold rounded-lg {{ $state === 'Rajasthan' ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-gray-50' }}">Rajasthan</a>
            </div>
            <div class="flex items-center gap-2 px-4 py-2 bg-white rounded-xl border border-gray-200 shadow-sm">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <span class="text-sm font-bold text-gray-900">Total Tours:</span>
                <span class="text-sm font-bold text-blue-600">{{ $tourCount }}</span>
            </div>
            <a href="#" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 text-sm font-bold rounded-xl transition-all shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                View Tours
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border border-green-100 text-green-700 px-6 py-4 rounded-2xl font-bold shadow-sm flex items-center gap-3">
        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
        </svg>
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-50 border border-red-100 text-red-700 px-6 py-4 rounded-2xl font-bold shadow-sm flex items-center gap-3">
        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
        </svg>
        {{ session('error') }}
    </div>
    @endif

    <!-- Employee Grid -->
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
        @foreach ($employees as $emp)
        <div class="emp-card bg-white border border-gray-200 p-4 rounded-2xl cursor-pointer hover:border-blue-400 hover:shadow-md transition-all group" id="emp-{{ $emp->id }}" onclick="openTourForm('{{ $emp->id }}', '{{ $emp->name }}')">
            <div class="font-bold text-gray-900 group-hover:text-blue-600 transition-colors">{{ $emp->name }}</div>
            <div class="text-xs text-gray-400 font-bold uppercase tracking-tight mt-1">{{ $emp->state ?: $state }}</div>
        </div>
        @endforeach
    </div>

    <!-- Tour Form (Initially Hidden) -->
    <form id="tourForm" action="{{ route('tours.store', ['state' => $state]) }}" method="POST" class="hidden grid grid-cols-12 gap-6">
        @csrf
        <input type="hidden" name="employee_id" id="employeeInput">
        <input type="hidden" name="status" id="statusInput">
        <input type="hidden" name="tour_date" id="dateInput" value="{{ date('Y-m-d') }}">

        <!-- Schedule Card -->
        <div class="col-span-12 bg-white rounded-3xl border border-gray-200 p-6 shadow-sm">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xs font-extrabold text-gray-400 uppercase tracking-[0.2em]" id="monthDisplay"></h2>
                <div class="flex gap-2 relative">
                    <button type="button" class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-xl transition-all" onclick="moveSchedule(-7)"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg></button>
                    <button type="button" class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-xl transition-all" onclick="moveSchedule(7)"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg></button>
                    <button type="button" class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-xl transition-all" onclick="toggleMonthPicker(event)"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg></button>

                    <div id="monthPicker" class="month-picker">
                        <div class="p-4 border-b border-gray-100 flex justify-between items-center">
                            <div class="flex items-center gap-3">
                                <button type="button" class="text-gray-400 hover:text-blue-600" onclick="changePickerYear(-1)">‹</button>
                                <span class="font-bold text-gray-900" id="pickerYear"></span>
                                <button type="button" class="text-gray-400 hover:text-blue-600" onclick="changePickerYear(1)">›</button>
                            </div>
                            <button type="button" class="text-gray-400 hover:text-red-500" onclick="closeMonthPicker()">×</button>
                        </div>
                        <div class="grid grid-cols-3 gap-2 p-4" id="monthGrid"></div>
                    </div>
                </div>
            </div>
            <div class="flex gap-3 overflow-x-auto pb-2 scrollbar-hide" id="dateGrid"></div>
        </div>

        <!-- Controls Card -->
        <div class="col-span-12 lg:col-span-4 bg-white rounded-3xl border border-gray-200 p-6 shadow-sm">
            <div class="flex justify-between items-center mb-8">
                <span class="text-sm font-bold text-gray-700">Supervisor Duty</span>
                <label class="switch"><input type="checkbox" name="is_supervisor"><span class="slider"></span></label>
            </div>
            <h3 class="text-xs font-extrabold text-gray-400 uppercase tracking-widest mb-4">Select Status</h3>
            <div class="space-y-2">
                @foreach($statuses as $s)
                <div class="control-item" onclick="setMode('{{ $s['key'] }}', this)">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 16 16">
                        <path d="{{ $s['icon'] }}" />
                    </svg>
                    {{ $s['key'] }}
                </div>
                @endforeach
            </div>
        </div>

        <!-- Cities Card -->
        <div class="col-span-12 lg:col-span-8 bg-white rounded-3xl border border-gray-200 p-6 shadow-sm" id="cityContainer">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xs font-extrabold text-gray-400 uppercase tracking-widest">Cities</h3>
                <div class="relative max-w-xs w-full">
                    <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </span>
                    <input type="text" id="citySearch" class="w-full pl-9 pr-4 py-2 bg-gray-50 border border-gray-100 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" placeholder="Search cities...">
                </div>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-3" id="cityGrid"></div>
        </div>

        <!-- Summary Footer Card -->
        <div class="col-span-12 bg-white rounded-3xl border border-gray-200 p-6 shadow-lg flex flex-col md:flex-row justify-between items-center gap-6">
            <div class="flex gap-12">
                <div class="flex flex-col">
                    <span class="text-[10px] font-extrabold text-gray-400 uppercase tracking-widest mb-1">Date</span>
                    <span id="sumDate" class="text-lg font-bold text-gray-900">---</span>
                </div>
                <div class="flex flex-col">
                    <span class="text-[10px] font-extrabold text-gray-400 uppercase tracking-widest mb-1">Status</span>
                    <span id="sumStatus" class="text-lg font-bold text-blue-600">Pending</span>
                </div>
                <div class="flex flex-col">
                    <span class="text-[10px] font-extrabold text-gray-400 uppercase tracking-widest mb-1">Cities</span>
                    <span id="sumCount" class="text-lg font-bold text-gray-900">0</span>
                </div>
            </div>
            <div class="flex flex-col md:items-end">
                <span class="text-[10px] font-extrabold text-gray-400 uppercase tracking-widest mb-1">Salesman</span>
                <span id="summaryName" class="text-lg font-bold text-gray-900">---</span>
            </div>
            <button type="submit" id="mainSubmitBtn" class="w-full md:w-auto px-12 py-4 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-2xl transition-all shadow-lg shadow-blue-200">
                Create Tour
            </button>
        </div>
    </form>
</div>

<script>
    const salesmanCityData = @json($salesmanCityData);
    const tourCitiesUrl = @json(route('tours.cities'));
    const cityCache = {};
    let baseDate = new Date();
    let activeSelectedDate = new Date();

    function openTourForm(employeeId, employeeName) {
        document.getElementById('tourForm').classList.remove('hidden');
        document.getElementById('employeeInput').value = employeeId;
        document.getElementById('summaryName').innerText = employeeName;
        document.querySelectorAll('.emp-card').forEach(c => c.classList.remove('active'));
        document.getElementById('emp-' + employeeId).classList.add('active');
        loadCities(employeeId);
        renderDates();
        updateSummary();
        window.scrollTo({
            top: document.getElementById('tourForm').offsetTop - 100,
            behavior: 'smooth'
        });
    }

    function renderDates() {
        const grid = document.getElementById('dateGrid');
        grid.innerHTML = '';
        const days = ['SUN', 'MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT'];
        const today = new Date();
        today.setHours(0, 0, 0, 0);

        for (let i = 0; i < 14; i++) {
            const d = new Date(baseDate);
            d.setDate(baseDate.getDate() + i);
            const isFuture = d > today;
            const pill = document.createElement('div');
            pill.className = 'date-pill' + (d.toDateString() === activeSelectedDate.toDateString() ? ' active' : '') + (isFuture ? ' disabled' : '');
            pill.innerHTML = `<span class="text-[10px] font-bold opacity-70">${days[d.getDay()]}</span><span class="text-xl font-black">${d.getDate()}</span>`;
            if (!isFuture) {
                pill.onclick = () => {
                    activeSelectedDate = new Date(d);
                    document.getElementById('dateInput').value = d.toISOString().split('T')[0];
                    renderDates();
                    updateSummary();
                };
            }
            grid.appendChild(pill);
        }
        document.getElementById('monthDisplay').innerText = baseDate.toLocaleString('default', {
            month: 'long',
            year: 'numeric'
        }).toUpperCase();
    }

    function moveSchedule(n) {
        baseDate.setDate(baseDate.getDate() + n);
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        if (baseDate > today) baseDate = new Date(today);
        renderDates();
    }

    let pickerYearValue = new Date().getFullYear();

    function toggleMonthPicker(ev) {
        ev.stopPropagation();
        const el = document.getElementById('monthPicker');
        const isActive = el.classList.contains('active');
        if (isActive) {
            el.classList.remove('active');
            return;
        }
        pickerYearValue = baseDate.getFullYear();
        renderMonthPicker();
        el.classList.add('active');
        document.addEventListener('click', () => el.classList.remove('active'), {
            once: true
        });
    }

    function closeMonthPicker() {
        document.getElementById('monthPicker').classList.remove('active');
    }

    function changePickerYear(delta) {
        pickerYearValue += delta;
        renderMonthPicker();
    }

    function renderMonthPicker() {
        const grid = document.getElementById('monthGrid');
        const title = document.getElementById('pickerYear');
        title.innerText = pickerYearValue;
        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        grid.innerHTML = '';
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        months.forEach((m, idx) => {
            const div = document.createElement('div');
            div.className = 'month-item';
            div.innerText = m;
            const monthStart = new Date(pickerYearValue, idx, 1);
            if (monthStart > today) {
                div.classList.add('disabled');
            } else {
                div.onclick = (e) => {
                    e.stopPropagation();
                    const d = new Date(pickerYearValue, idx, 1);
                    baseDate = new Date(d);
                    activeSelectedDate = new Date(d);
                    renderDates();
                    closeMonthPicker();
                };
            }
            grid.appendChild(div);
        });
    }

    function applyCityRow(row) {
        const cities = row ? (row.cities || []) : [];
        const counts = row ? (row.city_party_count || {}) : {};
        window.currentCities = (cities || []).filter(c => !!c).sort();
        window.currentCityCounts = counts || {};
        renderCityGrid('');
        setupCitySearch();
    }

    async function loadCities(employeeId) {
        const key = String(employeeId);
        if (cityCache[key]) {
            applyCityRow(cityCache[key]);
            return;
        }

        const grid = document.getElementById('cityGrid');
        grid.innerHTML = '<div class="col-span-full text-sm font-semibold text-gray-400 px-2 py-4">Loading cities...</div>';

        try {
            const res = await fetch(tourCitiesUrl + '?employee_id=' + encodeURIComponent(key), {
                headers: {
                    'Accept': 'application/json'
                }
            });
            if (!res.ok) throw new Error('Failed');
            const data = await res.json();
            cityCache[key] = data;
            applyCityRow(data);
            return;
        } catch (e) {
            const fallback = salesmanCityData[key] || null;
            cityCache[key] = fallback || {
                employee: {
                    id: employeeId
                },
                cities: [],
                city_party_count: {},
                city_parties: {}
            };
            applyCityRow(cityCache[key]);
        }
    }

    function renderCityGrid(term) {
        const grid = document.getElementById('cityGrid');
        grid.innerHTML = '';
        const filtered = window.currentCities.filter(c => c.toLowerCase().includes(term.toLowerCase()));
        if (filtered.length === 0) {
            grid.innerHTML = '<div class="col-span-full text-sm font-semibold text-gray-400 px-2 py-4">No cities found for this salesman</div>';
            return;
        }
        filtered.forEach(city => {
            const node = document.createElement('div');
            node.className = 'node';
            const count = window.currentCityCounts && window.currentCityCounts[city] ? window.currentCityCounts[city] : 0;
            node.innerHTML = `<div class="font-bold text-gray-900">${city}</div><div class="text-[10px] font-extrabold text-gray-400 uppercase tracking-widest mt-1">${count} parties</div>`;
            node.onclick = function() {
                this.classList.toggle('active');
                updateSummary();
            };
            const input = document.createElement('input');
            input.type = 'checkbox';
            input.name = 'cities[]';
            input.value = city;
            input.className = 'hidden';
            node.appendChild(input);
            grid.appendChild(node);
        });
    }

    function setMode(mode, el) {
        document.querySelectorAll('.control-item').forEach(i => i.classList.remove('active'));
        document.getElementById('statusInput').value = mode;
        el.classList.add('active');
        const container = document.getElementById('cityContainer');
        if (['Leave', 'Holiday', 'No Station', 'Office Visit'].includes(mode)) {
            container.classList.add('disabled');
            document.querySelectorAll('#cityGrid .node').forEach(n => {
                n.classList.remove('active');
                n.querySelector('input').checked = false;
            });
        } else {
            container.classList.remove('disabled');
        }
        updateSummary();
    }

    function updateSummary() {
        const monthsShort = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        document.getElementById('sumDate').innerText = `${monthsShort[activeSelectedDate.getMonth()]} ${activeSelectedDate.getDate()}`;
        document.getElementById('sumStatus').innerText = document.getElementById('statusInput').value || "Pending";

        const activeNodes = document.querySelectorAll('#cityGrid .node.active');
        document.getElementById('sumCount').innerText = activeNodes.length;

        // Sync checkboxes
        document.querySelectorAll('#cityGrid input').forEach(i => i.checked = false);
        activeNodes.forEach(n => n.querySelector('input').checked = true);
    }

    function setupCitySearch() {
        const input = document.getElementById('citySearch');
        input.oninput = function() {
            renderCityGrid(this.value.trim());
        };
    }

    // Form submit handler
    document.getElementById('tourForm').onsubmit = function() {
        const btn = document.getElementById('mainSubmitBtn');
        btn.innerHTML = `<svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Creating...`;
        btn.disabled = true;
    };
</script>
@endsection

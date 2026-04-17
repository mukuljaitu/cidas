@props([
'action',
'names' => collect(),
'statuses' => collect(),
'states' => collect(),
'months' => collect(),
])

@php
$selectedName = request('name', 'All');
$selectedStatus = request('status', 'All');
$selectedState = request('state', 'All');
$selectedMonth = request('month', 'All');
$selectedDateStart = request('date_start');
$selectedDateEnd = request('date_end');
$selectedStatusLabel = $selectedStatus === '1' ? 'Tour' : $selectedStatus;
@endphp

<form id="tourFilters" method="GET" action="{{ $action }}" class="filters-bar">
   <input type="hidden" name="name" id="filterNameInput" value="{{ $selectedName }}">
   <input type="hidden" name="status" id="filterStatusInput" value="{{ $selectedStatus }}">
   <input type="hidden" name="state" id="filterStateInput" value="{{ $selectedState }}">
   <input type="hidden" name="month" id="filterMonthInput" value="{{ $selectedMonth }}">

   <!-- Name Filter -->
   <div class="relative">
      <button type="button" id="chip-name" class="filter-chip {{ $selectedName !== 'All' ? 'active' : '' }}" data-popover="popover-name">
         <svg viewBox="0 0 24 24">
            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z" />
         </svg>
         <span id="label-name">Name: {{ $selectedName }}</span>
         <svg class="arrow" viewBox="0 0 24 24">
            <path d="M7 10l5 5 5-5H7z" />
         </svg>
      </button>
      <div id="popover-name" class="popover">
         <div class="popover-header">Filter by Name</div>
         <div class="popover-content">
            <div class="search-box">
               <svg viewBox="0 0 24 24">
                  <path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z" />
               </svg>
               <input type="text" id="nameSearch" placeholder="Search names...">
            </div>
            <div id="nameOptions" class="options-list">
               <button type="button" class="option-item {{ $selectedName === 'All' ? 'selected' : '' }}" data-filter-name="All">All Names</button>
               @foreach($names as $name)
               <button type="button" class="option-item {{ $selectedName === $name ? 'selected' : '' }}" data-filter-name="{{ $name }}">{{ $name }}</button>
               @endforeach
            </div>
         </div>
      </div>
   </div>

   <!-- Date Range Filter -->
   <div class="relative">
      <button type="button" id="chip-date" class="filter-chip {{ ($selectedDateStart || $selectedDateEnd) ? 'active' : '' }}" data-popover="popover-date">
         <svg viewBox="0 0 24 24">
            <path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z" />
         </svg>
         <span id="label-date">Date Range</span>
         <svg class="arrow" viewBox="0 0 24 24">
            <path d="M7 10l5 5 5-5H7z" />
         </svg>
      </button>
      <div id="popover-date" class="popover" style="min-width: 320px;">
         <div class="popover-header">Filter by Range</div>
         <div class="popover-content">
            <div class="fancy-date-grid">
               <div class="fancy-date-field">
                  <label>Start Date</label>
                  <input type="date" name="date_start" id="dateStart" value="{{ $selectedDateStart }}">
               </div>
               <div class="fancy-date-field">
                  <label>End Date</label>
                  <input type="date" name="date_end" id="dateEnd" value="{{ $selectedDateEnd }}">
               </div>
            </div>
         </div>
      </div>
   </div>

   <!-- Status Filter -->
   <div class="relative">
      <button type="button" id="chip-status" class="filter-chip {{ $selectedStatus !== 'All' ? 'active' : '' }}" data-popover="popover-status">
         <svg viewBox="0 0 24 24">
            <path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8z" />
            <path d="M12.5 7H11v6l5.25 3.15.75-1.23-4.5-2.67z" />
         </svg>
         <span id="label-status">Status: {{ $selectedStatusLabel }}</span>
         <svg class="arrow" viewBox="0 0 24 24">
            <path d="M7 10l5 5 5-5H7z" />
         </svg>
      </button>
      <div id="popover-status" class="popover">
         <div class="popover-header">Filter by Status</div>
         <div class="popover-content">
            <div id="statusOptions" class="options-list">
               <button type="button" class="option-item {{ $selectedStatus === 'All' ? 'selected' : '' }}" data-filter-status="All">All Statuses</button>
               @foreach($statuses as $status)
               <button type="button" class="option-item {{ $selectedStatus === $status ? 'selected' : '' }}" data-filter-status="{{ $status }}">{{ $status === '1' ? 'Tour' : $status }}</button>
               @endforeach
            </div>
         </div>
      </div>
   </div>

   <!-- State Filter -->
   <div class="relative">
      <button type="button" id="chip-state" class="filter-chip {{ $selectedState !== 'All' ? 'active' : '' }}" data-popover="popover-state">
         <svg viewBox="0 0 24 24">
            <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z" />
         </svg>
         <span id="label-state">State: {{ $selectedState }}</span>
         <svg class="arrow" viewBox="0 0 24 24">
            <path d="M7 10l5 5 5-5H7z" />
         </svg>
      </button>
      <div id="popover-state" class="popover">
         <div class="popover-header">Filter by State</div>
         <div class="popover-content">
            <div id="stateOptions" class="options-list">
               <button type="button" class="option-item {{ $selectedState === 'All' ? 'selected' : '' }}" data-filter-state="All">All States</button>
               @foreach($states as $state)
               <button type="button" class="option-item {{ $selectedState === $state ? 'selected' : '' }}" data-filter-state="{{ $state }}">{{ $state }}</button>
               @endforeach
            </div>
         </div>
      </div>
   </div>

   <!-- Month Filter -->
   <div class="relative">
      <button type="button" id="chip-month" class="filter-chip {{ $selectedMonth !== 'All' ? 'active' : '' }}" data-popover="popover-month">
         <svg viewBox="0 0 24 24">
            <path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11z" />
         </svg>
         <span id="label-month">Month: {{ $selectedMonth }}</span>
         <svg class="arrow" viewBox="0 0 24 24">
            <path d="M7 10l5 5 5-5H7z" />
         </svg>
      </button>
      <div id="popover-month" class="popover">
         <div class="popover-header">Filter by Month</div>
         <div class="popover-content">
            <div id="monthOptions" class="options-list">
               <button type="button" class="option-item {{ $selectedMonth === 'All' ? 'selected' : '' }}" data-filter-month="All">All Months</button>
               @foreach($months as $month)
               <button type="button" class="option-item {{ $selectedMonth === $month ? 'selected' : '' }}" data-filter-month="{{ $month }}">{{ $month }}</button>
               @endforeach
            </div>
         </div>
      </div>
   </div>

   <a class="clear-filters" href="{{ $action }}">Clear All</a>
</form>
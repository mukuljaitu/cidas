@props([
    'action',
    'names' => collect(),
    'statuses' => collect(),
    'roles' => collect(),
    'showDate' => true,
    'showStatus' => true,
    'showRole' => true,
])

@php
$selectedName = request('name', 'All');
$selectedStatus = $showStatus ? request('status', 'All') : 'All';
$selectedRole = $showRole ? request('role', 'All') : 'All';
$selectedDateStart = $showDate ? request('date_start') : null;
$selectedDateEnd = $showDate ? request('date_end') : null;
@endphp

<form id="employeeFilters" method="GET" action="{{ $action }}" style="display:flex; align-items:center; flex-wrap:wrap; gap:12px; width:100%;">
    <input type="hidden" name="name" id="filterNameInput" value="{{ $selectedName }}">
    @if($showStatus)
        <input type="hidden" name="status" id="filterStatusInput" value="{{ $selectedStatus }}">
    @endif
    @if($showRole)
        <input type="hidden" name="role" id="filterRoleInput" value="{{ $selectedRole }}">
    @endif

    <div style="position: relative;">
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

    @if($showDate)
        <div style="position: relative;">
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
    @endif

    @if($showStatus)
        <div style="position: relative;">
            <button type="button" id="chip-status" class="filter-chip {{ $selectedStatus !== 'All' ? 'active' : '' }}" data-popover="popover-status">
                <svg viewBox="0 0 24 24">
                    <path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8z" />
                    <path d="M12.5 7H11v6l5.25 3.15.75-1.23-4.5-2.67z" />
                </svg>
                <span id="label-status">Status: {{ $selectedStatus }}</span>
                <svg class="arrow" viewBox="0 0 24 24">
                    <path d="M7 10l5 5 5-5H7z" />
                </svg>
            </button>
            <div id="popover-status" class="popover">
                <div class="popover-header">Filter by Status</div>
                <div class="popover-content">
                    <div id="statusOptions" class="options-list">
                        @foreach($statuses as $status)
                            <button type="button" class="option-item {{ $selectedStatus === $status ? 'selected' : '' }}" data-filter-status="{{ $status }}">{{ $status === 'All' ? 'All Statuses' : $status }}</button>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if($showRole)
        <div style="position: relative;">
            <button type="button" id="chip-role" class="filter-chip {{ $selectedRole !== 'All' ? 'active' : '' }}" data-popover="popover-role">
                <svg viewBox="0 0 24 24">
                    <path d="M12 2C6.47 2 2 6.48 2 12s4.47 10 10 10 10-4.52 10-10S17.53 2 12 2zm-1 15l-4-4 1.41-1.41L11 14.17l6.59-6.59L19 9l-8 8z"/>
                </svg>
                <span id="label-role">Role: {{ $selectedRole }}</span>
                <svg class="arrow" viewBox="0 0 24 24">
                    <path d="M7 10l5 5 5-5H7z" />
                </svg>
            </button>
            <div id="popover-role" class="popover">
                <div class="popover-header">Filter by Role</div>
                <div class="popover-content">
                    <div id="roleOptions" class="options-list">
                        @foreach($roles as $role)
                            <button type="button" class="option-item {{ $selectedRole === $role ? 'selected' : '' }}" data-filter-role="{{ $role }}">{{ $role === 'All' ? 'All Roles' : $role }}</button>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif

    <a class="clear-filters" href="{{ $action }}">Clear All</a>
</form>

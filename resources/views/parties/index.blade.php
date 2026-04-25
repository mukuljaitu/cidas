@extends('layouts.admin')

@section('title', 'Parties')

@section('content')
<x-table-ui-layout title="Parties" :paginator="$parties">
    <x-slot name="headerActions">
        <button type="button" data-add-member-trigger class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm shadow-blue-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            <span>New</span>
        </button>
    </x-slot>

    <x-slot name="filters">
        @php
        $missingEnabled = request()->boolean('missing');
        $missingMin = (int) request('missing_min', 1);
        if ($missingMin < 1) $missingMin=1;
            $missingLabel=$missingEnabled ? ('Missing: ' . ($missingMin > 1 ? ($missingMin . ' +') : 'On' )) : 'Missing: Off' ;
            $missingSections=(array) request()->input('missing_sections', []);
            @endphp
            <form id="partyFilters" method="GET" action="{{ url('/parties') }}" class="filters-bar">
                <input type="hidden" name="name" id="filterNameInput" value="{{ request('name', 'All') }}">
                <input type="hidden" name="district" id="filterDistrictInput" value="{{ request('district', 'All') }}">
                <input type="hidden" name="state" id="filterStateInput" value="{{ request('state', 'All') }}">
                <input type="hidden" name="type" id="filterTypeInput" value="{{ request('type', 'All') }}">
                <input type="hidden" name="employee_id" id="filterEmployeeInput" value="{{ request('employee_id', 'All') }}">
                <input type="hidden" name="verified" id="filterVerifiedInput" value="{{ request('verified', 'All') }}">
                <input type="hidden" name="missing" id="filterMissingInput" value="{{ $missingEnabled ? '1' : '0' }}">
                <div class="flex items-center flex-wrap gap-3 flex-1">
                    <div class="relative">
                        <button type="button" id="chip-name" class="filter-chip {{ request('name', 'All') !== 'All' ? 'active' : '' }}" data-popover="popover-name">
                            <svg viewBox="0 0 24 24">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z" />
                            </svg>
                            <span id="label-name">Firm: {{ request('name', 'All') }}</span>
                        </button>
                        <div id="popover-name" class="popover">
                            <div class="popover-header">Search Firm</div>
                            <div class="popover-content">
                                <div class="search-box">
                                    <input type="text" id="nameSearch" placeholder="Search firm names...">
                                </div>
                                <div id="nameOptions" class="options-list">
                                    <button type="button" class="option-item {{ request('name', 'All') === 'All' ? 'selected' : '' }}" data-filter-name="All">All Firms</button>
                                    @foreach($names as $name)
                                    <button type="button" class="option-item {{ request('name') === $name ? 'selected' : '' }}" data-filter-name="{{ $name }}">{{ $name }}</button>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="relative">
                        <button type="button" id="chip-district" class="filter-chip {{ request('district', 'All') !== 'All' ? 'active' : '' }}" data-popover="popover-district">
                            <svg viewBox="0 0 24 24">
                                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-9-7-9zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z" />
                            </svg>
                            <span id="label-district">District: {{ request('district', 'All') }}</span>
                        </button>
                        <div id="popover-district" class="popover">
                            <div class="popover-header">Filter District</div>
                            <div class="popover-content">
                                <div class="options-list">
                                    <button type="button" class="option-item {{ request('district', 'All') === 'All' ? 'selected' : '' }}" data-filter-district="All">All Districts</button>
                                    @foreach($districts as $district)
                                    <button type="button" class="option-item {{ request('district', 'All') === $district ? 'selected' : '' }}" data-filter-district="{{ $district }}">{{ $district }}</button>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="relative">
                        <button type="button" id="chip-state" class="filter-chip {{ request('state', 'All') !== 'All' ? 'active' : '' }}" data-popover="popover-state">
                            <svg viewBox="0 0 24 24">
                                <path d="M12 2L4.5 20.29l.71.71L12 18l6.79 3 .71-.71z" />
                            </svg>
                            <span id="label-state">State: {{ request('state', 'All') }}</span>
                        </button>
                        <div id="popover-state" class="popover">
                            <div class="popover-header">Filter State</div>
                            <div class="popover-content">
                                <div class="options-list">
                                    <button type="button" class="option-item {{ request('state', 'All') === 'All' ? 'selected' : '' }}" data-filter-state="All">All States</button>
                                    @foreach($states as $state)
                                    <button type="button" class="option-item {{ request('state', 'All') === $state ? 'selected' : '' }}" data-filter-state="{{ $state }}">{{ $state }}</button>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="relative">
                        <button type="button" id="chip-type" class="filter-chip {{ request('type', 'All') !== 'All' ? 'active' : '' }}" data-popover="popover-type">
                            <svg viewBox="0 0 24 24">
                                <path d="M10 16h4v-2h-4v2zm3-14H5c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V7l-6-6zm-1 7V3.5L18.5 9H12z" />
                            </svg>
                            <span id="label-type">Type: {{ request('type', 'All') }}</span>
                        </button>
                        <div id="popover-type" class="popover">
                            <div class="popover-header">Firm Type</div>
                            <div class="popover-content">
                                <div class="options-list">
                                    <button type="button" class="option-item {{ request('type', 'All') === 'All' ? 'selected' : '' }}" data-filter-type="All">All Types</button>
                                    @foreach($types as $type)
                                    <button type="button" class="option-item {{ request('type', 'All') === $type ? 'selected' : '' }}" data-filter-type="{{ $type }}">{{ $type }}</button>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    @php
                    $selectedEmployeeId = request('employee_id', 'All');
                    $selectedEmployeeName = 'All';
                    if ($selectedEmployeeId !== '' && $selectedEmployeeId !== 'All') {
                    $selectedEmployeeName = $employeeOptions->firstWhere('id', (int) $selectedEmployeeId)?->name ?? 'All';
                    }
                    @endphp
                    <div class="relative">
                        <button type="button" id="chip-employee" class="filter-chip {{ request('employee_id', 'All') !== 'All' ? 'active' : '' }}" data-popover="popover-employee">
                            <svg viewBox="0 0 24 24">
                                <path d="M12 12c2.21 0 4-1.79 4-4S14.21 4 12 4 8 5.79 8 8s1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                            </svg>
                            <span id="label-employee">Salesman: {{ $selectedEmployeeName }}</span>
                        </button>
                        <div id="popover-employee" class="popover">
                            <div class="popover-header">Filter Salesman</div>
                            <div class="popover-content">
                                <div class="options-list">
                                    <button type="button" class="option-item {{ request('employee_id', 'All') === 'All' ? 'selected' : '' }}" data-filter-employee="All" data-filter-employee-label="All">All Salesmen</button>
                                    @foreach($employeeOptions as $emp)
                                    <button type="button" class="option-item {{ (string) request('employee_id') === (string) $emp->id ? 'selected' : '' }}" data-filter-employee="{{ $emp->id }}" data-filter-employee-label="{{ $emp->name }}">{{ $emp->name }}</button>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="relative">
                        <button type="button" id="chip-verified" class="filter-chip {{ request('verified', 'All') !== 'All' ? 'active' : '' }}" data-popover="popover-verified">
                            <svg viewBox="0 0 24 24">
                                <path d="M9 12l2 2 4-4m5 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span id="label-verified">Verification: {{ request('verified', 'All') }}</span>
                        </button>
                        <div id="popover-verified" class="popover">
                            <div class="popover-header">Verification</div>
                            <div class="popover-content">
                                <div class="options-list">
                                    <button type="button" class="option-item {{ request('verified', 'All') === 'All' ? 'selected' : '' }}" data-filter-verified="All">All</button>
                                    <button type="button" class="option-item {{ request('verified') === 'Verified' ? 'selected' : '' }}" data-filter-verified="Verified">Verified</button>
                                    <button type="button" class="option-item {{ request('verified') === 'Pending' ? 'selected' : '' }}" data-filter-verified="Pending">Pending</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="relative">
                        <button type="button" id="chip-missing" class="filter-chip {{ $missingEnabled ? 'active' : '' }}" data-popover="popover-missing">
                            <svg viewBox="0 0 24 24">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 5h-2v6h2V7zm0 8h-2v2h2v-2z" />
                            </svg>
                            <span id="label-missing">{{ $missingLabel }}</span>
                        </button>
                        <div id="popover-missing" class="popover" style="min-width: 360px;">
                            <div class="popover-header">Missing Data</div>
                            <div class="popover-content">
                                <label class="flex items-center gap-2 text-sm font-medium text-gray-700">
                                    <input type="checkbox" id="missingEnabledToggle" data-missing-toggle data-defer-submit="1" class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500" {{ $missingEnabled ? 'checked' : '' }}>
                                    Enable Missing Filter
                                </label>

                                <div class="mt-4 text-[10px] font-extrabold text-gray-400 uppercase tracking-wider">Filter By Section</div>
                                <div class="mt-2 grid grid-cols-2 gap-2">
                                    <label class="flex items-center gap-2 text-sm font-medium text-gray-700">
                                        <input type="checkbox" name="missing_sections[]" value="general" data-defer-submit="1" class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500" {{ in_array('general', $missingSections, true) ? 'checked' : '' }}>
                                        General Info
                                    </label>
                                    <label class="flex items-center gap-2 text-sm font-medium text-gray-700">
                                        <input type="checkbox" name="missing_sections[]" value="location" data-defer-submit="1" class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500" {{ in_array('location', $missingSections, true) ? 'checked' : '' }}>
                                        Location & Address
                                    </label>
                                    <label class="flex items-center gap-2 text-sm font-medium text-gray-700">
                                        <input type="checkbox" name="missing_sections[]" value="tax" data-defer-submit="1" class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500" {{ in_array('tax', $missingSections, true) ? 'checked' : '' }}>
                                        Tax & Licenses
                                    </label>
                                    <label class="flex items-center gap-2 text-sm font-medium text-gray-700">
                                        <input type="checkbox" name="missing_sections[]" value="banking" data-defer-submit="1" class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500" {{ in_array('banking', $missingSections, true) ? 'checked' : '' }}>
                                        Banking Details
                                    </label>
                                </div>

                                <div class="mt-4 flex items-center justify-between gap-3">
                                    <div class="text-sm font-medium text-gray-700">Missing at least</div>
                                    <select name="missing_min" data-defer-submit="1" class="px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm font-semibold text-gray-700">
                                        @for($i = 1; $i <= 10; $i++)
                                            <option value="{{ $i }}" {{ $missingMin === $i ? 'selected' : '' }}>{{ $i }}{{ $i === 1 ? '+' : '+' }}</option>
                                            @endfor
                                    </select>
                                </div>

                                <div class="mt-4 flex items-center justify-end gap-2">
                                    <button type="button" data-missing-cancel class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-lg transition-colors">Cancel</button>
                                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-colors">Apply</button>
                                </div>

                                <div class="mt-3 text-xs font-semibold text-gray-400">Tip: Leave all sections unchecked to match any missing field.</div>
                            </div>
                        </div>
                    </div>
                </div>
                <a href="{{ url('/parties') }}" class="clear-filters">Clear All</a>
            </form>
    </x-slot>

    <x-slot name="thead">
        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Identify</th>
        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Location</th>
        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Salesman</th>
        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Status</th>
        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider text-right">Actions</th>
    </x-slot>

    <x-slot name="tbody">
        @forelse($parties as $party)
        @php
        $city = $party->city;
        $district = $party->district;
        $employeeName = $party->employee?->name ?? 'Unassigned';
        $status = $party->status ?? 'Active';
        $statusLower = strtolower((string) $status);
        $dotColor = 'bg-green-500';
        if (str_contains($statusLower, 'inactive')) $dotColor = 'bg-red-500';

        $initials = strtoupper(substr($party->name, 0, 1));
        $isVerified = (bool) ($party->is_verified ?? false);
        @endphp
        <tr id="row-{{ $party->id }}" class="hover:bg-gray-50/50 transition-colors">
            <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center text-gray-500 font-bold border border-gray-200">
                        {{ $initials }}
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <div class="text-sm font-bold text-gray-900">{{ $party->name }}</div>
                            @if($isVerified)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-blue-50 text-blue-700 border border-blue-200 text-[10px] font-bold uppercase tracking-wider">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Verified
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-yellow-50 text-yellow-800 border border-yellow-200 text-[10px] font-bold uppercase tracking-wider">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Pending
                            </span>
                            @endif
                        </div>
                        <div class="text-xs text-gray-400 font-medium">{{ $party->alias ?: 'No alias' }}</div>
                    </div>
                </div>
            </td>
            <td class="px-6 py-4">
                <div class="text-sm font-bold text-gray-900">{{ $city ?: '—' }}</div>
                <div class="text-xs text-gray-400 font-medium">{{ $district ?: 'No district' }}</div>
            </td>
            <td class="px-6 py-4">
                <span class="px-2.5 py-1 rounded-md bg-blue-50 text-[10px] font-bold text-blue-600 uppercase tracking-wider border border-blue-100/50">
                    {{ $employeeName }}
                </span>
            </td>
            <td class="px-6 py-4">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full {{ $dotColor }}"></span>
                    <span class="text-sm font-semibold text-gray-700">{{ $status }}</span>
                </div>
            </td>
            <td class="px-6 py-4 text-right">
                <button
                    type="button"
                    class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-all"
                    data-edit-member-trigger
                    data-id="{{ $party->id }}"
                    data-name="{{ $party->name }}"
                    data-company_code="{{ $party->company_code }}"
                    data-alias="{{ $party->alias }}"
                    data-mobile="{{ $party->mobile }}"
                    data-gst_no="{{ $party->gst_no }}"
                    data-street_address="{{ $party->street_address }}"
                    data-city="{{ $party->city }}"
                    data-district="{{ $party->district }}"
                    data-state="{{ $party->state }}"
                    data-pin_code="{{ $party->pin_code }}"
                    data-bank_name="{{ $party->bank_name }}"
                    data-bank_account_no="{{ $party->bank_account_no }}"
                    data-bank_ifsc="{{ $party->bank_ifsc }}"
                    data-employee_id="{{ $party->employee_id }}"
                    data-status="{{ $party->status }}"
                    data-is_verified="{{ $party->is_verified ? 1 : 0 }}"
                    data-party_type="{{ $party->party_type }}"
                    data-pan_no="{{ $party->pan_no }}"
                    data-aadhar_card="{{ $party->aadhar_card }}"
                    data-owner_name="{{ $party->owner_name }}"
                    data-pest_lic="{{ $party->pest_lic }}"
                    data-fert_lic="{{ $party->fert_lic }}"
                    data-seed_lic="{{ $party->seed_lic }}"
                    data-cq1="{{ $party->cq1 }}"
                    data-cq2="{{ $party->cq2 }}"
                    data-stamp="{{ $party->stamp }}"
                    data-sign="{{ $party->sign }}"
                    data-pic="{{ $party->pic ? asset('storage/' . $party->pic) : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                    </svg>
                </button>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="5" class="px-6 py-12 text-center">
                <div class="flex flex-col items-center gap-2 text-gray-400">
                    <svg class="w-12 h-12 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                    </svg>
                    <span class="text-sm font-medium">No parties found</span>
                </div>
            </td>
        </tr>
        @endforelse
    </x-slot>

    <x-slot name="overlay">
        <!-- Add/Edit Party Panel -->
        <div id="addMemberPanel"
            data-entity-singular="Party"
            data-entity-plural="Parties"
            data-resource="{{ url('/parties') }}"
            data-draft-key="cidas:party:draft:new"
            data-form-fields="name,company_code,alias,owner_name,mobile,gst_no,pan_no,aadhar_card,street_address,city,district,state,pin_code,employee_id,party_type,status,is_verified,pest_lic,fert_lic,seed_lic,cq1,cq2,stamp,sign,bank_name,bank_account_no,bank_ifsc"
            class="fixed inset-y-0 right-0 w-[560px] bg-white shadow-2xl border-l border-gray-200 transform translate-x-full transition-transform duration-300 z-50 overflow-y-auto">
            <div class="p-8">
                <div class="flex items-center justify-between mb-8">
                    <h2 class="text-2xl font-bold text-gray-900" id="panelTitle">New</h2>
                    <div class="flex items-center gap-2">
                        <button type="button" data-add-member-clear-all class="px-3 py-2 text-xs font-bold text-gray-500 hover:text-gray-700 hover:bg-gray-50 rounded-lg transition-all">
                            Clear All
                        </button>
                        <button type="button" data-add-member-cancel class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-50 rounded-lg transition-all">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <form id="memberForm" method="POST" action="{{ url('/parties') }}" enctype="multipart/form-data" class="space-y-6 pb-20">
                    @csrf
                    <input type="hidden" name="_method" value="">

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-gray-700">Party Name</label>
                            <input name="name" type="text" required class="block w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" />
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-gray-700">Company Code</label>
                            <input name="company_code" type="text" class="block w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-gray-700">Alias</label>
                            <input name="alias" type="text" class="block w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" />
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-gray-700">Owner Name</label>
                            <input name="owner_name" type="text" class="block w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-gray-700">Mobile</label>
                            <input name="mobile" type="text" class="block w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" />
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-gray-700">GST No</label>
                            <input name="gst_no" type="text" class="block w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-gray-700">PAN</label>
                            <input name="pan_no" type="text" class="block w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" />
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-gray-700">Aadhar</label>
                            <input name="aadhar_card" type="text" class="block w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" />
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-gray-700">Address</label>
                        <textarea name="street_address" rows="2" class="block w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all"></textarea>
                    </div>

                    <div class="grid grid-cols-4 gap-4">
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-gray-700">City</label>
                            <input name="city" type="text" class="block w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" />
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-gray-700">District</label>
                            <input name="district" type="text" class="block w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" />
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-gray-700">State</label>
                            <input name="state" type="text" class="block w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" />
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-gray-700">Pin Code</label>
                            <input name="pin_code" type="text" class="block w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" />
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-4">
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-gray-700">Salesman</label>
                            <select name="employee_id" required class="block w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                                <option value="">Select Salesman</option>
                                @foreach(($employeeOptions ?? []) as $employee)
                                <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-gray-700">Type</label>
                            <input name="party_type" type="text" class="block w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" />
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-gray-700">Status</label>
                            <input name="status" type="text" class="block w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" />
                        </div>
                    </div>

                    <div class="flex items-center justify-between gap-4 p-4 rounded-lg bg-gray-50 border border-gray-200">
                        <div class="flex flex-col">
                            <span class="text-sm font-semibold text-gray-700">Verification</span>
                            <span class="text-xs font-medium text-gray-500">Mark as verified after checking documents and details.</span>
                        </div>
                        <label class="inline-flex items-center gap-2">
                            <input name="is_verified" type="checkbox" value="1" class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500" />
                            <span class="text-sm font-bold text-gray-700">Verified</span>
                        </label>
                    </div>

                    <div class="grid grid-cols-3 gap-4">
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-gray-700">Pest Lic</label>
                            <input name="pest_lic" type="text" class="block w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" />
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-gray-700">Fert Lic</label>
                            <input name="fert_lic" type="text" class="block w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" />
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-gray-700">Seed Lic</label>
                            <input name="seed_lic" type="text" class="block w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-gray-700">CQ1</label>
                            <input name="cq1" type="text" class="block w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" />
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-gray-700">CQ2</label>
                            <input name="cq2" type="text" class="block w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-gray-700">Stamp</label>
                            <input name="stamp" type="text" class="block w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" />
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-gray-700">Sign</label>
                            <input name="sign" type="text" class="block w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" />
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-4">
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-gray-700">Bank Name</label>
                            <input name="bank_name" type="text" class="block w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" />
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-gray-700">Account No</label>
                            <input name="bank_account_no" type="text" class="block w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" />
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-gray-700">IFSC Code</label>
                            <input name="bank_ifsc" type="text" class="block w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" />
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-gray-700">Registration Document</label>
                        <input name="pic" type="file" class="block w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" />
                        <div id="picPreview" class="hidden mt-2">
                            <a id="picLink" href="#" target="_blank" class="text-blue-600 hover:text-blue-700 text-sm font-medium">View existing document</a>
                        </div>
                    </div>

                    <div class="fixed bottom-0 right-0 w-[560px] bg-white p-6 border-t border-gray-200 flex items-center gap-3 z-10">
                        <button type="submit" class="flex-1 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl transition-all shadow-lg shadow-blue-200">
                            Save Party
                        </button>
                        <button type="button" data-add-member-cancel class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold rounded-xl transition-all">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </x-slot>
</x-table-ui-layout>

<x-modals />
@push('scripts')
<script>
    document.addEventListener('click', (e) => {
        const editBtn = e.target.closest('[data-edit-member-trigger]');
        if (editBtn) {
            const pic = editBtn.getAttribute('data-pic');
            const picPreview = document.getElementById('picPreview');
            const picLink = document.getElementById('picLink');

            if (pic && pic !== '') {
                picPreview.classList.remove('hidden');
                picLink.href = pic;
            } else {
                picPreview.classList.add('hidden');
            }
        }

        const addBtn = e.target.closest('[data-add-member-trigger]');
        if (addBtn) {
            document.getElementById('picPreview').classList.add('hidden');
        }

        const clearBtn = e.target.closest('[data-add-member-clear-all]');
        if (clearBtn) {
            document.getElementById('picPreview').classList.add('hidden');
        }
    });

    (() => {
        const status = @json(session('status'));
        if (status === 'party-created' || status === 'party-updated') {
            try {
                window.localStorage.removeItem('cidas:party:draft:new');
            } catch {}
        }
    })();
</script>
@endpush

@endsection

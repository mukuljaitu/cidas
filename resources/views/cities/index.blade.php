@extends('layouts.admin')

@section('title', 'Cities')

@section('content')
<x-table-ui-layout title="Cities" :paginator="$cities">
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
            $selectedName = request('name', 'All');
            $selectedEmployeeId = request('employee_id', 'All');
            $selectedEmployeeName = 'All';
            if ($selectedEmployeeId !== '' && $selectedEmployeeId !== 'All') {
                $selectedEmployeeName = $employeeOptions->firstWhere('id', (int) $selectedEmployeeId)?->name ?? 'All';
            }
        @endphp

        <form id="cityFilters" method="GET" action="{{ url('/cities') }}" class="filters-bar">
            <input type="hidden" name="name" id="filterNameInput" value="{{ $selectedName }}">
            <input type="hidden" name="employee_id" id="filterEmployeeInput" value="{{ $selectedEmployeeId }}">

            <div class="flex items-center flex-wrap gap-3 flex-1">
                <div class="relative">
                    <button type="button" id="chip-name" class="filter-chip {{ $selectedName !== 'All' ? 'active' : '' }}" data-popover="popover-name">
                        <svg viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z" />
                        </svg>
                        <span id="label-name">City: {{ $selectedName }}</span>
                    </button>
                    <div id="popover-name" class="popover">
                        <div class="popover-header">Filter by City</div>
                        <div class="popover-content">
                            <div id="nameOptions" class="options-list">
                                <button type="button" class="option-item {{ $selectedName === 'All' ? 'selected' : '' }}" data-filter-name="All">All Cities</button>
                                @foreach($names as $name)
                                <button type="button" class="option-item {{ $selectedName === $name ? 'selected' : '' }}" data-filter-name="{{ $name }}">{{ $name }}</button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div class="relative">
                    <button type="button" id="chip-employee" class="filter-chip {{ $selectedEmployeeId !== 'All' ? 'active' : '' }}" data-popover="popover-employee">
                        <svg viewBox="0 0 24 24">
                            <path d="M12 12c2.21 0 4-1.79 4-4S14.21 4 12 4 8 5.79 8 8s1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                        </svg>
                        <span id="label-employee">Employee: {{ $selectedEmployeeName }}</span>
                    </button>
                    <div id="popover-employee" class="popover">
                        <div class="popover-header">Filter by Employee</div>
                        <div class="popover-content">
                            <div class="options-list">
                                <button type="button" class="option-item {{ $selectedEmployeeId === 'All' ? 'selected' : '' }}" data-filter-employee="All" data-filter-employee-label="All">All Employees</button>
                                @foreach($employeeOptions as $employee)
                                <button type="button" class="option-item {{ (string) $selectedEmployeeId === (string) $employee->id ? 'selected' : '' }}" data-filter-employee="{{ $employee->id }}" data-filter-employee-label="{{ $employee->name }}">{{ $employee->name }}</button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <a href="{{ url('/cities') }}" class="clear-filters">Clear All</a>
        </form>
    </x-slot>

    <x-slot name="thead">
        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">City</th>
        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Employee</th>
        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Created</th>
        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider text-right">Actions</th>
    </x-slot>

    <x-slot name="tbody">
        @forelse($cities as $city)
            @php
                $cityName = trim((string) $city->city);
                $employeeName = $city->employee?->name ?? 'Unknown';
                $initial = strtoupper(substr($cityName, 0, 1));
            @endphp
            <tr id="row-{{ $city->id }}" class="hover:bg-gray-50/50 transition-colors">
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center text-gray-500 font-bold border border-gray-200">
                            {{ $initial }}
                        </div>
                        <div class="text-sm font-bold text-gray-900">{{ $cityName }}</div>
                    </div>
                </td>
                <td class="px-6 py-4">
                    <span class="px-2.5 py-1 rounded-md bg-blue-50 text-[10px] font-bold text-blue-600 uppercase tracking-wider border border-blue-100/50">
                        {{ $employeeName }}
                    </span>
                </td>
                <td class="px-6 py-4 text-sm text-gray-700 font-medium">
                    {{ optional($city->created_at)->format('Y-m-d') ?: '—' }}
                </td>
                <td class="px-6 py-4 text-right">
                    <button
                        type="button"
                        class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-all"
                        data-edit-member-trigger
                        data-id="{{ $city->id }}"
                        data-city="{{ $cityName }}"
                        data-employee_id="{{ $city->employee_id }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                        </svg>
                    </button>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="px-6 py-12 text-center">
                    <div class="flex flex-col items-center gap-2 text-gray-400">
                        <svg class="w-12 h-12 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                        </svg>
                        <span class="text-sm font-medium">No cities found</span>
                    </div>
                </td>
            </tr>
        @endforelse
    </x-slot>

    <x-slot name="overlay">
        <div id="addMemberPanel"
            data-entity-singular="City"
            data-entity-plural="Cities"
            data-resource="{{ url('/cities') }}"
            data-form-fields="city,employee_id"
            class="fixed inset-y-0 right-0 w-[480px] bg-white shadow-2xl border-l border-gray-200 transform translate-x-full transition-transform duration-300 z-50 overflow-y-auto">
            <div class="p-8">
                <div class="flex items-center justify-between mb-8">
                    <h2 class="text-2xl font-bold text-gray-900" id="panelTitle">New</h2>
                    <button type="button" data-add-member-cancel class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-50 rounded-lg transition-all">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <form id="memberForm" method="POST" action="{{ url('/cities') }}" class="space-y-6">
                    @csrf
                    <input type="hidden" name="_method" value="">

                    <div class="space-y-4">
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-gray-700">City Name</label>
                            <input name="city" type="text" required class="block w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" placeholder="e.g. Jaipur" />
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-gray-700">Employee</label>
                            <select name="employee_id" required class="block w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                                <option value="">Select Employee</option>
                                @foreach($employeeOptions as $employee)
                                <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="pt-8 flex items-center gap-3">
                        <button type="submit" class="flex-1 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl transition-all shadow-lg shadow-blue-200">
                            Save City
                        </button>
                        <button type="button" data-add-member-cancel class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold rounded-xl transition-all">
                            Cancel
                        </button>
                    </div>

                    <button type="button" data-delete-member class="w-full mt-4 px-6 py-3 bg-red-50 text-red-600 font-bold rounded-xl hover:bg-red-100 transition-all flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        Delete City
                    </button>
                </form>
            </div>
        </div>
    </x-slot>
</x-table-ui-layout>

<x-modals />
@endsection

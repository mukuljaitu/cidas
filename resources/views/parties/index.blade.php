@extends('layouts.admin')

@section('title', 'Parties')

@section('content')
<x-table-ui-layout title="Parties" :paginator="$parties">
    <x-slot name="toolbar">
        <div class="flex items-center gap-3 w-full">
            <form id="partyFilters" method="GET" action="{{ url('/parties') }}" class="flex items-center flex-wrap gap-3 flex-1">
                <div class="relative">
                    <button type="button" class="filter-chip {{ request('name', 'All') !== 'All' ? 'active' : '' }}" data-popover="popover-name">
                        <svg viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z" />
                        </svg>
                        <span>Firm: {{ request('name', 'All') }}</span>
                    </button>
                    <div id="popover-name" class="popover">
                        <div class="popover-header">Search Firm</div>
                        <div class="popover-content">
                            <div class="search-box">
                                <input type="text" id="nameSearch" placeholder="Search firm names...">
                            </div>
                            <div class="options-list">
                                <button type="submit" name="name" value="All" class="option-item {{ request('name', 'All') === 'All' ? 'selected' : '' }}">All Firms</button>
                                @foreach($names as $name)
                                <button type="submit" name="name" value="{{ $name }}" class="option-item {{ request('name') === $name ? 'selected' : '' }}">{{ $name }}</button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div class="relative">
                    <button type="button" class="filter-chip {{ request('district', 'All') !== 'All' ? 'active' : '' }}" data-popover="popover-district">
                        <svg viewBox="0 0 24 24">
                            <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-9-7-9zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z" />
                        </svg>
                        <span>District: {{ request('district', 'All') }}</span>
                    </button>
                    <div id="popover-district" class="popover">
                        <div class="popover-header">Filter District</div>
                        <div class="popover-content">
                            <div class="options-list">
                                @foreach($districts as $district)
                                <button type="submit" name="district" value="{{ $district }}" class="option-item {{ request('district', 'All') === $district ? 'selected' : '' }}">{{ $district }}</button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div class="relative">
                    <button type="button" class="filter-chip {{ request('state', 'All') !== 'All' ? 'active' : '' }}" data-popover="popover-state">
                        <svg viewBox="0 0 24 24">
                            <path d="M12 2L4.5 20.29l.71.71L12 18l6.79 3 .71-.71z" />
                        </svg>
                        <span>State: {{ request('state', 'All') }}</span>
                    </button>
                    <div id="popover-state" class="popover">
                        <div class="popover-header">Filter State</div>
                        <div class="popover-content">
                            <div class="options-list">
                                @foreach($states as $state)
                                <button type="submit" name="state" value="{{ $state }}" class="option-item {{ request('state', 'All') === $state ? 'selected' : '' }}">{{ $state }}</button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div class="relative">
                    <button type="button" class="filter-chip {{ request('type', 'All') !== 'All' ? 'active' : '' }}" data-popover="popover-type">
                        <svg viewBox="0 0 24 24">
                            <path d="M10 16h4v-2h-4v2zm3-14H5c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V7l-6-6zm-1 7V3.5L18.5 9H12z" />
                        </svg>
                        <span>Type: {{ request('type', 'All') }}</span>
                    </button>
                    <div id="popover-type" class="popover">
                        <div class="popover-header">Firm Type</div>
                        <div class="popover-content">
                            <div class="options-list">
                                @foreach($types as $type)
                                <button type="submit" name="type" value="{{ $type }}" class="option-item {{ request('type', 'All') === $type ? 'selected' : '' }}">{{ $type }}</button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-2 ml-auto">
                    <label class="flex items-center gap-2 cursor-pointer bg-white border border-gray-200 rounded-lg px-3 py-1.5 hover:bg-gray-50 transition-colors">
                        <input type="checkbox" name="missing" value="1" {{ request('missing') ? 'checked' : '' }} onchange="this.form.submit()" class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                        <span class="text-sm font-medium text-gray-700">Missing Data</span>
                    </label>
                    <a href="{{ url('/parties') }}" class="text-sm font-medium text-blue-600 hover:text-blue-700">Clear All</a>
                    <button type="button" data-add-member-trigger class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm shadow-blue-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        <span>New Party</span>
                    </button>
                </div>
            </form>
        </div>
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
        @endphp
        <tr id="row-{{ $party->id }}" class="hover:bg-gray-50/50 transition-colors">
            <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center text-gray-500 font-bold border border-gray-200">
                        {{ $initials }}
                    </div>
                    <div>
                        <div class="text-sm font-bold text-gray-900">{{ $party->name }}</div>
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
            data-form-fields="name,company_code,alias,owner_name,mobile,gst_no,pan_no,aadhar_card,street_address,city,district,state,pin_code,employee_id,party_type,status,pest_lic,fert_lic,seed_lic,cq1,cq2,stamp,sign,bank_name,bank_account_no,bank_ifsc"
            class="fixed inset-y-0 right-0 w-[560px] bg-white shadow-2xl border-l border-gray-200 transform translate-x-full transition-transform duration-300 z-50 overflow-y-auto">
            <div class="p-8">
                <div class="flex items-center justify-between mb-8">
                    <h2 class="text-2xl font-bold text-gray-900" id="panelTitle">New Party</h2>
                    <button type="button" data-add-member-cancel class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-50 rounded-lg transition-all">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
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
    });

    // Handle popover toggling for the custom filters
    document.querySelectorAll('[data-popover]').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            const popoverId = btn.getAttribute('data-popover');
            const popover = document.getElementById(popoverId);
            const isVisible = popover.classList.contains('show');

            document.querySelectorAll('.popover').forEach(p => p.classList.remove('show'));
            if (!isVisible) popover.classList.add('show');
        });
    });

    document.addEventListener('click', (e) => {
        if (!e.target.closest('.popover') && !e.target.closest('[data-popover]')) {
            document.querySelectorAll('.popover').forEach(p => p.classList.remove('show'));
        }
    });

    // Handle search in popover
    const nameSearchInput = document.getElementById('nameSearch');
    if (nameSearchInput) {
        nameSearchInput.addEventListener('input', (e) => {
            const term = e.target.value.toLowerCase();
            const options = e.target.closest('.popover-content').querySelectorAll('.options-list .option-item');
            options.forEach(opt => {
                const text = opt.textContent.toLowerCase();
                opt.style.display = text.includes(term) ? 'flex' : 'none';
            });
        });
    }
</script>
<style>
    .filter-chip {
        display: inline-flex;
        align-items: center;
        height: 32px;
        padding: 0 12px;
        background: white;
        border: 1px solid #dadce0;
        border-radius: 16px;
        font-size: 14px;
        color: #5f6368;
        cursor: pointer;
        transition: background 0.2s, border-color 0.2s;
        gap: 8px;
    }

    .filter-chip svg {
        width: 18px;
        height: 18px;
        fill: currentColor;
    }

    .filter-chip.active {
        background-color: #e8f0fe;
        border-color: #1a73e8;
        color: #1a73e8;
    }

    .popover {
        display: none;
        position: absolute;
        top: 40px;
        left: 0;
        background: white;
        border-radius: 8px;
        box-shadow: 0 1px 2px 0 rgba(60, 64, 67, .30), 0 2px 6px 2px rgba(60, 64, 67, .15);
        border: 1px solid #dadce0;
        z-index: 1000;
        min-width: 250px;
        padding: 8px 0;
        margin-top: 4px;
    }

    .popover.show {
        display: block;
    }

    .popover-header {
        padding: 12px 16px;
        font-size: 11px;
        font-weight: 700;
        color: #5f6368;
        text-transform: uppercase;
        border-bottom: 1px solid #f1f3f4;
    }

    .popover-content {
        padding: 16px;
    }

    .search-box input {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #dadce0;
        border-radius: 4px;
        font-size: 14px;
        box-sizing: border-box;
        margin-bottom: 8px;
    }

    .options-list {
        max-height: 200px;
        overflow-y: auto;
    }

    .option-item {
        width: 100%;
        text-align: left;
        padding: 8px 12px;
        font-size: 14px;
        cursor: pointer;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border: none;
        background: none;
    }

    .option-item:hover {
        background: #f1f3f4;
    }

    .option-item.selected {
        color: #1a73e8;
        font-weight: 600;
    }
</style>
@endpush

@endsection

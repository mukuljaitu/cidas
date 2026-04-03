@extends('layouts.admin')

@section('title', 'Transports')

@section('content')
<x-table-ui-layout title="Transports" :paginator="$transports">
    <x-slot name="toolbar">
        <div class="flex items-center gap-3">
            <x-table-filters :names="$names" :action="url('/transports')" :showDate="false" :showStatus="false" :showRole="false" />
            <button type="button" data-add-member-trigger class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm shadow-blue-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                <span>New Transport</span>
            </button>
        </div>
    </x-slot>

    <x-slot name="thead">
        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Transport Details</th>
        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Vehicle Info</th>
        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Contact</th>
        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Stats</th>
        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider text-right">Actions</th>
    </x-slot>

    <x-slot name="tbody">
        @forelse($transports as $transport)
            @php
            $lastTrip = $transport->last_trip?->format('Y-m-d');
            $dateOfJoining = $transport->date_of_joining?->format('Y-m-d');
            $initials = strtoupper(substr($transport->name, 0, 1));
            @endphp
            <tr id="row-{{ $transport->id }}" class="hover:bg-gray-50/50 transition-colors">
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center text-gray-500 font-bold border border-gray-200">
                            {{ $initials }}
                        </div>
                        <div>
                            <div class="text-sm font-bold text-gray-900">{{ $transport->name }}</div>
                            <div class="text-xs text-gray-400 font-medium">Joined: {{ $dateOfJoining ?: '—' }}</div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4">
                    <div class="text-sm font-bold text-gray-900">{{ $transport->vehicle ?: '—' }}</div>
                    <div class="text-xs text-gray-400 font-medium">{{ $transport->vehicle_number ?: 'No vehicle no.' }}</div>
                </td>
                <td class="px-6 py-4 text-sm text-gray-700 font-medium">
                    {{ $transport->contact ?: '—' }}
                </td>
                <td class="px-6 py-4">
                    <div class="flex flex-col gap-1">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-tight">Total:</span>
                            <span class="text-sm font-bold text-gray-900">{{ $transport->total_trips }} trips</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-tight">Last:</span>
                            <span class="text-xs font-medium text-gray-500">{{ $lastTrip ?: 'Never' }}</span>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 text-right">
                    <button
                        type="button"
                        class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-all"
                        data-edit-member-trigger
                        data-id="{{ $transport->id }}"
                        data-name="{{ $transport->name }}"
                        data-vehicle="{{ $transport->vehicle }}"
                        data-vehicle_number="{{ $transport->vehicle_number }}"
                        data-contact="{{ $transport->contact }}"
                        data-last_trip="{{ $lastTrip }}"
                        data-total_trips="{{ $transport->total_trips }}"
                        data-date_of_joining="{{ $dateOfJoining }}">
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
                        <span class="text-sm font-medium">No transports found</span>
                    </div>
                </td>
            </tr>
        @endforelse
    </x-slot>

    <x-slot name="overlay">
        <!-- Add/Edit Transport Panel -->
        <div id="addMemberPanel"
            data-entity-singular="Transport"
            data-entity-plural="Transports"
            data-resource="{{ url('/transports') }}"
            data-form-fields="name,vehicle,vehicle_number,contact,total_trips,date_of_joining,last_trip"
            class="fixed inset-y-0 right-0 w-[480px] bg-white shadow-2xl border-l border-gray-200 transform translate-x-full transition-transform duration-300 z-50 overflow-y-auto">
            <div class="p-8">
                <div class="flex items-center justify-between mb-8">
                    <h2 class="text-2xl font-bold text-gray-900" id="panelTitle">New Transport</h2>
                    <button type="button" data-add-member-cancel class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-50 rounded-lg transition-all">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <form id="memberForm" method="POST" action="{{ url('/transports') }}" class="space-y-6">
                    @csrf
                    <input type="hidden" name="_method" value="">
                    
                    <div class="space-y-4">
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-gray-700">Transport Name</label>
                            <input name="name" type="text" required class="block w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" />
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1.5">
                                <label class="text-sm font-semibold text-gray-700">Vehicle Type</label>
                                <input name="vehicle" type="text" class="block w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" placeholder="e.g. Truck, Van" />
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-sm font-semibold text-gray-700">Vehicle Number</label>
                                <input name="vehicle_number" type="text" class="block w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" placeholder="e.g. PB-65-AX-1234" />
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-gray-700">Contact Number</label>
                            <input name="contact" type="text" class="block w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" />
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1.5">
                                <label class="text-sm font-semibold text-gray-700">Total Trips</label>
                                <input name="total_trips" type="number" min="0" class="block w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" placeholder="0" />
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-sm font-semibold text-gray-700">Joining Date</label>
                                <input name="date_of_joining" type="date" class="block w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" />
                            </div>
                        </div>
                        
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-gray-700">Last Trip Date</label>
                            <input name="last_trip" type="date" class="block w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" />
                        </div>
                    </div>

                    <div class="pt-8 flex items-center gap-3">
                        <button type="submit" class="flex-1 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl transition-all shadow-lg shadow-blue-200">
                            Save Transport
                        </button>
                        <button type="button" data-add-member-cancel class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold rounded-xl transition-all">
                            Cancel
                        </button>
                    </div>

                    <button type="button" data-delete-member class="w-full mt-4 px-6 py-3 bg-red-50 text-red-600 font-bold rounded-xl hover:bg-red-100 transition-all flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        Delete Transport
                    </button>
                </form>
            </div>
        </div>
    </x-slot>
</x-table-ui-layout>

<x-modals />
@endsection

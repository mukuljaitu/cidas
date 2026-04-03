@extends('layouts.admin')

@section('title', 'Daily Sales Tours')

@section('content')
<x-table-ui-layout title="Daily Sales Tours" :paginator="$tours">
    <x-slot name="toolbar">
        <x-tour-filters
            :action="route('tours.index')"
            :names="$names"
            :statuses="$statuses"
            :states="$states"
            :months="$months" />
    </x-slot>

    <x-slot name="thead">
        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider w-[25%]">Name</th>
        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider w-[15%]">Date</th>
        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider w-[35%]">Cities</th>
        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider w-[15%]">Status</th>
        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider text-right w-[10%]">Actions</th>
    </x-slot>

    <x-slot name="tbody">
        @forelse($tours as $tour)
        <tr id="row-{{ $tour->id }}" class="hover:bg-gray-50/50 transition-colors">
            <td class="px-6 py-4">
                <div class="flex flex-col">
                    <span class="text-sm font-bold text-gray-900">{{ $tour->employee_name }}</span>
                    @if($tour->is_supervisor)
                    <span class="text-[10px] font-bold text-red-500 uppercase tracking-tighter">Supervisor</span>
                    @endif
                </div>
            </td>
            <td class="px-6 py-4">
                <span class="text-sm font-medium text-gray-600">{{ \Carbon\Carbon::parse($tour->tour_date)->format('d-m-Y') }}</span>
            </td>
            <td class="px-6 py-4">
                @php
                $cities = collect(explode(',', (string) $tour->cities))
                ->map(fn ($c) => trim((string) $c))
                ->filter()
                ->unique()
                ->values();
                @endphp
                <button type="button" class="w-full text-left" data-open-cities-modal data-employee_name="{{ $tour->employee_name }}" data-cities="{{ $cities->implode(',') }}">
                    @php
                    $displayCities = $cities;
                    @endphp
                    <div class="flex flex-wrap gap-1">
                        @forelse($displayCities as $city)
                        <span class="text-sm font-medium text-blue-600 hover:underline cursor-pointer">{{ $city }}{{ !$loop->last ? ',' : '' }}</span>
                        @empty
                        <span class="text-sm text-gray-400">—</span>
                        @endforelse
                    </div>
                    @if($displayCities->count() > 0)
                    <div class="mt-1 text-[10px] font-bold text-gray-400 uppercase tracking-wider">View all</div>
                    @endif
                </button>
                </div>
            <td class="px-6 py-4">
                @php
                $status = $tour->status;
                $statusClass = 'bg-gray-100 text-gray-600';
                $statusLabel = $status;

                if ($status == '1') {
                $statusClass = 'bg-blue-50 text-blue-600 border-blue-100';
                $statusLabel = 'Tour';
                } elseif (str_contains(strtolower($status), 'field')) {
                $statusClass = 'bg-green-50 text-green-600 border-green-100';
                } elseif (str_contains(strtolower($status), 'no station')) {
                $statusClass = 'bg-blue-50 text-blue-600 border-blue-100';
                $statusLabel = 'No Station';
                }
                @endphp
                <span class="px-3 py-1 rounded-full text-xs font-bold border {{ $statusClass }}">
                    {{ $statusLabel }}
                </span>
            </td>
            <td class="px-6 py-4 text-right">
                <button
                    type="button"
                    class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-all"
                    data-edit-member-trigger
                    data-id="{{ $tour->id }}"
                    data-employee_name="{{ $tour->employee_name }}"
                    data-tour_date="{{ \Carbon\Carbon::parse($tour->tour_date)->format('Y-m-d') }}"
                    data-status="{{ $tour->status }}"
                    data-cities="{{ $tour->cities }}">
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    <span class="text-sm font-medium">No tours found</span>
                </div>
            </td>
        </tr>
        @endforelse
    </x-slot>

    <x-slot name="overlay">
        <!-- Edit Tour Panel -->
        <div id="addMemberPanel"
            class="fixed inset-y-0 right-0 w-[480px] bg-white shadow-2xl border-l border-gray-200 transform translate-x-full transition-transform duration-300 z-50 overflow-y-auto"
            data-entity-singular="Tour"
            data-resource="/view-tours"
            data-form-fields="tour_date,status">
            <div class="p-8">
                <div class="flex items-center justify-between mb-8">
                    <h2 class="text-2xl font-bold text-gray-900" id="panelTitle">Edit Tour</h2>
                    <button type="button" data-add-member-cancel class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-50 rounded-lg transition-all">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form id="memberForm" method="POST" action="" class="space-y-6">
                    @csrf
                    <input type="hidden" name="_method" value="PUT">

                    <div class="space-y-4">
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-gray-700">Employee Name</label>
                            <input name="employee_name" type="text" readonly class="block w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-500 cursor-not-allowed focus:outline-none" />
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-gray-700">Tour Date</label>
                            <input name="tour_date" type="date" required class="block w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" />
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-gray-700">Status</label>
                            <select name="status" id="statusSelect" required class="block w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all appearance-none">
                                <option value="1">Tour</option>
                                <option value="Field Visit">Field Visit</option>
                                <option value="Holiday">Holiday</option>
                                <option value="Leave">Leave</option>
                                <option value="No Station">No Station</option>
                                <option value="Office Visit">Office Visit</option>
                            </select>
                        </div>

                        <div class="space-y-1.5" id="citySelectionSection">
                            <label class="text-sm font-semibold text-gray-700">Select Cities</label>
                            <div id="cityGrid" class="grid grid-cols-2 gap-2 mt-2">
                                <!-- City tiles will be injected here via JS -->
                            </div>
                            <div id="selectedCitiesInputs">
                                <!-- Hidden inputs for selected cities will be injected here -->
                            </div>
                        </div>
                    </div>

                    <div class="pt-8 flex items-center gap-3">
                        <button type="submit" class="flex-1 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl transition-all shadow-lg shadow-blue-200">
                            Save Changes
                        </button>
                        <button type="button" data-add-member-cancel class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold rounded-xl transition-all">
                            Cancel
                        </button>
                    </div>

                    <button type="button" data-delete-member class="w-full mt-4 px-6 py-3 bg-red-50 text-red-600 font-bold rounded-xl hover:bg-red-100 transition-all flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Delete Tour
                    </button>
                </form>
            </div>
        </div>

        <div id="citiesModal" class="fixed inset-0 hidden items-center justify-center bg-black/40 z-[80] px-4">
            <div class="w-full max-w-2xl rounded-2xl bg-white shadow-2xl border border-gray-200">
                <div class="px-6 py-5 flex items-center justify-between border-b border-gray-100">
                    <div class="text-lg font-extrabold text-gray-900" id="citiesModalTitle">Cities</div>
                    <button type="button" data-close-cities-modal class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-50 rounded-lg transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div class="px-6 py-5">
                    <div id="citiesModalBody" class="flex flex-wrap gap-2"></div>
                    <div class="mt-8 flex justify-end">
                        <button type="button" data-close-cities-modal class="px-8 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl transition-all shadow-lg shadow-blue-200">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>
</x-table-ui-layout>

@push('scripts')
<script>
    const employeeCityMap = @json($employeeCityMap);
    const citiesModal = document.getElementById('citiesModal');
    const citiesModalTitle = document.getElementById('citiesModalTitle');
    const citiesModalBody = document.getElementById('citiesModalBody');

    function openCitiesModal(employeeName, citiesCsv) {
        if (!citiesModal || !citiesModalTitle || !citiesModalBody) return;
        const raw = (citiesCsv || '').split(',').map(s => (s || '').trim()).filter(Boolean);
        const seen = new Set();
        const unique = [];
        raw.forEach(c => {
            const k = c.toLowerCase();
            if (seen.has(k)) return;
            seen.add(k);
            unique.push(c);
        });

        citiesModalTitle.textContent = employeeName ? `Cities for ${employeeName}` : 'Cities';
        citiesModalBody.innerHTML = '';
        if (unique.length === 0) {
            citiesModalBody.innerHTML = '<div class="text-sm font-semibold text-gray-400">No cities</div>';
        } else {
            unique.forEach(c => {
                const chip = document.createElement('span');
                chip.className = 'px-3 py-1 rounded-full text-xs font-bold border bg-gray-50 text-gray-700 border-gray-200';
                chip.textContent = c;
                citiesModalBody.appendChild(chip);
            });
        }
        citiesModal.classList.remove('hidden');
        citiesModal.classList.add('flex');
    }

    function closeCitiesModal() {
        if (!citiesModal) return;
        citiesModal.classList.add('hidden');
        citiesModal.classList.remove('flex');
    }

    document.addEventListener('click', function(e) {
        const openCities = e.target.closest('[data-open-cities-modal]');
        if (openCities) {
            openCitiesModal(openCities.getAttribute('data-employee_name') || '', openCities.getAttribute('data-cities') || '');
            return;
        }

        if (e.target.closest('[data-close-cities-modal]')) {
            closeCitiesModal();
            return;
        }

        if (citiesModal && e.target === citiesModal) {
            closeCitiesModal();
            return;
        }

        const trigger = e.target.closest('[data-edit-member-trigger]');
        if (trigger) {
            const employeeName = trigger.getAttribute('data-employee_name');
            const selectedCitiesStr = trigger.getAttribute('data-cities') || '';
            const selectedCities = selectedCitiesStr.split(',').map(s => s.trim()).filter(Boolean);
            const status = trigger.getAttribute('data-status');

            // Inject employee name into form
            const form = document.getElementById('memberForm');
            form.querySelector('[name="employee_name"]').value = employeeName;
            form.querySelector('[name="tour_date"]').value = trigger.getAttribute('data-tour_date');
            form.querySelector('[name="status"]').value = status;

            // Build city grid
            const cityGrid = document.getElementById('cityGrid');
            cityGrid.innerHTML = '';
            const availableCities = employeeCityMap[employeeName] || [];

            availableCities.forEach(city => {
                const isSelected = selectedCities.includes(city);
                const tile = document.createElement('div');
                tile.className = `cursor-pointer px-3 py-2 rounded-lg text-sm font-medium border transition-all ${isSelected ? 'bg-blue-50 border-blue-200 text-blue-700' : 'bg-gray-50 border-gray-100 text-gray-600 hover:bg-gray-100'}`;
                tile.textContent = city;
                tile.dataset.city = city;
                tile.dataset.selected = isSelected;

                tile.onclick = function() {
                    const nowSelected = this.dataset.selected === 'false';
                    this.dataset.selected = nowSelected;
                    this.className = `cursor-pointer px-3 py-2 rounded-lg text-sm font-medium border transition-all ${nowSelected ? 'bg-blue-50 border-blue-200 text-blue-700' : 'bg-gray-50 border-gray-100 text-gray-600 hover:bg-gray-100'}`;
                    updateCityInputs();
                };

                cityGrid.appendChild(tile);
            });

            updateCityInputs();
            handleStatusChange(status);
        }
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeCitiesModal();
    });

    const statusSelect = document.getElementById('statusSelect');
    statusSelect.onchange = function() {
        handleStatusChange(this.value);
    };

    function handleStatusChange(status) {
        const citySection = document.getElementById('citySelectionSection');
        const specialStatuses = ['Leave', 'Holiday', 'No Station', 'Office Visit'];
        if (specialStatuses.includes(status)) {
            citySection.style.display = 'none';
        } else {
            citySection.style.display = 'block';
        }
    }

    function updateCityInputs() {
        const container = document.getElementById('selectedCitiesInputs');
        container.innerHTML = '';
        document.querySelectorAll('#cityGrid [data-selected="true"]').forEach(tile => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'cities[]';
            input.value = tile.dataset.city;
            container.appendChild(input);
        });
    }
</script>
@endpush
@endsection
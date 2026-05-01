@extends('layouts.admin')

@section('title', 'City Intelligence')

@section('content')
@php
$selectedEmployeeId = (int) request('employee_id', 0);
$minSamples = (int) ($filters['min_samples'] ?? 1);
$minConfidence = (float) ($filters['min_confidence'] ?? 0);
@endphp

<div class="flex flex-col gap-6">
    <div class="flex flex-col gap-2">
        <h1 class="text-3xl font-bold text-gray-900 tracking-tight">City Intelligence</h1>
        <div class="text-sm font-semibold text-gray-400">Auto-categorize cities into districts using Party master data</div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/30">
            <div class="text-xs font-extrabold text-gray-400 uppercase tracking-[0.2em]">Filters</div>
        </div>
        <div class="p-6">
            <form method="GET" action="{{ route('cities.analyze') }}" class="flex flex-wrap items-end gap-3">
                <div class="flex flex-col gap-1">
                    <div class="text-xs font-extrabold text-gray-400 uppercase tracking-[0.2em]">State</div>
                    <select name="state" class="h-10 px-3 rounded-lg border border-gray-200 bg-white text-sm font-semibold text-gray-700">
                        <option value="All" {{ ($selectedState ?? 'All') === 'All' ? 'selected' : '' }}>All</option>
                        @foreach($states as $st)
                        <option value="{{ $st }}" {{ ($selectedState ?? 'All') === $st ? 'selected' : '' }}>{{ $st }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex flex-col gap-1 min-w-[240px]">
                    <div class="text-xs font-extrabold text-gray-400 uppercase tracking-[0.2em]">Employee</div>
                    <select name="employee_id" class="h-10 px-3 rounded-lg border border-gray-200 bg-white text-sm font-semibold text-gray-700">
                        <option value="All" {{ $selectedEmployeeId <= 0 ? 'selected' : '' }}>All</option>
                        @foreach($employeeOptions as $emp)
                        <option value="{{ $emp->id }}" {{ $selectedEmployeeId === (int) $emp->id ? 'selected' : '' }}>
                            {{ $emp->name }}{{ $emp->state ? ' - '.$emp->state : '' }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex flex-col gap-1">
                    <div class="text-xs font-extrabold text-gray-400 uppercase tracking-[0.2em]">Min Samples</div>
                    <input name="min_samples" type="number" min="1" max="10000" value="{{ $minSamples }}" title="Minimum number of Party records needed for a city before we show its inferred district" class="h-10 w-32 px-3 rounded-lg border border-gray-200 bg-white text-sm font-semibold text-gray-700" />
                </div>

                <div class="flex flex-col gap-1">
                    <div class="text-xs font-extrabold text-gray-400 uppercase tracking-[0.2em]">Min Confidence</div>
                    <input name="min_confidence" type="number" step="0.05" min="0" max="1" value="{{ $minConfidence }}" title="Confidence is (top district count / total samples) for that city. Example: 8 out of 10 = 0.8" class="h-10 w-40 px-3 rounded-lg border border-gray-200 bg-white text-sm font-semibold text-gray-700" />
                </div>

                <button type="submit" class="inline-flex items-center h-10 px-4 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold transition-colors">
                    Apply
                </button>
            </form>

            <div class="mt-3 text-xs font-semibold text-gray-400">
                Min Samples filters out cities with too little data. Min Confidence filters out cities where district mapping is ambiguous.
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
            <div class="text-xs font-extrabold text-gray-400 uppercase tracking-[0.2em]">Cities Analyzed</div>
            <div class="mt-2 text-3xl font-bold text-gray-900">{{ (int) ($summary['cities_total'] ?? 0) }}</div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
            <div class="text-xs font-extrabold text-gray-400 uppercase tracking-[0.2em]">Mapped</div>
            <div class="mt-2 text-3xl font-bold text-gray-900">{{ (int) ($summary['cities_mapped'] ?? 0) }}</div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
            <div class="text-xs font-extrabold text-gray-400 uppercase tracking-[0.2em]">Low Confidence</div>
            <div class="mt-2 text-3xl font-bold text-gray-900">{{ (int) ($summary['cities_low_confidence'] ?? 0) }}</div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
            <div class="text-xs font-extrabold text-gray-400 uppercase tracking-[0.2em]">Samples</div>
            <div class="mt-2 text-3xl font-bold text-gray-900">{{ (int) ($summary['samples_total'] ?? 0) }}</div>
        </div>
    </div>

    @if($selectedEmployee)
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/30 flex items-center justify-between gap-4">
            <div class="min-w-0">
                <div class="text-xs font-extrabold text-gray-400 uppercase tracking-[0.2em]">Employee District View</div>
                <div class="mt-1 text-sm font-bold text-gray-900 truncate">{{ $selectedEmployee->name }}{{ $selectedEmployee->state ? ' - '.$selectedEmployee->state : '' }}</div>
            </div>
            <div class="text-xs font-semibold text-gray-400 shrink-0">{{ count($employeeCities ?? []) }} cities</div>
        </div>
        <div class="p-6">
            @if(empty($employeeCitiesByDistrict))
            <div class="text-sm font-semibold text-gray-400">No cities found for this employee</div>
            @else
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                @foreach($employeeCitiesByDistrict as $district => $items)
                <div class="rounded-2xl border border-gray-200 p-5">
                    <div class="flex items-start justify-between gap-3">
                        <div class="text-sm font-extrabold text-gray-900">{{ $district }}</div>
                        <div class="text-xs font-semibold text-gray-400">{{ count($items) }}</div>
                    </div>
                    <div class="mt-3 flex flex-wrap gap-2">
                        @foreach($items as $it)
                        @php
                        $conf = (float) ($it['confidence'] ?? 0);
                        $samples = (int) ($it['samples'] ?? 0);
                        $label = (string) ($it['city'] ?? '');
                        $confPct = (int) round($conf * 100);
                        @endphp
                        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full border border-gray-200 bg-white text-xs font-semibold text-gray-700">
                            <span>{{ $label }}</span>
                            <span class="text-gray-400">{{ $samples > 0 ? ($confPct.'%/'.$samples) : '—' }}</span>
                        </span>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>
    @endif

    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/30 flex flex-wrap items-center justify-between gap-3">
            <div class="min-w-0">
                <div class="text-xs font-extrabold text-gray-400 uppercase tracking-[0.2em]">City → District Map</div>
                <div class="mt-1 text-xs font-semibold text-gray-400">Showing {{ count($rows ?? []) }} rows (after filters)</div>
            </div>
            <div class="min-w-[260px]">
                <input id="citySearch" type="search" placeholder="Search city / district..." class="h-10 w-full px-3 rounded-lg border border-gray-200 bg-white text-sm font-semibold text-gray-700" />
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-white">
                    <tr class="border-b border-gray-100">
                        <th class="text-left px-6 py-3 text-xs font-extrabold text-gray-400 uppercase tracking-[0.2em]">State</th>
                        <th class="text-left px-6 py-3 text-xs font-extrabold text-gray-400 uppercase tracking-[0.2em]">City</th>
                        <th class="text-left px-6 py-3 text-xs font-extrabold text-gray-400 uppercase tracking-[0.2em]">Inferred District</th>
                        <th class="text-left px-6 py-3 text-xs font-extrabold text-gray-400 uppercase tracking-[0.2em]">Confidence</th>
                        <th class="text-left px-6 py-3 text-xs font-extrabold text-gray-400 uppercase tracking-[0.2em]">Samples</th>
                        <th class="text-left px-6 py-3 text-xs font-extrabold text-gray-400 uppercase tracking-[0.2em]">Top Alternatives</th>
                    </tr>
                </thead>
                <tbody id="cityTableBody" class="divide-y divide-gray-100">
                    @forelse($rows as $r)
                    @php
                    $conf = (float) ($r['confidence'] ?? 0);
                    $confPct = (int) round($conf * 100);
                    $samples = (int) ($r['samples'] ?? 0);
                    $alt = $r['alternatives'] ?? [];
                    $altText = collect($alt)->take(3)->map(function ($a) {
                        $d = (string) ($a['district'] ?? '');
                        $c = (int) ($a['count'] ?? 0);
                        return $d !== '' ? ($d.' ('.$c.')') : '';
                    })->filter()->values()->implode(', ');
                    $search = mb_strtolower(trim(($r['state'] ?? '').' '.($r['city'] ?? '').' '.($r['district'] ?? '').' '.$altText));
                    @endphp
                    <tr data-search="{{ $search }}">
                        <td class="px-6 py-4 font-semibold text-gray-700 whitespace-nowrap">{{ $r['state'] ?? '' }}</td>
                        <td class="px-6 py-4 font-semibold text-gray-900 whitespace-nowrap">{{ $r['city'] ?? '' }}</td>
                        <td class="px-6 py-4 font-bold text-gray-900 whitespace-nowrap">{{ $r['district'] ?? '' }}</td>
                        <td class="px-6 py-4 font-semibold text-gray-700 whitespace-nowrap">{{ $confPct }}%</td>
                        <td class="px-6 py-4 font-semibold text-gray-700 whitespace-nowrap">{{ $samples }}</td>
                        <td class="px-6 py-4 text-gray-500">{{ $altText }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td class="px-6 py-8 text-sm font-semibold text-gray-400" colspan="6">No rows found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    (function() {
        const input = document.getElementById('citySearch');
        const body = document.getElementById('cityTableBody');
        if (!input || !body) return;

        const rows = Array.from(body.querySelectorAll('tr[data-search]'));

        function apply() {
            const term = String(input.value || '').trim().toLowerCase();
            let shown = 0;
            for (const tr of rows) {
                const hay = tr.getAttribute('data-search') || '';
                const ok = term === '' || hay.includes(term);
                tr.style.display = ok ? '' : 'none';
                if (ok) shown++;
            }
        }

        input.addEventListener('input', apply);
        apply();
    })();
</script>
@endpush

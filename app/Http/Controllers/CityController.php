<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Employee;
use App\Models\Party;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CityController extends Controller
{
    private function normalizeKey($value): string
    {
        $s = trim((string) $value);
        if ($s === '') {
            return '';
        }

        $s = preg_replace('/\s+/u', ' ', $s);
        $s = trim((string) $s);

        return mb_strtolower($s);
    }

    private function normalizeDisplay($value): string
    {
        $s = trim((string) $value);
        if ($s === '') {
            return '';
        }

        $s = preg_replace('/\s+/u', ' ', $s);

        return trim((string) $s);
    }

    public function index(Request $request)
    {
        $name = trim((string) $request->get('name', ''));
        $employeeId = trim((string) $request->get('employee_id', ''));

        $query = City::query()
            ->with('employee')
            ->orderBy('city')
            ->orderByDesc('id');

        if ($name !== '' && $name !== 'All') {
            $query->whereRaw('LOWER(TRIM(city)) = ?', [mb_strtolower($name)]);
        }

        if ($employeeId !== '' && $employeeId !== 'All') {
            $query->where('employee_id', (int) $employeeId);
        }

        $cities = $query->paginate(10)->withQueryString();

        $names = City::query()
            ->select('city')
            ->distinct()
            ->orderBy('city')
            ->pluck('city')
            ->map(fn($c) => trim((string) $c))
            ->filter()
            ->unique()
            ->values();

        $employeeOptions = Employee::query()
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('cities.index', compact('cities', 'names', 'employeeOptions'));
    }

    public function analyze(Request $request)
    {
        $selectedState = trim($request->string('state')->toString());
        $selectedStateNormalized = mb_strtolower($selectedState);
        if ($selectedState === '' || $selectedStateNormalized === 'all' || $selectedStateNormalized === 'all states') {
            $selectedState = 'All';
        }
        $stateFilter = $selectedState === 'All' ? null : $selectedState;

        $employeeIdRaw = trim((string) $request->get('employee_id', ''));
        $employeeId = ($employeeIdRaw === '' || mb_strtolower($employeeIdRaw) === 'all') ? 0 : (int) $employeeIdRaw;

        $minSamples = (int) $request->input('min_samples', 1);
        $minSamples = max(1, min($minSamples, 10000));

        $minConfidence = (float) $request->input('min_confidence', 0);
        if ($minConfidence < 0) {
            $minConfidence = 0;
        }
        if ($minConfidence > 1) {
            $minConfidence = 1;
        }

        $states = Party::query()
            ->select('state')
            ->distinct()
            ->orderBy('state')
            ->pluck('state')
            ->map(fn($s) => $this->normalizeDisplay($s))
            ->filter()
            ->unique()
            ->values();

        $employeeQuery = Employee::query()->orderBy('name');
        if ($stateFilter !== null) {
            $employeeQuery->whereRaw('LOWER(TRIM(state)) = ?', [mb_strtolower(trim($stateFilter))]);
        }
        $employeeOptions = $employeeQuery->get(['id', 'name', 'state']);

        $selectedEmployee = null;
        if ($employeeId > 0) {
            $selectedEmployee = $employeeOptions->firstWhere('id', $employeeId);
            if (! $selectedEmployee) {
                $selectedEmployee = Employee::query()->where('id', $employeeId)->first(['id', 'name', 'state']);
            }
        }

        $districtCountsQuery = Party::query()
            ->selectRaw('TRIM(state) as state, TRIM(city) as city, TRIM(district) as district, COUNT(*) as cnt')
            ->whereNotNull('city')
            ->whereRaw("TRIM(city) <> ''")
            ->whereNotNull('district')
            ->whereRaw("TRIM(district) <> ''");

        if ($stateFilter !== null) {
            $districtCountsQuery->whereRaw('LOWER(TRIM(state)) = ?', [mb_strtolower(trim($stateFilter))]);
        }

        $districtCounts = $districtCountsQuery
            ->groupBy(DB::raw('TRIM(state)'), DB::raw('TRIM(city)'), DB::raw('TRIM(district)'))
            ->get();

        $stateDisplayByKey = [];
        $cityDisplayByStateAndKey = [];
        $districtDisplayByKey = [];
        $counts = [];

        foreach ($districtCounts as $r) {
            $stateDisplay = $this->normalizeDisplay($r->state);
            $stateKey = $this->normalizeKey($stateDisplay);
            if ($stateKey === '') {
                $stateKey = '__unknown__';
                $stateDisplay = 'Unknown';
            }
            $stateDisplayByKey[$stateKey] = $stateDisplayByKey[$stateKey] ?? $stateDisplay;

            $cityDisplay = $this->normalizeDisplay($r->city);
            $cityKey = $this->normalizeKey($cityDisplay);
            if ($cityKey === '') {
                continue;
            }
            $cityDisplayByStateAndKey[$stateKey][$cityKey] = $cityDisplayByStateAndKey[$stateKey][$cityKey] ?? $cityDisplay;

            $districtDisplay = $this->normalizeDisplay($r->district);
            $districtKey = $this->normalizeKey($districtDisplay);
            if ($districtKey === '') {
                continue;
            }
            $districtDisplayByKey[$districtKey] = $districtDisplayByKey[$districtKey] ?? $districtDisplay;

            $cnt = (int) ($r->cnt ?? 0);
            if ($cnt <= 0) {
                continue;
            }

            $counts[$stateKey][$cityKey][$districtKey] = ($counts[$stateKey][$cityKey][$districtKey] ?? 0) + $cnt;
        }

        $inferred = [];
        $allRows = [];
        $summary = [
            'cities_total' => 0,
            'cities_mapped' => 0,
            'cities_low_confidence' => 0,
            'samples_total' => 0,
        ];

        foreach ($counts as $stateKey => $cityMap) {
            foreach ($cityMap as $cityKey => $districtMap) {
                $total = 0;
                foreach ($districtMap as $dk => $c) {
                    $total += (int) $c;
                }
                if ($total <= 0) {
                    continue;
                }

                arsort($districtMap);
                $topDistrictKey = (string) array_key_first($districtMap);
                $topCount = (int) ($districtMap[$topDistrictKey] ?? 0);
                $confidence = $topCount > 0 ? ($topCount / $total) : 0;

                $alternatives = [];
                foreach ($districtMap as $dk => $c) {
                    $alternatives[] = [
                        'district' => $districtDisplayByKey[$dk] ?? (string) $dk,
                        'count' => (int) $c,
                    ];
                    if (count($alternatives) >= 5) {
                        break;
                    }
                }

                $district = $districtDisplayByKey[$topDistrictKey] ?? (string) $topDistrictKey;
                $inferred[$stateKey][$cityKey] = [
                    'district' => $district,
                    'confidence' => $confidence,
                    'samples' => $total,
                    'alternatives' => $alternatives,
                ];

                $summary['cities_total']++;
                $summary['samples_total'] += $total;
                if ($district !== '') {
                    $summary['cities_mapped']++;
                }
                if ($confidence < 0.75) {
                    $summary['cities_low_confidence']++;
                }

                $allRows[] = [
                    'state' => $stateDisplayByKey[$stateKey] ?? ($stateKey === '__unknown__' ? 'Unknown' : $stateKey),
                    'state_key' => $stateKey,
                    'city' => $cityDisplayByStateAndKey[$stateKey][$cityKey] ?? $cityKey,
                    'city_key' => $cityKey,
                    'district' => $district,
                    'confidence' => $confidence,
                    'samples' => $total,
                    'alternatives' => $alternatives,
                ];
            }
        }

        usort($allRows, function ($a, $b) {
            $sa = mb_strtolower((string) ($a['state'] ?? ''));
            $sb = mb_strtolower((string) ($b['state'] ?? ''));
            if ($sa !== $sb) {
                return $sa <=> $sb;
            }

            $ca = mb_strtolower((string) ($a['city'] ?? ''));
            $cb = mb_strtolower((string) ($b['city'] ?? ''));

            return $ca <=> $cb;
        });

        $rows = array_values(array_filter($allRows, function ($r) use ($minSamples, $minConfidence) {
            $samples = (int) ($r['samples'] ?? 0);
            $confidence = (float) ($r['confidence'] ?? 0);

            return $samples >= $minSamples && $confidence >= $minConfidence;
        }));

        $employeeCitiesByDistrict = [];
        $employeeCities = [];

        if ($selectedEmployee) {
            $partyCities = Party::query()
                ->where('employee_id', (int) $selectedEmployee->id)
                ->whereNotNull('city')
                ->pluck('city')
                ->map(fn($c) => $this->normalizeDisplay($c))
                ->filter()
                ->values();

            $manualCities = City::query()
                ->where('employee_id', (int) $selectedEmployee->id)
                ->pluck('city')
                ->map(fn($c) => $this->normalizeDisplay($c))
                ->filter()
                ->values();

            $employeeCities = $partyCities
                ->merge($manualCities)
                ->map(fn($c) => $this->normalizeDisplay($c))
                ->filter()
                ->unique()
                ->sortBy(fn($c) => mb_strtolower((string) $c))
                ->values()
                ->all();

            $employeeStateKey = $this->normalizeKey($selectedEmployee->state);
            if ($employeeStateKey === '') {
                $employeeStateKey = $this->normalizeKey($stateFilter);
            }
            if ($employeeStateKey === '') {
                $employeeStateKey = '__unknown__';
            }

            foreach ($employeeCities as $city) {
                $cityKey = $this->normalizeKey($city);
                $meta = $inferred[$employeeStateKey][$cityKey] ?? null;
                $district = $meta['district'] ?? 'Unknown';
                $employeeCitiesByDistrict[$district][] = [
                    'city' => $city,
                    'confidence' => (float) ($meta['confidence'] ?? 0),
                    'samples' => (int) ($meta['samples'] ?? 0),
                ];
            }

            uksort($employeeCitiesByDistrict, function ($a, $b) {
                return mb_strtolower((string) $a) <=> mb_strtolower((string) $b);
            });
        }

        return view('cities.analyze', [
            'selectedState' => $selectedState,
            'stateFilter' => $stateFilter,
            'states' => $states,
            'employeeOptions' => $employeeOptions,
            'selectedEmployee' => $selectedEmployee,
            'employeeCities' => $employeeCities,
            'employeeCitiesByDistrict' => $employeeCitiesByDistrict,
            'rows' => $rows,
            'summary' => $summary,
            'filters' => [
                'min_samples' => $minSamples,
                'min_confidence' => $minConfidence,
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => ['required', 'integer', 'exists:employees,id'],
            'city' => ['required', 'string', 'max:255'],
        ]);

        $city = trim((string) $validated['city']);
        $employeeId = (int) $validated['employee_id'];
        if ($city === '') {
            return redirect('/cities')->withErrors(['city' => 'City is required']);
        }

        $exists = City::query()
            ->where('employee_id', $employeeId)
            ->whereRaw('LOWER(TRIM(city)) = ?', [mb_strtolower($city)])
            ->exists();
        if ($exists) {
            return redirect('/cities')->withErrors(['city' => 'This city is already assigned to the selected employee']);
        }

        City::create([
            'employee_id' => $employeeId,
            'city' => $city,
        ]);

        return redirect('/cities')->with('status', 'city-created');
    }

    public function update(Request $request, City $city)
    {
        $validated = $request->validate([
            'employee_id' => ['required', 'integer', 'exists:employees,id'],
            'city' => ['required', 'string', 'max:255'],
        ]);

        $nextCity = trim((string) $validated['city']);
        $employeeId = (int) $validated['employee_id'];
        if ($nextCity === '') {
            return redirect('/cities')->withErrors(['city' => 'City is required']);
        }

        $exists = City::query()
            ->where('id', '!=', $city->id)
            ->where('employee_id', $employeeId)
            ->whereRaw('LOWER(TRIM(city)) = ?', [mb_strtolower($nextCity)])
            ->exists();
        if ($exists) {
            return redirect('/cities')->withErrors(['city' => 'This city is already assigned to the selected employee']);
        }

        $city->update([
            'employee_id' => $employeeId,
            'city' => $nextCity,
        ]);

        return redirect('/cities')->with('status', 'city-updated');
    }

    public function destroy(City $city)
    {
        $city->delete();

        return redirect('/cities')->with('status', 'city-deleted');
    }
}

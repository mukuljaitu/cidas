<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeLegacy;
use App\Models\Party;
use App\Models\Role;
use App\Models\Tour;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TourController extends Controller
{
    private function salesmanRoleIds()
    {
        return Role::query()
            ->whereRaw('LOWER(name) like ?', ['%salesman%'])
            ->pluck('id');
    }

    private function salesmanEmployeesByState(string $state)
    {
        $salesmanRoleIds = $this->salesmanRoleIds();

        $query = Employee::query()->orderBy('name');

        if ($state !== '') {
            $normalized = mb_strtolower(trim($state));
            $query->whereRaw('LOWER(TRIM(state)) = ?', [$normalized]);
        }

        if ($salesmanRoleIds->isNotEmpty()) {
            $query->where(function ($q) use ($salesmanRoleIds) {
                $q->whereIn('role_id', $salesmanRoleIds)
                    ->orWhereHas('roles', function ($rq) use ($salesmanRoleIds) {
                        $rq->whereIn('roles.id', $salesmanRoleIds);
                    });
            });
        } else {
            $query->whereHas('roles', function ($q) {
                $q->whereRaw('LOWER(name) like ?', ['%salesman%']);
            });
        }

        return $query->get(['id', 'name', 'state']);
    }

    private function salesmanCityData($employees)
    {
        $employeeIds = $employees->pluck('id')->all();

        $parties = Party::query()
            ->whereIn('employee_id', $employeeIds)
            ->get(['id', 'employee_id', 'name', 'city']);

        $byEmployee = $parties->groupBy('employee_id');

        $data = [];
        foreach ($employees as $emp) {
            $empParties = $byEmployee->get($emp->id, collect());

            $cityMap = [];
            $cityPartyCount = [];
            $cityParties = [];

            foreach ($empParties as $p) {
                $rawCity = trim((string) $p->city);
                if ($rawCity === '') {
                    continue;
                }

                $key = mb_strtolower($rawCity);
                if (! isset($cityMap[$key])) {
                    $cityMap[$key] = $rawCity;
                    $cityPartyCount[$rawCity] = 0;
                    $cityParties[$rawCity] = [];
                }

                $displayCity = $cityMap[$key];
                $cityPartyCount[$displayCity] = ($cityPartyCount[$displayCity] ?? 0) + 1;
                if ((string) $p->name !== '') {
                    $cityParties[$displayCity][] = (string) $p->name;
                }
            }

            $cities = collect(array_values($cityMap))
                ->filter()
                ->unique()
                ->sortBy(fn ($c) => mb_strtolower($c))
                ->values()
                ->all();

            foreach ($cityParties as $city => $list) {
                $cityParties[$city] = collect($list)->filter()->unique()->values()->all();
            }

            $data[(string) $emp->id] = [
                'employee' => ['id' => $emp->id, 'name' => $emp->name, 'state' => $emp->state],
                'cities' => $cities,
                'city_party_count' => $cityPartyCount,
                'city_parties' => $cityParties,
            ];
        }

        return $data;
    }

    public function index(Request $request)
    {
        $query = Tour::query();

        if ($request->filled('name') && $request->name !== 'All') {
            $query->where('employee_name', $request->name);
        }

        if ($request->filled('status') && $request->status !== 'All') {
            $query->where('status', $request->status);
        }

        if ($request->filled('state')) {
            $query->where('state', $request->state);
        } else {
            $query->where('state', 'Punjab');
        }

        $dateStart = $request->string('date_start')->toString();
        if ($dateStart === '') {
            $dateStart = $request->string('start_date')->toString();
        }
        if ($dateStart !== '') {
            $query->whereDate('tour_date', '>=', $dateStart);
        }

        $dateEnd = $request->string('date_end')->toString();
        if ($dateEnd === '') {
            $dateEnd = $request->string('end_date')->toString();
        }
        if ($dateEnd !== '') {
            $query->whereDate('tour_date', '<=', $dateEnd);
        }

        if ($request->filled('month') && $request->month !== 'All') {
            $query->whereMonth('tour_date', date('m', strtotime($request->month)));
        }

        $export = $request->string('export')->toString();
        if ($export !== '') {
            if ($export === 'csv') {
                $fileName = 'tours.csv';
                $rows = $query->orderBy('tour_date', 'desc')->get([
                    'employee_name',
                    'tour_date',
                    'cities',
                    'status',
                    'state',
                    'created_at',
                ]);

                return response()->streamDownload(function () use ($rows) {
                    $out = fopen('php://output', 'w');
                    fputcsv($out, ['Employee', 'Date', 'Cities', 'Status', 'State', 'Created At']);
                    foreach ($rows as $r) {
                        $status = (string) $r->status;
                        $statusLabel = $status === '1' ? 'Tour' : $status;
                        fputcsv($out, [
                            (string) $r->employee_name,
                            (string) $r->tour_date,
                            (string) $r->cities,
                            $statusLabel,
                            (string) $r->state,
                            (string) $r->created_at,
                        ]);
                    }
                    fclose($out);
                }, $fileName, ['Content-Type' => 'text/csv; charset=UTF-8']);
            }

            return redirect()->back()->withErrors(['export' => 'Unsupported export type']);
        }

        $tours = $query->orderBy('tour_date', 'desc')->paginate(15)->withQueryString();

        $state = $request->string('state')->toString() ?: 'Punjab';
        $names = Tour::query()
            ->where('state', $state)
            ->select('employee_name')
            ->distinct()
            ->orderBy('employee_name')
            ->pluck('employee_name')
            ->map(fn ($n) => trim((string) $n))
            ->filter()
            ->unique()
            ->values();
        $statuses = ['Field Visit', 'Holiday', 'Leave', 'No Station', 'Office Visit', '1'];
        $states = ['Punjab', 'Rajasthan'];
        $months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

        $employeeCityMap = [];
        $salesmen = $this->salesmanEmployeesByState($state);
        $cityData = $this->salesmanCityData($salesmen);
        foreach ($cityData as $row) {
            $employeeCityMap[$row['employee']['name']] = $row['cities'];
        }

        return view('viewtours', compact('tours', 'names', 'statuses', 'states', 'months', 'employeeCityMap'));
    }

    public function update(Request $request, Tour $tour)
    {
        $request->validate([
            'tour_date' => 'required|date',
            'status' => 'required|string',
            'cities' => 'nullable|array',
        ]);

        $status = $request->status;
        $cities = $request->cities ?? [];
        $citiesStr = ! empty($cities) ? implode(',', $cities) : '';

        if (in_array($status, ['Leave', 'Holiday', 'No Station', 'Office Visit'])) {
            $citiesStr = '';
        }

        $tour->update([
            'tour_date' => $request->tour_date,
            'status' => $status,
            'cities' => $citiesStr,
        ]);

        return redirect()->back()->with('status', 'tour-updated');
    }

    public function destroy(Tour $tour)
    {
        $tour->delete();

        return redirect()->back()->with('status', 'tour-deleted');
    }

    public function create(Request $request)
    {
        $state = trim((string) $request->get('state', 'Punjab'));
        $allowedStates = ['Punjab', 'Rajasthan'];

        if (! in_array($state, $allowedStates)) {
            abort(404, 'Invalid state');
        }

        $employees = $this->salesmanEmployeesByState($state);

        $tourCount = Tour::where('state', $state)->count();

        $salesmanCityData = $this->salesmanCityData($employees);

        return view('tours.create', compact('state', 'employees', 'tourCount', 'salesmanCityData'));
    }

    public function cities(Request $request)
    {
        $request->validate([
            'employee_id' => ['required', 'integer'],
        ]);

        $employeeId = (int) $request->input('employee_id');

        $parties = Party::query()
            ->where('employee_id', $employeeId)
            ->get(['id', 'name', 'city']);

        $cityMap = [];
        $cityPartyCount = [];
        $cityParties = [];

        foreach ($parties as $p) {
            $rawCity = trim((string) $p->city);
            if ($rawCity === '') {
                continue;
            }

            $key = mb_strtolower($rawCity);
            if (! isset($cityMap[$key])) {
                $cityMap[$key] = $rawCity;
                $cityPartyCount[$rawCity] = 0;
                $cityParties[$rawCity] = [];
            }

            $displayCity = $cityMap[$key];
            $cityPartyCount[$displayCity] = ($cityPartyCount[$displayCity] ?? 0) + 1;
            if ((string) $p->name !== '') {
                $cityParties[$displayCity][] = (string) $p->name;
            }
        }

        $cities = collect(array_values($cityMap))
            ->filter()
            ->unique()
            ->sortBy(fn ($c) => mb_strtolower($c))
            ->values()
            ->all();

        foreach ($cityParties as $city => $list) {
            $cityParties[$city] = collect($list)->filter()->unique()->values()->all();
        }

        return response()->json([
            'employee' => ['id' => $employeeId],
            'cities' => $cities,
            'city_party_count' => $cityPartyCount,
            'city_parties' => $cityParties,
        ]);
    }

    public function store(Request $request)
    {
        $salesmanRoleIds = $this->salesmanRoleIds();

        $request->validate([
            'employee_id' => [
                'required',
                'integer',
                Rule::exists('employees', 'id')->where(function ($query) use ($salesmanRoleIds) {
                    if ($salesmanRoleIds->isEmpty()) {
                        return;
                    }

                    $query->where(function ($q) use ($salesmanRoleIds) {
                        $q->whereIn('role_id', $salesmanRoleIds)
                            ->orWhereExists(function ($sub) use ($salesmanRoleIds) {
                                $sub->selectRaw('1')
                                    ->from('employee_role')
                                    ->whereColumn('employee_role.employee_id', 'employees.id')
                                    ->whereIn('employee_role.role_id', $salesmanRoleIds);
                            });
                    });
                }),
            ],
            'tour_date' => 'required|date',
            'status' => 'nullable|string',
            'cities' => 'nullable|array',
        ]);

        $employeeModel = Employee::query()->findOrFail((int) $request->input('employee_id'));
        $employee = $employeeModel->name;
        $date = $request->tour_date;
        $today = now()->toDateString();

        if ($date > $today) {
            $date = $today;
        }

        $isSupervisor = $request->has('is_supervisor');
        $status = $request->status;
        $cities = $request->cities ?? [];
        $allowedCities = Party::query()
            ->where('employee_id', $employeeModel->id)
            ->whereNotNull('city')
            ->pluck('city')
            ->map(fn($c) => trim((string) $c))
            ->filter()
            ->unique()
            ->values();

        $invalidCities = collect($cities)
            ->map(fn($c) => trim((string) $c))
            ->filter()
            ->diff($allowedCities);
        if ($invalidCities->isNotEmpty()) {
            return redirect()->back()->withErrors(['cities' => 'Invalid city selection']);
        }

        $citiesStr = ! empty($cities) ? implode(',', $cities) : '';

        if (in_array($status, ['Leave', 'Holiday', 'No Station', 'Office Visit'])) {
            $citiesStr = '';
            $finalStatus = $status;
        } else {
            $finalStatus = (! empty($status)) ? $status : '1';
        }

        if (empty($citiesStr) && $finalStatus === '1') {
            return redirect()->back()->withErrors(['cities' => 'Please select cities or choose a status']);
        }

        Tour::create([
            'employee_name' => $employee,
            'state' => $request->get('state', 'Punjab'),
            'tour_date' => $date,
            'is_supervisor' => $isSupervisor,
            'cities' => $citiesStr,
            'status' => $finalStatus,
        ]);

        return redirect()->route('tours.create', ['state' => $request->get('state', 'Punjab')])
            ->with('status', 'tour-created');
    }

    public function quickAddCity(Request $request)
    {
        $request->validate([
            'city' => 'required|string',
            'employee' => 'required|string',
        ]);

        $employee = EmployeeLegacy::where('Name', $request->employee)->first();
        if ($employee) {
            $cities = array_map('trim', explode(',', $employee->cities));
            if (! in_array($request->city, $cities)) {
                $cities[] = $request->city;
                $employee->cities = implode(',', array_filter($cities));
                $employee->save();
            }

            return response()->json(['status' => 'success']);
        }

        return response()->json(['status' => 'error'], 404);
    }
}

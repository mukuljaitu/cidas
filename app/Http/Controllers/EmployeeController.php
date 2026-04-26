<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $name = $request->string('name')->toString();
        $status = $request->string('status')->toString();
        $roleName = $request->string('role')->toString();

        $query = Employee::query()->with('roles')->orderBy('id', 'desc');

        if ($name !== '' && $name !== 'All') {
            $query->where('name', $name);
        }

        if ($status !== '' && $status !== 'All') {
            $query->where('status', $status);
        }

        if ($roleName !== '' && $roleName !== 'All') {
            $query->whereHas('roles', function ($q) use ($roleName) {
                $q->where('name', $roleName);
            });
        }

        $employees = $query->paginate(10)->withQueryString();

        $names = Employee::query()
            ->select('name')
            ->distinct()
            ->orderBy('name')
            ->pluck('name');

        $statuses = Employee::query()
            ->select('status')
            ->whereNotNull('status')
            ->distinct()
            ->orderBy('status')
            ->pluck('status')
            ->prepend('All');

        $roleOptions = Role::query()->whereNotNull('name')->orderBy('name')->get();
        $roleFilters = Role::query()->whereNotNull('name')->orderBy('name')->pluck('name')->prepend('All');

        return view('employees.index', compact('employees', 'names', 'statuses', 'roleOptions', 'roleFilters'));
    }

    public function analyze(Request $request)
    {
        $maxDays = 365;
        $defaultDays = 90;

        $requestedDays = (int) $request->query('days', $defaultDays);
        if ($requestedDays <= 0) {
            $requestedDays = $defaultDays;
        }
        if ($requestedDays > $maxDays) {
            $requestedDays = $maxDays;
        }

        $startParam = trim((string) $request->query('start', ''));
        $endParam = trim((string) $request->query('end', ''));

        $usingCustom = false;
        $end = Carbon::today();
        $start = Carbon::today()->subDays($requestedDays - 1);

        if ($startParam !== '' && $endParam !== '') {
            try {
                $customStart = Carbon::parse($startParam)->startOfDay();
                $customEnd = Carbon::parse($endParam)->startOfDay();

                if ($customEnd->gt(Carbon::today())) {
                    $customEnd = Carbon::today();
                }

                if ($customStart->lte($customEnd)) {
                    $usingCustom = true;
                    $end = $customEnd;
                    $days = $customStart->diffInDays($customEnd) + 1;
                    if ($days > $maxDays) {
                        $days = $maxDays;
                        $start = $end->copy()->subDays($days - 1);
                    } else {
                        $start = $customStart;
                    }
                }
            } catch (\Throwable $e) {
            }
        }

        if (! $usingCustom) {
            $days = $requestedDays;
        }

        $tourRows = DB::table('tours')
            ->whereBetween('tour_date', [$start->toDateString(), $end->toDateString()])
            ->select(
                DB::raw('TRIM(employee_name) as employee'),
                DB::raw("COALESCE(NULLIF(TRIM(status), ''), 'Unknown') as status"),
                DB::raw('COUNT(*) as qty')
            )
            ->groupBy(DB::raw('TRIM(employee_name)'), DB::raw("COALESCE(NULLIF(TRIM(status), ''), 'Unknown')"))
            ->get();

        $employeeTotals = [];
        $employeeStatusCounts = [];
        foreach ($tourRows as $r) {
            $employee = trim((string) $r->employee);
            $status = trim((string) $r->status);
            $qty = (int) ($r->qty ?? 0);

            if ($employee === '') {
                continue;
            }

            $employeeTotals[$employee] = ($employeeTotals[$employee] ?? 0) + $qty;
            $employeeStatusCounts[$employee][$status] = ($employeeStatusCounts[$employee][$status] ?? 0) + $qty;
        }

        arsort($employeeTotals);
        $topEmployees = collect(array_keys($employeeTotals))
            ->take(12)
            ->values()
            ->all();

        $normalizedCounts = [];
        foreach ($employeeStatusCounts as $employee => $rows) {
            foreach ($rows as $status => $qty) {
                $k = mb_strtolower(trim((string) $status));
                $k = preg_replace('/\s+/', ' ', $k);
                $normalizedCounts[$employee][$k] = ($normalizedCounts[$employee][$k] ?? 0) + (int) $qty;
            }
        }

        $g1 = [
            'Leave' => ['leave'],
            'Holiday' => ['holiday'],
            'No Station' => ['no station', 'nostation', 'no-station'],
        ];

        $g2 = [
            'Field Visit' => ['field visit', 'fieldvisit', 'field-visit'],
            'Office Visit' => ['office visit', 'officevisit', 'office-visit'],
            'Tour' => ['1'],
        ];

        $g1Datasets = [];
        foreach ($g1 as $label => $keys) {
            $points = [];
            foreach ($topEmployees as $employee) {
                $sum = 0;
                foreach ($keys as $k) {
                    $sum += (int) ($normalizedCounts[$employee][$k] ?? 0);
                }
                $points[] = $sum;
            }
            $g1Datasets[] = ['label' => $label, 'data' => $points];
        }

        $g2Datasets = [];
        foreach ($g2 as $label => $keys) {
            $points = [];
            foreach ($topEmployees as $employee) {
                $sum = 0;
                foreach ($keys as $k) {
                    $sum += (int) ($normalizedCounts[$employee][$k] ?? 0);
                }
                $points[] = $sum;
            }
            $g2Datasets[] = ['label' => $label, 'data' => $points];
        }

        return view('employees.analyze', [
            'range' => [
                'start' => $start->toDateString(),
                'end' => $end->toDateString(),
                'days' => $days,
            ],
            'rangeUi' => [
                'mode' => $usingCustom ? 'custom' : 'days',
                'days' => $days,
                'quickDays' => [7, 30, 90, 180, 365],
            ],
            'attendanceChart' => [
                'labels' => $topEmployees,
                'datasets' => $g1Datasets,
            ],
            'toursCountChart' => [
                'labels' => $topEmployees,
                'datasets' => $g2Datasets,
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'mobile' => ['nullable', 'string', 'max:25'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'pin_code' => ['nullable', 'string', 'max:20'],
            'date_of_joining' => ['nullable', 'date'],
            'role_id' => ['required', 'exists:roles,id'],
            'role_ids' => ['nullable', 'array'],
            'role_ids.*' => ['integer', 'exists:roles,id'],
        ]);

        do {
            $displayId = 'EMP-'.strtoupper(Str::random(6));
        } while (Employee::query()->where('display_id', $displayId)->exists());
        $createdBy = optional($request->user())->email ?? 'system@local';

        $employee = Employee::create([
            'display_id' => $displayId,
            'company_scope_id' => 1,
            'name' => $validated['name'],
            'mobile' => $validated['mobile'] ?? null,
            'city' => $validated['city'] ?? null,
            'state' => $validated['state'] ?? null,
            'pin_code' => $validated['pin_code'] ?? null,
            'date_of_joining' => $validated['date_of_joining'] ?? null,
            'role_id' => $validated['role_id'],
            'created_by_email' => $createdBy,
            'status' => $request->string('status')->toString() ?: 'Active',
        ]);

        $roleIds = collect($request->input('role_ids', []))
            ->filter(fn ($v) => $v !== null && $v !== '')
            ->map(fn ($v) => (int) $v)
            ->unique()
            ->values();
        if ($roleIds->isEmpty()) {
            $roleIds = collect([(int) $validated['role_id']]);
        }
        if (! $roleIds->contains((int) $validated['role_id'])) {
            $roleIds->push((int) $validated['role_id']);
        }
        $employee->roles()->sync($roleIds->all());

        return redirect('/employees')->with('status', 'member-created');
    }

    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'mobile' => ['nullable', 'string', 'max:25'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'pin_code' => ['nullable', 'string', 'max:20'],
            'date_of_joining' => ['nullable', 'date'],
            'role_id' => ['required', 'exists:roles,id'],
            'role_ids' => ['nullable', 'array'],
            'role_ids.*' => ['integer', 'exists:roles,id'],
        ]);

        $employee->update([
            'name' => $validated['name'],
            'mobile' => $validated['mobile'] ?? null,
            'city' => $validated['city'] ?? null,
            'state' => $validated['state'] ?? null,
            'pin_code' => $validated['pin_code'] ?? null,
            'date_of_joining' => $validated['date_of_joining'] ?? null,
            'role_id' => $validated['role_id'],
        ]);

        $roleIds = collect($request->input('role_ids', []))
            ->filter(fn ($v) => $v !== null && $v !== '')
            ->map(fn ($v) => (int) $v)
            ->unique()
            ->values();
        if ($roleIds->isEmpty()) {
            $roleIds = collect([(int) $validated['role_id']]);
        }
        if (! $roleIds->contains((int) $validated['role_id'])) {
            $roleIds->push((int) $validated['role_id']);
        }
        $employee->roles()->sync($roleIds->all());

        return redirect('/employees')->with('status', 'member-updated');
    }

    public function destroy(Employee $employee)
    {
        $employee->roles()->detach();
        $employee->delete();

        return redirect('/employees')->with('status', 'member-deleted');
    }
}

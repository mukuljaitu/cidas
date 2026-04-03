<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Role;
use Illuminate\Http\Request;
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

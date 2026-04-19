<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Employee;
use Illuminate\Http\Request;

class CityController extends Controller
{
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
            ->map(fn ($c) => trim((string) $c))
            ->filter()
            ->unique()
            ->values();

        $employeeOptions = Employee::query()
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('cities.index', compact('cities', 'names', 'employeeOptions'));
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

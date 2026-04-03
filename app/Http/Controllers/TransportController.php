<?php

namespace App\Http\Controllers;

use App\Models\Transport;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TransportController extends Controller
{
    public function index(Request $request)
    {
        $name = $request->string('name')->toString();

        $query = Transport::query()->orderBy('id', 'desc');

        if ($name !== '' && $name !== 'All') {
            $query->where('name', $name);
        }

        $transports = $query->paginate(10)->withQueryString();

        $names = Transport::query()
            ->select('name')
            ->distinct()
            ->orderBy('name')
            ->pluck('name');

        return view('transports.index', compact('transports', 'names'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'vehicle' => ['nullable', 'string', 'max:255'],
            'vehicle_number' => ['nullable', 'string', 'max:255'],
            'contact' => ['nullable', 'string', 'max:255'],
            'last_trip' => ['nullable', 'date'],
            'total_trips' => ['nullable', 'integer', 'min:0'],
            'date_of_joining' => ['nullable', 'date'],
        ]);

        do {
            $displayId = 'TRN-'.strtoupper(Str::random(6));
        } while (Transport::query()->where('display_id', $displayId)->exists());
        $createdBy = optional($request->user())->email ?? 'system@local';

        Transport::create([
            'display_id' => $displayId,
            'company_scope_id' => 1,
            'name' => $validated['name'],
            'vehicle' => $validated['vehicle'] ?? null,
            'vehicle_number' => $validated['vehicle_number'] ?? null,
            'contact' => $validated['contact'] ?? null,
            'last_trip' => $validated['last_trip'] ?? null,
            'total_trips' => $validated['total_trips'] ?? 0,
            'date_of_joining' => $validated['date_of_joining'] ?? null,
            'created_by_email' => $createdBy,
        ]);

        return redirect('/transports')->with('status', 'transport-created');
    }

    public function update(Request $request, Transport $transport)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'vehicle' => ['nullable', 'string', 'max:255'],
            'vehicle_number' => ['nullable', 'string', 'max:255'],
            'contact' => ['nullable', 'string', 'max:255'],
            'last_trip' => ['nullable', 'date'],
            'total_trips' => ['nullable', 'integer', 'min:0'],
            'date_of_joining' => ['nullable', 'date'],
        ]);

        $transport->update([
            'name' => $validated['name'],
            'vehicle' => $validated['vehicle'] ?? null,
            'vehicle_number' => $validated['vehicle_number'] ?? null,
            'contact' => $validated['contact'] ?? null,
            'last_trip' => $validated['last_trip'] ?? null,
            'total_trips' => $validated['total_trips'] ?? $transport->total_trips,
            'date_of_joining' => $validated['date_of_joining'] ?? null,
        ]);

        return redirect('/transports')->with('status', 'transport-updated');
    }

    public function destroy(Transport $transport)
    {
        $transport->delete();

        return redirect('/transports')->with('status', 'transport-deleted');
    }
}

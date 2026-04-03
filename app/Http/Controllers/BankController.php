<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\Employee;
use App\Models\Party;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BankController extends Controller
{
    public function index()
    {
        $banks = Bank::with(['employee', 'party'])->orderBy('transaction_date', 'desc')->paginate(10);
        $employees = Employee::orderBy('name')->get();
        $parties = Party::orderBy('name')->get();

        $allBanks = Bank::all();
        $totalAmount = $allBanks->sum('amount');
        $pendingCount = $allBanks->where('status', 'Pending')->count();
        $clearedCount = $allBanks->where('status', 'Cleared')->count();
        $returnCount = $allBanks->where('status', 'Return')->count();

        return view('banks.index', compact(
            'banks',
            'employees',
            'parties',
            'totalAmount',
            'pendingCount',
            'clearedCount',
            'returnCount'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'transaction_date' => 'required|date',
            'state' => 'required|string',
            'employee_id' => 'required|exists:employees,id',
            'party_id' => 'required|exists:parties,id',
            'station' => 'nullable|string',
            'issuing_bank' => 'nullable|string',
            'ifsc_code' => 'nullable|string',
            'reference_no' => 'nullable|string',
            'amount' => 'required|numeric',
            'receiving_bank' => 'nullable|string',
            'clear_date' => 'nullable|date',
            'comments' => 'nullable|string',
            'status' => 'required|in:Pending,Cleared,Return',
            'receipt_image.*' => 'nullable|image|max:2048',
        ]);

        $imagePaths = [];
        if ($request->hasFile('receipt_image')) {
            foreach ($request->file('receipt_image') as $image) {
                $path = $image->store('uploads', 'public');
                $imagePaths[] = 'storage/'.$path;
            }
        }

        $validated['image_paths'] = $imagePaths;

        Bank::create($validated);

        return redirect()->route('banks.index')->with('success', 'Transaction saved successfully.');
    }

    public function update(Request $request, Bank $bank)
    {
        $validated = $request->validate([
            'transaction_date' => 'required|date',
            'state' => 'required|string',
            'employee_id' => 'required|exists:employees,id',
            'party_id' => 'required|exists:parties,id',
            'station' => 'nullable|string',
            'issuing_bank' => 'nullable|string',
            'ifsc_code' => 'nullable|string',
            'reference_no' => 'nullable|string',
            'amount' => 'required|numeric',
            'receiving_bank' => 'nullable|string',
            'clear_date' => 'nullable|date',
            'comments' => 'nullable|string',
            'status' => 'required|in:Pending,Cleared,Return',
            'receipt_image.*' => 'nullable|image|max:2048',
            'existing_images' => 'nullable|string', // JSON array of existing image paths
        ]);

        $imagePaths = json_decode($request->existing_images, true) ?? [];

        if ($request->hasFile('receipt_image')) {
            foreach ($request->file('receipt_image') as $image) {
                $path = $image->store('uploads', 'public');
                $imagePaths[] = 'storage/'.$path;
            }
        }

        $validated['image_paths'] = $imagePaths;

        $bank->update($validated);

        return redirect()->route('banks.index')->with('success', 'Transaction updated successfully.');
    }

    public function destroy(Bank $bank)
    {
        // Optionally delete images from storage
        if ($bank->image_paths) {
            foreach ($bank->image_paths as $path) {
                $storagePath = str_replace('storage/', '', $path);
                Storage::disk('public')->delete($storagePath);
            }
        }

        $bank->delete();

        return redirect()->route('banks.index')->with('success', 'Transaction deleted successfully.');
    }
}

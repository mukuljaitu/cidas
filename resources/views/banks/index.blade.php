@extends('layouts.admin')

@section('title', 'Bank Transactions')

@section('content')
<div class="mb-8">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200">
            <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Total Transactions</div>
            <div class="text-3xl font-bold text-gray-900">{{ $banks->total() }}</div>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200">
            <div class="text-xs font-bold text-amber-500 uppercase tracking-wider mb-1">Pending Clearance</div>
            <div class="text-3xl font-bold text-amber-600">{{ $pendingCount }}</div>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200">
            <div class="text-xs font-bold text-blue-500 uppercase tracking-wider mb-1">Cleared</div>
            <div class="text-3xl font-bold text-blue-600">{{ $clearedCount }}</div>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200">
            <div class="text-xs font-bold text-red-500 uppercase tracking-wider mb-1">Returned</div>
            <div class="text-3xl font-bold text-red-600">{{ $returnCount }}</div>
        </div>
    </div>
</div>

<x-table-ui-layout title="Bank Transactions" :paginator="$banks">
    <x-slot name="toolbar">
        <div class="flex items-center gap-3">
            <button type="button" onclick="openAddModal()" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                <span>New Transaction</span>
            </button>
        </div>
    </x-slot>

    <x-slot name="thead">
        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Date</th>
        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Party / Salesman</th>
        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Reference / Bank</th>
        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Amount</th>
        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Status</th>
        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider text-right">Receipt</th>
    </x-slot>

    <x-slot name="tbody">
        @forelse($banks as $bank)
        <tr class="hover:bg-gray-50/50 transition-colors cursor-pointer" onclick="openEditModal({{ $bank->toJson() }})">
            <td class="px-6 py-4">
                <div class="text-sm font-bold text-gray-900">{{ $bank->transaction_date->format('d M, Y') }}</div>
            </td>
            <td class="px-6 py-4">
                <div class="text-sm font-bold text-gray-900">{{ $bank->party->name ?? 'N/A' }}</div>
                <div class="text-xs text-gray-400 font-medium">{{ $bank->employee->name ?? 'N/A' }}</div>
            </td>
            <td class="px-6 py-4">
                <div class="flex flex-wrap gap-1">
                    <span class="px-2 py-0.5 rounded bg-gray-100 text-[10px] font-bold text-gray-600 uppercase">{{ $bank->reference_no ?: 'No Ref' }}</span>
                    <span class="px-2 py-0.5 rounded bg-gray-100 text-[10px] font-bold text-gray-600 uppercase">{{ $bank->issuing_bank ?: 'No Bank' }}</span>
                </div>
            </td>
            <td class="px-6 py-4">
                <div class="text-sm font-bold text-gray-900">₹{{ number_format($bank->amount, 2) }}</div>
            </td>
            <td class="px-6 py-4">
                @if($bank->status === 'Return')
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-red-50 text-red-600 border border-red-100">
                    <span class="w-1.5 h-1.5 rounded-full bg-red-600"></span>
                    Returned
                </span>
                @elseif($bank->status === 'Cleared')
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-green-50 text-green-600 border border-green-100">
                    <span class="w-1.5 h-1.5 rounded-full bg-green-600"></span>
                    Cleared ({{ $bank->clear_date ? $bank->clear_date->format('d M') : 'N/A' }})
                </span>
                @else
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-600 border border-amber-100">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span>
                    Pending
                </span>
                @endif
            </td>
            <td class="px-6 py-4 text-right">
                @if($bank->image_paths && count($bank->image_paths) > 0)
                <div class="flex items-center justify-end gap-1">
                    <img src="{{ asset($bank->image_paths[0]) }}" class="w-8 h-8 rounded border border-gray-200 object-cover">
                    @if(count($bank->image_paths) > 1)
                    <span class="text-[10px] font-bold text-blue-600">+{{ count($bank->image_paths) - 1 }}</span>
                    @endif
                </div>
                @else
                <span class="text-xs text-gray-300">No Image</span>
                @endif
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="6" class="px-6 py-12 text-center text-gray-400">No transactions found</td>
        </tr>
        @endforelse
    </x-slot>

    <x-slot name="overlay">
        <!-- Add/Edit Transaction Panel -->
        <div id="bankPanel" class="fixed inset-y-0 right-0 w-[550px] bg-white shadow-2xl border-l border-gray-200 transform translate-x-full transition-transform duration-300 z-50 overflow-y-auto">
            <div class="p-8">
                <div class="flex items-center justify-between mb-8">
                    <h2 class="text-2xl font-bold text-gray-900" id="panelTitle">New Transaction</h2>
                    <button type="button" onclick="closeModal()" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-50 rounded-lg transition-all">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form id="bankForm" method="POST" action="{{ route('banks.store') }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    <input type="hidden" name="_method" id="formMethod" value="POST">
                    <input type="hidden" name="existing_images" id="field_existing_images" value="[]">
                    <input type="hidden" name="status" id="field_status" value="Pending">

                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1.5">
                                <label class="text-sm font-semibold text-gray-700">Transaction Date</label>
                                <input name="transaction_date" id="field_transaction_date" type="date" required class="block w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" />
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-sm font-semibold text-gray-700">Amount</label>
                                <input name="amount" id="field_amount" type="number" step="0.01" required class="block w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" placeholder="0.00" />
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1.5">
                                <label class="text-sm font-semibold text-gray-700">Salesman</label>
                                <select name="employee_id" id="field_employee_id" required class="block w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all appearance-none">
                                    <option value="">Select Salesman</option>
                                    @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}" data-state="{{ $employee->state }}">{{ $employee->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-sm font-semibold text-gray-700">Party Name</label>
                                <select name="party_id" id="field_party_id" required class="block w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all appearance-none">
                                    <option value="">Select Party</option>
                                    @foreach($parties as $party)
                                    <option value="{{ $party->id }}" data-city="{{ $party->city }}" data-state="{{ $party->state }}">{{ $party->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1.5">
                                <label class="text-sm font-semibold text-gray-700">Station (City)</label>
                                <input name="station" id="field_station" type="text" class="block w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" placeholder="City" />
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-sm font-semibold text-gray-700">State</label>
                                <input name="state" id="field_state" type="text" required class="block w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" placeholder="State" />
                            </div>
                        </div>

                        <div class="border-t border-gray-100 pt-6">
                            <h3 class="text-xs font-bold text-blue-600 uppercase tracking-wider mb-4">Bank Details</h3>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-1.5">
                                    <label class="text-sm font-semibold text-gray-700">Issuing Bank</label>
                                    <input name="issuing_bank" id="field_issuing_bank" type="text" class="block w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" placeholder="Bank Name" />
                                </div>
                                <div class="space-y-1.5">
                                    <label class="text-sm font-semibold text-gray-700">IFSC Code</label>
                                    <input name="ifsc_code" id="field_ifsc_code" type="text" class="block w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" placeholder="IFSC" />
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4 mt-4">
                                <div class="space-y-1.5">
                                    <label class="text-sm font-semibold text-gray-700">Reference No (Check #)</label>
                                    <input name="reference_no" id="field_reference_no" type="text" class="block w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" placeholder="Ref #" />
                                </div>
                                <div class="space-y-1.5">
                                    <label class="text-sm font-semibold text-gray-700">Receiving Bank</label>
                                    <select name="receiving_bank" id="field_receiving_bank" class="block w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all appearance-none">
                                        <option value="">Select Bank</option>
                                        <option value="776">776</option>
                                        <option value="418">418</option>
                                        <option value="480">480</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="border-t border-gray-100 pt-6">
                            <h3 class="text-xs font-bold text-blue-600 uppercase tracking-wider mb-4">Status & Clearance</h3>
                            <div class="flex gap-2 mb-4">
                                <button type="button" onclick="setStatus('Pending')" id="statusBtnPending" class="flex-1 py-2 rounded-lg text-xs font-bold border transition-all status-selector" data-status="Pending">Pending</button>
                                <button type="button" onclick="setStatus('Cleared')" id="statusBtnCleared" class="flex-1 py-2 rounded-lg text-xs font-bold border transition-all status-selector" data-status="Cleared">Cleared</button>
                                <button type="button" onclick="setStatus('Return')" id="statusBtnReturn" class="flex-1 py-2 rounded-lg text-xs font-bold border transition-all status-selector" data-status="Return">Return</button>
                            </div>
                            <div class="space-y-1.5" id="clearanceDateField" style="display: none;">
                                <label class="text-sm font-semibold text-gray-700">Clearance Date</label>
                                <input name="clear_date" id="field_clear_date" type="date" class="block w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" />
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-gray-700">Comments</label>
                            <textarea name="comments" id="field_comments" rows="2" class="block w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" placeholder="Optional comments..."></textarea>
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-gray-700">Receipt Images</label>
                            <div id="imagePreviewContainer" class="grid grid-cols-3 gap-2 mb-2"></div>
                            <input type="file" name="receipt_image[]" multiple accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
                        </div>
                    </div>

                    <div class="pt-8 flex items-center gap-3">
                        <button type="submit" class="flex-1 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl transition-all shadow-lg shadow-blue-200">
                            Save Transaction
                        </button>
                        <button type="button" onclick="closeModal()" class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold rounded-xl transition-all">
                            Cancel
                        </button>
                    </div>

                    <button type="button" id="deleteBtn" onclick="deleteTransaction()" class="w-full mt-4 px-6 py-3 bg-red-50 text-red-600 font-bold rounded-xl hover:bg-red-100 transition-all flex items-center justify-center gap-2 hidden">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Delete Transaction
                    </button>
                </form>
            </div>
        </div>
    </x-slot>
</x-table-ui-layout>

<form id="deleteForm" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>

<style>
    .status-selector.active[data-status="Pending"] {
        background-color: #fffbeb;
        border-color: #fbbf24;
        color: #d97706;
    }

    .status-selector.active[data-status="Cleared"] {
        background-color: #ecfdf5;
        border-color: #86efac;
        color: #059669;
    }

    .status-selector.active[data-status="Return"] {
        background-color: #fef2f2;
        border-color: #fecaca;
        color: #dc2626;
    }

    .status-selector:not(.active) {
        background-color: #f9fafb;
        border-color: #f3f4f6;
        color: #9ca3af;
    }
</style>

<script>
    function openAddModal() {
        document.getElementById('panelTitle').innerText = 'New Transaction';
        document.getElementById('bankForm').action = "{{ route('banks.store') }}";
        document.getElementById('formMethod').value = 'POST';
        document.getElementById('bankForm').reset();
        document.getElementById('field_existing_images').value = '[]';
        document.getElementById('imagePreviewContainer').innerHTML = '';
        document.getElementById('deleteBtn').classList.add('hidden');
        setStatus('Pending');
        openPanel();
    }

    function openEditModal(bank) {
        document.getElementById('panelTitle').innerText = 'Edit Transaction';
        document.getElementById('bankForm').action = `/banks/${bank.id}`;
        document.getElementById('formMethod').value = 'PUT';

        document.getElementById('field_transaction_date').value = bank.transaction_date.split('T')[0];
        document.getElementById('field_amount').value = bank.amount;
        document.getElementById('field_employee_id').value = bank.employee_id;
        document.getElementById('field_party_id').value = bank.party_id;
        document.getElementById('field_station').value = bank.station;
        document.getElementById('field_state').value = bank.state;
        document.getElementById('field_issuing_bank').value = bank.issuing_bank;
        document.getElementById('field_ifsc_code').value = bank.ifsc_code;
        document.getElementById('field_reference_no').value = bank.reference_no;
        document.getElementById('field_receiving_bank').value = bank.receiving_bank;
        document.getElementById('field_comments').value = bank.comments;

        if (bank.clear_date) {
            document.getElementById('field_clear_date').value = bank.clear_date.split('T')[0];
        } else {
            document.getElementById('field_clear_date').value = '';
        }

        setStatus(bank.status);

        const images = bank.image_paths || [];
        document.getElementById('field_existing_images').value = JSON.stringify(images);
        renderImagePreviews(images);

        document.getElementById('deleteBtn').classList.remove('hidden');
        document.getElementById('deleteForm').action = `/banks/${bank.id}`;

        openPanel();
    }

    function openPanel() {
        document.getElementById('bankPanel').classList.remove('translate-x-full');
    }

    function closeModal() {
        document.getElementById('bankPanel').classList.add('translate-x-full');
    }

    function setStatus(status) {
        document.getElementById('field_status').value = status;
        document.querySelectorAll('.status-selector').forEach(btn => {
            btn.classList.remove('active');
            if (btn.dataset.status === status) btn.classList.add('active');
        });

        const clearanceField = document.getElementById('clearanceDateField');
        if (status === 'Cleared') {
            clearanceField.style.display = 'block';
        } else {
            clearanceField.style.display = 'none';
        }
    }

    function renderImagePreviews(images) {
        const container = document.getElementById('imagePreviewContainer');
        container.innerHTML = '';
        images.forEach((path, index) => {
            const div = document.createElement('div');
            div.className = 'relative group aspect-square';
            div.innerHTML = `
                <img src="/${path}" class="w-full h-full object-cover rounded-lg border border-gray-200">
                <button type="button" onclick="removeImage(${index})" class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="3"></path></svg>
                </button>
            `;
            container.appendChild(div);
        });
    }

    function removeImage(index) {
        const images = JSON.parse(document.getElementById('field_existing_images').value);
        images.splice(index, 1);
        document.getElementById('field_existing_images').value = JSON.stringify(images);
        renderImagePreviews(images);
    }

    function deleteTransaction() {
        if (confirm('Are you sure you want to delete this transaction?')) {
            document.getElementById('deleteForm').submit();
        }
    }

    // Auto-fill logic
    document.getElementById('field_party_id').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption.value) {
            document.getElementById('field_station').value = selectedOption.dataset.city || '';
            document.getElementById('field_state').value = selectedOption.dataset.state || '';
        }
    });

    document.getElementById('field_employee_id').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption.value && !document.getElementById('field_state').value) {
            document.getElementById('field_state').value = selectedOption.dataset.state || '';
        }
    });
</script>
@endsection
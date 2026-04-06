@extends('layouts.admin')

@section('title', 'All Members')

@section('content')
<x-table-ui-layout title="All Members" :paginator="$employees">
    <x-slot name="headerActions">
        <button type="button" data-add-member-trigger class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm shadow-blue-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            <span>New</span>
        </button>
    </x-slot>

    <x-slot name="filters">
        <x-table-filters :names="$names" :statuses="$statuses" :roles="$roleFilters" :action="url('/employees')" :showDate="false" :showStatus="true" :showRole="true" />
    </x-slot>

    <x-slot name="thead">
        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Identify</th>
        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Metadata</th>
        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Classification</th>
        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Pulse</th>
        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider text-right">Actions</th>
    </x-slot>

    <x-slot name="tbody">
        @forelse($employees as $employee)
            @php
            $mobile = $employee->mobile;
            $status = $employee->status ?? 'Active';
            $statusLower = strtolower((string) $status);
            $dotColor = 'bg-green-500';
            if (str_contains($statusLower, 'away') || str_contains($statusLower, 'leave')) $dotColor = 'bg-orange-500';
            elseif (str_contains($statusLower, 'holiday')) $dotColor = 'bg-blue-500';
            
            $initials = strtoupper(substr($employee->name, 0, 1));
            $roleId = $employee->role_id ?? ($employee->roles->first()->id ?? null);
            $roleIdsCsv = $employee->roles->pluck('id')->implode(',');
            @endphp
            <tr id="row-{{ $employee->id }}" class="hover:bg-gray-50/50 transition-colors">
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center text-gray-500 font-bold border border-gray-200">
                            {{ $initials }}
                        </div>
                        <div>
                            <div class="text-sm font-bold text-gray-900">{{ $employee->name }}</div>
                            <div class="text-xs text-gray-400 font-medium">ID - {{ $employee->display_id ?? $employee->id }}</div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4">
                    <div class="text-sm font-bold text-gray-900">{{ $mobile ?: '—' }}</div>
                    <div class="text-xs text-gray-400 font-medium">{{ $employee->city ?: 'Location unknown' }}</div>
                </td>
                <td class="px-6 py-4">
                    <div class="flex flex-wrap gap-1">
                        @foreach($employee->roles as $role)
                            <span class="px-2.5 py-1 rounded-md bg-blue-50 text-[10px] font-bold text-blue-600 uppercase tracking-wider border border-blue-100/50">
                                {{ $role->name }}
                            </span>
                        @endforeach
                        @if($employee->roles->isEmpty())
                            <span class="text-sm text-gray-400">—</span>
                        @endif
                    </div>
                </td>
                <td class="px-6 py-4">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full {{ $dotColor }}"></span>
                        <span class="text-sm font-semibold text-gray-700">{{ $status }}</span>
                    </div>
                </td>
                <td class="px-6 py-4 text-right">
                    <button
                        type="button"
                        class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-all"
                        data-edit-member-trigger
                        data-id="{{ $employee->id }}"
                        data-name="{{ $employee->name }}"
                        data-mobile="{{ $employee->mobile }}"
                        data-city="{{ $employee->city }}"
                        data-state="{{ $employee->state }}"
                        data-pin_code="{{ $employee->pin_code }}"
                        data-date_of_joining="{{ optional($employee->date_of_joining)->format('Y-m-d') }}"
                        data-role-id="{{ $roleId }}"
                        data-role_ids="{{ $roleIdsCsv }}"
                    >
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                        </svg>
                        <span class="text-sm font-medium">No members found</span>
                    </div>
                </td>
            </tr>
        @endforelse
    </x-slot>

    <x-slot name="overlay">
        <!-- Add/Edit Member Panel -->
        <div id="addMemberPanel" class="fixed inset-y-0 right-0 w-[480px] bg-white shadow-2xl border-l border-gray-200 transform translate-x-full transition-transform duration-300 z-50 overflow-y-auto">
            <div class="p-8">
                <div class="flex items-center justify-between mb-8">
                    <h2 class="text-2xl font-bold text-gray-900" id="panelTitle">New</h2>
                    <button type="button" data-add-member-cancel class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-50 rounded-lg transition-all">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <form id="memberForm" method="POST" action="{{ url('/employees') }}" class="space-y-6">
                    @csrf
                    <input type="hidden" name="_method" value="">
                    
                    <div class="space-y-4">
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-gray-700">Full Name</label>
                            <input name="name" type="text" required class="block w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" placeholder="e.g. John Doe" />
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-gray-700">Mobile Number</label>
                            <input name="mobile" type="text" class="block w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" placeholder="e.g. +91 98765 43210" />
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-gray-700">Role</label>
                            <select name="role_id" required class="block w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all appearance-none">
                                @foreach(($roleOptions ?? []) as $role)
                                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex items-center justify-between gap-3">
                            <button type="button" data-multi-roles-toggle class="text-sm font-semibold text-blue-600 hover:text-blue-700">
                                Add More Roles
                            </button>
                            <div id="multiRolesSummary" class="text-xs font-semibold text-gray-400"></div>
                        </div>

                        <div id="multiRolesSection" class="hidden rounded-xl border border-gray-200 bg-white p-4">
                            <div class="text-xs font-bold text-gray-400 uppercase tracking-wider">Additional Roles</div>
                            <div class="mt-3 grid grid-cols-2 gap-3">
                                @foreach(($roleOptions ?? []) as $role)
                                    <label class="flex items-center gap-2 rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm font-semibold text-gray-700">
                                        <input type="checkbox" class="multi-role-checkbox w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500" name="role_ids[]" value="{{ $role->id }}">
                                        <span>{{ $role->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1.5">
                                <label class="text-sm font-semibold text-gray-700">City</label>
                                <input name="city" type="text" class="block w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" placeholder="City" />
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-sm font-semibold text-gray-700">State</label>
                                <input name="state" type="text" class="block w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" placeholder="State" />
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1.5">
                                <label class="text-sm font-semibold text-gray-700">Pin Code</label>
                                <input name="pin_code" type="text" class="block w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" placeholder="Pincode" />
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-sm font-semibold text-gray-700">Joining Date</label>
                                <input name="date_of_joining" type="date" class="block w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" />
                            </div>
                        </div>
                    </div>

                    <div class="pt-8 flex items-center gap-3">
                        <button type="submit" class="flex-1 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl transition-all shadow-lg shadow-blue-200">
                            Save Member
                        </button>
                        <button type="button" data-add-member-cancel class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold rounded-xl transition-all">
                            Cancel
                        </button>
                    </div>
                    
                    <button type="button" data-delete-member class="w-full mt-4 px-6 py-3 bg-red-50 text-red-600 font-bold rounded-xl hover:bg-red-100 transition-all flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        Delete Member
                    </button>
                </form>
            </div>
        </div>
    </x-slot>
</x-table-ui-layout>

<x-modals />
@push('scripts')
<script>
    (() => {
        const panel = document.getElementById('addMemberPanel');
        const form = document.getElementById('memberForm');
        const toggleBtn = document.querySelector('[data-multi-roles-toggle]');
        const section = document.getElementById('multiRolesSection');
        const summary = document.getElementById('multiRolesSummary');

        function getPrimaryRoleId() {
            const el = form ? form.querySelector('select[name="role_id"]') : null;
            if (!(el instanceof HTMLSelectElement)) return '';
            return el.value || '';
        }

        function setSummary() {
            if (!summary) return;
            if (!form) return;
            const checked = Array.from(form.querySelectorAll('input.multi-role-checkbox[name="role_ids[]"]:checked'));
            summary.textContent = checked.length > 0 ? `${checked.length} selected` : '';
        }

        function setRolesFromCsv(csv) {
            if (!form) return;
            const ids = (csv || '')
                .split(',')
                .map(s => s.trim())
                .filter(Boolean);

            const boxes = Array.from(form.querySelectorAll('input.multi-role-checkbox[name="role_ids[]"]'));
            boxes.forEach(b => b.checked = false);
            boxes.forEach(b => {
                if (ids.includes(String(b.value))) b.checked = true;
            });

            const primary = getPrimaryRoleId();
            if (primary) {
                const primaryBox = form.querySelector(`input.multi-role-checkbox[name="role_ids[]"][value="${primary}"]`);
                if (primaryBox instanceof HTMLInputElement) primaryBox.checked = true;
            }

            setSummary();
        }

        function resetRoles() {
            if (!form) return;
            const boxes = Array.from(form.querySelectorAll('input.multi-role-checkbox[name="role_ids[]"]'));
            boxes.forEach(b => b.checked = false);
            const primary = getPrimaryRoleId();
            if (primary) {
                const primaryBox = form.querySelector(`input.multi-role-checkbox[name="role_ids[]"][value="${primary}"]`);
                if (primaryBox instanceof HTMLInputElement) primaryBox.checked = true;
            }
            setSummary();
        }

        function showSectionIfNeeded() {
            if (!section) return;
            const checked = form ? Array.from(form.querySelectorAll('input.multi-role-checkbox[name="role_ids[]"]:checked')) : [];
            if (checked.length > 1) {
                section.classList.remove('hidden');
            }
        }

        if (toggleBtn && section) {
            toggleBtn.addEventListener('click', () => {
                section.classList.toggle('hidden');
                if (!section.classList.contains('hidden')) resetRoles();
            });
        }

        document.addEventListener('click', (e) => {
            const addBtn = e.target.closest('[data-add-member-trigger]');
            if (addBtn) {
                if (section) section.classList.add('hidden');
                setTimeout(resetRoles, 0);
                return;
            }

            const editBtn = e.target.closest('[data-edit-member-trigger]');
            if (editBtn) {
                const csv = editBtn.getAttribute('data-role_ids') || editBtn.getAttribute('data-role-ids') || '';
                setTimeout(() => {
                    setRolesFromCsv(csv);
                    showSectionIfNeeded();
                }, 0);
                return;
            }

            const cancelBtn = e.target.closest('[data-add-member-cancel]');
            if (cancelBtn) {
                if (section) section.classList.add('hidden');
            }
        });

        if (form) {
            form.addEventListener('change', (e) => {
                const target = e.target;
                if (target instanceof HTMLSelectElement && target.name === 'role_id') {
                    resetRoles();
                    return;
                }

                if (target instanceof HTMLInputElement && target.classList.contains('multi-role-checkbox')) {
                    const primary = getPrimaryRoleId();
                    if (primary && target.value === primary && !target.checked) {
                        target.checked = true;
                    }
                    setSummary();
                }
            });
        }

        setSummary();
    })();
</script>
@endpush
@endsection

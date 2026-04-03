@extends('layouts.admin')

@section('title', 'Variants')

@section('content')
<x-table-ui-layout title="Variants" :paginator="$variants">
    <x-slot name="toolbar">
        <div class="flex items-center gap-3 w-full">
            <form method="GET" action="{{ url('/variants') }}" class="flex items-center gap-3 flex-1">
                <select name="product_id" onchange="this.form.submit()" class="px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm font-semibold text-gray-700">
                    <option value="All" {{ request('product_id', 'All') === 'All' ? 'selected' : '' }}>All Products</option>
                    @foreach(($productOptions ?? []) as $p)
                        <option value="{{ $p->id }}" {{ (string) request('product_id') === (string) $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                    @endforeach
                </select>
                <select name="name" onchange="this.form.submit()" class="px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm font-semibold text-gray-700">
                    <option value="All" {{ request('name', 'All') === 'All' ? 'selected' : '' }}>All Names</option>
                    @foreach(($names ?? []) as $n)
                        <option value="{{ $n }}" {{ request('name') === $n ? 'selected' : '' }}>{{ $n }}</option>
                    @endforeach
                </select>
                <a href="{{ url('/variants') }}" class="text-sm font-medium text-blue-600 hover:text-blue-700 ml-auto">Clear</a>
            </form>

            <button type="button" data-add-member-trigger class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm shadow-blue-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                <span>New Variant</span>
            </button>
        </div>
    </x-slot>

    <x-slot name="thead">
        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Variant</th>
        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Product</th>
        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">SKU</th>
        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Unit / Size</th>
        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider text-right">Actions</th>
    </x-slot>

    <x-slot name="tbody">
        @forelse($variants as $variant)
            @php
                $initials = strtoupper(substr($variant->name, 0, 1));
            @endphp
            <tr id="row-{{ $variant->id }}" class="hover:bg-gray-50/50 transition-colors">
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center text-gray-500 font-bold border border-gray-200">
                            {{ $initials }}
                        </div>
                        <div>
                            <div class="text-sm font-bold text-gray-900">{{ $variant->name }}</div>
                            <div class="text-xs text-gray-400 font-medium">{{ $variant->display_id }}</div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4">
                    <div class="text-sm font-bold text-gray-900">{{ $variant->product?->name ?: '—' }}</div>
                </td>
                <td class="px-6 py-4 text-sm text-gray-700 font-medium">
                    {{ $variant->sku ?: '—' }}
                </td>
                <td class="px-6 py-4 text-sm text-gray-700 font-medium">
                    {{ ($variant->unit ?: '—') }} / {{ ($variant->size ?: '—') }}
                </td>
                <td class="px-6 py-4 text-right">
                    <button
                        type="button"
                        class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-all"
                        data-edit-member-trigger
                        data-id="{{ $variant->id }}"
                        data-product_id="{{ $variant->product_id }}"
                        data-name="{{ $variant->name }}"
                        data-sku="{{ $variant->sku }}"
                        data-unit="{{ $variant->unit }}"
                        data-size="{{ $variant->size }}">
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
                        <span class="text-sm font-medium">No variants found</span>
                    </div>
                </td>
            </tr>
        @endforelse
    </x-slot>

    <x-slot name="overlay">
        <div id="addMemberPanel"
            data-entity-singular="Variant"
            data-entity-plural="Variants"
            data-resource="{{ url('/variants') }}"
            data-form-fields="product_id,name,sku,unit,size"
            class="fixed inset-y-0 right-0 w-[520px] bg-white shadow-2xl border-l border-gray-200 transform translate-x-full transition-transform duration-300 z-50 overflow-y-auto">
            <div class="p-8">
                <div class="flex items-center justify-between mb-8">
                    <h2 class="text-2xl font-bold text-gray-900" id="panelTitle">New Variant</h2>
                    <button type="button" data-add-member-cancel class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-50 rounded-lg transition-all">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <form id="memberForm" method="POST" action="{{ url('/variants') }}" class="space-y-6">
                    @csrf
                    <input type="hidden" name="_method" value="">

                    <div class="space-y-4">
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-gray-700">Product</label>
                            <select name="product_id" required class="block w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                                <option value="">Select Product</option>
                                @foreach(($productOptions ?? []) as $p)
                                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1.5">
                                <label class="text-sm font-semibold text-gray-700">Size</label>
                                <input id="variantSize" name="size" type="text" required class="block w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" placeholder="250, 500..." />
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-sm font-semibold text-gray-700">Unit</label>
                                <select id="variantUnit" name="unit" required class="block w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                                    <option value="">Select Unit</option>
                                    <option value="mg">mg</option>
                                    <option value="g">g</option>
                                    <option value="kg">kg</option>
                                    <option value="ml">ml</option>
                                    <option value="L">L</option>
                                </select>
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-gray-700">SKU</label>
                            <input id="variantSku" name="sku" type="text" class="block w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" placeholder="Bottles, Pouches..." />
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-gray-700">Variant Name</label>
                            <input id="variantName" name="name" type="text" readonly required class="block w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" placeholder="250ml Bottles" />
                        </div>
                    </div>

                    <div class="pt-8 flex items-center gap-3">
                        <button type="submit" class="flex-1 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl transition-all shadow-lg shadow-blue-200">
                            Save Variant
                        </button>
                        <button type="button" data-add-member-cancel class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold rounded-xl transition-all">
                            Cancel
                        </button>
                    </div>

                    <button type="button" data-delete-member class="w-full mt-4 px-6 py-3 bg-red-50 text-red-600 font-bold rounded-xl hover:bg-red-100 transition-all flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        Delete Variant
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
        const sizeEl = document.getElementById('variantSize');
        const unitEl = document.getElementById('variantUnit');
        const skuEl = document.getElementById('variantSku');
        const nameEl = document.getElementById('variantName');

        function compute() {
            if (!(sizeEl instanceof HTMLInputElement) || !(unitEl instanceof HTMLSelectElement) || !(skuEl instanceof HTMLInputElement) || !(nameEl instanceof HTMLInputElement)) return;
            const size = (sizeEl.value || '').trim();
            const unit = (unitEl.value || '').trim();
            const sku = (skuEl.value || '').trim();
            const base = `${size}${unit}`.trim();
            nameEl.value = [base, sku].filter(Boolean).join(' ').trim();
        }

        [sizeEl, unitEl, skuEl].forEach((el) => {
            if (!el) return;
            el.addEventListener('input', compute);
            el.addEventListener('change', compute);
        });

        document.addEventListener('click', (e) => {
            if (e.target.closest('[data-add-member-trigger]') || e.target.closest('[data-edit-member-trigger]')) {
                setTimeout(compute, 0);
            }
        });
    })();
</script>
@endpush
@endsection

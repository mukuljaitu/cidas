@extends('layouts.admin')

@section('title', 'Products')

@section('content')
<x-table-ui-layout title="Products" :paginator="$products">
    <x-slot name="toolbar">
        <div class="flex items-center gap-3">
            <x-table-filters :names="$names" :action="url('/products')" :showDate="false" :showStatus="false" :showRole="false" />
            <button type="button" data-add-member-trigger class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm shadow-blue-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                <span>New Product</span>
            </button>
            <button type="button" data-add-variant-trigger class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 text-sm font-semibold rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>New Variant</span>
            </button>
        </div>
    </x-slot>

    <x-slot name="thead">
        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Product</th>
        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Description</th>
        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Variants</th>
        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider text-right">Actions</th>
    </x-slot>

    <x-slot name="tbody">
        @forelse($products as $product)
            @php
                $initials = strtoupper(substr($product->name, 0, 1));
                $variantCount = (int) ($product->variants_count ?? 0);
            @endphp
            <tr id="row-{{ $product->id }}" class="hover:bg-gray-50/50 transition-colors">
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center text-gray-500 font-bold border border-gray-200">
                            {{ $initials }}
                        </div>
                        <div>
                            <div class="text-sm font-bold text-gray-900">{{ $product->name }}</div>
                            <div class="flex items-center gap-2">
                                <div class="text-xs text-gray-400 font-medium">{{ $product->display_id }}</div>
                                @if($product->type)
                                    <div class="px-2 py-0.5 rounded-full text-[10px] font-bold border border-gray-200 bg-gray-50 text-gray-700 uppercase tracking-wider">
                                        {{ $product->type === 'Fer' ? 'Fertilizer' : 'Pesticide' }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4">
                    <div class="text-sm text-gray-700 font-medium">
                        {{ $product->description ?: '—' }}
                    </div>
                </td>
                <td class="px-6 py-4">
                    <button
                        type="button"
                        data-view-variants
                        data-product_id="{{ $product->id }}"
                        data-product_name="{{ $product->name }}"
                        class="px-2.5 py-1 rounded-md bg-blue-50 text-[10px] font-bold text-blue-600 uppercase tracking-wider border border-blue-100/50 hover:bg-blue-100/50 transition-colors">
                        {{ $variantCount }} variants
                    </button>
                </td>
                <td class="px-6 py-4 text-right">
                    <div class="inline-flex items-center gap-2">
                        <button
                            type="button"
                            class="px-3 py-2 text-xs font-bold rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-50 transition-colors"
                            data-add-variant-trigger
                            data-product_id="{{ $product->id }}">
                            New Variant
                        </button>
                        <button
                            type="button"
                            class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-all"
                            data-edit-member-trigger
                            data-id="{{ $product->id }}"
                            data-name="{{ $product->name }}"
                            data-type="{{ $product->type }}"
                            data-description="{{ $product->description }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                            </svg>
                        </button>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="px-6 py-12 text-center">
                    <div class="flex flex-col items-center gap-2 text-gray-400">
                        <svg class="w-12 h-12 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                        </svg>
                        <span class="text-sm font-medium">No products found</span>
                    </div>
                </td>
            </tr>
        @endforelse
    </x-slot>

    <x-slot name="overlay">
        <div id="addMemberPanel"
            data-entity-singular="Product"
            data-entity-plural="Products"
            data-resource="{{ url('/products') }}"
            data-form-fields="name,type,description"
            class="fixed inset-y-0 right-0 w-[520px] bg-white shadow-2xl border-l border-gray-200 transform translate-x-full transition-transform duration-300 z-50 overflow-y-auto">
            <div class="p-8">
                <div class="flex items-center justify-between mb-8">
                    <h2 class="text-2xl font-bold text-gray-900" id="panelTitle">New Product</h2>
                    <button type="button" data-add-member-cancel class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-50 rounded-lg transition-all">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <form id="memberForm" method="POST" action="{{ url('/products') }}" class="space-y-6">
                    @csrf
                    <input type="hidden" name="_method" value="">

                    <div class="space-y-4">
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-gray-700">Product Name</label>
                            <input name="name" type="text" required class="block w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" />
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-gray-700">Type</label>
                            <select name="type" required class="block w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                                <option value="">Select Type</option>
                                <option value="Fer">Fertilizer</option>
                                <option value="Pes">Pesticide</option>
                            </select>
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-gray-700">Description</label>
                            <textarea name="description" rows="4" class="block w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all"></textarea>
                        </div>
                    </div>

                    <div class="pt-8 flex items-center gap-3">
                        <button type="submit" class="flex-1 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl transition-all shadow-lg shadow-blue-200">
                            Save Product
                        </button>
                        <button type="button" data-add-member-cancel class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold rounded-xl transition-all">
                            Cancel
                        </button>
                    </div>

                    <button type="button" data-delete-member class="w-full mt-4 px-6 py-3 bg-red-50 text-red-600 font-bold rounded-xl hover:bg-red-100 transition-all flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        Delete Product
                    </button>
                </form>
            </div>
        </div>

        <div id="variantPanel" class="fixed inset-y-0 right-0 w-[520px] bg-white shadow-2xl border-l border-gray-200 transform translate-x-full transition-transform duration-300 z-[60] overflow-y-auto">
            <div class="p-8">
                <div class="flex items-center justify-between mb-8">
                    <h2 class="text-2xl font-bold text-gray-900" id="variantPanelTitle">New Variant</h2>
                    <button type="button" data-variant-cancel class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-50 rounded-lg transition-all">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <form id="variantForm" method="POST" action="{{ url('/variants') }}" class="space-y-6">
                    @csrf
                    <input type="hidden" name="redirect_to" value="products">

                    <div class="space-y-4">
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-gray-700">Product</label>
                            <select name="product_id" id="variantProductId" required class="block w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
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

                        <div class="rounded-xl border border-gray-200 bg-white px-4 py-3">
                            <div class="text-xs font-bold text-gray-400 uppercase tracking-wider">Existing Variants</div>
                            <div id="existingVariantsList" class="mt-2 space-y-2"></div>
                        </div>
                    </div>

                    <div class="pt-8 flex items-center gap-3">
                        <button type="submit" class="flex-1 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl transition-all shadow-lg shadow-blue-200">
                            Save Variant
                        </button>
                        <button type="button" data-variant-cancel class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold rounded-xl transition-all">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div id="variantsListPanel" class="fixed inset-y-0 right-0 w-[520px] bg-white shadow-2xl border-l border-gray-200 transform translate-x-full transition-transform duration-300 z-[55] overflow-y-auto">
            <div class="p-8">
                <div class="flex items-center justify-between mb-8">
                    <h2 class="text-2xl font-bold text-gray-900" id="variantsListTitle">Variants</h2>
                    <button type="button" data-variants-list-cancel class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-50 rounded-lg transition-all">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                <div id="variantsListBody" class="space-y-3"></div>
            </div>
        </div>
    </x-slot>
</x-table-ui-layout>

<x-modals />
@push('scripts')
<script>
    (() => {
        const variantPanel = document.getElementById('variantPanel');
        const variantForm = document.getElementById('variantForm');
        const variantProductId = document.getElementById('variantProductId');
        const variantsListPanel = document.getElementById('variantsListPanel');
        const variantsListTitle = document.getElementById('variantsListTitle');
        const variantsListBody = document.getElementById('variantsListBody');
        const existingVariantsList = document.getElementById('existingVariantsList');
        const variantSize = document.getElementById('variantSize');
        const variantUnit = document.getElementById('variantUnit');
        const variantSku = document.getElementById('variantSku');
        const variantName = document.getElementById('variantName');

        async function fetchProductVariants(productId) {
            if (!productId) return null;
            const res = await fetch(`/products/${productId}/variants`, {
                headers: { 'Accept': 'application/json' },
            });
            if (!res.ok) return null;
            return await res.json();
        }

        function renderVariants(container, variants) {
            if (!container) return;
            container.innerHTML = '';

            if (!variants || variants.length === 0) {
                container.innerHTML = '<div class="text-sm text-gray-500 font-medium">No variants yet</div>';
                return;
            }

            variants.forEach((v) => {
                const sizeUnit = `${v.size || ''}${v.unit || ''}`.trim();
                const extra = [sizeUnit, v.sku].filter(Boolean).join(' • ');
                const row = document.createElement('div');
                row.className = 'flex items-center justify-between rounded-lg border border-gray-200 bg-gray-50 px-3 py-2';
                row.innerHTML = `
                    <div class="min-w-0">
                        <div class="text-sm font-bold text-gray-900 truncate">${v.name || '—'}</div>
                        <div class="text-xs text-gray-500 font-medium truncate">${extra || ''}</div>
                    </div>
                    <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">${v.display_id || ''}</div>
                `;
                container.appendChild(row);
            });
        }

        function computeVariantName() {
            if (!(variantSize instanceof HTMLInputElement) || !(variantUnit instanceof HTMLSelectElement) || !(variantSku instanceof HTMLInputElement) || !(variantName instanceof HTMLInputElement)) return;
            const size = (variantSize.value || '').trim();
            const unit = (variantUnit.value || '').trim();
            const sku = (variantSku.value || '').trim();
            const base = `${size}${unit}`.trim();
            const combined = [base, sku].filter(Boolean).join(' ').trim();
            variantName.value = combined;
        }

        async function refreshExistingVariants(productId) {
            if (!existingVariantsList) return;
            existingVariantsList.innerHTML = '<div class="text-sm text-gray-500 font-medium">Loading...</div>';
            const data = await fetchProductVariants(productId);
            renderVariants(existingVariantsList, data?.variants || []);
        }

        function openVariantPanel(productId = '') {
            if (!variantPanel) return;
            variantPanel.classList.remove('translate-x-full');
            if (variantForm instanceof HTMLFormElement) {
                variantForm.reset();
            }
            if (variantProductId instanceof HTMLSelectElement) {
                variantProductId.value = productId ? String(productId) : '';
            }
            computeVariantName();
            refreshExistingVariants(variantProductId instanceof HTMLSelectElement ? variantProductId.value : productId);
            setTimeout(() => {
                const first = variantPanel.querySelector('select, input, textarea');
                if (first) first.focus();
            }, 100);
        }

        function closeVariantPanel() {
            if (!variantPanel) return;
            variantPanel.classList.add('translate-x-full');
        }

        function openVariantsListPanel(productId, productName) {
            if (!variantsListPanel) return;
            if (variantPanel) variantPanel.classList.add('translate-x-full');
            variantsListPanel.classList.remove('translate-x-full');
            if (variantsListTitle) {
                variantsListTitle.textContent = productName ? `Variants • ${productName}` : 'Variants';
            }
            if (variantsListBody) {
                variantsListBody.innerHTML = '<div class="text-sm text-gray-500 font-medium">Loading...</div>';
            }
            fetchProductVariants(productId).then((data) => {
                if (!variantsListBody) return;
                renderVariants(variantsListBody, data?.variants || []);
            });
        }

        function closeVariantsListPanel() {
            if (!variantsListPanel) return;
            variantsListPanel.classList.add('translate-x-full');
        }

        document.addEventListener('click', (e) => {
            const btn = e.target.closest('[data-add-variant-trigger]');
            if (btn) {
                openVariantPanel(btn.getAttribute('data-product_id') || '');
                return;
            }

            const viewBtn = e.target.closest('[data-view-variants]');
            if (viewBtn) {
                openVariantsListPanel(viewBtn.getAttribute('data-product_id') || '', viewBtn.getAttribute('data-product_name') || '');
                return;
            }

            const cancelBtn = e.target.closest('[data-variant-cancel]');
            if (cancelBtn) {
                closeVariantPanel();
                return;
            }

            const cancelListBtn = e.target.closest('[data-variants-list-cancel]');
            if (cancelListBtn) {
                closeVariantsListPanel();
                return;
            }

            if (variantPanel && !variantPanel.classList.contains('translate-x-full') && !e.target.closest('#variantPanel') && !e.target.closest('[data-add-variant-trigger]')) {
                closeVariantPanel();
            }

            if (variantsListPanel && !variantsListPanel.classList.contains('translate-x-full') && !e.target.closest('#variantsListPanel') && !e.target.closest('[data-view-variants]')) {
                closeVariantsListPanel();
            }
        });

        if (variantProductId instanceof HTMLSelectElement) {
            variantProductId.addEventListener('change', () => {
                refreshExistingVariants(variantProductId.value);
            });
        }

        [variantSize, variantUnit, variantSku].forEach((el) => {
            if (!el) return;
            el.addEventListener('input', computeVariantName);
            el.addEventListener('change', computeVariantName);
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeVariantPanel();
            if (e.key === 'Escape') closeVariantsListPanel();
        });
    })();
</script>
@endpush
@endsection

@php
$createItems = [
['label' => 'Tour', 'url' => route('tours.create'), 'active' => request()->is('create-tour*')],
['label' => 'Order', 'url' => route('orders.index'), 'active' => request()->is('orders*')],
];

$analyseItems = [
['label' => 'View Tours', 'url' => route('tours.index'), 'active' => request()->is('view-tours*')],
];

$assetsItems = [
['label' => 'Employees', 'url' => url('/employees'), 'active' => request()->is('employees*')],
['label' => 'Parties', 'url' => url('/parties'), 'active' => request()->is('parties*')],
['label' => 'Products', 'url' => url('/products'), 'active' => request()->is('products*')],
['label' => 'Variants', 'url' => url('/variants'), 'active' => request()->is('variants*')],
['label' => 'Transports', 'url' => url('/transports'), 'active' => request()->is('transports*')],
];

$financialsItems = [
['label' => 'Banking', 'url' => route('banks.index'), 'active' => request()->is('banks*')],
];

$groups = [$createItems, $analyseItems, $assetsItems, $financialsItems];

$switchItems = [];
foreach ($groups as $items) {
foreach ($items as $it) {
if (!empty($it['active'])) {
$switchItems = $items;
break 2;
}
}
}
@endphp

<header class="h-16 bg-white border-b border-gray-200 px-8 flex items-center justify-between sticky top-0 z-30">
    <div class="flex items-center gap-4 flex-1 min-w-0">
        <div class="flex-1 min-w-0 max-w-xl">
            <div class="google-search">
                <span class="google-search-icon">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </span>
                <input
                    id="globalSearch"
                    type="search"
                    placeholder="Search index..."
                    class="google-search-input" />
                <button type="button" class="google-search-clear" aria-label="Clear search" onclick="const i=this.previousElementSibling; if(i){ i.value=''; i.dispatchEvent(new Event('input')); i.focus(); }">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Right Actions -->
    <div class="flex items-center gap-4">
        <a href="{{ url('/') }}" class="inline-flex items-center h-9 px-4 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold transition-colors shrink-0">Dashboard</a>

        @if(!empty($switchItems))
        <div class="hidden lg:flex items-center gap-2 shrink-0">
            @foreach($switchItems as $item)
            <a
                href="{{ $item['url'] }}"
                class="inline-flex items-center h-9 px-3 rounded-lg text-sm font-semibold transition-colors {{ $item['active'] ? 'bg-blue-50 text-blue-700 border border-blue-100' : 'bg-white text-gray-700 border border-gray-200 hover:bg-gray-50' }}">
                {{ $item['label'] }}
            </a>
            @endforeach
        </div>
        @endif

        <button class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-50 rounded-lg transition-all relative">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
            </svg>
            <span class="absolute top-2 right-2 w-2 h-2 bg-red-500 border-2 border-white rounded-full"></span>
        </button>

        <div class="w-8 h-8 rounded-full bg-gray-100 border border-gray-200 flex items-center justify-center overflow-hidden">
            @php
            $user = Auth::user();
            $initial = strtoupper(substr($user?->name ?: 'U', 0, 1));
            @endphp
            <span class="text-xs font-bold text-gray-600">{{ $initial }}</span>
        </div>
    </div>
</header>
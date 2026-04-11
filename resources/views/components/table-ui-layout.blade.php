@props([
'title' => null,
'paginator',
])

<div class="flex flex-col gap-6" id="mainCard">
    <!-- Page Header -->
    @if($title)
    <div class="flex flex-col gap-3">
        <div class="flex items-center justify-between">
            <h1 class="text-3xl font-bold text-gray-900 tracking-tight table-title">{{ $title }}</h1>
            <div class="flex items-center gap-3">
                <div class="relative">
                    <button type="button" class="filter-chip" data-popover="popover-export">
                        <svg viewBox="0 0 24 24">
                            <path d="M5 20h14v-2H5v2zM12 2l-5.5 5.5 1.42 1.42L11 5.84V16h2V5.84l3.08 3.08 1.42-1.42L12 2z" />
                        </svg>
                        <span>Export</span>
                        <svg class="arrow" viewBox="0 0 24 24">
                            <path d="M7 10l5 5 5-5H7z" />
                        </svg>
                    </button>
                    <div id="popover-export" class="popover popover-right">
                        <div class="popover-header">Download Data</div>
                        <div class="popover-content">
                            <div class="options-list">
                                <button type="button" class="option-item" data-export-type="xlsx">Excel (.xlsx)</button>
                                <button type="button" class="option-item" data-export-type="pdf">PDF (.pdf)</button>
                            </div>
                        </div>
                    </div>
                </div>
                {{ $headerActions ?? ($toolbar ?? '') }}
            </div>
        </div>
        @if(isset($filters))
        <div class="w-full">
            {{ $filters }}
        </div>
        @endif
    </div>
    @endif

    <!-- Main Card -->
    <div id="tableCard" class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        {{ $thead ?? '' }}
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    {{ $tbody ?? '' }}
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 bg-gray-50/30 border-t border-gray-100 flex items-center justify-between">
            <p class="text-sm text-gray-500">
                Showing <span class="font-medium text-gray-900">{{ $paginator->firstItem() ?? 0 }}</span> to <span class="font-medium text-gray-900">{{ $paginator->lastItem() ?? 0 }}</span> of <span class="font-medium text-gray-900">{{ $paginator->total() }}</span> results
            </p>
            <div class="flex items-center gap-2">
                @if($paginator->previousPageUrl())
                <a href="{{ $paginator->previousPageUrl() }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-gray-200 bg-white text-gray-600 hover:bg-gray-50 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                @else
                <button disabled class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-gray-200 bg-gray-50 text-gray-300 cursor-not-allowed">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </button>
                @endif

                @if($paginator->nextPageUrl())
                <a href="{{ $paginator->nextPageUrl() }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-gray-200 bg-white text-gray-600 hover:bg-gray-50 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
                @else
                <button disabled class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-gray-200 bg-gray-50 text-gray-300 cursor-not-allowed">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
                @endif
            </div>
        </div>
    </div>

    {{ $afterTable ?? '' }}
</div>

@if(isset($actions))
<!-- Overlay Actions (Mobile/Side Panel) -->
<div id="sidePanel" class="fixed inset-y-0 right-0 w-96 bg-white shadow-2xl border-l border-gray-200 transform translate-x-full transition-transform duration-300 z-50">
    <div class="h-full flex flex-col p-6">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-xl font-bold text-gray-900">Quick Actions</h2>
            <button onclick="document.getElementById('sidePanel').classList.add('translate-x-full')" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-50 rounded-lg">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        {{ $actions }}
    </div>
</div>
@endif

{{ $overlay ?? '' }}

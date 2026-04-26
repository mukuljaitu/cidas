@extends('layouts.admin')

@section('title', 'Order Analysis')

@section('content')
<div class="flex flex-col gap-6">
    <div class="flex flex-col gap-1">
        <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Order Analyzer</h1>
        <div class="text-sm font-semibold text-gray-400">Range: {{ $range['start'] }} to {{ $range['end'] }} ({{ $range['days'] }} days)</div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/30">
                <div class="text-xs font-extrabold text-gray-400 uppercase tracking-[0.2em]">Orders by Product</div>
            </div>
            <div class="p-6 h-[380px]">
                <canvas id="productChart"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/30">
                <div class="text-xs font-extrabold text-gray-400 uppercase tracking-[0.2em]">Orders by Location (Party City)</div>
            </div>
            <div class="p-6 h-[380px]">
                <canvas id="cityChart"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    (function() {
        const productChartData = @json($productChart);
        const cityChartData = @json($cityChart);

        const palette = [
            '#2563EB',
            '#10B981',
            '#F59E0B',
            '#EF4444',
            '#8B5CF6',
            '#06B6D4',
            '#0EA5E9',
            '#22C55E',
            '#F97316',
            '#E11D48'
        ];

        function buildDatasets(raw) {
            const list = Array.isArray(raw) ? raw : [];
            return list.map((ds, i) => {
                const color = palette[i % palette.length];
                return {
                    label: ds.label,
                    data: Array.isArray(ds.data) ? ds.data : [],
                    borderColor: color,
                    backgroundColor: color,
                    borderWidth: 2,
                    tension: 0.25,
                    pointRadius: 0,
                    pointHoverRadius: 3
                };
            });
        }

        function renderLineChart(canvasId, chartData) {
            const el = document.getElementById(canvasId);
            if (!el || !chartData || !Array.isArray(chartData.labels)) return;

            const datasets = buildDatasets(chartData.datasets);
            if (datasets.length === 0) {
                return new Chart(el.getContext('2d'), {
                    type: 'line',
                    data: { labels: chartData.labels, datasets: [] },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: { y: { beginAtZero: true } }
                    }
                });
            }

            return new Chart(el.getContext('2d'), {
                type: 'line',
                data: { labels: chartData.labels, datasets },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: { position: 'bottom', labels: { boxWidth: 10, usePointStyle: true } },
                        tooltip: { enabled: true }
                    },
                    scales: {
                        x: { ticks: { autoSkip: true, maxTicksLimit: 10 } },
                        y: { beginAtZero: true }
                    }
                }
            });
        }

        renderLineChart('productChart', productChartData);
        renderLineChart('cityChart', cityChartData);
    })();
</script>
@endpush


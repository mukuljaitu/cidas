@extends('layouts.admin')

@section('title', 'Employee Analysis')

@section('content')
<div class="flex flex-col gap-6">
    <div class="flex flex-col gap-3">
        <div class="flex flex-col gap-1">
            <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Employee Analyzer</h1>
            <div class="text-sm font-semibold text-gray-400">Range: {{ $range['start'] }} to {{ $range['end'] }} ({{ $range['days'] }} days)</div>
        </div>

        @php
        $quickDays = $rangeUi['quickDays'] ?? [7, 30, 90, 180, 365];
        $mode = $rangeUi['mode'] ?? 'days';
        $selectedDays = (int) ($rangeUi['days'] ?? 90);
        @endphp

        <div class="flex flex-wrap items-center gap-2">
            @foreach($quickDays as $d)
            @php $active = ($mode === 'days' && $selectedDays === (int) $d); @endphp
            <a href="{{ route('employees.analyze', ['days' => $d]) }}"
                class="inline-flex items-center h-9 px-3 rounded-lg text-sm font-semibold transition-colors {{ $active ? 'bg-blue-50 text-blue-700 border border-blue-100' : 'bg-white text-gray-700 border border-gray-200 hover:bg-gray-50' }}">
                {{ $d }}D
            </a>
            @endforeach

            <form method="GET" action="{{ route('employees.analyze') }}" class="flex flex-wrap items-center gap-2">
                <div class="text-xs font-extrabold text-gray-400 uppercase tracking-[0.2em] ml-1">Custom</div>
                <input type="date" name="start" value="{{ request('start', $range['start']) }}" class="h-9 px-3 rounded-lg border border-gray-200 bg-white text-sm font-semibold text-gray-700" />
                <input type="date" name="end" value="{{ request('end', $range['end']) }}" class="h-9 px-3 rounded-lg border border-gray-200 bg-white text-sm font-semibold text-gray-700" />
                <button type="submit" class="inline-flex items-center h-9 px-4 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold transition-colors">
                    Apply
                </button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/30">
                <div class="text-xs font-extrabold text-gray-400 uppercase tracking-[0.2em]">Employees vs Attendance / Leave</div>
            </div>
            <div class="p-6 h-[420px]">
                <canvas id="attendanceChart"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/30">
                <div class="text-xs font-extrabold text-gray-400 uppercase tracking-[0.2em]">Employees vs Field / Office / Tour</div>
            </div>
            <div class="p-6 h-[420px]">
                <canvas id="toursCountChart"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    (function() {
        const attendanceChartData = @json($attendanceChart);
        const toursCountChartData = @json($toursCountChart);

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

        const zebraBackground = {
            id: 'zebraBackground',
            beforeDraw(chart, args, pluginOptions) {
                const enabled = pluginOptions && pluginOptions.enabled !== undefined ? pluginOptions.enabled : true;
                if (!enabled) return;

                const x = chart?.scales?.x;
                const area = chart?.chartArea;
                if (!x || !area) return;

                const tickCount = Array.isArray(x.ticks) ? x.ticks.length : 0;
                if (tickCount <= 0) return;

                const evenColor = pluginOptions?.evenColor ?? 'rgba(17, 24, 39, 0.04)';
                const oddColor = pluginOptions?.oddColor ?? 'rgba(255, 255, 255, 0)';

                const ctx = chart.ctx;
                ctx.save();

                for (let i = 0; i < tickCount; i++) {
                    const center = x.getPixelForTick(i);
                    const prevCenter = i > 0 ? x.getPixelForTick(i - 1) : center;
                    const nextCenter = i < tickCount - 1 ? x.getPixelForTick(i + 1) : center;

                    const left = i === 0 ? area.left : (prevCenter + center) / 2;
                    const right = i === tickCount - 1 ? area.right : (center + nextCenter) / 2;

                    ctx.fillStyle = i % 2 === 0 ? evenColor : oddColor;
                    ctx.fillRect(left, area.top, right - left, area.bottom - area.top);
                }

                ctx.restore();
            }
        };

        Chart.register(zebraBackground);

        function buildBarDatasets(raw, opts) {
            const list = Array.isArray(raw) ? raw : [];
            return list.map((ds, i) => {
                const color = palette[i % palette.length];
                return {
                    label: ds.label,
                    data: Array.isArray(ds.data) ? ds.data : [],
                    borderColor: color,
                    backgroundColor: color,
                    borderWidth: 1,
                    borderRadius: 6,
                    barThickness: opts && opts.barThickness ? opts.barThickness : undefined,
                    maxBarThickness: opts && opts.maxBarThickness ? opts.maxBarThickness : 22,
                    stack: opts && opts.stacked ? 'stack1' : undefined
                };
            });
        }

        function renderBarChart(canvasId, chartData, opts) {
            const el = document.getElementById(canvasId);
            if (!el || !chartData || !Array.isArray(chartData.labels)) return;

            const stacked = !!(opts && opts.stacked);
            const datasets = buildBarDatasets(chartData.datasets, {
                stacked,
                maxBarThickness: 18
            });

            return new Chart(el.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: chartData.labels,
                    datasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    plugins: {
                        zebraBackground: {
                            enabled: true,
                            evenColor: 'rgba(17, 24, 39, 0.04)',
                            oddColor: 'rgba(255, 255, 255, 0)'
                        },
                        legend: {
                            position: 'bottom',
                            labels: {
                                boxWidth: 10,
                                usePointStyle: true
                            }
                        },
                        tooltip: {
                            enabled: true
                        }
                    },
                    scales: {
                        x: {
                            stacked,
                            ticks: {
                                maxRotation: 60,
                                minRotation: 60
                            }
                        },
                        y: {
                            stacked,
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        renderBarChart('attendanceChart', attendanceChartData, {
            stacked: false
        });
        renderBarChart('toursCountChart', toursCountChartData, {
            stacked: false
        });
    })();
</script>
@endpush
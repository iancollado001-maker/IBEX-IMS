@extends('layouts.app')

@section('content')

<div class="reports-container">

    <!-- Page Header -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 style="font-family: var(--font-display); font-weight: 800; font-size: 1.6rem; margin: 0;">
                <i class="bi bi-bar-chart-line me-2" style="color: var(--ibex-accent);"></i>
                Inventory Report
            </h1>
            <p class="text-muted mb-0" style="font-size: 13px; margin-top: 2px;">
                Asset overview and export tools
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('reports.csv') }}?{{ http_build_query(request()->all()) }}"
               class="btn-ibex btn-ibex-success">
                <i class="bi bi-file-earmark-spreadsheet"></i> Export CSV
            </a>
            <a href="{{ route('reports.pdf') }}?{{ http_build_query(request()->all()) }}"
               target="_blank" class="btn-ibex btn-ibex-danger">
                <i class="bi bi-file-earmark-pdf"></i> Export PDF
            </a>
            <a href="{{ route('inventory.index') }}" class="btn-ibex btn-ibex-ghost">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="ibex-panel mb-4">
        <div class="panel-title">
            <i class="bi bi-funnel"></i> Filter Report
        </div>
        <form method="GET" action="{{ route('reports.index') }}" class="row g-2 align-items-end">
            <div class="col-md-2">
                <label class="form-label">Date From</label>
                <input type="date" name="date_from" class="ibex-input form-control"
                    value="{{ request('date_from') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">Date To</label>
                <input type="date" name="date_to" class="ibex-input form-control"
                    value="{{ request('date_to') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select name="status" class="ibex-input form-select">
                    <option value="">All</option>
                    <option value="Available"  {{ request('status') === 'Available'  ? 'selected' : '' }}>Available</option>
                    <option value="Deployed"   {{ request('status') === 'Deployed'   ? 'selected' : '' }}>Deployed</option>
                    <option value="Spare"      {{ request('status') === 'Spare'      ? 'selected' : '' }}>Spare</option>
                    <option value="Defective"  {{ request('status') === 'Defective'  ? 'selected' : '' }}>Defective</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Category</label>
                <select name="category_id" class="ibex-input form-select">
                    <option value="">All</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->category_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Brand</label>
                <select name="brand_id" class="ibex-input form-select">
                    <option value="">All</option>
                    @foreach($brands as $brand)
                        <option value="{{ $brand->id }}" {{ request('brand_id') == $brand->id ? 'selected' : '' }}>
                            {{ $brand->brand_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn-ibex btn-ibex-primary flex-fill">
                    <i class="bi bi-search"></i> Filter
                </button>
                <a href="{{ route('reports.index') }}" class="btn-ibex btn-ibex-ghost">
                    <i class="bi bi-x"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Summary Stats -->
    <div class="row g-3 mb-4">
        @php
        $statItems = [
            ['total',     'Total Assets',  'boxes',             '#58a6ff', $summary['total']],
            ['available', 'Available',     'check-circle',      '#3fb950', $summary['available']],
            ['deployed',  'Deployed',      'send-check',        '#d29922', $summary['deployed']],
            ['defective', 'Defective',     'exclamation-triangle','#f85149', $summary['defective']],
            ['spare',     'Spare',         'archive',           '#a371f7', $summary['spare']],
        ];
        @endphp
        @foreach($statItems as [$key, $label, $icon, $color, $val])
        <div class="col-md col-6">
            <div class="report-stat-card" style="--card-color: {{ $color }}">
                <div style="font-size: 28px; color: {{ $color }}; margin-bottom: 6px;">
                    <i class="bi bi-{{ $icon }}"></i>
                </div>
                <div style="font-family: var(--font-display); font-weight: 800; font-size: 2rem; color: {{ $color }}">
                    {{ $val }}
                </div>
                <div style="font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; color: var(--ibex-text-muted);">
                    {{ $label }}
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Charts Row -->
    <div class="row g-3 mb-4">
        <div class="col-md-5">
            <div class="ibex-panel" style="height: 280px;">
                <div class="panel-title"><i class="bi bi-pie-chart"></i> By Status</div>
                <canvas id="statusChart"></canvas>
            </div>
        </div>
        <div class="col-md-7">
            <div class="ibex-panel" style="height: 280px;">
                <div class="panel-title"><i class="bi bi-bar-chart"></i> By Category</div>
                <canvas id="categoryChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Asset Table -->
    <div class="ibex-table-panel">
        <div class="table-header">
            <div class="table-title">
                <i class="bi bi-table"></i> Assets
                <span class="filter-chip ms-1">{{ $assets->count() }} records</span>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table" id="reportTable" style="width:100%">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Serial Number</th>
                        <th>Asset Tag</th>
                        <th>Category</th>
                        <th>Brand</th>
                        <th>Status</th>
                        <th>Date Added</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assets as $i => $asset)
                    <tr>
                        <td><span class="row-number">{{ $i + 1 }}</span></td>
                        <td><span class="serial-badge">{{ $asset->serial_number }}</span></td>
                        <td>{{ $asset->asset_tag ?: '—' }}</td>
                        <td>{{ $asset->category->category_name ?? '—' }}</td>
                        <td>{{ $asset->brand->brand_name ?? '—' }}</td>
                        <td>
                            <span class="status-badge status-{{ strtolower($asset->status) }}">
                                {{ $asset->status }}
                            </span>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($asset->date_added)->format('M d, Y') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7">
                            <div class="empty-state">
                                <i class="bi bi-inbox"></i>
                                <p>No assets match the selected filters.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
const CHART_DEFAULTS = {
    color: '#7d8590',
    plugins: {
        legend: { labels: { color: '#7d8590', font: { family: "'DM Sans'" } } }
    }
};

// Status Pie Chart
new Chart(document.getElementById('statusChart'), {
    type: 'doughnut',
    data: {
        labels: ['Available', 'Deployed', 'Defective', 'Spare'],
        datasets: [{
            data: [{{ $summary['available'] }}, {{ $summary['deployed'] }}, {{ $summary['defective'] }}, {{ $summary['spare'] }}],
            backgroundColor: ['#3fb950', '#d29922', '#f85149', '#a371f7'],
            borderColor: '#1c2333',
            borderWidth: 3,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '65%',
        plugins: {
            ...CHART_DEFAULTS.plugins,
        }
    }
});

// Category Bar Chart
const catLabels  = @json($byCategory->keys());
const catValues  = @json($byCategory->values());

new Chart(document.getElementById('categoryChart'), {
    type: 'bar',
    data: {
        labels: catLabels,
        datasets: [{
            label: 'Assets',
            data: catValues,
            backgroundColor: '#f97316',
            borderRadius: 6,
            borderSkipped: false,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { ...CHART_DEFAULTS.plugins },
        scales: {
            x: { ticks: { color: '#7d8590' }, grid: { color: '#2a3547' } },
            y: { ticks: { color: '#7d8590', stepSize: 1 }, grid: { color: '#2a3547' }, beginAtZero: true },
        }
    }
});

// DataTable for report
$('#reportTable').DataTable({
    pageLength: 25,
    order: [[6, 'desc']],
    language: { search: '', searchPlaceholder: 'Search...' }
});
</script>
@endpush

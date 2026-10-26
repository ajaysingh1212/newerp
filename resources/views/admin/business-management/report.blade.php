@extends('admin.business-management.layout')
@section('business-workspace-class', 'br-reference-workspace')
@section('styles')
@parent
@include('admin.business-management.partials.report-styles')
@endsection
@section('business-content')
<h1 class="sr-only">Business Management Report</h1>
<div class="br-kpis">
    <div class="br-kpi"><div class="br-kpi-icon"><i class="fas fa-bolt"></i></div><div class="br-kpi-value" data-report-count="{{ $totals->devices }}">{{ number_format($totals->devices) }}</div><div class="br-kpi-label">Total devices</div></div>
    <div class="br-kpi"><div class="br-kpi-icon"><i class="fas fa-user-tie"></i></div><div class="br-kpi-value" data-report-count="{{ $reportStats['parties'] }}">{{ number_format($reportStats['parties']) }}</div><div class="br-kpi-label">Parties involved</div></div>
    <div class="br-kpi"><div class="br-kpi-icon"><i class="fas fa-box"></i></div><div class="br-kpi-value" data-report-count="{{ $reportStats['products'] }}">{{ number_format($reportStats['products']) }}</div><div class="br-kpi-label">Products involved</div></div>
    <div class="br-kpi"><div class="br-kpi-icon"><i class="fas fa-map-marker-alt"></i></div><div class="br-kpi-value" data-report-count="{{ $reportStats['states'] }}">{{ number_format($reportStats['states']) }}</div><div class="br-kpi-label">States covered</div></div>
</div>
<div class="br-filter-bar">
    <form method="GET" id="report-filters">
        <input type="hidden" name="view" id="report-view" value="{{ request('view', 'charts') }}">
        <input type="hidden" name="chart" id="report-chart" value="{{ request('chart', 'pie') }}">
        <input type="hidden" name="metric" id="report-metric" value="{{ request('metric', 'devices') }}">
        <input type="hidden" name="group_by" id="report-group" value="{{ $groupBy }}">
        <div class="br-filter-grid">
            <div><label for="report-party">Party</label><select id="report-party" class="form-control select2" name="party_id"><option value="" >All Parties</option>@foreach($parties as $party)<option value="{{ $party->id }}" {{ (string)request('party_id') === (string)$party->id ? 'selected' : '' }}>{{ $party->name }}</option>@endforeach</select></div>
            <div><label for="report-model">Product / item</label><select id="report-model" class="form-control select2" name="product_id"><option value="">All Products</option>@foreach($products as $product)<option value="{{ $product->id }}" {{ (string)request('product_id') === (string)$product->id ? 'selected' : '' }}>{{ $product->name }}</option>@endforeach</select></div>
            <div><label for="report-status">Status</label><select id="report-status" class="form-control select2" name="status"><option value="">All Statuses</option>@foreach(['pending', 'activated'] as $status)<option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>@endforeach</select></div>
            @foreach(['state', 'district', 'city'] as $location)
                @php($locationValues = $reportLocations->pluck($location)->filter(fn ($value) => $value !== null && trim($value) !== '')->unique()->sort()->values())
                <div><label for="report-{{ $location }}">{{ ucfirst($location) }}</label><select id="report-{{ $location }}" class="form-control select2" name="{{ $location }}"><option value="">All {{ $location === 'city' ? 'Cities' : ucfirst($location).'s' }}</option>@foreach($locationValues as $value)<option value="{{ $value }}" {{ request($location) === $value ? 'selected' : '' }}>{{ $value }}</option>@endforeach</select></div>
            @endforeach
            <div><label for="report-date-range">Date range</label><select id="report-date-range" name="date_range" class="form-control select2">@foreach(['all' => 'All Time', 'today' => 'Today', 'week' => 'This Week', 'month' => 'This Month', 'year' => 'This Year', 'custom' => 'Custom Range'] as $value => $label)<option value="{{ $value }}" {{ $dateRange === $value ? 'selected' : '' }}>{{ $label }}</option>@endforeach</select></div>
            <button class="btn br-apply"><i class="fas fa-filter"></i> Apply</button><a class="btn br-reset" href="{{ route('admin.business-management.report') }}"><i class="fas fa-redo-alt"></i> Reset</a>
        </div>
        <div id="report-custom-range" class="br-custom-range" {{ $dateRange !== 'custom' ? 'hidden' : '' }}><div><label for="report-from">Assigned from</label><input id="report-from" class="form-control select2" type="date" name="from" value="{{ request('from') }}" {{ $dateRange !== 'custom' ? 'disabled' : '' }}></div><div><label for="report-to">Assigned to</label><input id="report-to" class="form-control select2" type="date" name="to" value="{{ request('to') }}" {{ $dateRange !== 'custom' ? 'disabled' : '' }}></div></div>
    </form>
</div>
<div class="br-main-card">
    <div class="br-card-top">
        <div class="br-chart-totals">
            <div class="br-chart-total-card"><div id="report-total-label" class="br-total-label">Total devices</div><div id="report-chart-total" class="br-total-value">{{ number_format($totals->devices) }}</div></div>
            <div class="br-chart-total-card br-chart-total-activated"><div class="br-total-label">Total activated</div><div id="report-activated-total" class="br-total-value" data-report-count="{{ $totals->activated }}">{{ number_format($totals->activated) }}</div></div>
            <div class="br-chart-total-card br-chart-total-pending"><div class="br-total-label">Total pending</div><div id="report-pending-total" class="br-total-value" data-report-count="{{ $totals->devices - $totals->activated }}">{{ number_format($totals->devices - $totals->activated) }}</div></div>
        </div>
        <div class="br-chart-tabs" role="tablist" aria-label="Chart type">
            @foreach(['pie' => ['fa-chart-pie', 'Pie'], 'circle' => ['fa-circle-notch', 'Donut'], 'bar' => ['fa-chart-bar', 'Bar'], 'wave' => ['fa-wave-square', 'Wave'], 'radar' => ['fa-braille', 'Radar']] as $type => $chart)
                <button type="button" id="chart-tab-{{ $type }}" role="tab" aria-selected="{{ $type === 'pie' ? 'true' : 'false' }}" aria-controls="report-chart-stage" data-chart-type="{{ $type }}" tabindex="{{ $type === 'pie' ? '0' : '-1' }}"><i class="fas {{ $chart[0] }}"></i>{{ $chart[1] }}</button>
            @endforeach
        </div>
    </div>
    <div class="br-group-row">
        <div><div class="br-group-label">Group by</div><div class="br-group-chips" aria-label="Group report by">
            @foreach(['model' => 'Product', 'party' => 'Party', 'state' => 'State', 'district' => 'District', 'city' => 'City'] as $value => $label)<button type="button" data-report-group="{{ $value }}" aria-pressed="{{ $groupBy === $value ? 'true' : 'false' }}">{{ $label }}</button>@endforeach
        </div></div>
        <div class="br-secondary-controls">
            <div class="br-view-tabs" role="tablist" aria-label="Report view"><button type="button" id="charts-tab" role="tab" aria-selected="true" aria-controls="charts-panel" data-report-view="charts">Charts</button><button type="button" id="content-tab" role="tab" aria-selected="false" aria-controls="content-panel" data-report-view="content" tabindex="-1">Content</button></div>
            <label class="sr-only" for="chart-metric">Metric</label><select class="form-control br-metric-select" id="chart-metric"><option value="devices">Device quantity</option><option value="activated">Activated quantity</option><option value="pending">Pending quantity</option><option value="sales">Sales value</option></select>
            <button type="button" id="report-replay" class="br-replay" title="Replay animation"><i class="fas fa-redo-alt"></i><span class="sr-only">Replay animation</span></button>
        </div>
    </div>
    <section id="charts-panel" role="tabpanel" aria-labelledby="charts-tab">
        <h2 id="report-chart-title" class="sr-only">Product distribution</h2><p id="report-chart-subtitle" class="sr-only"></p>
        <div class="br-chart-layout">
            <div class="br-plot-wrap"><div id="report-chart-stage" class="br-chart-stage" role="tabpanel" aria-labelledby="chart-tab-pie"></div><div id="report-chart-tooltip" class="br-chart-tooltip" role="tooltip" hidden></div></div>
            <aside class="br-chart-aside" aria-label="Filtered quantities and percentages"><h3 id="report-legend-title" class="sr-only">Product quantities</h3><div id="report-chart-legend"></div></aside>
        </div>
        <p id="report-chart-note" class="br-chart-note" aria-live="polite"></p>
    </section>
    <section id="content-panel" role="tabpanel" aria-labelledby="content-tab" hidden>
        <div class="br-content-toolbar"><strong>Filtered report content</strong><div class="bm-actions"><button class="btn btn-light" form="report-filters" name="export" value="xlsx">Export Excel</button><button class="btn btn-light" form="report-filters" name="export" value="csv">Export CSV</button></div></div>
        <h3 class="br-content-title">Party × product quantities</h3>
        <table id="report-party-products-table" class="table table-striped"><thead><tr><th>Party</th><th>Product / item</th><th>Quantity</th><th>Activated</th><th>Pending</th><th>Sales value</th></tr></thead><tbody>@foreach($partyProducts as $item)<tr><td>{{ $item->party_name }}</td><td>{{ $item->product_name ?? 'Not supplied' }}</td><td>{{ $item->devices }}</td><td>{{ $item->activated }}</td><td>{{ $item->devices - $item->activated }}</td><td data-order="{{ $item->sales }}">{{ $item->sales !== null ? number_format($item->sales, 2) : 'Not supplied' }}</td></tr>@endforeach</tbody></table>
        <h3 class="br-content-title">{{ $groupBy === 'model' ? 'Product' : ucfirst($groupBy) }} summary</h3>
        <table id="report-summary-table" class="table table-striped"><thead><tr><th>{{ $groupBy === 'model' ? 'Product' : ucfirst($groupBy) }}</th><th>Quantity</th><th>Activated</th><th>Pending</th><th>Sales value</th></tr></thead><tbody>@foreach($summaries as $summary)<tr><td>@foreach($labelColumns as $index => $column){{ $index ? ' / ' : '' }}{{ trim((string)$summary->{'label_'.$index}) ?: 'Not supplied' }}@endforeach</td><td>{{ $summary->devices }}</td><td>{{ $summary->activated }}</td><td>{{ $summary->devices - $summary->activated }}</td><td data-order="{{ $summary->sales }}">{{ $summary->sales !== null ? number_format($summary->sales, 2) : 'Not supplied' }}</td></tr>@endforeach</tbody></table>
        <h3 class="br-content-title">Device details</h3>
        <table id="report-devices-table" class="table table-striped"><thead><tr><th>Party</th><th>State / district / city</th><th>Product</th><th>VTS number</th><th>IMEI number</th><th>SIM number</th><th>Sales price</th><th>Assigned date</th><th>Status</th><th>Activated by / at</th></tr></thead><tbody></tbody></table>
    </section>
</div>
<noscript><p>Enable JavaScript for animated charts. The complete content can be downloaded from the report exports.</p></noscript>
@endsection
@section('business-scripts')
<script>
window.businessReportConfig = {data: @json($reportChartData), tableUrl: @json(route('admin.business-management.report')), view: @json(request('view', 'charts')), chart: @json(request('chart', 'pie')), metric: @json(request('metric', 'devices'))};
</script>
<script src="{{ asset('js/business-reports.js') }}"></script>
@endsection

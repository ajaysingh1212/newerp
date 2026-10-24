@extends('admin.business-management.layout')
@section('business-title', 'Reports & insights')
@section('business-description', 'Explore your business visually, or switch to Content for the complete report.')
@section('business-icon', 'fa-chart-pie')
@section('styles')
@parent
<link rel="stylesheet" href="{{ asset('css/business-reports.css') }}">
@endsection
@section('business-content')
<div class="bm-stats br-stats">
    <div class="bm-stat"><div class="bm-stat-label">Total devices <i class="fas fa-cubes"></i></div><div class="bm-stat-value" data-report-count="{{ $totals->devices }}">{{ number_format($totals->devices) }}</div><small>Current report selection</small></div>
    <div class="bm-stat" style="--accent:#218960"><div class="bm-stat-label">Activated <i class="fas fa-check-circle"></i></div><div class="bm-stat-value" data-report-count="{{ $totals->activated }}">{{ number_format($totals->activated) }}</div><small>{{ $totals->devices ? number_format($totals->activated / $totals->devices * 100, 1) : 0 }}% activation rate</small></div>
    <div class="bm-stat" style="--accent:#ba8428"><div class="bm-stat-label">Pending <i class="fas fa-clock"></i></div><div class="bm-stat-value" data-report-count="{{ $totals->devices - $totals->activated }}">{{ number_format($totals->devices - $totals->activated) }}</div><small>Awaiting activation</small></div>
    <div class="bm-stat" style="--accent:#7961b6"><div class="bm-stat-label">Sales value <i class="fas fa-wallet"></i></div><div class="bm-stat-value" data-report-count="{{ $totals->sales }}" data-decimals="2">{{ number_format($totals->sales, 2) }}</div><small>Known sales values only</small></div>
</div>
<details class="br-filters">
    <summary><span><i class="fas fa-sliders-h"></i> Filters & exports</span><span class="br-filter-hint">Party, model, location & dates <i class="fas fa-chevron-down"></i></span></summary>
    <form method="GET" id="report-filters" class="br-filter-body">
        <input type="hidden" name="view" id="report-view" value="{{ request('view', 'charts') }}">
        <input type="hidden" name="chart" id="report-chart" value="{{ request('chart', 'pie') }}">
        <input type="hidden" name="metric" id="report-metric" value="{{ request('metric', 'devices') }}">
        <div class="form-row">
            <div class="form-group col-md-4"><label for="report-party">Party</label><select id="report-party" class="form-control select2" name="party_id"><option value="">All parties</option>@foreach($parties as $party)<option value="{{ $party->id }}" {{ (string)request('party_id') === (string)$party->id ? 'selected' : '' }}>{{ $party->name }}</option>@endforeach</select></div>
            <div class="form-group col-md-4"><label for="report-model">Model</label><select id="report-model" class="form-control select2" name="product_id"><option value="">All models</option>@foreach($products as $product)<option value="{{ $product->id }}" {{ (string)request('product_id') === (string)$product->id ? 'selected' : '' }}>{{ $product->name }}</option>@endforeach</select></div>
            <div class="form-group col-md-4"><label for="report-status">Status</label><select id="report-status" class="form-control" name="status"><option value="">All statuses</option>@foreach(['pending', 'activated'] as $status)<option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>@endforeach</select></div>
            @foreach(['state', 'district', 'city'] as $location)<div class="form-group col-md-4"><label for="report-{{ $location }}">{{ ucfirst($location) }}</label><input id="report-{{ $location }}" class="form-control" name="{{ $location }}" value="{{ request($location) }}" placeholder="All locations" maxlength="255"></div>@endforeach
            <div class="form-group col-md-6"><label for="report-from">Assigned from</label><input id="report-from" class="form-control" type="date" name="from" value="{{ request('from') }}"></div>
            <div class="form-group col-md-6"><label for="report-to">Assigned to</label><input id="report-to" class="form-control" type="date" name="to" value="{{ request('to') }}"></div>
        </div>
        <div class="bm-actions"><button class="btn btn-primary"><i class="fas fa-filter mr-1"></i> Apply filters</button><button class="btn btn-light" name="export" value="xlsx"><i class="fas fa-file-excel mr-1"></i> Excel</button><button class="btn btn-light" name="export" value="csv"><i class="fas fa-file-csv mr-1"></i> CSV</button><a class="btn btn-light" href="{{ route('admin.business-management.report') }}">Reset</a></div>
    </form>
</details>
<div class="br-view-toolbar">
    <div class="br-view-tabs" role="tablist" aria-label="Report view">
        <button type="button" id="charts-tab" role="tab" aria-selected="true" aria-controls="charts-panel" data-report-view="charts"><i class="fas fa-chart-pie"></i> Charts</button>
        <button type="button" id="content-tab" role="tab" aria-selected="false" aria-controls="content-panel" data-report-view="content" tabindex="-1"><i class="fas fa-list-alt"></i> Content</button>
    </div>
    <div class="br-group-control"><label for="report-group">Group by</label><select id="report-group" class="form-control" name="group_by" form="report-filters">@foreach(['party', 'model', 'state', 'district', 'city'] as $group)<option value="{{ $group }}" {{ $groupBy === $group ? 'selected' : '' }}>{{ ucfirst($group) }}</option>@endforeach</select></div>
</div>
<section id="charts-panel" role="tabpanel" aria-labelledby="charts-tab">
    <div class="card br-chart-card"><div class="card-header"><div><div class="bm-kicker">VISUAL REPORT</div><h4 id="report-chart-title">Party distribution</h4><p id="report-chart-subtitle">Switch between charts to explore the same filtered report.</p></div><button type="button" id="report-replay" class="btn btn-light" title="Replay chart animation"><i class="fas fa-redo-alt"></i><span class="br-replay-label"> Replay</span></button></div>
        <div class="br-chart-controls">
            <div class="br-chart-tabs" role="tablist" aria-label="Chart type">
                @foreach(['pie' => ['fa-chart-pie', 'Pie'], 'circle' => ['fa-circle-notch', 'Circle'], 'bar' => ['fa-chart-bar', 'Bar'], 'wave' => ['fa-chart-area', 'Wave'], 'candle' => ['fa-sliders-h', 'Candle']] as $type => $chart)
                    <button type="button" id="chart-tab-{{ $type }}" role="tab" aria-selected="{{ $type === 'pie' ? 'true' : 'false' }}" aria-controls="report-chart-stage" data-chart-type="{{ $type }}" tabindex="{{ $type === 'pie' ? '0' : '-1' }}"><i class="fas {{ $chart[0] }}"></i>{{ $chart[1] }}</button>
                @endforeach
            </div>
            <div class="br-metric-control"><label class="sr-only" for="chart-metric">Chart metric</label><select class="form-control" id="chart-metric"><option value="devices">Total devices</option><option value="activated">Activated</option><option value="pending">Pending</option><option value="sales">Sales value</option></select></div>
        </div>
        <div class="br-chart-layout">
            <div class="br-plot-wrap"><div id="report-chart-stage" class="br-chart-stage" role="tabpanel" aria-labelledby="chart-tab-pie"></div><div id="report-chart-tooltip" class="br-chart-tooltip" role="tooltip" hidden></div><p id="report-chart-note" class="br-chart-note" aria-live="polite"></p></div>
            <aside class="br-chart-aside" aria-label="Chart data legend"><h5 id="report-legend-title">Distribution</h5><div id="report-chart-legend"></div><p class="br-legend-tip"><i class="fas fa-mouse-pointer"></i> Hover or focus a chart item to see its value.</p></aside>
        </div>
    </div>
</section>
<section id="content-panel" role="tabpanel" aria-labelledby="content-tab" hidden>
    <div class="card"><div class="card-header"><div><h4>{{ ucfirst($groupBy) }} summary</h4><p>Complete values for every group in the current selection.</p></div><span class="bm-status neutral">{{ $summaries->count() }} groups</span></div><div class="card-body">
        <table id="report-summary-table" class="table table-striped"><thead><tr><th>{{ ucfirst($groupBy) }}</th><th>Devices</th><th>Activated</th><th>Pending</th><th>Sales value</th></tr></thead><tbody>@foreach($summaries as $summary)<tr><td>@foreach($labelColumns as $index => $column){{ $index ? ' / ' : '' }}{{ trim((string)$summary->{'label_'.$index}) ?: 'Not supplied' }}@endforeach</td><td>{{ $summary->devices }}</td><td>{{ $summary->activated }}</td><td>{{ $summary->devices - $summary->activated }}</td><td data-order="{{ $summary->sales }}">{{ $summary->sales !== null ? number_format($summary->sales, 2) : 'Not supplied' }}</td></tr>@endforeach</tbody></table>
    </div></div>
    <div class="card"><div class="card-header"><div><h4>Device details</h4><p>Search the complete filtered device report.</p></div></div><div class="card-body">
        <table id="report-devices-table" class="table table-striped"><thead><tr><th>Party</th><th>State / district / city</th><th>Model</th><th>VTS number</th><th>IMEI number</th><th>SIM number</th><th>Sales price</th><th>Assigned date</th><th>Status</th><th>Activated by / at</th></tr></thead><tbody></tbody></table>
    </div></div>
</section>
<noscript><p class="bm-note">Charts need JavaScript. Use Excel or CSV in Filters & exports to download the report.</p></noscript>
@endsection
@section('business-scripts')
<script>
window.businessReportConfig = {data: @json($reportChartData), tableUrl: @json(route('admin.business-management.report')), view: @json(request('view', 'charts')), chart: @json(request('chart', 'pie')), metric: @json(request('metric', 'devices'))};
</script>
<script src="{{ asset('js/business-reports.js') }}"></script>
@endsection

@extends('admin.business-management.layout')
@section('business-title', 'Reports & insights')
@section('business-description', 'See where your devices go, understand model distribution and track activation progress.')
@section('business-icon', 'fa-chart-pie')
@section('business-content')
<div class="bm-stats">
    <div class="bm-stat"><div class="bm-stat-label">Devices distributed <i class="fas fa-cubes"></i></div><div class="bm-stat-value">{{ number_format($totals->devices) }}</div><small>Across the current report filters</small></div>
    <div class="bm-stat" style="--accent:#218960"><div class="bm-stat-label">Activated <i class="fas fa-check-circle"></i></div><div class="bm-stat-value">{{ number_format($totals->activated) }}</div><small>{{ $totals->devices ? number_format($totals->activated / $totals->devices * 100, 1) : 0 }}% activation rate</small></div>
    <div class="bm-stat" style="--accent:#ba8428"><div class="bm-stat-label">Pending <i class="fas fa-clock"></i></div><div class="bm-stat-value">{{ number_format($totals->devices - $totals->activated) }}</div><small>Devices yet to be activated</small></div>
    <div class="bm-stat" style="--accent:#7961b6"><div class="bm-stat-label">Total sales value <i class="fas fa-wallet"></i></div><div class="bm-stat-value">{{ number_format($totals->sales, 2) }}</div><small>Combined value of filtered devices</small></div>
</div>
<div class="card"><div class="card-header"><div><h4><i class="fas fa-sliders-h bm-section-icon"></i>Build your report</h4><p>Combine party, model, location and distribution dates.</p></div></div><div class="card-body">
    <form method="GET" id="report-filters">
        <div class="form-row">
            <div class="form-group col-md-4"><label for="report-party">Party</label><select id="report-party" class="form-control select2" name="party_id"><option value="">All parties</option>@foreach($parties as $party)<option value="{{ $party->id }}" {{ (string)request('party_id') === (string)$party->id ? 'selected' : '' }}>{{ $party->name }}</option>@endforeach</select></div>
            <div class="form-group col-md-4"><label for="report-model">Model</label><select id="report-model" class="form-control select2" name="product_id"><option value="">All models</option>@foreach($products as $product)<option value="{{ $product->id }}" {{ (string)request('product_id') === (string)$product->id ? 'selected' : '' }}>{{ $product->name }}</option>@endforeach</select></div>
            <div class="form-group col-md-4"><label for="report-status">Status</label><select id="report-status" class="form-control" name="status"><option value="">All statuses</option>@foreach(['pending', 'activated'] as $status)<option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>@endforeach</select></div>
            @foreach(['state', 'district', 'city'] as $location)<div class="form-group col-md-4"><label for="report-{{ $location }}">{{ ucfirst($location) }}</label><input id="report-{{ $location }}" class="form-control" name="{{ $location }}" value="{{ request($location) }}" placeholder="All {{ $location === 'city' ? 'cities' : $location.'s' }}" maxlength="255"></div>@endforeach
            <div class="form-group col-md-4"><label for="report-from">Assigned from</label><input id="report-from" class="form-control" type="date" name="from" value="{{ request('from') }}"></div>
            <div class="form-group col-md-4"><label for="report-to">Assigned to</label><input id="report-to" class="form-control" type="date" name="to" value="{{ request('to') }}"></div>
            <div class="form-group col-md-4"><label for="report-group">Group summary by</label><select id="report-group" class="form-control" name="group_by">@foreach(['party', 'model', 'state', 'district', 'city'] as $group)<option value="{{ $group }}" {{ $groupBy === $group ? 'selected' : '' }}>{{ ucfirst($group) }}</option>@endforeach</select></div>
        </div>
        <div class="bm-actions"><button class="btn btn-primary"><i class="fas fa-filter mr-1"></i> Apply filters</button><button class="btn btn-light" name="export" value="xlsx"><i class="fas fa-file-excel mr-1"></i> Export Excel</button><button class="btn btn-light" name="export" value="csv"><i class="fas fa-file-csv mr-1"></i> Export CSV</button><a class="btn btn-light" href="{{ route('admin.business-management.report') }}">Reset</a></div>
    </form>
</div></div>
<div class="card"><div class="card-header"><div><h4><i class="fas fa-chart-bar bm-section-icon"></i>{{ ucfirst($groupBy) }} summary</h4><p>Distribution, activation and sales at a glance.</p></div><span class="bm-status neutral">{{ $summaries->count() }} groups</span></div><div class="card-body">
    <table class="table table-striped" data-business-table="standard"><thead><tr><th>{{ ucfirst($groupBy) }}</th><th>Devices</th><th>Activated</th><th>Pending</th><th>Sales value</th></tr></thead><tbody>@foreach($summaries as $summary)<tr><td>@foreach($labelColumns as $index => $column){{ $index ? ' / ' : '' }}{{ $summary->{'label_'.$index} }}@endforeach</td><td>{{ $summary->devices }}</td><td>{{ $summary->activated }}</td><td>{{ $summary->devices - $summary->activated }}</td><td data-order="{{ $summary->sales }}">{{ number_format($summary->sales, 2) }}</td></tr>@endforeach</tbody></table>
</div></div>
<div class="card"><div class="card-header"><div><h4><i class="fas fa-list bm-section-icon"></i>Device details</h4><p>Search all matching devices. Exports include the complete report filters.</p></div></div><div class="card-body">
    <table id="report-devices-table" class="table table-striped"><thead><tr><th>Party</th><th>State / district / city</th><th>Model</th><th>VTS number</th><th>SIM number</th><th>Sales price</th><th>Assigned date</th><th>Status</th><th>Activated by / at</th></tr></thead><tbody></tbody></table>
</div></div>
@endsection
@section('business-scripts')
<script>
jQuery(function () {
    const table = BusinessTables;
    const url = new URL(@json(route('admin.business-management.report')));
    new FormData(document.getElementById('report-filters')).forEach((value, key) => url.searchParams.set(key, value));
    table.init('#report-devices-table', {processing: true, serverSide: true, ajax: url.toString(), columns: [
        table.textColumn('party_name', 'party.name'), table.textColumn('location', 'location', {searchable: false, orderable: false}), table.textColumn('model_name', 'product.name'),
        table.textColumn('vts_number'), table.textColumn('sim_number'), table.moneyColumn(), table.textColumn('assigned_at'), table.statusColumn(), table.textColumn('activation', 'activator.name', {orderable: false})
    ]});
});
</script>
@endsection

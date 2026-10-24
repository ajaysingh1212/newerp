@extends('admin.business-management.layout')
@section('business-title', 'Imported source ledger')
@section('business-description', 'Every original worksheet row, payment detail and formula stays traceable. Current inventory follows the latest clear sale date.')
@section('business-icon', 'fa-book')
@section('business-content')
@if($batch)
<div class="bm-stats">
    <div class="bm-stat"><div class="bm-stat-label">Worksheets preserved</div><div class="bm-stat-value">{{ $batch->summary['sheets'] }}</div><small>{{ $batch->file_name }}</small></div>
    <div class="bm-stat"><div class="bm-stat-label">Original populated rows</div><div class="bm-stat-value">{{ number_format($batch->summary['source_rows']) }}</div><small>{{ number_format($batch->summary['source_cells']) }} original cells retained</small></div>
    <div class="bm-stat"><div class="bm-stat-label">Current devices</div><div class="bm-stat-value">{{ number_format($batch->summary['current_devices']) }}</div><small>{{ $batch->summary['conflicting_devices'] }} conflicting identities retained for review</small></div>
    <div class="bm-stat"><div class="bm-stat-label">Party matching</div><div class="bm-stat-value">{{ $batch->summary['parties_reused'] }} / {{ $batch->summary['parties_created'] }}</div><small>Existing parties reused / new parties created</small></div>
</div>
<div class="bm-note"><i class="fas fa-info-circle"></i><span>Sale history includes repeat devices and bulk-quantity entries; it is separate from current device inventory. Unknown source values stay blank. Review flags identify missing values, source errors and ambiguous assignments. Template, summary and control rows are also retained.</span></div>
@endif
<div class="card"><div class="card-header"><h4>Source records & review</h4>@if($batch)<a class="btn btn-light" href="{{ route('admin.business-imports.download', $batch) }}">Download original workbook</a>@endif</div><div class="card-body">
    <form id="ledger-filters" method="GET"><div class="form-row">
        <div class="form-group col-md-4"><label for="ledger-batch">Import</label><select id="ledger-batch" name="batch_id" class="form-control">@foreach($batches as $item)<option value="{{ $item->id }}" {{ $batch?->id === $item->id ? 'selected' : '' }}>{{ $item->file_name }} (#{{ $item->id }})</option>@endforeach</select></div>
        <div class="form-group col-md-4"><label for="ledger-party">Party</label><select id="ledger-party" name="party_id" class="form-control select2"><option value="">All parties</option>@foreach($parties as $party)<option value="{{ $party->id }}" {{ (string)request('party_id') === (string)$party->id ? 'selected' : '' }}>{{ $party->name }}</option>@endforeach</select></div>
        <div class="form-group col-md-4"><label for="ledger-sheet">Worksheet</label><select id="ledger-sheet" name="sheet" class="form-control"><option value="">All worksheets</option>@foreach($sheets as $sheet)<option value="{{ $sheet }}" {{ request('sheet') === $sheet ? 'selected' : '' }}>{{ $sheet }}</option>@endforeach</select></div>
        <div class="form-group col-md-4"><label for="ledger-kind">Record type</label><select id="ledger-kind" name="kind" class="form-control"><option value="">All original rows</option>@foreach(['sale', 'payment', 'summary', 'note', 'source', 'template', 'header', 'control'] as $kind)<option value="{{ $kind }}" {{ request('kind') === $kind ? 'selected' : '' }}>{{ ucfirst($kind) }}</option>@endforeach</select></div>
        <div class="form-group col-md-4"><label for="ledger-review">Review status</label><select id="ledger-review" name="review" class="form-control"><option value="">All records</option><option value="1" {{ request('review') === '1' ? 'selected' : '' }}>Needs review</option></select></div>
    </div><div class="bm-actions"><button class="btn btn-primary">Apply filters</button><button class="btn btn-light" name="export" value="csv">Export complete source CSV</button><a class="btn btn-light" href="{{ route('admin.business-imports.index') }}">Reset</a></div></form>
    <table id="ledger-table" class="table table-striped mt-3"><thead><tr><th>Sheet</th><th>Row</th><th>Party</th><th>Type</th><th>Model</th><th>VTS</th><th>Qty</th><th>Sale date</th><th>Source sales value</th><th>Review notes</th><th>Original fields</th></tr></thead><tbody></tbody></table>
</div></div>
@endsection
@section('business-scripts')
<script>
jQuery(function () {
    const t = BusinessTables;
    const url = new URL(@json(route('admin.business-imports.index')));
    new FormData(document.getElementById('ledger-filters')).forEach((value, name) => url.searchParams.set(name, value));
    const field = name => t.textColumn(name, name, {searchable: false, orderable: false});
    t.init('#ledger-table', {serverSide: true, processing: true, ajax: url.toString(), columns: [
        t.textColumn('sheet', 'sheet'), t.textColumn('row_number', 'row_number'), field('party_name'), t.textColumn('kind', 'kind'), field('model'), field('vts_number'), field('quantity'), field('sale_date'), field('sales_value'), field('review_notes'),
        {data: 'detail_url', orderable: false, searchable: false, render: value => '<a class="btn btn-xs btn-info" href="' + t.escape(value) + '">View source</a>'}
    ]});
});
</script>
@endsection

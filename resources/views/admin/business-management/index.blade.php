@extends('admin.business-management.layout')
@section('business-title', 'Device inventory')
@section('business-description', 'Manage every device, follow each activation and keep your party inventory in focus.')
@section('business-content')
<div class="bm-stats">
    <div class="bm-stat"><div class="bm-stat-label">Total devices <i class="fas fa-cubes"></i></div><div class="bm-stat-value">{{ number_format($counts->devices) }}</div><small>For the selected party & filters</small></div>
    <div class="bm-stat" style="--accent:#ba8428"><div class="bm-stat-label">Pending <i class="fas fa-clock"></i></div><div class="bm-stat-value">{{ number_format($counts->devices - $counts->activated) }}</div><small>Awaiting device activation</small></div>
    <div class="bm-stat" style="--accent:#218960"><div class="bm-stat-label">Activated <i class="fas fa-check-circle"></i></div><div class="bm-stat-value">{{ number_format($counts->activated ?? 0) }}</div><small>Activation user & time recorded</small></div>
    <div class="bm-stat" style="--accent:#7961b6"><div class="bm-stat-label">Total sales <i class="fas fa-wallet"></i></div><div class="bm-stat-value">{{ number_format($total, 2) }}</div><small>Combined device sales value</small></div>
</div>
<div class="card"><div class="card-header"><div><h4><i class="fas fa-sliders-h bm-section-icon"></i>Inventory filters</h4><p>Choose a party to view its device inventory.</p></div></div><div class="card-body">
    <form method="GET" class="form-row align-items-end">
        <div class="form-group col-md-6"><label for="party_id">Party</label><select class="form-control select2" name="party_id" id="party_id">@forelse($parties as $party)<option value="{{ $party->id }}" {{ (string)$partyId === (string)$party->id ? 'selected' : '' }}>{{ $party->name }}</option>@empty<option value="">No parties available</option>@endforelse</select></div>
        <div class="form-group col-md-3"><label for="status">Device status</label><select name="status" id="status" class="form-control"><option value="">All statuses</option>@foreach(['pending', 'activated'] as $status)<option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>@endforeach</select></div>
        <div class="form-group col-md-3"><button class="btn btn-primary"><i class="fas fa-filter mr-1"></i> Apply filters</button></div>
    </form>
</div></div>
<div class="card"><div class="card-header"><div><h4><i class="fas fa-list bm-section-icon"></i>Party devices</h4><p>Search, sort and manage your complete filtered inventory.</p></div><div class="bm-actions">
    @can('business_management_import')<a class="btn btn-light" href="{{ route('admin.business-management.importForm', ['party_id' => $partyId]) }}"><i class="fas fa-file-import mr-1"></i> Import</a>@endcan
    @can('business_management_create')<a class="btn btn-primary" href="{{ route('admin.business-management.create', ['party_id' => $partyId]) }}"><i class="fas fa-plus mr-1"></i> Add devices</a>@endcan
</div></div><div class="card-body">
    <table id="inventory-table" class="table table-striped"><thead><tr><th>Model</th><th>VTS number</th><th>SIM number</th><th>Sales price</th><th>Assigned date</th><th>Status</th><th>Activated by / at</th><th>Actions</th></tr></thead><tbody></tbody></table>
</div></div>
@endsection
@section('business-scripts')
<script>
jQuery(function () {
    const table = BusinessTables;
    const url = new URL(@json(route('admin.business-management.index')));
    url.searchParams.set('party_id', @json((string)$partyId));
    url.searchParams.set('status', @json(request('status', '')));
    table.init('#inventory-table', {processing: true, serverSide: true, ajax: url.toString(), columns: [
        table.textColumn('model_name', 'product.name'), table.textColumn('vts_number'), table.textColumn('sim_number'), table.moneyColumn(), table.textColumn('assigned_at'), table.statusColumn(),
        table.textColumn('activation', 'activator.name', {orderable: false}),
        {data: 'actions', name: 'actions', orderable: false, searchable: false}
    ]});
});
</script>
@endsection

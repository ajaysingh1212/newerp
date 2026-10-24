@extends('admin.business-management.layout')
@section('business-title', 'Import workspace')
@section('business-description', 'Bring in your spreadsheet, match its columns and review every device before importing.')
@section('business-icon', 'fa-file-import')
@section('business-content')
<div class="bm-stepper"><span><b class="bm-step">1</b> Upload spreadsheet</span><span><b class="bm-step">2</b> Match your columns</span><span><b class="bm-step">3</b> Review & import</span></div>
<form id="import-form" method="POST" action="{{ route('admin.business-management.import') }}" enctype="multipart/form-data">@csrf
    <div class="card"><div class="card-header"><div><h4><span class="bm-step">1</span>Choose your file & party</h4><p>CSV, XLSX or XLS · Maximum 5 MB · Up to 1000 device rows</p></div><a class="btn btn-light" href="{{ route('admin.business-management.template') }}"><i class="fas fa-download mr-1"></i> CSV template</a></div><div class="card-body">
        <div class="form-row"><div class="form-group col-md-8"><label for="manual_party_id">Assign to party <span class="bm-required">Required</span></label><select required class="form-control select2" name="manual_party_id" id="manual_party_id"><option value="">Select party</option>@foreach($parties as $party)<option value="{{ $party->id }}" {{ (string)old('manual_party_id', request('party_id')) === (string)$party->id ? 'selected' : '' }}>{{ $party->name }}</option>@endforeach</select></div><div class="form-group col-md-4"><label for="assigned_at">Assigned date <span class="bm-required">Required</span></label><input type="date" class="form-control" name="assigned_at" id="assigned_at" value="{{ old('assigned_at', now()->format('Y-m-d')) }}" required></div></div>
        <div class="bm-upload"><div class="bm-upload-icon"><i class="fas fa-cloud-upload-alt"></i></div><label for="file">Select your Excel or CSV spreadsheet</label><p>The first row should contain column names. We read the first worksheet. Keep VTS and SIM numbers as Text in Excel to preserve leading zeros and long numbers.</p><input type="file" class="form-control" name="file" id="file" accept=".csv,.xlsx,.xls" required><div class="bm-file-meta" id="file-meta" aria-live="polite">No file selected</div></div>
        <div id="import-error" class="alert alert-danger mt-3" role="alert" hidden></div>
    </div></div>
    <div class="card" id="mapping-card"><div class="card-header"><div><h4><span class="bm-step">2</span>Match spreadsheet columns</h4><p>System field → your file column → sample value. All four fields are required.</p></div><span class="bm-status neutral" id="mapping-state">Waiting for file</span></div><div class="card-body">
        <div class="bm-note"><i class="fas fa-info-circle"></i><span>Choose the column in your file that belongs to each system field. Recognized headers are matched automatically; you can change every selection. Each file column can be used once.</span></div>
        <table id="mapping-table" class="table" data-business-table="compact"><thead><tr><th>System field</th><th>Your file column</th><th>First row sample</th><th>Expected value</th></tr></thead><tbody>
            @foreach(['manual_product_id' => ['Device / model', 'Existing product ID or a unique product name'], 'vts_number' => ['VTS number', 'Unique device / IMEI number; maximum 100 characters'], 'sim_number' => ['SIM number', 'SIM identifier; maximum 100 characters'], 'sales_price' => ['Sales price', 'Zero or a positive number, up to 2 decimal places']] as $field => $description)
                <tr><td><label for="map-{{ $field }}">{{ $description[0] }} <span class="bm-required">*</span></label><br><code>{{ $field }}</code></td><td><select class="form-control import-mapping" name="mapping[{{ $field }}]" id="map-{{ $field }}" data-field="{{ $field }}" required disabled><option value="">Select a file first</option></select></td><td class="bm-mapping-sample" id="sample-{{ $field }}">—</td><td>{{ $description[1] }}</td></tr>
            @endforeach
        </tbody></table>
    </div></div>
    <div class="card" id="preview-card" hidden><div class="card-header"><div><h4><span class="bm-step">3</span>Review your import</h4><p>Check the original spreadsheet and the mapped device data.</p></div><span class="bm-status neutral" id="preview-row-count"></span></div><div class="card-body">
        <div class="bm-preview-controls" role="tablist" aria-label="Import preview"><button type="button" id="mapped-tab" role="tab" aria-selected="true" aria-controls="mapped-panel">Mapped device preview</button><button type="button" id="original-tab" role="tab" aria-selected="false" aria-controls="original-panel">Original file data</button></div>
        <div id="mapped-panel" role="tabpanel" aria-labelledby="mapped-tab"><table id="mapped-preview" class="table table-striped"><thead><tr><th>Row</th><th>Device / model</th><th>VTS number</th><th>SIM number</th><th>Sales price</th><th>Status</th></tr></thead><tbody></tbody></table></div>
        <div id="original-panel" role="tabpanel" aria-labelledby="original-tab" hidden><table id="original-preview" class="table table-striped"><thead></thead><tbody></tbody></table></div>
        <div class="bm-import-footer"><div class="bm-review-summary" id="import-summary" aria-live="polite">Match all required columns to continue.</div><button type="submit" id="import-submit" class="btn btn-primary" disabled><i class="fas fa-check-circle mr-1"></i> Import devices</button></div>
        <div class="bm-note mt-3 mb-0"><i class="fas fa-shield-alt"></i><span>All devices start as Pending. Before saving, every row is validated for product, unique VTS, SIM and price. If any row fails, no devices are imported.</span></div>
    </div></div>
</form>
<div class="card"><div class="card-header"><div><h4><i class="fas fa-boxes bm-section-icon"></i>Product reference</h4><p>Use an existing product ID or its unique name in the device column of your file.</p></div></div><div class="card-body"><table class="table table-striped" data-business-table="standard"><thead><tr><th>Product ID</th><th>Device / model name</th></tr></thead><tbody>@foreach($products as $product)<tr><td>{{ $product->id }}</td><td>{{ $product->name }}</td></tr>@endforeach</tbody></table></div></div>
@endsection
@section('business-scripts')
@php($importProducts = $products->map->only(['id', 'name'])->values())
<script>
window.businessImportConfig = {previewUrl: @json(route('admin.business-management.importPreview')), products: @json($importProducts)};
</script>
<script src="{{ asset('js/business-import.js') }}"></script>
@endsection

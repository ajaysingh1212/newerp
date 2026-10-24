@extends('admin.business-management.layout')
@section('business-title', 'Original source record')
@section('business-description', 'Original cell values and cached formulas are preserved exactly as supplied in the workbook.')
@section('business-content')
<div class="card"><div class="card-header"><div><h4>Sheet {{ $businessImportRow->sheet }} · Row {{ $businessImportRow->row_number }}</h4><p>{{ $businessImportRow->party?->name ?? 'Workbook summary' }} · {{ ucfirst($businessImportRow->kind) }}</p></div><a class="btn btn-light" href="{{ route('admin.business-imports.index', ['batch_id' => $businessImportRow->business_import_id, 'sheet' => $businessImportRow->sheet]) }}">Back to source ledger</a></div><div class="card-body">
    @if($businessImportRow->issues)<div class="bm-note"><ul>@foreach($businessImportRow->issues as $issue)<li>{{ $issue }}</li>@endforeach</ul></div>@endif
    @if($businessImportRow->business_device_id)<a class="btn btn-light mb-3" href="{{ route('admin.business-management.show', $businessImportRow->business_device_id) }}">View current device assignment</a>@endif
    <table class="table table-striped" data-business-table="standard"><thead><tr><th>Cell</th><th>Column header</th><th>Original value</th><th>Original formula</th><th>Cell type</th></tr></thead><tbody>
        @foreach($businessImportRow->source_cells as $coordinate => $cell)
            @php($column = preg_replace('/\d+$/', '', $coordinate))
            <tr><td>{{ $coordinate }}</td><td>{{ $header?->source_cells[$column.'4']['value'] ?? 'Extra source field' }}</td><td>{{ $cell['value'] }}</td><td>{{ $cell['formula'] }}</td><td>{{ $cell['type'] ?? 'numeric' }}</td></tr>
        @endforeach
    </tbody></table>
</div></div>
@endsection

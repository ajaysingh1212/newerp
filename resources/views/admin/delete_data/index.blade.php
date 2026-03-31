@extends('layouts.admin')
@section('content')

{{-- ✅ SUCCESS --}}
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

{{-- ❌ ERROR --}}
@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

{{-- ⚠️ ROW ERRORS --}}
@if(session('import_errors'))
    <div class="alert alert-warning">
        <strong>⚠️ Some rows failed:</strong>
        <ul>
            @foreach(session('import_errors') as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="row mb-2">
    <div class="col-lg-12 d-flex gap-2">
        <a class="btn btn-success" href="{{ route('admin.delete-data.create') }}">
            <i class="fa fa-plus"></i> Add New
        </a>

        <button class="btn btn-primary" data-toggle="modal" data-target="#csvImportModal">
            Import CSV
        </button>
    </div>
</div>

{{-- ================= CSV MODAL ================= --}}
<div class="modal fade" id="csvImportModal">
    <div class="modal-dialog modal-xl">
        <form method="POST" action="{{ route('admin.delete-data.parseCsvImport') }}" enctype="multipart/form-data">
            @csrf

            <div class="modal-content">
                <div class="modal-header">
                    <h5>CSV Import with Preview</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">

                    {{-- ✅ FILE INPUT FIX --}}
                    <div class="form-group">
                        <label>Select CSV</label>
                        <input type="file" id="csvFile" name="csv_file" class="form-control" required>
                    </div>

                    {{-- PREVIEW --}}
                    <div id="previewSection" style="display:none;">
                        <h6>Preview Data</h6>

                        <div class="table-responsive">
                            <table class="table table-bordered" id="previewTable"></table>
                        </div>

                        <hr>

                        <h6>Field Mapping</h6>
                        <div class="row" id="mappingSection"></div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Import Data</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- ================= TABLE ================= --}}
<div class="card">
    <div class="card-body">
        <table class="table table-bordered ajaxTable datatable datatable-DeleteData">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User Name</th>
                    <th>Number</th>
                    <th>Email</th>
                    <th>Product</th>
                    <th>Vehicle No</th>
                    <th>IMEI</th>
                    <th>Owner Name</th>
                    <th>Owner Phone</th>
                    <th>Delete Date</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

@endsection

@section('scripts')
@parent

<script>

// 🔥 DB fields
let dbFields = [
    'user_name','number','email','product','counter_name',
    'vehicle_no','imei_no','vts_no','delete_date',
    'owner_name','owner_phone','date_of_fitting',
    'expiry_date','sim_number','reason_for_deletion'
];

// 📂 CSV PREVIEW
document.getElementById('csvFile').addEventListener('change', function(e){

    let file = e.target.files[0];

    if(!file) return;

    let reader = new FileReader();

    reader.onload = function(event){

        let text = event.target.result;
        let rows = text.split("\n").map(r => r.split(","));

        let header = rows[0];

        // 🧠 PREVIEW TABLE
        let table = "<thead><tr>";
        header.forEach(h => table += `<th>${h}</th>`);
        table += "</tr></thead><tbody>";

        for(let i=1; i<Math.min(rows.length,6); i++){
            table += "<tr>";
            rows[i].forEach(col => table += `<td>${col}</td>`);
            table += "</tr>";
        }

        table += "</tbody>";

        document.getElementById('previewTable').innerHTML = table;
        document.getElementById('previewSection').style.display = 'block';

        // 🔥 AUTO MAPPING UI
        let mappingHTML = "";

        dbFields.forEach(field => {

            mappingHTML += `
                <div class="col-md-3 mb-2">
                    <label>${field}</label>
                    <select name="mapping[${field}]" class="form-control">
                        <option value="">-- Skip --</option>
                        ${header.map(h => {
                            let val = h.toLowerCase().trim();
                            let selected = (val === field) ? 'selected' : '';
                            return `<option value="${val}" ${selected}>${h}</option>`;
                        }).join("")}
                    </select>
                </div>
            `;
        });

        document.getElementById('mappingSection').innerHTML = mappingHTML;
    };

    reader.readAsText(file);
});


// 📊 DATATABLE
$('.datatable-DeleteData').DataTable({
    processing:true,
    serverSide:true,
    ajax:"{{ route('admin.delete-data.index') }}",
    columns:[
        {data:'id'},
        {data:'user_name'},
        {data:'number'},
        {data:'email'},
        {data:'product'},
        {data:'vehicle_no'},
        {data:'imei_no'},
        {data:'owner_name'},
        {data:'owner_phone'},
        {data:'delete_date'}
    ]
});

</script>

@endsection

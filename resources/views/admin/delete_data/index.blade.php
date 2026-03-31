@extends('layouts.admin')
@section('content')

@can('delete_data_create')
<div style="margin-bottom: 10px;" class="row">
    <div class="col-lg-12 d-flex gap-2">
        <a class="btn btn-success" href="{{ route('admin.delete-data.create') }}">
            <i class="fa fa-plus"></i> Add New Record
        </a>

        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#csvImportModal">
            <i class="fa fa-file-csv"></i> Import CSV
        </button>

        {{-- CSV Import Modal --}}
        <div class="modal fade" id="csvImportModal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-dialog-centered">
                <form method="POST" action="{{ route('admin.delete-data.parseCsvImport') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Import Delete Data via CSV</h5>
                            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                        </div>

                        <div class="modal-body">
                            <div class="form-group">
                                <label>Select CSV File</label>
                                <input type="file" name="csv_file" class="form-control" required>
                                <small class="text-muted">
                                    Columns: user_name, number, email, product, counter_name, vehicle_no, imei_no, vts_no, delete_date
                                </small>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Upload</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
@endcan

<div class="card">
    <div class="card-header">
        <h5>Deleted Data List</h5>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover ajaxTable datatable datatable-DeleteData">
                <thead>
                    <tr>
                        <th width="10"></th>
                        <th>ID</th>
                        <th>User Name</th>
                        <th>Number</th>
                        <th>Email</th>
                        <th>Product</th>
                        <th>Counter Name</th>
                        <th>Vehicle No</th>
                        <th>IMEI No</th>
                        <th>VTS No</th>

                        {{-- NEW FIELDS 🔥 --}}
                        <th>Owner Name</th>
                        <th>Owner Phone</th>
                        <th>SIM No</th>
                        <th>Fitting Date</th>
                        <th>Expiry Date</th>

                        <th>Delete Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

@endsection

@section('scripts')
@parent
<script>
$(function () {

    let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)

    @can('delete_data_delete')
    let deleteButton = {
        text: 'Delete Selected',
        url: "{{ route('admin.delete-data.massDestroy') }}",
        className: 'btn-danger',
        action: function (e, dt, node, config) {

            var ids = $.map(dt.rows({ selected: true }).nodes(), function (entry) {
                return $(entry).data('entry-id')
            });

            if (ids.length === 0) {
                alert('No records selected')
                return
            }

            if (confirm('Are you sure you want to delete selected records?')) {
                $.ajax({
                    headers: {'x-csrf-token': _token},
                    method: 'POST',
                    url: config.url,
                    data: { ids: ids, _method: 'DELETE' }
                }).done(function () { location.reload() })
            }
        }
    }
    dtButtons.push(deleteButton)
    @endcan


    let dtOverrideGlobals = {
        buttons: dtButtons,
        processing: true,
        serverSide: true,
        retrieve: true,
        aaSorting: [],
        ajax: "{{ route('admin.delete-data.index') }}",

        columns: [
            { data: 'placeholder', name: 'placeholder' },
            { data: 'id', name: 'id' },
            { data: 'user_name', name: 'user_name' },
            { data: 'number', name: 'number' },
            { data: 'email', name: 'email' },
            { data: 'product', name: 'product' },
            { data: 'counter_name', name: 'counter_name' },
            { data: 'vehicle_no', name: 'vehicle_no' },
            { data: 'imei_no', name: 'imei_no' },
            { data: 'vts_no', name: 'vts_no' },

            // NEW FIELDS 🔥
            { data: 'owner_name', name: 'owner_name' },
            { data: 'owner_phone', name: 'owner_phone' },
            { data: 'sim_number', name: 'sim_number' },
            { data: 'date_of_fitting', name: 'date_of_fitting' },
            { data: 'expiry_date', name: 'expiry_date' },

            { data: 'delete_date', name: 'delete_date' },
            { data: 'actions', name: 'actions', orderable:false, searchable:false }
        ],

        orderCellsTop: true,
        order: [[ 1, 'desc' ]],
        pageLength: 25,
    }

    let table = $('.datatable-DeleteData').DataTable(dtOverrideGlobals)

    $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
        $($.fn.dataTable.tables(true)).DataTable().columns.adjust();
    });

})
</script>
@endsection

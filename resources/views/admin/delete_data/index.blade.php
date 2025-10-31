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

        {{-- ✅ CSV Import Modal --}}
        <div class="modal fade" id="csvImportModal" tabindex="-1" role="dialog" aria-labelledby="csvImportLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <form method="POST" action="{{ route('admin.delete-data.parseCsvImport') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="csvImportLabel">Import Delete Data via CSV</h5>
                            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                        </div>

                        <div class="modal-body">
                            <div class="form-group">
                                <label for="csv_file">Select CSV File</label>
                                <input type="file" name="csv_file" class="form-control" required>
                                <small class="text-muted">* Must include columns: user_name, number, email, product, counter_name, vehicle_no, imei_no, vts_no, delete_date</small>
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
        Deleted Data List
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-DeleteData">
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
                        <th>Delete Date</th>
                        <th>&nbsp;</th>
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
            { data: 'delete_date', name: 'delete_date' },
            { data: 'actions', name: 'actions' }
        ],
        orderCellsTop: true,
        order: [[ 1, 'desc' ]],
        pageLength: 25,
    }

    let table = $('.datatable-DeleteData').DataTable(dtOverrideGlobals)

    $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
        $($.fn.dataTable.tables(true)).DataTable()
            .columns.adjust();
    });
})
</script>
@endsection

@extends('layouts.admin')
@section('content')

@can('stock_transfer_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('admin.stock-transfers.create') }}">
                {{ trans('global.add') }} {{ trans('cruds.stockTransfer.title_singular') }}
            </a>
            <button class="btn btn-warning" data-toggle="modal" data-target="#csvImportModal">
                {{ trans('global.app_csvImport') }}
            </button>
            @include('csvImport.modal', ['model' => 'StockTransfer', 'route' => 'admin.stock-transfers.parseCsvImport'])
        </div>
    </div>
@endcan

<div class="card">
    <div class="card-header">
        {{ trans('cruds.stockTransfer.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        @include('watermark')
        <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-StockTransfer">
            <thead>
                <tr>
                    <th width="10"></th>
                    <th>{{ trans('cruds.stockTransfer.fields.id') }}</th>
                    <th>{{ trans('cruds.stockTransfer.fields.transfer_date') }}</th>
                    <th>Resealler Type</th>
                    <th>Company Name</th>
                    <th>{{ trans('cruds.user.fields.email') }}</th>
                    <th>{{ trans('cruds.stockTransfer.fields.select_product') }}</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody>
                <!-- Data will be filled by DataTables -->
            </tbody>
        </table>
    </div>
</div>

@endsection

@section('scripts')
@parent
<script>
    $(function () {
        let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
        @can('stock_transfer_delete')
            let deleteButtonTrans = '{{ trans('global.datatables.delete') }}';
            let deleteButton = {
                text: deleteButtonTrans,
                url: "{{ route('admin.stock-transfers.massDestroy') }}",
                className: 'btn-danger',
                action: function (e, dt, node, config) {
                    var ids = $.map(dt.rows({ selected: true }).data(), function (entry) {
                        return entry.id
                    });

                    if (ids.length === 0) {
                        alert('{{ trans('global.datatables.zero_selected') }}')
                        return
                    }

                    if (confirm('{{ trans('global.areYouSure') }}')) {
                        $.ajax({
                            headers: { 'x-csrf-token': _token },
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
            ajax: "{{ route('admin.stock-transfers.index') }}",
            columns: [
                { data: 'placeholder', name: 'placeholder' },
                { data: 'id', name: 'id' },
                { data: 'transfer_date', name: 'transfer_date' },
                { data: 'select_user.roles.0.title', name: 'select_user.roles.title' },  // Access role title from select_user
                { data: 'select_user.company_name', name: 'select_user.company_name' },  // Reseller's company name
                { data: 'select_user.email', name: 'select_user.email' },  // User's email
                { data: 'select_product', name: 'select_products.sku' },
                { data: 'actions', name: '{{ trans('global.actions') }}' }
            ],
            orderCellsTop: true,
            order: [[1, 'desc']],
            pageLength: 100,
        };

        let table = $('.datatable-StockTransfer').DataTable(dtOverrideGlobals);

        $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
            $($.fn.dataTable.tables(true)).DataTable()
                .columns.adjust();
        });
    });
</script>
@endsection

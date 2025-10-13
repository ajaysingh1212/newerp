@extends('layouts.admin')
@section('content')
@can('product_model_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('admin.product-models.create') }}">
                {{ trans('global.add') }} {{ trans('cruds.productModel.title_singular') }}
            </a>
            <button class="btn btn-warning" data-toggle="modal" data-target="#csvImportModal">
                {{ trans('global.app_csvImport') }}
            </button>
            @include('csvImport.modal', ['model' => 'ProductModel', 'route' => 'admin.product-models.parseCsvImport'])
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        {{ trans('cruds.productModel.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        @include('watermark')
        <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-ProductModel">
            <thead>
                <tr>
                    <th width="10">

                    </th>
                    <th>
                        {{ trans('cruds.productModel.fields.id') }}
                    </th>
                    <th>
                        {{ trans('cruds.productModel.fields.product_model') }}
                    </th>
                    <th>
                        {{ trans('cruds.productModel.fields.warranty') }}
                    </th>
                    <th>
                        {{ trans('cruds.productModel.fields.subscription') }}
                    </th>
                    <th>
                        {{ trans('cruds.productModel.fields.amc') }}
                    </th>
                    <th>
                        {{ trans('cruds.productModel.fields.mrp') }}
                    </th>
                    <th>
                        {{ trans('cruds.productModel.fields.cnf_price') }}
                    </th>
                    <th>
                        {{ trans('cruds.productModel.fields.distributor_price') }}
                    </th>
                    <th>
                        {{ trans('cruds.productModel.fields.dealer_price') }}
                    </th>
                    <th>
                        {{ trans('cruds.productModel.fields.customer_price') }}
                    </th>
                    <th>
                        {{ trans('cruds.productModel.fields.status') }}
                    </th>
                    <th>
                        &nbsp;
                    </th>
                </tr>
            </thead>
        </table>
    </div>
</div>



@endsection
@section('scripts')
@parent
<script>
    $(function () {
  let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
@can('product_model_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}';
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.product-models.massDestroy') }}",
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
          headers: {'x-csrf-token': _token},
          method: 'POST',
          url: config.url,
          data: { ids: ids, _method: 'DELETE' }})
          .done(function () { location.reload() })
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
    ajax: "{{ route('admin.product-models.index') }}",
    columns: [
      { data: 'placeholder', name: 'placeholder' },
{ data: 'id', name: 'id' },
{ data: 'product_model', name: 'product_model' },
{ data: 'warranty', name: 'warranty' },
{ data: 'subscription', name: 'subscription' },
{ data: 'amc', name: 'amc' },
{ data: 'mrp', name: 'mrp' },
{ data: 'cnf_price', name: 'cnf_price' },
{ data: 'distributor_price', name: 'distributor_price' },
{ data: 'dealer_price', name: 'dealer_price' },
{ data: 'customer_price', name: 'customer_price' },
{ data: 'status', name: 'status' },
{ data: 'actions', name: '{{ trans('global.actions') }}' }
    ],
    orderCellsTop: true,
    order: [[ 1, 'desc' ]],
    pageLength: 100,
  };
  let table = $('.datatable-ProductModel').DataTable(dtOverrideGlobals);
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
});

</script>
@endsection
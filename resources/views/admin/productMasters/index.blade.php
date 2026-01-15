@extends('layouts.admin')
@section('content')

@can('product_master_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('admin.product-masters.create') }}">
                {{ trans('global.add') }} {{ trans('cruds.productMaster.title_singular') }}
            </a>
            <button class="btn btn-warning" data-toggle="modal" data-target="#csvImportModal">
                {{ trans('global.app_csvImport') }}
            </button>
            @include('csvImport.modal', ['model' => 'ProductMaster', 'route' => 'admin.product-masters.parseCsvImport'])
        </div>
    </div>
@endcan

<div class="card">
    
    <div class="card-header">
        {{ trans('cruds.productMaster.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
         @include('watermark')
        <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-ProductMaster">
            <thead>
                <tr>
                    <th width="10">

                    </th>
                   
                    <th>
                        {{ trans('cruds.productMaster.fields.id') }}
                    </th>
                     <th>
                        SKU
                    </th>
                    <th>
                        {{ trans('cruds.productMaster.fields.product_model') }}
                    </th>
                    <th>
                        {{ trans('cruds.productModel.fields.status') }}
                    </th>
                    <th>
                        {{ trans('cruds.productMaster.fields.imei') }}
                    </th>
   
                    <th>
                        {{ trans('cruds.productMaster.fields.vts') }}
                    </th>
                    <th>
                        {{ trans('cruds.vt.fields.sim_number') }}
                    </th>
                    <th>
                        {{ trans('cruds.productMaster.fields.warranty') }}
                    </th>
                    <th>
                        {{ trans('cruds.productMaster.fields.subscription') }}
                    </th>
                    <th>
                        {{ trans('cruds.productMaster.fields.amc') }}
                    </th>
                    <th>
                        {{ trans('cruds.productMaster.fields.status') }}
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
@can('product_master_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}';
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.product-masters.massDestroy') }}",
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
    ajax: "{{ route('admin.product-masters.index') }}",
    columns: [
      { data: 'placeholder', name: 'placeholder' },
{ data: 'id', name: 'id' },
{ data: 'sku', name: 'sku' },
{ data: 'product_model_product_model', name: 'product_model.product_model' },
{ data: 'product_model.status', name: 'product_model.status' },
{ data: 'imei_imei_number', name: 'imei.imei_number' },

{ data: 'vts_vts_number', name: 'vts.vts_number' },
{ data: 'vts.sim_number', name: 'vts.sim_number' },
{ data: 'warranty', name: 'warranty' },
{ data: 'subscription', name: 'subscription' },
{ data: 'amc', name: 'amc' },
{ data: 'status', name: 'status' },
{ data: 'actions', name: '{{ trans('global.actions') }}' }
    ],
    orderCellsTop: true,
    order: [[ 1, 'desc' ]],
    pageLength: 100,
  };
  let table = $('.datatable-ProductMaster').DataTable(dtOverrideGlobals);
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
});

</script>
@endsection
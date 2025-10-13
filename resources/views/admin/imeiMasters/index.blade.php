@extends('layouts.admin')
@section('content')
@can('imei_master_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('admin.imei-masters.create') }}">
                {{ trans('global.add') }} {{ trans('cruds.imeiMaster.title_singular') }}
            </a>
            <button class="btn btn-warning" data-toggle="modal" data-target="#csvImportModal">
                {{ trans('global.app_csvImport') }}
            </button>
            @include('csvImport.modal', ['model' => 'ImeiMaster', 'route' => 'admin.imei-masters.parseCsvImport'])
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        {{ trans('cruds.imeiMaster.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        @include('watermark')
        <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-ImeiMaster">
            <thead>
                <tr>
                    <th width="10">

                    </th>
                    <th>
                        {{ trans('cruds.imeiMaster.fields.id') }}
                    </th>
                    <th>
                        {{ trans('cruds.imeiMaster.fields.imei_model') }}
                    </th>
                    <th>
                        {{ trans('cruds.imeiModel.fields.status') }}
                    </th>
                    <th>
                        {{ trans('cruds.imeiMaster.fields.imei_number') }}
                    </th>
                    <th>
                        {{ trans('cruds.imeiMaster.fields.status') }}
                    </th>
                    <th>
                        {{ trans('cruds.imeiMaster.fields.product_status') }}
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
@can('imei_master_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}';
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.imei-masters.massDestroy') }}",
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
    ajax: "{{ route('admin.imei-masters.index') }}",
    columns: [
      { data: 'placeholder', name: 'placeholder' },
{ data: 'id', name: 'id' },
{ data: 'imei_model_imei_model_number', name: 'imei_model.imei_model_number' },
{ data: 'imei_model.status', name: 'imei_model.status' },
{ data: 'imei_number', name: 'imei_number' },
{ data: 'status', name: 'status' },
{ data: 'product_status', name: 'product_status' },
{ data: 'actions', name: '{{ trans('global.actions') }}' }
    ],
    orderCellsTop: true,
    order: [[ 1, 'desc' ]],
    pageLength: 100,
  };
  let table = $('.datatable-ImeiMaster').DataTable(dtOverrideGlobals);
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
});

</script>
@endsection
<div class="m-3">
    @can('imei_master_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route('admin.imei-masters.create') }}">
                    {{ trans('global.add') }} {{ trans('cruds.imeiMaster.title_singular') }}
                </a>
            </div>
        </div>
    @endcan
    <div class="card">
        <div class="card-header">
            {{ trans('cruds.imeiMaster.title_singular') }} {{ trans('global.list') }}
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class=" table table-bordered table-striped table-hover datatable datatable-imeiModelImeiMasters">
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
                    <tbody>
                        @foreach($imeiMasters as $key => $imeiMaster)
                            <tr data-entry-id="{{ $imeiMaster->id }}">
                                <td>

                                </td>
                                <td>
                                    {{ $imeiMaster->id ?? '' }}
                                </td>
                                <td>
                                    {{ $imeiMaster->imei_model->imei_model_number ?? '' }}
                                </td>
                                <td>
                                    @if($imeiMaster->imei_model)
                                        {{ $imeiMaster->imei_model::STATUS_SELECT[$imeiMaster->imei_model->status] ?? '' }}
                                    @endif
                                </td>
                                <td>
                                    {{ $imeiMaster->imei_number ?? '' }}
                                </td>
                                <td>
                                    {{ App\Models\ImeiMaster::STATUS_SELECT[$imeiMaster->status] ?? '' }}
                                </td>
                                <td>
                                    {{ App\Models\ImeiMaster::PRODUCT_STATUS_SELECT[$imeiMaster->product_status] ?? '' }}
                                </td>
                                <td>
                                    @can('imei_master_show')
                                        <a class="btn btn-xs btn-primary" href="{{ route('admin.imei-masters.show', $imeiMaster->id) }}">
                                            {{ trans('global.view') }}
                                        </a>
                                    @endcan

                                    @can('imei_master_edit')
                                        <a class="btn btn-xs btn-info" href="{{ route('admin.imei-masters.edit', $imeiMaster->id) }}">
                                            {{ trans('global.edit') }}
                                        </a>
                                    @endcan

                                    @can('imei_master_delete')
                                        <form action="{{ route('admin.imei-masters.destroy', $imeiMaster->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                                            <input type="hidden" name="_method" value="DELETE">
                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                            <input type="submit" class="btn btn-xs btn-danger" value="{{ trans('global.delete') }}">
                                        </form>
                                    @endcan

                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@section('scripts')
@parent
<script>
    $(function () {
  let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
@can('imei_master_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.imei-masters.massDestroy') }}",
    className: 'btn-danger',
    action: function (e, dt, node, config) {
      var ids = $.map(dt.rows({ selected: true }).nodes(), function (entry) {
          return $(entry).data('entry-id')
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

  $.extend(true, $.fn.dataTable.defaults, {
    orderCellsTop: true,
    order: [[ 1, 'desc' ]],
    pageLength: 100,
  });
  let table = $('.datatable-imeiModelImeiMasters:not(.ajaxTable)').DataTable({ buttons: dtButtons })
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
})

</script>
@endsection
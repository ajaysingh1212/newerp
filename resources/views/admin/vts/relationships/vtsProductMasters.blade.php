<div class="m-3">
    @can('product_master_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route('admin.product-masters.create') }}">
                    {{ trans('global.add') }} {{ trans('cruds.productMaster.title_singular') }}
                </a>
            </div>
        </div>
    @endcan
    <div class="card">
        <div class="card-header">
            {{ trans('cruds.productMaster.title_singular') }} {{ trans('global.list') }}
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class=" table table-bordered table-striped table-hover datatable datatable-vtsProductMasters">
                    <thead>
                        <tr>
                            <th width="10">

                            </th>
                            <th>
                                {{ trans('cruds.productMaster.fields.id') }}
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
                                {{ trans('cruds.imeiMaster.fields.product_status') }}
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
                    <tbody>
                        @foreach($productMasters as $key => $productMaster)
                            <tr data-entry-id="{{ $productMaster->id }}">
                                <td>

                                </td>
                                <td>
                                    {{ $productMaster->id ?? '' }}
                                </td>
                                <td>
                                    {{ $productMaster->product_model->product_model ?? '' }}
                                </td>
                                <td>
                                    @if($productMaster->product_model)
                                        {{ $productMaster->product_model::STATUS_SELECT[$productMaster->product_model->status] ?? '' }}
                                    @endif
                                </td>
                                <td>
                                    {{ $productMaster->imei->imei_number ?? '' }}
                                </td>
                                <td>
                                    @if($productMaster->imei)
                                        {{ $productMaster->imei::PRODUCT_STATUS_SELECT[$productMaster->imei->product_status] ?? '' }}
                                    @endif
                                </td>
                                <td>
                                    {{ $productMaster->vts->vts_number ?? '' }}
                                </td>
                                <td>
                                    {{ $productMaster->vts->sim_number ?? '' }}
                                </td>
                                <td>
                                    {{ $productMaster->warranty ?? '' }}
                                </td>
                                <td>
                                    {{ $productMaster->subscription ?? '' }}
                                </td>
                                <td>
                                    {{ $productMaster->amc ?? '' }}
                                </td>
                                <td>
                                    {{ App\Models\ProductMaster::STATUS_SELECT[$productMaster->status] ?? '' }}
                                </td>
                                <td>
                                    @can('product_master_show')
                                        <a class="btn btn-xs btn-primary" href="{{ route('admin.product-masters.show', $productMaster->id) }}">
                                            {{ trans('global.view') }}
                                        </a>
                                    @endcan

                                    @can('product_master_edit')
                                        <a class="btn btn-xs btn-info" href="{{ route('admin.product-masters.edit', $productMaster->id) }}">
                                            {{ trans('global.edit') }}
                                        </a>
                                    @endcan

                                    @can('product_master_delete')
                                        <form action="{{ route('admin.product-masters.destroy', $productMaster->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
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
@can('product_master_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.product-masters.massDestroy') }}",
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
  let table = $('.datatable-vtsProductMasters:not(.ajaxTable)').DataTable({ buttons: dtButtons })
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
})

</script>
@endsection
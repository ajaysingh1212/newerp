<div class="m-3">
    @can('stock_transfer_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route('admin.stock-transfers.create') }}">
                    {{ trans('global.add') }} {{ trans('cruds.stockTransfer.title_singular') }}
                </a>
            </div>
        </div>
    @endcan
    <div class="card">
        <div class="card-header">
            {{ trans('cruds.stockTransfer.title_singular') }} {{ trans('global.list') }}
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class=" table table-bordered table-striped table-hover datatable datatable-selectProductStockTransfers">
                    <thead>
                        <tr>
                            <th width="10">

                            </th>
                            <th>
                                {{ trans('cruds.stockTransfer.fields.id') }}
                            </th>
                            <th>
                                {{ trans('cruds.stockTransfer.fields.transfer_date') }}
                            </th>
                            <th>
                                {{ trans('cruds.stockTransfer.fields.select_user') }}
                            </th>
                            <th>
                                {{ trans('cruds.stockTransfer.fields.reseller') }}
                            </th>
                            <th>
                                {{ trans('cruds.user.fields.email') }}
                            </th>
                            <th>
                                {{ trans('cruds.stockTransfer.fields.select_product') }}
                            </th>
                            <th>
                                &nbsp;
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($stockTransfers as $key => $stockTransfer)
                            <tr data-entry-id="{{ $stockTransfer->id }}">
                                <td>

                                </td>
                                <td>
                                    {{ $stockTransfer->id ?? '' }}
                                </td>
                                <td>
                                    {{ $stockTransfer->transfer_date ?? '' }}
                                </td>
                                <td>
                                    {{ $stockTransfer->select_user->title ?? '' }}
                                </td>
                                <td>
                                    {{ $stockTransfer->reseller->name ?? '' }}
                                </td>
                                <td>
                                    {{ $stockTransfer->reseller->email ?? '' }}
                                </td>
                                <td>
                                    @foreach($stockTransfer->select_products as $key => $item)
                                        <span class="badge badge-info">{{ $item->sku }}</span>
                                    @endforeach
                                </td>
                                <td>
                                    @can('stock_transfer_show')
                                        <a class="btn btn-xs btn-primary" href="{{ route('admin.stock-transfers.show', $stockTransfer->id) }}">
                                            {{ trans('global.view') }}
                                        </a>
                                    @endcan

                                    @can('stock_transfer_edit')
                                        <a class="btn btn-xs btn-info" href="{{ route('admin.stock-transfers.edit', $stockTransfer->id) }}">
                                            {{ trans('global.edit') }}
                                        </a>
                                    @endcan

                                    @can('stock_transfer_delete')
                                        <form action="{{ route('admin.stock-transfers.destroy', $stockTransfer->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
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
@can('stock_transfer_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.stock-transfers.massDestroy') }}",
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
  let table = $('.datatable-selectProductStockTransfers:not(.ajaxTable)').DataTable({ buttons: dtButtons })
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
})

</script>
@endsection
<div class="m-3">
    @can('recharge_request_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route('admin.recharge-requests.create') }}">
                    {{ trans('global.add') }} {{ trans('cruds.rechargeRequest.title_singular') }}
                </a>
            </div>
        </div>
    @endcan
    <div class="card">
        <div class="card-header">
            {{ trans('cruds.rechargeRequest.title_singular') }} {{ trans('global.list') }}
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class=" table table-bordered table-striped table-hover datatable datatable-productRechargeRequests">
                    <thead>
                        <tr>
                            <th width="10">

                            </th>
                            <th>
                                {{ trans('cruds.rechargeRequest.fields.id') }}
                            </th>
                            <th>
                                {{ trans('cruds.rechargeRequest.fields.user') }}
                            </th>
                            <th>
                                {{ trans('cruds.rechargeRequest.fields.vehicle_number') }}
                            </th>
                            <th>
                                {{ trans('cruds.rechargeRequest.fields.product') }}
                            </th>
                            <th>
                                {{ trans('cruds.currentStock.fields.product_name') }}
                            </th>
                            <th>
                                {{ trans('cruds.rechargeRequest.fields.select_recharge') }}
                            </th>
                            <th>
                                {{ trans('cruds.rechargePlan.fields.plan_name') }}
                            </th>
                            <th>
                                {{ trans('cruds.rechargeRequest.fields.attechment') }}
                            </th>
                            <th>
                                &nbsp;
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rechargeRequests as $key => $rechargeRequest)
                            <tr data-entry-id="{{ $rechargeRequest->id }}">
                                <td>

                                </td>
                                <td>
                                    {{ $rechargeRequest->id ?? '' }}
                                </td>
                                <td>
                                    {{ $rechargeRequest->user->name ?? '' }}
                                </td>
                                <td>
                                    {{ $rechargeRequest->vehicle_number ?? '' }}
                                </td>
                                <td>
                                    {{ $rechargeRequest->product->sku ?? '' }}
                                </td>
                                <td>
                                    {{ $rechargeRequest->product->product_name ?? '' }}
                                </td>
                                <td>
                                    {{ $rechargeRequest->select_recharge->type ?? '' }}
                                </td>
                                <td>
                                    {{ $rechargeRequest->select_recharge->plan_name ?? '' }}
                                </td>
                                <td>
                                    @if($rechargeRequest->attechment)
                                        <a href="{{ $rechargeRequest->attechment->getUrl() }}" target="_blank">
                                            {{ trans('global.view_file') }}
                                        </a>
                                    @endif
                                </td>
                                <td>
                                    @can('recharge_request_show')
                                        <a class="btn btn-xs btn-primary" href="{{ route('admin.recharge-requests.show', $rechargeRequest->id) }}">
                                            {{ trans('global.view') }}
                                        </a>
                                    @endcan

                                    @can('recharge_request_edit')
                                        <a class="btn btn-xs btn-info" href="{{ route('admin.recharge-requests.edit', $rechargeRequest->id) }}">
                                            {{ trans('global.edit') }}
                                        </a>
                                    @endcan

                                    @can('recharge_request_delete')
                                        <form action="{{ route('admin.recharge-requests.destroy', $rechargeRequest->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
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
@can('recharge_request_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.recharge-requests.massDestroy') }}",
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
  let table = $('.datatable-productRechargeRequests:not(.ajaxTable)').DataTable({ buttons: dtButtons })
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
})

</script>
@endsection
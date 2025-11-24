<div class="m-3">
    @can('withdrawal_request_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route('admin.withdrawal-requests.create') }}">
                    {{ trans('global.add') }} {{ trans('cruds.withdrawalRequest.title_singular') }}
                </a>
            </div>
        </div>
    @endcan
    <div class="card">
        <div class="card-header">
            {{ trans('cruds.withdrawalRequest.title_singular') }} {{ trans('global.list') }}
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class=" table table-bordered table-striped table-hover datatable datatable-investmentWithdrawalRequests">
                    <thead>
                        <tr>
                            <th width="10">

                            </th>
                            <th>
                                {{ trans('cruds.withdrawalRequest.fields.id') }}
                            </th>
                            <th>
                                {{ trans('cruds.withdrawalRequest.fields.select_investor') }}
                            </th>
                            <th>
                                {{ trans('cruds.registration.fields.aadhaar_number') }}
                            </th>
                            <th>
                                {{ trans('cruds.registration.fields.pan_number') }}
                            </th>
                            <th>
                                {{ trans('cruds.withdrawalRequest.fields.investment') }}
                            </th>
                            <th>
                                {{ trans('cruds.investment.fields.secure_interest_percent') }}
                            </th>
                            <th>
                                {{ trans('cruds.investment.fields.total_interest_percent') }}
                            </th>
                            <th>
                                {{ trans('cruds.investment.fields.start_date') }}
                            </th>
                            <th>
                                {{ trans('cruds.withdrawalRequest.fields.amount') }}
                            </th>
                            <th>
                                {{ trans('cruds.withdrawalRequest.fields.type') }}
                            </th>
                            <th>
                                {{ trans('cruds.withdrawalRequest.fields.status') }}
                            </th>
                            <th>
                                {{ trans('cruds.withdrawalRequest.fields.processing_hours') }}
                            </th>
                            <th>
                                {{ trans('cruds.withdrawalRequest.fields.requested_at') }}
                            </th>
                            <th>
                                {{ trans('cruds.withdrawalRequest.fields.approved_at') }}
                            </th>
                            <th>
                                &nbsp;
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($withdrawalRequests as $key => $withdrawalRequest)
                            <tr data-entry-id="{{ $withdrawalRequest->id }}">
                                <td>

                                </td>
                                <td>
                                    {{ $withdrawalRequest->id ?? '' }}
                                </td>
                                <td>
                                    {{ $withdrawalRequest->select_investor->reg ?? '' }}
                                </td>
                                <td>
                                    {{ $withdrawalRequest->select_investor->aadhaar_number ?? '' }}
                                </td>
                                <td>
                                    {{ $withdrawalRequest->select_investor->pan_number ?? '' }}
                                </td>
                                <td>
                                    {{ $withdrawalRequest->investment->principal_amount ?? '' }}
                                </td>
                                <td>
                                    {{ $withdrawalRequest->investment->secure_interest_percent ?? '' }}
                                </td>
                                <td>
                                    {{ $withdrawalRequest->investment->total_interest_percent ?? '' }}
                                </td>
                                <td>
                                    {{ $withdrawalRequest->investment->start_date ?? '' }}
                                </td>
                                <td>
                                    {{ $withdrawalRequest->amount ?? '' }}
                                </td>
                                <td>
                                    {{ App\Models\WithdrawalRequest::TYPE_SELECT[$withdrawalRequest->type] ?? '' }}
                                </td>
                                <td>
                                    {{ App\Models\WithdrawalRequest::STATUS_SELECT[$withdrawalRequest->status] ?? '' }}
                                </td>
                                <td>
                                    {{ $withdrawalRequest->processing_hours ?? '' }}
                                </td>
                                <td>
                                    {{ $withdrawalRequest->requested_at ?? '' }}
                                </td>
                                <td>
                                    {{ $withdrawalRequest->approved_at ?? '' }}
                                </td>
                                <td>
                                    @can('withdrawal_request_show')
                                        <a class="btn btn-xs btn-primary" href="{{ route('admin.withdrawal-requests.show', $withdrawalRequest->id) }}">
                                            {{ trans('global.view') }}
                                        </a>
                                    @endcan

                                    @can('withdrawal_request_edit')
                                        <a class="btn btn-xs btn-info" href="{{ route('admin.withdrawal-requests.edit', $withdrawalRequest->id) }}">
                                            {{ trans('global.edit') }}
                                        </a>
                                    @endcan

                                    @can('withdrawal_request_delete')
                                        <form action="{{ route('admin.withdrawal-requests.destroy', $withdrawalRequest->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
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
@can('withdrawal_request_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.withdrawal-requests.massDestroy') }}",
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
  let table = $('.datatable-investmentWithdrawalRequests:not(.ajaxTable)').DataTable({ buttons: dtButtons })
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
})

</script>
@endsection
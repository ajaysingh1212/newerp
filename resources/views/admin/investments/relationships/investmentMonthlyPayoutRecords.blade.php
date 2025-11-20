<div class="m-3">
    @can('monthly_payout_record_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route('admin.monthly-payout-records.create') }}">
                    {{ trans('global.add') }} {{ trans('cruds.monthlyPayoutRecord.title_singular') }}
                </a>
            </div>
        </div>
    @endcan
    <div class="card">
        <div class="card-header">
            {{ trans('cruds.monthlyPayoutRecord.title_singular') }} {{ trans('global.list') }}
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class=" table table-bordered table-striped table-hover datatable datatable-investmentMonthlyPayoutRecords">
                    <thead>
                        <tr>
                            <th width="10">

                            </th>
                            <th>
                                {{ trans('cruds.monthlyPayoutRecord.fields.id') }}
                            </th>
                            <th>
                                {{ trans('cruds.monthlyPayoutRecord.fields.investment') }}
                            </th>
                            <th>
                                {{ trans('cruds.investment.fields.secure_interest_percent') }}
                            </th>
                            <th>
                                {{ trans('cruds.investment.fields.market_interest_percent') }}
                            </th>
                            <th>
                                {{ trans('cruds.investment.fields.total_interest_percent') }}
                            </th>
                            <th>
                                {{ trans('cruds.investment.fields.start_date') }}
                            </th>
                            <th>
                                {{ trans('cruds.investment.fields.lockin_end_date') }}
                            </th>
                            <th>
                                {{ trans('cruds.investment.fields.next_payout_date') }}
                            </th>
                            <th>
                                {{ trans('cruds.investment.fields.status') }}
                            </th>
                            <th>
                                {{ trans('cruds.monthlyPayoutRecord.fields.investor') }}
                            </th>
                            <th>
                                {{ trans('cruds.registration.fields.referral_code') }}
                            </th>
                            <th>
                                {{ trans('cruds.registration.fields.aadhaar_number') }}
                            </th>
                            <th>
                                {{ trans('cruds.monthlyPayoutRecord.fields.secure_interest_amount') }}
                            </th>
                            <th>
                                {{ trans('cruds.monthlyPayoutRecord.fields.market_interest_amount') }}
                            </th>
                            <th>
                                {{ trans('cruds.monthlyPayoutRecord.fields.total_payout_amount') }}
                            </th>
                            <th>
                                {{ trans('cruds.monthlyPayoutRecord.fields.month_for') }}
                            </th>
                            <th>
                                {{ trans('cruds.monthlyPayoutRecord.fields.status') }}
                            </th>
                            <th>
                                &nbsp;
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($monthlyPayoutRecords as $key => $monthlyPayoutRecord)
                            <tr data-entry-id="{{ $monthlyPayoutRecord->id }}">
                                <td>

                                </td>
                                <td>
                                    {{ $monthlyPayoutRecord->id ?? '' }}
                                </td>
                                <td>
                                    {{ $monthlyPayoutRecord->investment->principal_amount ?? '' }}
                                </td>
                                <td>
                                    {{ $monthlyPayoutRecord->investment->secure_interest_percent ?? '' }}
                                </td>
                                <td>
                                    {{ $monthlyPayoutRecord->investment->market_interest_percent ?? '' }}
                                </td>
                                <td>
                                    {{ $monthlyPayoutRecord->investment->total_interest_percent ?? '' }}
                                </td>
                                <td>
                                    {{ $monthlyPayoutRecord->investment->start_date ?? '' }}
                                </td>
                                <td>
                                    {{ $monthlyPayoutRecord->investment->lockin_end_date ?? '' }}
                                </td>
                                <td>
                                    {{ $monthlyPayoutRecord->investment->next_payout_date ?? '' }}
                                </td>
                                <td>
                                    @if($monthlyPayoutRecord->investment)
                                        {{ $monthlyPayoutRecord->investment::STATUS_SELECT[$monthlyPayoutRecord->investment->status] ?? '' }}
                                    @endif
                                </td>
                                <td>
                                    {{ $monthlyPayoutRecord->investor->reg ?? '' }}
                                </td>
                                <td>
                                    {{ $monthlyPayoutRecord->investor->referral_code ?? '' }}
                                </td>
                                <td>
                                    {{ $monthlyPayoutRecord->investor->aadhaar_number ?? '' }}
                                </td>
                                <td>
                                    {{ $monthlyPayoutRecord->secure_interest_amount ?? '' }}
                                </td>
                                <td>
                                    {{ $monthlyPayoutRecord->market_interest_amount ?? '' }}
                                </td>
                                <td>
                                    {{ $monthlyPayoutRecord->total_payout_amount ?? '' }}
                                </td>
                                <td>
                                    {{ $monthlyPayoutRecord->month_for ?? '' }}
                                </td>
                                <td>
                                    {{ App\Models\MonthlyPayoutRecord::STATUS_SELECT[$monthlyPayoutRecord->status] ?? '' }}
                                </td>
                                <td>
                                    @can('monthly_payout_record_show')
                                        <a class="btn btn-xs btn-primary" href="{{ route('admin.monthly-payout-records.show', $monthlyPayoutRecord->id) }}">
                                            {{ trans('global.view') }}
                                        </a>
                                    @endcan

                                    @can('monthly_payout_record_edit')
                                        <a class="btn btn-xs btn-info" href="{{ route('admin.monthly-payout-records.edit', $monthlyPayoutRecord->id) }}">
                                            {{ trans('global.edit') }}
                                        </a>
                                    @endcan

                                    @can('monthly_payout_record_delete')
                                        <form action="{{ route('admin.monthly-payout-records.destroy', $monthlyPayoutRecord->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
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
@can('monthly_payout_record_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.monthly-payout-records.massDestroy') }}",
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
  let table = $('.datatable-investmentMonthlyPayoutRecords:not(.ajaxTable)').DataTable({ buttons: dtButtons })
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
})

</script>
@endsection
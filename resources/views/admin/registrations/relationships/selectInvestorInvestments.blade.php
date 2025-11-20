<div class="m-3">
    @can('investment_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route('admin.investments.create') }}">
                    {{ trans('global.add') }} {{ trans('cruds.investment.title_singular') }}
                </a>
            </div>
        </div>
    @endcan
    <div class="card">
        <div class="card-header">
            {{ trans('cruds.investment.title_singular') }} {{ trans('global.list') }}
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class=" table table-bordered table-striped table-hover datatable datatable-selectInvestorInvestments">
                    <thead>
                        <tr>
                            <th width="10">

                            </th>
                            <th>
                                {{ trans('cruds.investment.fields.id') }}
                            </th>
                            <th>
                                {{ trans('cruds.investment.fields.select_investor') }}
                            </th>
                            <th>
                                {{ trans('cruds.registration.fields.referral_code') }}
                            </th>
                            <th>
                                {{ trans('cruds.registration.fields.aadhaar_number') }}
                            </th>
                            <th>
                                {{ trans('cruds.registration.fields.pan_number') }}
                            </th>
                            <th>
                                {{ trans('cruds.investment.fields.select_plan') }}
                            </th>
                            <th>
                                {{ trans('cruds.plan.fields.total_interest_percent') }}
                            </th>
                            <th>
                                {{ trans('cruds.plan.fields.min_invest_amount') }}
                            </th>
                            <th>
                                {{ trans('cruds.plan.fields.max_invest_amount') }}
                            </th>
                            <th>
                                {{ trans('cruds.investment.fields.principal_amount') }}
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
                                &nbsp;
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($investments as $key => $investment)
                            <tr data-entry-id="{{ $investment->id }}">
                                <td>

                                </td>
                                <td>
                                    {{ $investment->id ?? '' }}
                                </td>
                                <td>
                                    {{ $investment->select_investor->reg ?? '' }}
                                </td>
                                <td>
                                    {{ $investment->select_investor->referral_code ?? '' }}
                                </td>
                                <td>
                                    {{ $investment->select_investor->aadhaar_number ?? '' }}
                                </td>
                                <td>
                                    {{ $investment->select_investor->pan_number ?? '' }}
                                </td>
                                <td>
                                    {{ $investment->select_plan->plan_name ?? '' }}
                                </td>
                                <td>
                                    {{ $investment->select_plan->total_interest_percent ?? '' }}
                                </td>
                                <td>
                                    {{ $investment->select_plan->min_invest_amount ?? '' }}
                                </td>
                                <td>
                                    {{ $investment->select_plan->max_invest_amount ?? '' }}
                                </td>
                                <td>
                                    {{ $investment->principal_amount ?? '' }}
                                </td>
                                <td>
                                    {{ $investment->secure_interest_percent ?? '' }}
                                </td>
                                <td>
                                    {{ $investment->market_interest_percent ?? '' }}
                                </td>
                                <td>
                                    {{ $investment->total_interest_percent ?? '' }}
                                </td>
                                <td>
                                    {{ $investment->start_date ?? '' }}
                                </td>
                                <td>
                                    {{ $investment->lockin_end_date ?? '' }}
                                </td>
                                <td>
                                    {{ $investment->next_payout_date ?? '' }}
                                </td>
                                <td>
                                    {{ App\Models\Investment::STATUS_SELECT[$investment->status] ?? '' }}
                                </td>
                                <td>
                                    @can('investment_show')
                                        <a class="btn btn-xs btn-primary" href="{{ route('admin.investments.show', $investment->id) }}">
                                            {{ trans('global.view') }}
                                        </a>
                                    @endcan

                                    @can('investment_edit')
                                        <a class="btn btn-xs btn-info" href="{{ route('admin.investments.edit', $investment->id) }}">
                                            {{ trans('global.edit') }}
                                        </a>
                                    @endcan

                                    @can('investment_delete')
                                        <form action="{{ route('admin.investments.destroy', $investment->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
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
@can('investment_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.investments.massDestroy') }}",
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
  let table = $('.datatable-selectInvestorInvestments:not(.ajaxTable)').DataTable({ buttons: dtButtons })
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
})

</script>
@endsection
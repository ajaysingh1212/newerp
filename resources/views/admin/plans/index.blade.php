@extends('layouts.admin')
@section('content')
@can('plan_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('admin.plans.create') }}">
                {{ trans('global.add') }} {{ trans('cruds.plan.title_singular') }}
            </a>
            <button class="btn btn-warning" data-toggle="modal" data-target="#csvImportModal">
                {{ trans('global.app_csvImport') }}
            </button>
            @include('csvImport.modal', ['model' => 'Plan', 'route' => 'admin.plans.parseCsvImport'])
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        {{ trans('cruds.plan.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover datatable datatable-Plan">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('cruds.plan.fields.id') }}
                        </th>
                        <th>
                            {{ trans('cruds.plan.fields.plan_name') }}
                        </th>
                        <th>
                            {{ trans('cruds.plan.fields.secure_interest_percent') }}
                        </th>
                        <th>
                            {{ trans('cruds.plan.fields.market_interest_percent') }}
                        </th>
                        <th>
                            {{ trans('cruds.plan.fields.total_interest_percent') }}
                        </th>
                        <th>
                            {{ trans('cruds.plan.fields.payout_frequency') }}
                        </th>
                        <th>
                            {{ trans('cruds.plan.fields.min_invest_amount') }}
                        </th>
                        <th>
                            {{ trans('cruds.plan.fields.max_invest_amount') }}
                        </th>
                        <th>
                            {{ trans('cruds.plan.fields.lockin_days') }}
                        </th>
                        <th>
                            {{ trans('cruds.plan.fields.withdraw_processing_hours') }}
                        </th>
                        <th>
                            {{ trans('cruds.plan.fields.status') }}
                        </th>
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($plans as $key => $plan)
                        <tr data-entry-id="{{ $plan->id }}">
                            <td>

                            </td>
                            <td>
                                {{ $plan->id ?? '' }}
                            </td>
                            <td>
                                {{ $plan->plan_name ?? '' }}
                            </td>
                            <td>
                                {{ $plan->secure_interest_percent ?? '' }}
                            </td>
                            <td>
                                {{ $plan->market_interest_percent ?? '' }}
                            </td>
                            <td>
                                {{ $plan->total_interest_percent ?? '' }}
                            </td>
                            <td>
                                {{ App\Models\Plan::PAYOUT_FREQUENCY_SELECT[$plan->payout_frequency] ?? '' }}
                            </td>
                            <td>
                                {{ $plan->min_invest_amount ?? '' }}
                            </td>
                            <td>
                                {{ $plan->max_invest_amount ?? '' }}
                            </td>
                            <td>
                                {{ $plan->lockin_days ?? '' }}
                            </td>
                            <td>
                                {{ $plan->withdraw_processing_hours ?? '' }}
                            </td>
                            <td>
                                {{ App\Models\Plan::STATUS_SELECT[$plan->status] ?? '' }}
                            </td>
                            <td>
                                @can('plan_show')
                                    <a class="btn btn-xs btn-primary" href="{{ route('admin.plans.show', $plan->id) }}">
                                        {{ trans('global.view') }}
                                    </a>
                                @endcan

                                @can('plan_edit')
                                    <a class="btn btn-xs btn-info" href="{{ route('admin.plans.edit', $plan->id) }}">
                                        {{ trans('global.edit') }}
                                    </a>
                                @endcan

                                @can('plan_delete')
                                    <form action="{{ route('admin.plans.destroy', $plan->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
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



@endsection
@section('scripts')
@parent
<script>
    $(function () {
  let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
@can('plan_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.plans.massDestroy') }}",
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
  let table = $('.datatable-Plan:not(.ajaxTable)').DataTable({ buttons: dtButtons })
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
})

</script>
@endsection
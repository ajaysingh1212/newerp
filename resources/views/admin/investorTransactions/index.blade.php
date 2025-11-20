@extends('layouts.admin')
@section('content')
@can('investor_transaction_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('admin.investor-transactions.create') }}">
                {{ trans('global.add') }} {{ trans('cruds.investorTransaction.title_singular') }}
            </a>
            <button class="btn btn-warning" data-toggle="modal" data-target="#csvImportModal">
                {{ trans('global.app_csvImport') }}
            </button>
            @include('csvImport.modal', ['model' => 'InvestorTransaction', 'route' => 'admin.investor-transactions.parseCsvImport'])
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        {{ trans('cruds.investorTransaction.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover datatable datatable-InvestorTransaction">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('cruds.investorTransaction.fields.id') }}
                        </th>
                        <th>
                            {{ trans('cruds.investorTransaction.fields.investor') }}
                        </th>
                        <th>
                            {{ trans('cruds.investment.fields.secure_interest_percent') }}
                        </th>
                        <th>
                            {{ trans('cruds.investment.fields.total_interest_percent') }}
                        </th>
                        <th>
                            {{ trans('cruds.investorTransaction.fields.investment') }}
                        </th>
                        <th>
                            {{ trans('cruds.registration.fields.aadhaar_number') }}
                        </th>
                        <th>
                            {{ trans('cruds.registration.fields.pan_number') }}
                        </th>
                        <th>
                            {{ trans('cruds.investorTransaction.fields.transaction_type') }}
                        </th>
                        <th>
                            {{ trans('cruds.investorTransaction.fields.amount') }}
                        </th>
                        <th>
                            {{ trans('cruds.investorTransaction.fields.status') }}
                        </th>
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($investorTransactions as $key => $investorTransaction)
                        <tr data-entry-id="{{ $investorTransaction->id }}">
                            <td>

                            </td>
                            <td>
                                {{ $investorTransaction->id ?? '' }}
                            </td>
                            <td>
                                {{ $investorTransaction->investor->principal_amount ?? '' }}
                            </td>
                            <td>
                                {{ $investorTransaction->investor->secure_interest_percent ?? '' }}
                            </td>
                            <td>
                                {{ $investorTransaction->investor->total_interest_percent ?? '' }}
                            </td>
                            <td>
                                {{ $investorTransaction->investment->reg ?? '' }}
                            </td>
                            <td>
                                {{ $investorTransaction->investment->aadhaar_number ?? '' }}
                            </td>
                            <td>
                                {{ $investorTransaction->investment->pan_number ?? '' }}
                            </td>
                            <td>
                                {{ App\Models\InvestorTransaction::TRANSACTION_TYPE_SELECT[$investorTransaction->transaction_type] ?? '' }}
                            </td>
                            <td>
                                {{ $investorTransaction->amount ?? '' }}
                            </td>
                            <td>
                                {{ App\Models\InvestorTransaction::STATUS_SELECT[$investorTransaction->status] ?? '' }}
                            </td>
                            <td>
                                @can('investor_transaction_show')
                                    <a class="btn btn-xs btn-primary" href="{{ route('admin.investor-transactions.show', $investorTransaction->id) }}">
                                        {{ trans('global.view') }}
                                    </a>
                                @endcan

                                @can('investor_transaction_edit')
                                    <a class="btn btn-xs btn-info" href="{{ route('admin.investor-transactions.edit', $investorTransaction->id) }}">
                                        {{ trans('global.edit') }}
                                    </a>
                                @endcan

                                @can('investor_transaction_delete')
                                    <form action="{{ route('admin.investor-transactions.destroy', $investorTransaction->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
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
@can('investor_transaction_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.investor-transactions.massDestroy') }}",
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
  let table = $('.datatable-InvestorTransaction:not(.ajaxTable)').DataTable({ buttons: dtButtons })
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
})

</script>
@endsection
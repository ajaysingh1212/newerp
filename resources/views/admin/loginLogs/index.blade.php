@extends('layouts.admin')
@section('content')
@can('login_log_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('admin.login-logs.create') }}">
                {{ trans('global.add') }} {{ trans('cruds.loginLog.title_singular') }}
            </a>
            <button class="btn btn-warning" data-toggle="modal" data-target="#csvImportModal">
                {{ trans('global.app_csvImport') }}
            </button>
            @include('csvImport.modal', ['model' => 'LoginLog', 'route' => 'admin.login-logs.parseCsvImport'])
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        {{ trans('cruds.loginLog.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover datatable datatable-LoginLog">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('cruds.loginLog.fields.id') }}
                        </th>
                        <th>
                            {{ trans('cruds.loginLog.fields.use') }}
                        </th>
                        <th>
                            {{ trans('cruds.user.fields.email') }}
                        </th>
                        <th>
                            {{ trans('cruds.loginLog.fields.ip_address') }}
                        </th>
                        <th>
                            {{ trans('cruds.loginLog.fields.device') }}
                        </th>
                        <th>
                            {{ trans('cruds.loginLog.fields.location') }}
                        </th>
                        <th>
                            {{ trans('cruds.loginLog.fields.logged_in_at') }}
                        </th>
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($loginLogs as $key => $loginLog)
                        <tr data-entry-id="{{ $loginLog->id }}">
                            <td>

                            </td>
                            <td>
                                {{ $loginLog->id ?? '' }}
                            </td>
                            <td>
                                {{ $loginLog->use->name ?? '' }}
                            </td>
                            <td>
                                {{ $loginLog->use->email ?? '' }}
                            </td>
                            <td>
                                {{ $loginLog->ip_address ?? '' }}
                            </td>
                            <td>
                                {{ $loginLog->device ?? '' }}
                            </td>
                            <td>
                                {{ $loginLog->location ?? '' }}
                            </td>
                            <td>
                                {{ $loginLog->logged_in_at ?? '' }}
                            </td>
                            <td>
                                @can('login_log_show')
                                    <a class="btn btn-xs btn-primary" href="{{ route('admin.login-logs.show', $loginLog->id) }}">
                                        {{ trans('global.view') }}
                                    </a>
                                @endcan

                                @can('login_log_edit')
                                    <a class="btn btn-xs btn-info" href="{{ route('admin.login-logs.edit', $loginLog->id) }}">
                                        {{ trans('global.edit') }}
                                    </a>
                                @endcan

                                @can('login_log_delete')
                                    <form action="{{ route('admin.login-logs.destroy', $loginLog->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
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
@can('login_log_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.login-logs.massDestroy') }}",
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
  let table = $('.datatable-LoginLog:not(.ajaxTable)').DataTable({ buttons: dtButtons })
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
})

</script>
@endsection
@extends('layouts.admin')
@section('content')
@can('app_download_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('admin.app-downloads.create') }}">
                {{ trans('global.add') }} {{ trans('cruds.appDownload.title_singular') }}
            </a>
            <button class="btn btn-warning" data-toggle="modal" data-target="#csvImportModal">
                {{ trans('global.app_csvImport') }}
            </button>
            @include('csvImport.modal', ['model' => 'AppDownload', 'route' => 'admin.app-downloads.parseCsvImport'])
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        {{ trans('cruds.appDownload.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover datatable datatable-AppDownload">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('cruds.appDownload.fields.id') }}
                        </th>
                        <th>
                            {{ trans('cruds.appDownload.fields.title') }}
                        </th>
                        <th>
                            {{ trans('cruds.appDownload.fields.user') }}
                        </th>
                        <th>
                            {{ trans('cruds.appDownload.fields.password') }}
                        </th>
                        <th>
                            {{ trans('cruds.appDownload.fields.appurl') }}
                        </th>
                        <th>
                            {{ trans('cruds.appDownload.fields.appfile') }}
                        </th>
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($appDownloads as $key => $appDownload)
                        <tr data-entry-id="{{ $appDownload->id }}">
                            <td>

                            </td>
                            <td>
                                {{ $appDownload->id ?? '' }}
                            </td>
                            <td>
                                {{ $appDownload->title ?? '' }}
                            </td>
                            <td>
                                {{ $appDownload->user ?? '' }}
                            </td>
                            <td>
                                {{ $appDownload->password ?? '' }}
                            </td>
                            <td>
                                {{ $appDownload->appurl ?? '' }}
                            </td>
                            <td>
                                @if($appDownload->appfile)
                                    <a href="{{ $appDownload->appfile->getUrl() }}" target="_blank">
                                        {{ trans('global.view_file') }}
                                    </a>
                                @endif
                            </td>
                            <td>
                                @can('app_download_show')
                                    <a class="btn btn-xs btn-primary" href="{{ route('admin.app-downloads.show', $appDownload->id) }}">
                                        {{ trans('global.view') }}
                                    </a>
                                @endcan

                                @can('app_download_edit')
                                    <a class="btn btn-xs btn-info" href="{{ route('admin.app-downloads.edit', $appDownload->id) }}">
                                        {{ trans('global.edit') }}
                                    </a>
                                @endcan

                                @can('app_download_delete')
                                    <form action="{{ route('admin.app-downloads.destroy', $appDownload->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
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
@can('app_download_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.app-downloads.massDestroy') }}",
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
  let table = $('.datatable-AppDownload:not(.ajaxTable)').DataTable({ buttons: dtButtons })
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
})

</script>
@endsection
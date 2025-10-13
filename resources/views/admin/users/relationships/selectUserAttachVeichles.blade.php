<div class="m-3">
    @can('attach_veichle_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route('admin.attach-veichles.create') }}">
                    {{ trans('global.add') }} {{ trans('cruds.attachVeichle.title_singular') }}
                </a>
            </div>
        </div>
    @endcan
    <div class="card">
        <div class="card-header">
            {{ trans('cruds.attachVeichle.title_singular') }} {{ trans('global.list') }}
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class=" table table-bordered table-striped table-hover datatable datatable-selectUserAttachVeichles">
                    <thead>
                        <tr>
                            <th width="10">

                            </th>
                            <th>
                                {{ trans('cruds.attachVeichle.fields.id') }}
                            </th>
                            <th>
                                {{ trans('cruds.attachVeichle.fields.select_user') }}
                            </th>
                            <th>
                                {{ trans('cruds.user.fields.email') }}
                            </th>
                            <th>
                                {{ trans('cruds.attachVeichle.fields.vehicle') }}
                            </th>
                            <th>
                                &nbsp;
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($attachVeichles as $key => $attachVeichle)
                            <tr data-entry-id="{{ $attachVeichle->id }}">
                                <td>

                                </td>
                                <td>
                                    {{ $attachVeichle->id ?? '' }}
                                </td>
                                <td>
                                    {{ $attachVeichle->select_user->name ?? '' }}
                                </td>
                                <td>
                                    {{ $attachVeichle->select_user->email ?? '' }}
                                </td>
                                <td>
                                    @foreach($attachVeichle->vehicles as $key => $item)
                                        <span class="badge badge-info">{{ $item->vehicle_reg_no }}</span>
                                    @endforeach
                                </td>
                                <td>
                                    @can('attach_veichle_show')
                                        <a class="btn btn-xs btn-primary" href="{{ route('admin.attach-veichles.show', $attachVeichle->id) }}">
                                            {{ trans('global.view') }}
                                        </a>
                                    @endcan

                                    @can('attach_veichle_edit')
                                        <a class="btn btn-xs btn-info" href="{{ route('admin.attach-veichles.edit', $attachVeichle->id) }}">
                                            {{ trans('global.edit') }}
                                        </a>
                                    @endcan

                                    @can('attach_veichle_delete')
                                        <form action="{{ route('admin.attach-veichles.destroy', $attachVeichle->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
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
@can('attach_veichle_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.attach-veichles.massDestroy') }}",
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
  let table = $('.datatable-selectUserAttachVeichles:not(.ajaxTable)').DataTable({ buttons: dtButtons })
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
})

</script>
@endsection
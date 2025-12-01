@extends('layouts.admin')
@section('content')
@can('agent_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('admin.agents.create') }}">
                {{ trans('global.add') }} {{ trans('cruds.agent.title_singular') }}
            </a>
            <button class="btn btn-warning" data-toggle="modal" data-target="#csvImportModal">
                {{ trans('global.app_csvImport') }}
            </button>
            @include('csvImport.modal', ['model' => 'Agent', 'route' => 'admin.agents.parseCsvImport'])
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        {{ trans('cruds.agent.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover datatable datatable-Agent">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('cruds.agent.fields.id') }}
                        </th>
                        <th>
                            {{ trans('cruds.agent.fields.full_name') }}
                        </th>
                        <th>
                            {{ trans('cruds.agent.fields.phone_number') }}
                        </th>
                        <th>
                            {{ trans('cruds.agent.fields.whatsapp_number') }}
                        </th>
                        <th>
                            {{ trans('cruds.agent.fields.email') }}
                        </th>
                        <th>
                            {{ trans('cruds.agent.fields.pin_code') }}
                        </th>
                        <th>
                            {{ trans('cruds.agent.fields.state') }}
                        </th>
                        <th>
                            {{ trans('cruds.agent.fields.city') }}
                        </th>
                        <th>
                            {{ trans('cruds.agent.fields.district') }}
                        </th>
                        <th>
                            {{ trans('cruds.agent.fields.aadhar_front') }}
                        </th>
                        <th>
                            {{ trans('cruds.agent.fields.aadhar_back') }}
                        </th>
                        <th>
                            {{ trans('cruds.agent.fields.pan_card') }}
                        </th>
                        <th>
                            {{ trans('cruds.agent.fields.additional_document') }}
                        </th>
                        <th>
                            {{ trans('cruds.agent.fields.status') }}
                        </th>
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($agents as $key => $agent)
                        <tr data-entry-id="{{ $agent->id }}">
                            <td>

                            </td>
                            <td>
                                {{ $agent->id ?? '' }}
                            </td>
                            <td>
                                {{ $agent->full_name ?? '' }}
                            </td>
                            <td>
                                {{ $agent->phone_number ?? '' }}
                            </td>
                            <td>
                                {{ $agent->whatsapp_number ?? '' }}
                            </td>
                            <td>
                                {{ $agent->email ?? '' }}
                            </td>
                            <td>
                                {{ $agent->pin_code ?? '' }}
                            </td>
                            <td>
                                {{ $agent->state ?? '' }}
                            </td>
                            <td>
                                {{ $agent->city ?? '' }}
                            </td>
                            <td>
                                {{ $agent->district ?? '' }}
                            </td>
                            <td>
                                @if($agent->aadhar_front)
                                    <a href="{{ $agent->aadhar_front->getUrl() }}" target="_blank">
                                        {{ trans('global.view_file') }}
                                    </a>
                                @endif
                            </td>
                            <td>
                                @if($agent->aadhar_back)
                                    <a href="{{ $agent->aadhar_back->getUrl() }}" target="_blank">
                                        {{ trans('global.view_file') }}
                                    </a>
                                @endif
                            </td>
                            <td>
                                @if($agent->pan_card)
                                    <a href="{{ $agent->pan_card->getUrl() }}" target="_blank">
                                        {{ trans('global.view_file') }}
                                    </a>
                                @endif
                            </td>
                            <td>
                                @foreach($agent->additional_document as $key => $media)
                                    <a href="{{ $media->getUrl() }}" target="_blank">
                                        {{ trans('global.view_file') }}
                                    </a>
                                @endforeach
                            </td>
                            <td>
                                {{ App\Models\Agent::STATUS_SELECT[$agent->status] ?? '' }}
                            </td>
                            <td>
                                @can('agent_show')
                                    <a class="btn btn-xs btn-primary" href="{{ route('admin.agents.show', $agent->id) }}">
                                        {{ trans('global.view') }}
                                    </a>
                                @endcan

                                @can('agent_edit')
                                    <a class="btn btn-xs btn-info" href="{{ route('admin.agents.edit', $agent->id) }}">
                                        {{ trans('global.edit') }}
                                    </a>
                                @endcan

                                @can('agent_delete')
                                    <form action="{{ route('admin.agents.destroy', $agent->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
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
@can('agent_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.agents.massDestroy') }}",
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
  let table = $('.datatable-Agent:not(.ajaxTable)').DataTable({ buttons: dtButtons })
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
})

</script>
@endsection
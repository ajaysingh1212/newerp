@extends('layouts.admin')
@section('content')
@can('activation_request_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('admin.activation-requests.create') }}">
                {{ trans('global.add') }} {{ trans('cruds.activationRequest.title_singular') }}
            </a>
            <button class="btn btn-warning" data-toggle="modal" data-target="#csvImportModal">
                {{ trans('global.app_csvImport') }}
            </button>
            @include('csvImport.modal', ['model' => 'ActivationRequest', 'route' => 'admin.activation-requests.parseCsvImport'])
        </div>
    </div>
@endcan
<div class="card">
     @include('watermark')
    <div class="card-header">
        {{ trans('cruds.activationRequest.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-ActivationRequest">
            <thead>
                <tr>
                    <th width="10">

                    </th>
                    <th>
                        {{ trans('cruds.activationRequest.fields.id') }}
                    </th>
                    <th>
                        {{ trans('cruds.activationRequest.fields.party_type') }}
                    </th>
                    <th>
                        {{ trans('cruds.activationRequest.fields.select_party') }}
                    </th>
                    <th>
                        {{ trans('cruds.user.fields.email') }}
                    </th>
                    <th>
                        {{ trans('cruds.activationRequest.fields.product') }}
                    </th>
         
                   
                    <th>
                        {{ trans('cruds.activationRequest.fields.request_date') }}
                    </th>
                    <th>
                        {{ trans('cruds.activationRequest.fields.vehicle_model') }}
                    </th>
                    <th>
                        {{ trans('cruds.activationRequest.fields.vehicle_reg_no') }}
                    </th>
                    <th>Activation Status</th>
                  
                   
                    <th  class="px-5">
                        &nbsp;
                    </th>
                </tr>
            </thead>
        </table>
    </div>
</div>



@endsection
@section('scripts')
@parent
<script>
    $(document).on('click', '.view-more-toggle', function () {
        let targetId = $(this).data('target');
        $('#' + targetId).slideToggle();
    });
</script>

<script>
    $(function () {
  let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
@can('activation_request_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}';
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.activation-requests.massDestroy') }}",
    className: 'btn-danger',
    action: function (e, dt, node, config) {
      var ids = $.map(dt.rows({ selected: true }).data(), function (entry) {
          return entry.id
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

  let dtOverrideGlobals = {
    buttons: dtButtons,
    processing: true,
    serverSide: true,
    retrieve: true,
    aaSorting: [],
    ajax: "{{ route('admin.activation-requests.index') }}",
    columns: [
      { data: 'placeholder', name: 'placeholder' },
{ data: 'id', name: 'id' },
{ data: 'party_type_title', name: 'party_type.title' },
{ data: 'select_party_name', name: 'select_party.name' },
{ data: 'select_party.email', name: 'select_party.email' },
 { data: 'product_details', name: 'product_details' },

{ data: 'request_date', name: 'request_date' },
{ data: 'vehicle_model', name: 'vehicle_model' },
{ data: 'vehicle_reg_no', name: 'vehicle_reg_no' },
{ data: 'status', name: 'status' },

{ data: 'actions', name: '{{ trans('global.actions') }}' }
    ],
    orderCellsTop: true,
    order: [[ 1, 'desc' ]],
    pageLength: 100,
  };
  let table = $('.datatable-ActivationRequest').DataTable(dtOverrideGlobals);
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
});

</script>
@endsection
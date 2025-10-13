<div class="m-3">
    @can('activation_request_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route('admin.activation-requests.create') }}">
                    {{ trans('global.add') }} {{ trans('cruds.activationRequest.title_singular') }}
                </a>
            </div>
        </div>
    @endcan
    <div class="card">
        <div class="card-header">
            {{ trans('cruds.activationRequest.title_singular') }} {{ trans('global.list') }}
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class=" table table-bordered table-striped table-hover datatable datatable-partyTypeActivationRequests">
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
                                {{ trans('cruds.currentStock.fields.product_name') }}
                            </th>
                            <th>
                                {{ trans('cruds.activationRequest.fields.customer_name') }}
                            </th>
                            <th>
                                {{ trans('cruds.activationRequest.fields.mobile_number') }}
                            </th>
                            <th>
                                {{ trans('cruds.activationRequest.fields.whatsapp_number') }}
                            </th>
                            <th>
                                {{ trans('cruds.activationRequest.fields.email') }}
                            </th>
                            <th>
                                {{ trans('cruds.activationRequest.fields.state') }}
                            </th>
                            <th>
                                {{ trans('cruds.state.fields.country') }}
                            </th>
                            <th>
                                {{ trans('cruds.activationRequest.fields.disrict') }}
                            </th>
                            <th>
                                {{ trans('cruds.district.fields.country') }}
                            </th>
                            <th>
                                {{ trans('cruds.activationRequest.fields.request_date') }}
                            </th>
                            <th>
                                {{ trans('cruds.activationRequest.fields.vehicle_model') }}
                            </th>
                            <th>
                                {{ trans('cruds.activationRequest.fields.vehicle_type') }}
                            </th>
                            <th>
                                {{ trans('cruds.activationRequest.fields.vehicle_reg_no') }}
                            </th>
                            <th>
                                {{ trans('cruds.activationRequest.fields.chassis_number') }}
                            </th>
                            <th>
                                {{ trans('cruds.activationRequest.fields.engine_number') }}
                            </th>
                            <th>
                                {{ trans('cruds.activationRequest.fields.vehicle_color') }}
                            </th>
                            <th>
                                {{ trans('cruds.activationRequest.fields.id_proofs') }}
                            </th>
                            <th>
                                {{ trans('cruds.activationRequest.fields.customer_image') }}
                            </th>
                            <th>
                                {{ trans('cruds.activationRequest.fields.vehicle_photos') }}
                            </th>
                            <th>
                                {{ trans('cruds.activationRequest.fields.product_images') }}
                            </th>
                            <th>
                                &nbsp;
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($activationRequests as $key => $activationRequest)
                            <tr data-entry-id="{{ $activationRequest->id }}">
                                <td>

                                </td>
                                <td>
                                    {{ $activationRequest->id ?? '' }}
                                </td>
                                <td>
                                    {{ $activationRequest->party_type->title ?? '' }}
                                </td>
                                <td>
                                    {{ $activationRequest->select_party->name ?? '' }}
                                </td>
                                <td>
                                    {{ $activationRequest->select_party->email ?? '' }}
                                </td>
                                <td>
                                    {{ $activationRequest->product->sku ?? '' }}
                                </td>
                                <td>
                                    {{ $activationRequest->product->product_name ?? '' }}
                                </td>
                                <td>
                                    {{ $activationRequest->customer_name ?? '' }}
                                </td>
                                <td>
                                    {{ $activationRequest->mobile_number ?? '' }}
                                </td>
                                <td>
                                    {{ $activationRequest->whatsapp_number ?? '' }}
                                </td>
                                <td>
                                    {{ $activationRequest->email ?? '' }}
                                </td>
                                <td>
                                    {{ $activationRequest->state->state_name ?? '' }}
                                </td>
                                <td>
                                    {{ $activationRequest->state->country ?? '' }}
                                </td>
                                <td>
                                    {{ $activationRequest->disrict->districts ?? '' }}
                                </td>
                                <td>
                                    {{ $activationRequest->disrict->country ?? '' }}
                                </td>
                                <td>
                                    {{ $activationRequest->request_date ?? '' }}
                                </td>
                                <td>
                                    {{ $activationRequest->vehicle_model ?? '' }}
                                </td>
                                <td>
                                    {{ $activationRequest->vehicle_type->vehicle_type ?? '' }}
                                </td>
                                <td>
                                    {{ $activationRequest->vehicle_reg_no ?? '' }}
                                </td>
                                <td>
                                    {{ $activationRequest->chassis_number ?? '' }}
                                </td>
                                <td>
                                    {{ $activationRequest->engine_number ?? '' }}
                                </td>
                                <td>
                                    {{ $activationRequest->vehicle_color ?? '' }}
                                </td>
                                <td>
                                    @if($activationRequest->id_proofs)
                                        <a href="{{ $activationRequest->id_proofs->getUrl() }}" target="_blank" style="display: inline-block">
                                            <img src="{{ $activationRequest->id_proofs->getUrl('thumb') }}">
                                        </a>
                                    @endif
                                </td>
                                <td>
                                    @if($activationRequest->customer_image)
                                        <a href="{{ $activationRequest->customer_image->getUrl() }}" target="_blank" style="display: inline-block">
                                            <img src="{{ $activationRequest->customer_image->getUrl('thumb') }}">
                                        </a>
                                    @endif
                                </td>
                                <td>
                                    @if($activationRequest->vehicle_photos)
                                        <a href="{{ $activationRequest->vehicle_photos->getUrl() }}" target="_blank" style="display: inline-block">
                                            <img src="{{ $activationRequest->vehicle_photos->getUrl('thumb') }}">
                                        </a>
                                    @endif
                                </td>
                                <td>
                                    @if($activationRequest->product_images)
                                        <a href="{{ $activationRequest->product_images->getUrl() }}" target="_blank" style="display: inline-block">
                                            <img src="{{ $activationRequest->product_images->getUrl('thumb') }}">
                                        </a>
                                    @endif
                                </td>
                                <td>
                                    @can('activation_request_show')
                                        <a class="btn btn-xs btn-primary" href="{{ route('admin.activation-requests.show', $activationRequest->id) }}">
                                            {{ trans('global.view') }}
                                        </a>
                                    @endcan

                                    @can('activation_request_edit')
                                        <a class="btn btn-xs btn-info" href="{{ route('admin.activation-requests.edit', $activationRequest->id) }}">
                                            {{ trans('global.edit') }}
                                        </a>
                                    @endcan

                                    @can('activation_request_delete')
                                        <form action="{{ route('admin.activation-requests.destroy', $activationRequest->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
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
@can('activation_request_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.activation-requests.massDestroy') }}",
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
  let table = $('.datatable-partyTypeActivationRequests:not(.ajaxTable)').DataTable({ buttons: dtButtons })
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
})

</script>
@endsection
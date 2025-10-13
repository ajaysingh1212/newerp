<div class="m-3">
    @can('user_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route('admin.users.create') }}">
                    {{ trans('global.add') }} {{ trans('cruds.user.title_singular') }}
                </a>
            </div>
        </div>
    @endcan
    <div class="card">
        <div class="card-header">
            {{ trans('cruds.user.title_singular') }} {{ trans('global.list') }}
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class=" table table-bordered table-striped table-hover datatable datatable-stateUsers">
                    <thead>
                        <tr>
                            <th width="10">

                            </th>
                            <th>
                                {{ trans('cruds.user.fields.id') }}
                            </th>
                            <th>
                                {{ trans('cruds.user.fields.name') }}
                            </th>
                            <th>
                                {{ trans('cruds.user.fields.company_name') }}
                            </th>
                            <th>
                                {{ trans('cruds.user.fields.email') }}
                            </th>
                            <th>
                                {{ trans('cruds.user.fields.gst_number') }}
                            </th>
                            <th>
                                {{ trans('cruds.user.fields.date_inc') }}
                            </th>
                            <th>
                                {{ trans('cruds.user.fields.date_joining') }}
                            </th>
                            <th>
                                {{ trans('cruds.user.fields.mobile_number') }}
                            </th>
                            <th>
                                {{ trans('cruds.user.fields.whatsapp_number') }}
                            </th>
                            <th>
                                {{ trans('cruds.user.fields.state') }}
                            </th>
                            <th>
                                {{ trans('cruds.state.fields.country') }}
                            </th>
                            <th>
                                {{ trans('cruds.user.fields.district') }}
                            </th>
                            <th>
                                {{ trans('cruds.district.fields.country') }}
                            </th>
                            <th>
                                {{ trans('cruds.user.fields.pin_code') }}
                            </th>
                            <th>
                                {{ trans('cruds.user.fields.bank_name') }}
                            </th>
                            <th>
                                {{ trans('cruds.user.fields.branch_name') }}
                            </th>
                            <th>
                                {{ trans('cruds.user.fields.ifsc') }}
                            </th>
                            <th>
                                {{ trans('cruds.user.fields.ac_holder_name') }}
                            </th>
                            <th>
                                {{ trans('cruds.user.fields.pan_number') }}
                            </th>
                            <th>
                                {{ trans('cruds.user.fields.profile_image') }}
                            </th>
                            <th>
                                {{ trans('cruds.user.fields.upload_signature') }}
                            </th>
                            <th>
                                {{ trans('cruds.user.fields.upload_pan_aadhar') }}
                            </th>
                            <th>
                                {{ trans('cruds.user.fields.passbook_statement') }}
                            </th>
                            <th>
                                {{ trans('cruds.user.fields.shop_photo') }}
                            </th>
                            <th>
                                {{ trans('cruds.user.fields.gst_certificate') }}
                            </th>
                            <th>
                                {{ trans('cruds.user.fields.status') }}
                            </th>
                            <th>
                                {{ trans('cruds.user.fields.roles') }}
                            </th>
                            <th>
                                {{ trans('cruds.user.fields.email_verified_at') }}
                            </th>
                            <th>
                                &nbsp;
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $key => $user)
                            <tr data-entry-id="{{ $user->id }}">
                                <td>

                                </td>
                                <td>
                                    {{ $user->id ?? '' }}
                                </td>
                                <td>
                                    {{ $user->name ?? '' }}
                                </td>
                                <td>
                                    {{ $user->company_name ?? '' }}
                                </td>
                                <td>
                                    {{ $user->email ?? '' }}
                                </td>
                                <td>
                                    {{ $user->gst_number ?? '' }}
                                </td>
                                <td>
                                    {{ $user->date_inc ?? '' }}
                                </td>
                                <td>
                                    {{ $user->date_joining ?? '' }}
                                </td>
                                <td>
                                    {{ $user->mobile_number ?? '' }}
                                </td>
                                <td>
                                    {{ $user->whatsapp_number ?? '' }}
                                </td>
                                <td>
                                    {{ $user->state->state_name ?? '' }}
                                </td>
                                <td>
                                    {{ $user->state->country ?? '' }}
                                </td>
                                <td>
                                    {{ $user->district->districts ?? '' }}
                                </td>
                                <td>
                                    {{ $user->district->country ?? '' }}
                                </td>
                                <td>
                                    {{ $user->pin_code ?? '' }}
                                </td>
                                <td>
                                    {{ $user->bank_name ?? '' }}
                                </td>
                                <td>
                                    {{ $user->branch_name ?? '' }}
                                </td>
                                <td>
                                    {{ $user->ifsc ?? '' }}
                                </td>
                                <td>
                                    {{ $user->ac_holder_name ?? '' }}
                                </td>
                                <td>
                                    {{ $user->pan_number ?? '' }}
                                </td>
                                <td>
                                    @if($user->profile_image)
                                        <a href="{{ $user->profile_image->getUrl() }}" target="_blank" style="display: inline-block">
                                            <img src="{{ $user->profile_image->getUrl('thumb') }}">
                                        </a>
                                    @endif
                                </td>
                                <td>
                                    @if($user->upload_signature)
                                        <a href="{{ $user->upload_signature->getUrl() }}" target="_blank" style="display: inline-block">
                                            <img src="{{ $user->upload_signature->getUrl('thumb') }}">
                                        </a>
                                    @endif
                                </td>
                                <td>
                                    @if($user->upload_pan_aadhar)
                                        <a href="{{ $user->upload_pan_aadhar->getUrl() }}" target="_blank">
                                            {{ trans('global.view_file') }}
                                        </a>
                                    @endif
                                </td>
                                <td>
                                    @if($user->passbook_statement)
                                        <a href="{{ $user->passbook_statement->getUrl() }}" target="_blank">
                                            {{ trans('global.view_file') }}
                                        </a>
                                    @endif
                                </td>
                                <td>
                                    @if($user->shop_photo)
                                        <a href="{{ $user->shop_photo->getUrl() }}" target="_blank" style="display: inline-block">
                                            <img src="{{ $user->shop_photo->getUrl('thumb') }}">
                                        </a>
                                    @endif
                                </td>
                                <td>
                                    @if($user->gst_certificate)
                                        <a href="{{ $user->gst_certificate->getUrl() }}" target="_blank">
                                            {{ trans('global.view_file') }}
                                        </a>
                                    @endif
                                </td>
                                <td>
                                    {{ App\Models\User::STATUS_SELECT[$user->status] ?? '' }}
                                </td>
                                <td>
                                    @foreach($user->roles as $key => $item)
                                        <span class="badge badge-info">{{ $item->title }}</span>
                                    @endforeach
                                </td>
                                <td>
                                    {{ $user->email_verified_at ?? '' }}
                                </td>
                                <td>
                                    @can('user_show')
                                        <a class="btn btn-xs btn-primary" href="{{ route('admin.users.show', $user->id) }}">
                                            {{ trans('global.view') }}
                                        </a>
                                    @endcan

                                    @can('user_edit')
                                        <a class="btn btn-xs btn-info" href="{{ route('admin.users.edit', $user->id) }}">
                                            {{ trans('global.edit') }}
                                        </a>
                                    @endcan

                                    @can('user_delete')
                                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
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
@can('user_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.users.massDestroy') }}",
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
  let table = $('.datatable-stateUsers:not(.ajaxTable)').DataTable({ buttons: dtButtons })
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
})

</script>
@endsection
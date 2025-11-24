@extends('layouts.admin')
@section('content')

@can('registration_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('admin.registrations.create') }}">
                {{ trans('global.add') }} {{ trans('cruds.registration.title_singular') }}
            </a>
        </div>
    </div>
@endcan

<div class="card">
    <div class="card-header">
        Investor Registrations
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover datatable datatable-Registration">
                <thead>
                    <tr>
                        <th width="10"></th>
                        <th>ID</th>
                        <th>Reg No</th>
                        <th>Investor</th>
                        <th>Email</th>
                        <th>Aadhaar</th>
                        <th>PAN</th>
                        <th>DOB</th>
                        <th>KYC Status</th>
                        <th>Account</th>
                        <th>PAN Image</th>
                        <th>Aadhaar Front</th>
                        <th>Aadhaar Back</th>
                        <th>Profile</th>
                        <th>Signature</th>
                        <th>&nbsp;</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($registrations as $registration)
                        <tr data-entry-id="{{ $registration->id }}">

                            <td></td>

                            <td>{{ $registration->id }}</td>

                            <td>{{ $registration->reg }}</td>

                            <td>{{ $registration->investor->name ?? '' }}</td>

                            <td>{{ $registration->investor->email ?? '' }}</td>

                            <td>{{ $registration->aadhaar_number }}</td>

                            <td>{{ $registration->pan_number }}</td>

                            <td>{{ $registration->dob }}</td>

                            <td>
                                {{ App\Models\Registration::KYC_STATUS_SELECT[$registration->kyc_status] ?? '' }}
                            </td>

                            <td>
                                {{ App\Models\Registration::ACCOUNT_STATUS_SELECT[$registration->account_status] ?? '' }}
                            </td>

                            <td>
                                @foreach($registration->pan_card_image as $media)
                                    <a href="{{ $media->getUrl() }}" target="_blank">
                                        {{ trans('global.view_file') }}
                                    </a><br>
                                @endforeach
                            </td>

                            <td>
                                @if($registration->aadhaar_front_image)
                                    <a href="{{ $registration->aadhaar_front_image->getUrl() }}" target="_blank">
                                        View
                                    </a>
                                @endif
                            </td>

                            <td>
                                @if($registration->aadhaar_back_image)
                                    <a href="{{ $registration->aadhaar_back_image->getUrl() }}" target="_blank">
                                        View
                                    </a>
                                @endif
                            </td>

                            <td>
                                @if($registration->profile_image)
                                    <a href="{{ $registration->profile_image->getUrl() }}" target="_blank">
                                        View
                                    </a>
                                @endif
                            </td>

                            <td>
                                @if($registration->signature_image)
                                    <a href="{{ $registration->signature_image->getUrl() }}" target="_blank">
                                        View
                                    </a>
                                @endif
                            </td>

                            <td>
                                @can('registration_show')
                                    <a class="btn btn-xs btn-primary" href="{{ route('admin.registrations.show', $registration->id) }}">
                                        View
                                    </a>
                                @endcan

                                @can('registration_edit')
                                    <a class="btn btn-xs btn-info" href="{{ route('admin.registrations.edit', $registration->id) }}">
                                        Edit
                                    </a>
                                @endcan

                                @can('registration_delete')
                                    <form action="{{ route('admin.registrations.destroy', $registration->id) }}"
                                          method="POST"
                                          style="display: inline-block;"
                                          onsubmit="return confirm('Are you sure?');">

                                        <input type="hidden" name="_method" value="DELETE">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input type="submit" class="btn btn-xs btn-danger" value="Delete">
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
@can('registration_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.registrations.massDestroy') }}",
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
  let table = $('.datatable-Registration:not(.ajaxTable)').DataTable({ buttons: dtButtons })
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
})

</script>
@endsection
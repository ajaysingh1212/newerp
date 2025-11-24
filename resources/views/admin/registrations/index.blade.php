@extends('layouts.admin')
@section('content')
@can('registration_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('admin.registrations.create') }}">
                {{ trans('global.add') }} {{ trans('cruds.registration.title_singular') }}
            </a>
            <button class="btn btn-warning" data-toggle="modal" data-target="#csvImportModal">
                {{ trans('global.app_csvImport') }}
            </button>
            @include('csvImport.modal', ['model' => 'Registration', 'route' => 'admin.registrations.parseCsvImport'])
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        {{ trans('cruds.registration.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover datatable datatable-Registration">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('cruds.registration.fields.id') }}
                        </th>
                        <th>
                            {{ trans('cruds.registration.fields.reg') }}
                        </th>
                        <th>
                            {{ trans('cruds.registration.fields.investor') }}
                        </th>
                        <th>
                            {{ trans('cruds.user.fields.email') }}
                        </th>
                        <th>
                            {{ trans('cruds.registration.fields.referral_code') }}
                        </th>
                        <th>
                            {{ trans('cruds.registration.fields.aadhaar_number') }}
                        </th>
                        <th>
                            {{ trans('cruds.registration.fields.pan_number') }}
                        </th>
                        <th>
                            {{ trans('cruds.registration.fields.dob') }}
                        </th>
                        <th>
                            {{ trans('cruds.registration.fields.gender') }}
                        </th>
                        <th>
                            {{ trans('cruds.registration.fields.father_name') }}
                        </th>
                        <th>
                            {{ trans('cruds.registration.fields.pincode') }}
                        </th>
                        <th>
                            {{ trans('cruds.registration.fields.city') }}
                        </th>
                        <th>
                            {{ trans('cruds.registration.fields.state') }}
                        </th>
                        <th>
                            {{ trans('cruds.registration.fields.country') }}
                        </th>
                        <th>
                            {{ trans('cruds.registration.fields.bank_account_holder_name') }}
                        </th>
                        <th>
                            {{ trans('cruds.registration.fields.bank_account_number') }}
                        </th>
                        <th>
                            {{ trans('cruds.registration.fields.ifsc_code') }}
                        </th>
                        <th>
                            {{ trans('cruds.registration.fields.bank_name') }}
                        </th>
                        <th>
                            {{ trans('cruds.registration.fields.bank_branch') }}
                        </th>
                        <th>
                            {{ trans('cruds.registration.fields.pan_card_image') }}
                        </th>
                        <th>
                            {{ trans('cruds.registration.fields.aadhaar_front_image') }}
                        </th>
                        <th>
                            {{ trans('cruds.registration.fields.aadhaar_back_image') }}
                        </th>
                        <th>
                            {{ trans('cruds.registration.fields.profile_image') }}
                        </th>
                        <th>
                            {{ trans('cruds.registration.fields.signature_image') }}
                        </th>
                        <th>
                            {{ trans('cruds.registration.fields.income_range') }}
                        </th>
                        <th>
                            {{ trans('cruds.registration.fields.occupation') }}
                        </th>
                        <th>
                            {{ trans('cruds.registration.fields.risk_profile') }}
                        </th>
                        <th>
                            {{ trans('cruds.registration.fields.investment_experience') }}
                        </th>
                        <th>
                            {{ trans('cruds.registration.fields.kyc_status') }}
                        </th>
                        <th>
                            {{ trans('cruds.registration.fields.account_status') }}
                        </th>
                        <th>
                            {{ trans('cruds.registration.fields.is_email_verified') }}
                        </th>
                        <th>
                            {{ trans('cruds.registration.fields.is_phone_verified') }}
                        </th>
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($registrations as $key => $registration)
                        <tr data-entry-id="{{ $registration->id }}">
                            <td>

                            </td>
                            <td>
                                {{ $registration->id ?? '' }}
                            </td>
                            <td>
                                {{ $registration->reg ?? '' }}
                            </td>
                            <td>
                                {{ $registration->investor->name ?? '' }}
                            </td>
                            <td>
                                {{ $registration->investor->email ?? '' }}
                            </td>
                            <td>
                                {{ $registration->referral_code ?? '' }}
                            </td>
                            <td>
                                {{ $registration->aadhaar_number ?? '' }}
                            </td>
                            <td>
                                {{ $registration->pan_number ?? '' }}
                            </td>
                            <td>
                                {{ $registration->dob ?? '' }}
                            </td>
                            <td>
                                {{ App\Models\Registration::GENDER_SELECT[$registration->gender] ?? '' }}
                            </td>
                            <td>
                                {{ $registration->father_name ?? '' }}
                            </td>
                            <td>
                                {{ $registration->pincode ?? '' }}
                            </td>
                            <td>
                                {{ $registration->city ?? '' }}
                            </td>
                            <td>
                                {{ $registration->state ?? '' }}
                            </td>
                            <td>
                                {{ $registration->country ?? '' }}
                            </td>
                            <td>
                                {{ $registration->bank_account_holder_name ?? '' }}
                            </td>
                            <td>
                                {{ $registration->bank_account_number ?? '' }}
                            </td>
                            <td>
                                {{ $registration->ifsc_code ?? '' }}
                            </td>
                            <td>
                                {{ $registration->bank_name ?? '' }}
                            </td>
                            <td>
                                {{ $registration->bank_branch ?? '' }}
                            </td>
                            <td>
                                @foreach($registration->pan_card_image as $key => $media)
                                    <a href="{{ $media->getUrl() }}" target="_blank">
                                        {{ trans('global.view_file') }}
                                    </a>
                                @endforeach
                            </td>
                            <td>
                                @if($registration->aadhaar_front_image)
                                    <a href="{{ $registration->aadhaar_front_image->getUrl() }}" target="_blank">
                                        {{ trans('global.view_file') }}
                                    </a>
                                @endif
                            </td>
                            <td>
                                @if($registration->aadhaar_back_image)
                                    <a href="{{ $registration->aadhaar_back_image->getUrl() }}" target="_blank">
                                        {{ trans('global.view_file') }}
                                    </a>
                                @endif
                            </td>
                            <td>
                                @foreach($registration->profile_image as $key => $media)
                                    <a href="{{ $media->getUrl() }}" target="_blank">
                                        {{ trans('global.view_file') }}
                                    </a>
                                @endforeach
                            </td>
                            <td>
                                @foreach($registration->signature_image as $key => $media)
                                    <a href="{{ $media->getUrl() }}" target="_blank">
                                        {{ trans('global.view_file') }}
                                    </a>
                                @endforeach
                            </td>
                            <td>
                                {{ App\Models\Registration::INCOME_RANGE_SELECT[$registration->income_range] ?? '' }}
                            </td>
                            <td>
                                {{ App\Models\Registration::OCCUPATION_SELECT[$registration->occupation] ?? '' }}
                            </td>
                            <td>
                                {{ App\Models\Registration::RISK_PROFILE_SELECT[$registration->risk_profile] ?? '' }}
                            </td>
                            <td>
                                {{ App\Models\Registration::INVESTMENT_EXPERIENCE_SELECT[$registration->investment_experience] ?? '' }}
                            </td>
                            <td>
                                {{ App\Models\Registration::KYC_STATUS_SELECT[$registration->kyc_status] ?? '' }}
                            </td>
                            <td>
                                {{ App\Models\Registration::ACCOUNT_STATUS_SELECT[$registration->account_status] ?? '' }}
                            </td>
                            <td>
                                {{ App\Models\Registration::IS_EMAIL_VERIFIED_RADIO[$registration->is_email_verified] ?? '' }}
                            </td>
                            <td>
                                {{ App\Models\Registration::IS_PHONE_VERIFIED_RADIO[$registration->is_phone_verified] ?? '' }}
                            </td>
                            <td>
                                @can('registration_show')
                                    <a class="btn btn-xs btn-primary" href="{{ route('admin.registrations.show', $registration->id) }}">
                                        {{ trans('global.view') }}
                                    </a>
                                @endcan

                                @can('registration_edit')
                                    <a class="btn btn-xs btn-info" href="{{ route('admin.registrations.edit', $registration->id) }}">
                                        {{ trans('global.edit') }}
                                    </a>
                                @endcan

                                @can('registration_delete')
                                    <form action="{{ route('admin.registrations.destroy', $registration->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
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
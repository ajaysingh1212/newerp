@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.registration.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.registrations.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.registration.fields.id') }}
                        </th>
                        <td>
                            {{ $registration->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.registration.fields.reg') }}
                        </th>
                        <td>
                            {{ $registration->reg }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.registration.fields.investor') }}
                        </th>
                        <td>
                            {{ $registration->investor->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.registration.fields.referral_code') }}
                        </th>
                        <td>
                            {{ $registration->referral_code }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.registration.fields.aadhaar_number') }}
                        </th>
                        <td>
                            {{ $registration->aadhaar_number }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.registration.fields.pan_number') }}
                        </th>
                        <td>
                            {{ $registration->pan_number }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.registration.fields.dob') }}
                        </th>
                        <td>
                            {{ $registration->dob }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.registration.fields.gender') }}
                        </th>
                        <td>
                            {{ App\Models\Registration::GENDER_SELECT[$registration->gender] ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.registration.fields.father_name') }}
                        </th>
                        <td>
                            {{ $registration->father_name }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.registration.fields.address_line_1') }}
                        </th>
                        <td>
                            {!! $registration->address_line_1 !!}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.registration.fields.address_line_2') }}
                        </th>
                        <td>
                            {!! $registration->address_line_2 !!}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.registration.fields.pincode') }}
                        </th>
                        <td>
                            {{ $registration->pincode }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.registration.fields.city') }}
                        </th>
                        <td>
                            {{ $registration->city }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.registration.fields.state') }}
                        </th>
                        <td>
                            {{ $registration->state }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.registration.fields.country') }}
                        </th>
                        <td>
                            {{ $registration->country }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.registration.fields.bank_account_holder_name') }}
                        </th>
                        <td>
                            {{ $registration->bank_account_holder_name }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.registration.fields.bank_account_number') }}
                        </th>
                        <td>
                            {{ $registration->bank_account_number }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.registration.fields.ifsc_code') }}
                        </th>
                        <td>
                            {{ $registration->ifsc_code }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.registration.fields.bank_name') }}
                        </th>
                        <td>
                            {{ $registration->bank_name }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.registration.fields.bank_branch') }}
                        </th>
                        <td>
                            {{ $registration->bank_branch }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.registration.fields.pan_card_image') }}
                        </th>
                        <td>
                            @foreach($registration->pan_card_image as $key => $media)
                                <a href="{{ $media->getUrl() }}" target="_blank">
                                    {{ trans('global.view_file') }}
                                </a>
                            @endforeach
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.registration.fields.aadhaar_front_image') }}
                        </th>
                        <td>
                            @if($registration->aadhaar_front_image)
                                <a href="{{ $registration->aadhaar_front_image->getUrl() }}" target="_blank">
                                    {{ trans('global.view_file') }}
                                </a>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.registration.fields.aadhaar_back_image') }}
                        </th>
                        <td>
                            @if($registration->aadhaar_back_image)
                                <a href="{{ $registration->aadhaar_back_image->getUrl() }}" target="_blank">
                                    {{ trans('global.view_file') }}
                                </a>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.registration.fields.profile_image') }}
                        </th>
                        <td>
                            @foreach($registration->profile_image as $key => $media)
                                <a href="{{ $media->getUrl() }}" target="_blank">
                                    {{ trans('global.view_file') }}
                                </a>
                            @endforeach
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.registration.fields.signature_image') }}
                        </th>
                        <td>
                            @foreach($registration->signature_image as $key => $media)
                                <a href="{{ $media->getUrl() }}" target="_blank">
                                    {{ trans('global.view_file') }}
                                </a>
                            @endforeach
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.registration.fields.income_range') }}
                        </th>
                        <td>
                            {{ App\Models\Registration::INCOME_RANGE_SELECT[$registration->income_range] ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.registration.fields.occupation') }}
                        </th>
                        <td>
                            {{ App\Models\Registration::OCCUPATION_SELECT[$registration->occupation] ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.registration.fields.risk_profile') }}
                        </th>
                        <td>
                            {{ App\Models\Registration::RISK_PROFILE_SELECT[$registration->risk_profile] ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.registration.fields.investment_experience') }}
                        </th>
                        <td>
                            {{ App\Models\Registration::INVESTMENT_EXPERIENCE_SELECT[$registration->investment_experience] ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.registration.fields.kyc_status') }}
                        </th>
                        <td>
                            {{ App\Models\Registration::KYC_STATUS_SELECT[$registration->kyc_status] ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.registration.fields.account_status') }}
                        </th>
                        <td>
                            {{ App\Models\Registration::ACCOUNT_STATUS_SELECT[$registration->account_status] ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.registration.fields.is_email_verified') }}
                        </th>
                        <td>
                            {{ App\Models\Registration::IS_EMAIL_VERIFIED_RADIO[$registration->is_email_verified] ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.registration.fields.is_phone_verified') }}
                        </th>
                        <td>
                            {{ App\Models\Registration::IS_PHONE_VERIFIED_RADIO[$registration->is_phone_verified] ?? '' }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.registrations.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        {{ trans('global.relatedData') }}
    </div>
    <ul class="nav nav-tabs" role="tablist" id="relationship-tabs">
        <li class="nav-item">
            <a class="nav-link" href="#select_investor_investments" role="tab" data-toggle="tab">
                {{ trans('cruds.investment.title') }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#investor_monthly_payout_records" role="tab" data-toggle="tab">
                {{ trans('cruds.monthlyPayoutRecord.title') }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#select_investor_withdrawal_requests" role="tab" data-toggle="tab">
                {{ trans('cruds.withdrawalRequest.title') }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#investment_investor_transactions" role="tab" data-toggle="tab">
                {{ trans('cruds.investorTransaction.title') }}
            </a>
        </li>
    </ul>
    {{-- <div class="tab-content">
        <div class="tab-pane" role="tabpanel" id="select_investor_investments">
            @includeIf('admin.registrations.relationships.selectInvestorInvestments', ['investments' => $registration->selectInvestorInvestments])
        </div>
        <div class="tab-pane" role="tabpanel" id="investor_monthly_payout_records">
            @includeIf('admin.registrations.relationships.investorMonthlyPayoutRecords', ['monthlyPayoutRecords' => $registration->investorMonthlyPayoutRecords])
        </div>
        <div class="tab-pane" role="tabpanel" id="select_investor_withdrawal_requests">
            @includeIf('admin.registrations.relationships.selectInvestorWithdrawalRequests', ['withdrawalRequests' => $registration->selectInvestorWithdrawalRequests])
        </div>
        <div class="tab-pane" role="tabpanel" id="investment_investor_transactions">
            @includeIf('admin.registrations.relationships.investmentInvestorTransactions', ['investorTransactions' => $registration->investmentInvestorTransactions])
        </div>
    </div> --}}
</div>

@endsection
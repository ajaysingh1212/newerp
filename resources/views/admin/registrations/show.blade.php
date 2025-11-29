@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.registration.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <a class="btn btn-default" href="{{ route('admin.registrations.index') }}">
                {{ trans('global.back_to_list') }}
            </a>
        </div>

        <table class="table table-bordered table-striped">
            <tbody>

                {{-- BASIC FIELDS --}}
                <tr><th>ID</th><td>{{ $registration->id }}</td></tr>
                <tr><th>Reg</th><td>{{ $registration->reg }}</td></tr>
                <tr><th>Investor</th><td>{{ $registration->investor->name ?? '' }}</td></tr>
                <tr><th>Referral Code</th><td>{{ $registration->referral_code }}</td></tr>
                <tr><th>Aadhaar Number</th><td>{{ $registration->aadhaar_number }}</td></tr>
                <tr><th>PAN Number</th><td>{{ $registration->pan_number }}</td></tr>
                <tr><th>DOB</th><td>{{ $registration->dob }}</td></tr>
                <tr><th>Gender</th><td>{{ App\Models\Registration::GENDER_SELECT[$registration->gender] ?? '' }}</td></tr>
                <tr><th>Father Name</th><td>{{ $registration->father_name }}</td></tr>
                <tr><th>Address Line 1</th><td>{!! $registration->address_line_1 !!}</td></tr>
                <tr><th>Address Line 2</th><td>{!! $registration->address_line_2 !!}</td></tr>
                <tr><th>Pincode</th><td>{{ $registration->pincode }}</td></tr>
                <tr><th>City</th><td>{{ $registration->city }}</td></tr>
                <tr><th>State</th><td>{{ $registration->state }}</td></tr>
                <tr><th>Country</th><td>{{ $registration->country }}</td></tr>
                <tr><th>Bank A/C Holder Name</th><td>{{ $registration->bank_account_holder_name }}</td></tr>
                <tr><th>Bank A/C Number</th><td>{{ $registration->bank_account_number }}</td></tr>
                <tr><th>IFSC</th><td>{{ $registration->ifsc_code }}</td></tr>
                <tr><th>Bank Name</th><td>{{ $registration->bank_name }}</td></tr>
                <tr><th>Bank Branch</th><td>{{ $registration->bank_branch }}</td></tr>

                {{-- PAN CARD (MULTIPLE FILES) --}}
                <tr>
                    <th>PAN Card Images</th>
                    <td>
                        @if($registration->pan_card_image->count() > 0)
                            @foreach($registration->pan_card_image as $media)
                                <a href="{{ $media->getUrl() }}" target="_blank">
                                    {{ trans('global.view_file') }}
                                </a><br>
                            @endforeach
                        @else
                            <span>No File</span>
                        @endif
                    </td>
                </tr>

                {{-- Aadhaar Front (Single) --}}
                <tr>
                    <th>Aadhaar Front</th>
                    <td>
                        @if($registration->aadhaar_front_image)
                            <a href="{{ $registration->aadhaar_front_image->getUrl() }}" target="_blank">
                                {{ trans('global.view_file') }}
                            </a>
                        @else
                            <span>No File</span>
                        @endif
                    </td>
                </tr>

                {{-- Aadhaar Back (Single) --}}
                <tr>
                    <th>Aadhaar Back</th>
                    <td>
                        @if($registration->aadhaar_back_image)
                            <a href="{{ $registration->aadhaar_back_image->getUrl() }}" target="_blank">
                                {{ trans('global.view_file') }}
                            </a>
                        @else
                            <span>No File</span>
                        @endif
                    </td>
                </tr>

                {{-- Profile Image (Single) --}}
                <tr>
                    <th>Profile Image</th>
                    <td>
                        @if($registration->profile_image)
                            <a href="{{ $registration->profile_image->getUrl() }}" target="_blank">
                                {{ trans('global.view_file') }}
                            </a>
                        @else
                            <span>No File</span>
                        @endif
                    </td>
                </tr>

                {{-- Signature Image (Single) --}}
                <tr>
                    <th>Signature Image</th>
                    <td>
                        @if($registration->signature_image)
                            <a href="{{ $registration->signature_image->getUrl() }}" target="_blank">
                                {{ trans('global.view_file') }}
                            </a>
                        @else
                            <span>No File</span>
                        @endif
                    </td>
                </tr>

                {{-- SELECT FIELDS --}}
                <tr><th>Income Range</th><td>{{ App\Models\Registration::INCOME_RANGE_SELECT[$registration->income_range] ?? '' }}</td></tr>
                <tr><th>Occupation</th><td>{{ App\Models\Registration::OCCUPATION_SELECT[$registration->occupation] ?? '' }}</td></tr>
                <tr><th>Risk Profile</th><td>{{ App\Models\Registration::RISK_PROFILE_SELECT[$registration->risk_profile] ?? '' }}</td></tr>
                <tr><th>Investment Experience</th><td>{{ App\Models\Registration::INVESTMENT_EXPERIENCE_SELECT[$registration->investment_experience] ?? '' }}</td></tr>
                <tr><th>KYC Status</th><td>{{ App\Models\Registration::KYC_STATUS_SELECT[$registration->kyc_status] ?? '' }}</td></tr>
                <tr><th>Account Status</th><td>{{ App\Models\Registration::ACCOUNT_STATUS_SELECT[$registration->account_status] ?? '' }}</td></tr>
                <tr><th>Email Verified</th><td>{{ App\Models\Registration::IS_EMAIL_VERIFIED_RADIO[$registration->is_email_verified] ?? '' }}</td></tr>
                <tr><th>Phone Verified</th><td>{{ App\Models\Registration::IS_PHONE_VERIFIED_RADIO[$registration->is_phone_verified] ?? '' }}</td></tr>

            </tbody>
        </table>

        <div class="form-group">
            <a class="btn btn-default" href="{{ route('admin.registrations.index') }}">
                {{ trans('global.back_to_list') }}
            </a>
        </div>
    </div>
</div>

{{-- RELATED DATA --}}
<div class="card mt-4">
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

    <div class="tab-content">
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
    </div>

</div>

@endsection

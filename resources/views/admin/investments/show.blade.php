@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.investment.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.investments.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.investment.fields.id') }}
                        </th>
                        <td>
                            {{ $investment->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.investment.fields.select_investor') }}
                        </th>
                        <td>
                            {{ $investment->select_investor->reg ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.investment.fields.select_plan') }}
                        </th>
                        <td>
                            {{ $investment->select_plan->plan_name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.investment.fields.principal_amount') }}
                        </th>
                        <td>
                            {{ $investment->principal_amount }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.investment.fields.secure_interest_percent') }}
                        </th>
                        <td>
                            {{ $investment->secure_interest_percent }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.investment.fields.market_interest_percent') }}
                        </th>
                        <td>
                            {{ $investment->market_interest_percent }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.investment.fields.total_interest_percent') }}
                        </th>
                        <td>
                            {{ $investment->total_interest_percent }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.investment.fields.start_date') }}
                        </th>
                        <td>
                            {{ $investment->start_date }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.investment.fields.lockin_end_date') }}
                        </th>
                        <td>
                            {{ $investment->lockin_end_date }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.investment.fields.next_payout_date') }}
                        </th>
                        <td>
                            {{ $investment->next_payout_date }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.investment.fields.status') }}
                        </th>
                        <td>
                            {{ App\Models\Investment::STATUS_SELECT[$investment->status] ?? '' }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.investments.index') }}">
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
            <a class="nav-link" href="#investment_monthly_payout_records" role="tab" data-toggle="tab">
                {{ trans('cruds.monthlyPayoutRecord.title') }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#investment_withdrawal_requests" role="tab" data-toggle="tab">
                {{ trans('cruds.withdrawalRequest.title') }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#investor_investor_transactions" role="tab" data-toggle="tab">
                {{ trans('cruds.investorTransaction.title') }}
            </a>
        </li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane" role="tabpanel" id="investment_monthly_payout_records">
            @includeIf('admin.investments.relationships.investmentMonthlyPayoutRecords', ['monthlyPayoutRecords' => $investment->investmentMonthlyPayoutRecords])
        </div>
        <div class="tab-pane" role="tabpanel" id="investment_withdrawal_requests">
            @includeIf('admin.investments.relationships.investmentWithdrawalRequests', ['withdrawalRequests' => $investment->investmentWithdrawalRequests])
        </div>
        <div class="tab-pane" role="tabpanel" id="investor_investor_transactions">
            @includeIf('admin.investments.relationships.investorInvestorTransactions', ['investorTransactions' => $investment->investorInvestorTransactions])
        </div>
    </div>
</div>

@endsection
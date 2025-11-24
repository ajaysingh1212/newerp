@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.plan.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.plans.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.plan.fields.id') }}
                        </th>
                        <td>
                            {{ $plan->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.plan.fields.plan_name') }}
                        </th>
                        <td>
                            {{ $plan->plan_name }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.plan.fields.secure_interest_percent') }}
                        </th>
                        <td>
                            {{ $plan->secure_interest_percent }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.plan.fields.market_interest_percent') }}
                        </th>
                        <td>
                            {{ $plan->market_interest_percent }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.plan.fields.total_interest_percent') }}
                        </th>
                        <td>
                            {{ $plan->total_interest_percent }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.plan.fields.payout_frequency') }}
                        </th>
                        <td>
                            {{ App\Models\Plan::PAYOUT_FREQUENCY_SELECT[$plan->payout_frequency] ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.plan.fields.min_invest_amount') }}
                        </th>
                        <td>
                            {{ $plan->min_invest_amount }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.plan.fields.max_invest_amount') }}
                        </th>
                        <td>
                            {{ $plan->max_invest_amount }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.plan.fields.lockin_days') }}
                        </th>
                        <td>
                            {{ $plan->lockin_days }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.plan.fields.withdraw_processing_hours') }}
                        </th>
                        <td>
                            {{ $plan->withdraw_processing_hours }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.plan.fields.status') }}
                        </th>
                        <td>
                            {{ App\Models\Plan::STATUS_SELECT[$plan->status] ?? '' }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.plans.index') }}">
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
            <a class="nav-link" href="#select_plan_investments" role="tab" data-toggle="tab">
                {{ trans('cruds.investment.title') }}
            </a>
        </li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane" role="tabpanel" id="select_plan_investments">
            @includeIf('admin.plans.relationships.selectPlanInvestments', ['investments' => $plan->selectPlanInvestments])
        </div>
    </div>
</div>

@endsection
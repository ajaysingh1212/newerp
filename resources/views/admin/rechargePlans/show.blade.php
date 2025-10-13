@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.rechargePlan.title') }}
    </div>

    <div class="card-body">
        @include('watermark')
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.recharge-plans.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.rechargePlan.fields.id') }}
                        </th>
                        <td>
                            {{ $rechargePlan->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.rechargePlan.fields.type') }}
                        </th>
                        <td>
                            {{ $rechargePlan->type }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.rechargePlan.fields.plan_name') }}
                        </th>
                        <td>
                            {{ $rechargePlan->plan_name }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.rechargePlan.fields.amc_duration') }}
                        </th>
                        <td>
                            {{ $rechargePlan->amc_duration }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.rechargePlan.fields.warranty_duration') }}
                        </th>
                        <td>
                            {{ $rechargePlan->warranty_duration }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.rechargePlan.fields.subscription_duration') }}
                        </th>
                        <td>
                            {{ $rechargePlan->subscription_duration }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.rechargePlan.fields.discription') }}
                        </th>
                        <td>
                            {{ $rechargePlan->discription }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.rechargePlan.fields.price') }}
                        </th>
                        <td>
                            {{ $rechargePlan->price }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.recharge-plans.index') }}">
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
            <a class="nav-link" href="#select_recharge_recharge_requests" role="tab" data-toggle="tab">
                {{ trans('cruds.rechargeRequest.title') }}
            </a>
        </li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane" role="tabpanel" id="select_recharge_recharge_requests">
            @includeIf('admin.rechargePlans.relationships.selectRechargeRechargeRequests', ['rechargeRequests' => $rechargePlan->selectRechargeRechargeRequests])
        </div>
    </div>
</div>

@endsection
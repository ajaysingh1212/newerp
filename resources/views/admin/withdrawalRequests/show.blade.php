@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.withdrawalRequest.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.withdrawal-requests.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.withdrawalRequest.fields.id') }}
                        </th>
                        <td>
                            {{ $withdrawalRequest->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.withdrawalRequest.fields.select_investor') }}
                        </th>
                        <td>
                            {{ $withdrawalRequest->select_investor->reg ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.withdrawalRequest.fields.investment') }}
                        </th>
                        <td>
                            {{ $withdrawalRequest->investment->principal_amount ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.withdrawalRequest.fields.amount') }}
                        </th>
                        <td>
                            {{ $withdrawalRequest->amount }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.withdrawalRequest.fields.type') }}
                        </th>
                        <td>
                            {{ App\Models\WithdrawalRequest::TYPE_SELECT[$withdrawalRequest->type] ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.withdrawalRequest.fields.status') }}
                        </th>
                        <td>
                            {{ App\Models\WithdrawalRequest::STATUS_SELECT[$withdrawalRequest->status] ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.withdrawalRequest.fields.processing_hours') }}
                        </th>
                        <td>
                            {{ $withdrawalRequest->processing_hours }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.withdrawalRequest.fields.requested_at') }}
                        </th>
                        <td>
                            {{ $withdrawalRequest->requested_at }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.withdrawalRequest.fields.approved_at') }}
                        </th>
                        <td>
                            {{ $withdrawalRequest->approved_at }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.withdrawalRequest.fields.notes') }}
                        </th>
                        <td>
                            {!! $withdrawalRequest->notes !!}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.withdrawalRequest.fields.remarks') }}
                        </th>
                        <td>
                            {!! $withdrawalRequest->remarks !!}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.withdrawal-requests.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection
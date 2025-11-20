@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.monthlyPayoutRecord.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.monthly-payout-records.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.monthlyPayoutRecord.fields.id') }}
                        </th>
                        <td>
                            {{ $monthlyPayoutRecord->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.monthlyPayoutRecord.fields.investment') }}
                        </th>
                        <td>
                            {{ $monthlyPayoutRecord->investment->principal_amount ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.monthlyPayoutRecord.fields.investor') }}
                        </th>
                        <td>
                            {{ $monthlyPayoutRecord->investor->reg ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.monthlyPayoutRecord.fields.secure_interest_amount') }}
                        </th>
                        <td>
                            {{ $monthlyPayoutRecord->secure_interest_amount }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.monthlyPayoutRecord.fields.market_interest_amount') }}
                        </th>
                        <td>
                            {{ $monthlyPayoutRecord->market_interest_amount }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.monthlyPayoutRecord.fields.total_payout_amount') }}
                        </th>
                        <td>
                            {{ $monthlyPayoutRecord->total_payout_amount }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.monthlyPayoutRecord.fields.month_for') }}
                        </th>
                        <td>
                            {{ $monthlyPayoutRecord->month_for }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.monthlyPayoutRecord.fields.status') }}
                        </th>
                        <td>
                            {{ App\Models\MonthlyPayoutRecord::STATUS_SELECT[$monthlyPayoutRecord->status] ?? '' }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.monthly-payout-records.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection
@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.investorTransaction.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.investor-transactions.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.investorTransaction.fields.id') }}
                        </th>
                        <td>
                            {{ $investorTransaction->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.investorTransaction.fields.investor') }}
                        </th>
                        <td>
                            {{ $investorTransaction->investor->principal_amount ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.investorTransaction.fields.investment') }}
                        </th>
                        <td>
                            {{ $investorTransaction->investment->reg ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.investorTransaction.fields.transaction_type') }}
                        </th>
                        <td>
                            {{ App\Models\InvestorTransaction::TRANSACTION_TYPE_SELECT[$investorTransaction->transaction_type] ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.investorTransaction.fields.amount') }}
                        </th>
                        <td>
                            {{ $investorTransaction->amount }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.investorTransaction.fields.narration') }}
                        </th>
                        <td>
                            {!! $investorTransaction->narration !!}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.investorTransaction.fields.status') }}
                        </th>
                        <td>
                            {{ App\Models\InvestorTransaction::STATUS_SELECT[$investorTransaction->status] ?? '' }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.investor-transactions.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection
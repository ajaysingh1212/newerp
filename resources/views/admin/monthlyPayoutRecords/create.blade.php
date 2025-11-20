@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('cruds.monthlyPayoutRecord.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.monthly-payout-records.store") }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label class="required" for="investment_id">{{ trans('cruds.monthlyPayoutRecord.fields.investment') }}</label>
                <select class="form-control select2 {{ $errors->has('investment') ? 'is-invalid' : '' }}" name="investment_id" id="investment_id" required>
                    @foreach($investments as $id => $entry)
                        <option value="{{ $id }}" {{ old('investment_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('investment'))
                    <span class="text-danger">{{ $errors->first('investment') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.monthlyPayoutRecord.fields.investment_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="investor_id">{{ trans('cruds.monthlyPayoutRecord.fields.investor') }}</label>
                <select class="form-control select2 {{ $errors->has('investor') ? 'is-invalid' : '' }}" name="investor_id" id="investor_id" required>
                    @foreach($investors as $id => $entry)
                        <option value="{{ $id }}" {{ old('investor_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('investor'))
                    <span class="text-danger">{{ $errors->first('investor') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.monthlyPayoutRecord.fields.investor_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="secure_interest_amount">{{ trans('cruds.monthlyPayoutRecord.fields.secure_interest_amount') }}</label>
                <input class="form-control {{ $errors->has('secure_interest_amount') ? 'is-invalid' : '' }}" type="text" name="secure_interest_amount" id="secure_interest_amount" value="{{ old('secure_interest_amount', '') }}" required>
                @if($errors->has('secure_interest_amount'))
                    <span class="text-danger">{{ $errors->first('secure_interest_amount') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.monthlyPayoutRecord.fields.secure_interest_amount_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="market_interest_amount">{{ trans('cruds.monthlyPayoutRecord.fields.market_interest_amount') }}</label>
                <input class="form-control {{ $errors->has('market_interest_amount') ? 'is-invalid' : '' }}" type="text" name="market_interest_amount" id="market_interest_amount" value="{{ old('market_interest_amount', '') }}" required>
                @if($errors->has('market_interest_amount'))
                    <span class="text-danger">{{ $errors->first('market_interest_amount') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.monthlyPayoutRecord.fields.market_interest_amount_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="total_payout_amount">{{ trans('cruds.monthlyPayoutRecord.fields.total_payout_amount') }}</label>
                <input class="form-control {{ $errors->has('total_payout_amount') ? 'is-invalid' : '' }}" type="number" name="total_payout_amount" id="total_payout_amount" value="{{ old('total_payout_amount', '') }}" step="0.01" required>
                @if($errors->has('total_payout_amount'))
                    <span class="text-danger">{{ $errors->first('total_payout_amount') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.monthlyPayoutRecord.fields.total_payout_amount_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="month_for">{{ trans('cruds.monthlyPayoutRecord.fields.month_for') }}</label>
                <input class="form-control date {{ $errors->has('month_for') ? 'is-invalid' : '' }}" type="text" name="month_for" id="month_for" value="{{ old('month_for') }}" required>
                @if($errors->has('month_for'))
                    <span class="text-danger">{{ $errors->first('month_for') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.monthlyPayoutRecord.fields.month_for_helper') }}</span>
            </div>
            <div class="form-group">
                <label>{{ trans('cruds.monthlyPayoutRecord.fields.status') }}</label>
                <select class="form-control {{ $errors->has('status') ? 'is-invalid' : '' }}" name="status" id="status">
                    <option value disabled {{ old('status', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                    @foreach(App\Models\MonthlyPayoutRecord::STATUS_SELECT as $key => $label)
                        <option value="{{ $key }}" {{ old('status', 'pending') === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @if($errors->has('status'))
                    <span class="text-danger">{{ $errors->first('status') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.monthlyPayoutRecord.fields.status_helper') }}</span>
            </div>
            <div class="form-group">
                <button class="btn btn-danger" type="submit">
                    {{ trans('global.save') }}
                </button>
            </div>
        </form>
    </div>
</div>



@endsection
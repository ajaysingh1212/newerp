@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.plan.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.plans.update", [$plan->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="form-group">
                <label class="required" for="plan_name">{{ trans('cruds.plan.fields.plan_name') }}</label>
                <input class="form-control {{ $errors->has('plan_name') ? 'is-invalid' : '' }}" type="text" name="plan_name" id="plan_name" value="{{ old('plan_name', $plan->plan_name) }}" required>
                @if($errors->has('plan_name'))
                    <span class="text-danger">{{ $errors->first('plan_name') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.plan.fields.plan_name_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="secure_interest_percent">{{ trans('cruds.plan.fields.secure_interest_percent') }}</label>
                <input class="form-control {{ $errors->has('secure_interest_percent') ? 'is-invalid' : '' }}" type="text" name="secure_interest_percent" id="secure_interest_percent" value="{{ old('secure_interest_percent', $plan->secure_interest_percent) }}" required>
                @if($errors->has('secure_interest_percent'))
                    <span class="text-danger">{{ $errors->first('secure_interest_percent') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.plan.fields.secure_interest_percent_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="market_interest_percent">{{ trans('cruds.plan.fields.market_interest_percent') }}</label>
                <input class="form-control {{ $errors->has('market_interest_percent') ? 'is-invalid' : '' }}" type="text" name="market_interest_percent" id="market_interest_percent" value="{{ old('market_interest_percent', $plan->market_interest_percent) }}" required>
                @if($errors->has('market_interest_percent'))
                    <span class="text-danger">{{ $errors->first('market_interest_percent') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.plan.fields.market_interest_percent_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="total_interest_percent">{{ trans('cruds.plan.fields.total_interest_percent') }}</label>
                <input class="form-control {{ $errors->has('total_interest_percent') ? 'is-invalid' : '' }}" type="text" name="total_interest_percent" id="total_interest_percent" value="{{ old('total_interest_percent', $plan->total_interest_percent) }}" required>
                @if($errors->has('total_interest_percent'))
                    <span class="text-danger">{{ $errors->first('total_interest_percent') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.plan.fields.total_interest_percent_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required">{{ trans('cruds.plan.fields.payout_frequency') }}</label>
                <select class="form-control {{ $errors->has('payout_frequency') ? 'is-invalid' : '' }}" name="payout_frequency" id="payout_frequency" required>
                    <option value disabled {{ old('payout_frequency', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                    @foreach(App\Models\Plan::PAYOUT_FREQUENCY_SELECT as $key => $label)
                        <option value="{{ $key }}" {{ old('payout_frequency', $plan->payout_frequency) === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @if($errors->has('payout_frequency'))
                    <span class="text-danger">{{ $errors->first('payout_frequency') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.plan.fields.payout_frequency_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="min_invest_amount">{{ trans('cruds.plan.fields.min_invest_amount') }}</label>
                <input class="form-control {{ $errors->has('min_invest_amount') ? 'is-invalid' : '' }}" type="number" name="min_invest_amount" id="min_invest_amount" value="{{ old('min_invest_amount', $plan->min_invest_amount) }}" step="0.01" required>
                @if($errors->has('min_invest_amount'))
                    <span class="text-danger">{{ $errors->first('min_invest_amount') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.plan.fields.min_invest_amount_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="max_invest_amount">{{ trans('cruds.plan.fields.max_invest_amount') }}</label>
                <input class="form-control {{ $errors->has('max_invest_amount') ? 'is-invalid' : '' }}" type="number" name="max_invest_amount" id="max_invest_amount" value="{{ old('max_invest_amount', $plan->max_invest_amount) }}" step="0.01" required>
                @if($errors->has('max_invest_amount'))
                    <span class="text-danger">{{ $errors->first('max_invest_amount') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.plan.fields.max_invest_amount_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="lockin_days">{{ trans('cruds.plan.fields.lockin_days') }}</label>
                <input class="form-control {{ $errors->has('lockin_days') ? 'is-invalid' : '' }}" type="text" name="lockin_days" id="lockin_days" value="{{ old('lockin_days', $plan->lockin_days) }}" required>
                @if($errors->has('lockin_days'))
                    <span class="text-danger">{{ $errors->first('lockin_days') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.plan.fields.lockin_days_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="withdraw_processing_hours">{{ trans('cruds.plan.fields.withdraw_processing_hours') }}</label>
                <input class="form-control {{ $errors->has('withdraw_processing_hours') ? 'is-invalid' : '' }}" type="text" name="withdraw_processing_hours" id="withdraw_processing_hours" value="{{ old('withdraw_processing_hours', $plan->withdraw_processing_hours) }}" required>
                @if($errors->has('withdraw_processing_hours'))
                    <span class="text-danger">{{ $errors->first('withdraw_processing_hours') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.plan.fields.withdraw_processing_hours_helper') }}</span>
            </div>
            <div class="form-group">
                <label>{{ trans('cruds.plan.fields.status') }}</label>
                <select class="form-control {{ $errors->has('status') ? 'is-invalid' : '' }}" name="status" id="status">
                    <option value disabled {{ old('status', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                    @foreach(App\Models\Plan::STATUS_SELECT as $key => $label)
                        <option value="{{ $key }}" {{ old('status', $plan->status) === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @if($errors->has('status'))
                    <span class="text-danger">{{ $errors->first('status') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.plan.fields.status_helper') }}</span>
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
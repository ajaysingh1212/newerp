@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('cruds.investment.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.investments.store") }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label class="required" for="select_investor_id">{{ trans('cruds.investment.fields.select_investor') }}</label>
                <select class="form-control select2 {{ $errors->has('select_investor') ? 'is-invalid' : '' }}" name="select_investor_id" id="select_investor_id" required>
                    @foreach($select_investors as $id => $entry)
                        <option value="{{ $id }}" {{ old('select_investor_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('select_investor'))
                    <span class="text-danger">{{ $errors->first('select_investor') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.investment.fields.select_investor_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="select_plan_id">{{ trans('cruds.investment.fields.select_plan') }}</label>
                <select class="form-control select2 {{ $errors->has('select_plan') ? 'is-invalid' : '' }}" name="select_plan_id" id="select_plan_id">
                    @foreach($select_plans as $id => $entry)
                        <option value="{{ $id }}" {{ old('select_plan_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('select_plan'))
                    <span class="text-danger">{{ $errors->first('select_plan') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.investment.fields.select_plan_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="principal_amount">{{ trans('cruds.investment.fields.principal_amount') }}</label>
                <input class="form-control {{ $errors->has('principal_amount') ? 'is-invalid' : '' }}" type="number" name="principal_amount" id="principal_amount" value="{{ old('principal_amount', '') }}" step="0.01" required>
                @if($errors->has('principal_amount'))
                    <span class="text-danger">{{ $errors->first('principal_amount') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.investment.fields.principal_amount_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="secure_interest_percent">{{ trans('cruds.investment.fields.secure_interest_percent') }}</label>
                <input class="form-control {{ $errors->has('secure_interest_percent') ? 'is-invalid' : '' }}" type="text" name="secure_interest_percent" id="secure_interest_percent" value="{{ old('secure_interest_percent', '') }}">
                @if($errors->has('secure_interest_percent'))
                    <span class="text-danger">{{ $errors->first('secure_interest_percent') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.investment.fields.secure_interest_percent_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="market_interest_percent">{{ trans('cruds.investment.fields.market_interest_percent') }}</label>
                <input class="form-control {{ $errors->has('market_interest_percent') ? 'is-invalid' : '' }}" type="text" name="market_interest_percent" id="market_interest_percent" value="{{ old('market_interest_percent', '') }}">
                @if($errors->has('market_interest_percent'))
                    <span class="text-danger">{{ $errors->first('market_interest_percent') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.investment.fields.market_interest_percent_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="total_interest_percent">{{ trans('cruds.investment.fields.total_interest_percent') }}</label>
                <input class="form-control {{ $errors->has('total_interest_percent') ? 'is-invalid' : '' }}" type="text" name="total_interest_percent" id="total_interest_percent" value="{{ old('total_interest_percent', '') }}">
                @if($errors->has('total_interest_percent'))
                    <span class="text-danger">{{ $errors->first('total_interest_percent') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.investment.fields.total_interest_percent_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="start_date">{{ trans('cruds.investment.fields.start_date') }}</label>
                <input class="form-control date {{ $errors->has('start_date') ? 'is-invalid' : '' }}" type="text" name="start_date" id="start_date" value="{{ old('start_date') }}" required>
                @if($errors->has('start_date'))
                    <span class="text-danger">{{ $errors->first('start_date') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.investment.fields.start_date_helper') }}</span>
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
@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.rechargePlan.title_singular') }}
    </div>

    <div class="card-body">
        @include('watermark')
        <form method="POST" action="{{ route("admin.recharge-plans.update", [$rechargePlan->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="card px-3">
                <h4 class= "text-center mt-2 py-2 bg-1">Create User alert</h4>
            <div class="row">
            <div class="form-group col-lg-4">
                <label class="required" for="type">{{ trans('cruds.rechargePlan.fields.type') }}</label>
                <input class="form-control {{ $errors->has('type') ? 'is-invalid' : '' }}" type="text" name="type" id="type" value="{{ old('type', $rechargePlan->type) }}" required>
                @if($errors->has('type'))
                    <span class="text-danger">{{ $errors->first('type') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.rechargePlan.fields.type_helper') }}</span>
            </div>
            <div class="form-group col-lg-4">
                <label class="required" for="plan_name">{{ trans('cruds.rechargePlan.fields.plan_name') }}</label>
                <input class="form-control {{ $errors->has('plan_name') ? 'is-invalid' : '' }}" type="text" name="plan_name" id="plan_name" value="{{ old('plan_name', $rechargePlan->plan_name) }}" required>
                @if($errors->has('plan_name'))
                    <span class="text-danger">{{ $errors->first('plan_name') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.rechargePlan.fields.plan_name_helper') }}</span>
            </div>
             <div class="form-group col-lg-4">
                <label class="required" for="price">{{ trans('cruds.rechargePlan.fields.price') }}</label>
                <input class="form-control {{ $errors->has('price') ? 'is-invalid' : '' }}" type="text" name="price" id="price" value="{{ old('price', $rechargePlan->price) }}" required>
                @if($errors->has('price'))
                    <span class="text-danger">{{ $errors->first('price') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.rechargePlan.fields.price_helper') }}</span>
            </div>
            <div class="form-group col-lg-4">
                <label for="amc_duration">{{ trans('cruds.rechargePlan.fields.amc_duration') }}</label>
                <input class="form-control {{ $errors->has('amc_duration') ? 'is-invalid' : '' }}" type="text" name="amc_duration" id="amc_duration" value="{{ old('amc_duration', $rechargePlan->amc_duration) }}">
                @if($errors->has('amc_duration'))
                    <span class="text-danger">{{ $errors->first('amc_duration') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.rechargePlan.fields.amc_duration_helper') }}</span>
            </div>
            <div class="form-group col-lg-4">
                <label for="warranty_duration">{{ trans('cruds.rechargePlan.fields.warranty_duration') }}</label>
                <input class="form-control {{ $errors->has('warranty_duration') ? 'is-invalid' : '' }}" type="text" name="warranty_duration" id="warranty_duration" value="{{ old('warranty_duration', $rechargePlan->warranty_duration) }}">
                @if($errors->has('warranty_duration'))
                    <span class="text-danger">{{ $errors->first('warranty_duration') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.rechargePlan.fields.warranty_duration_helper') }}</span>
            </div>
            <div class="form-group col-lg-4">
                <label for="subscription_duration">{{ trans('cruds.rechargePlan.fields.subscription_duration') }}</label>
                <input class="form-control {{ $errors->has('subscription_duration') ? 'is-invalid' : '' }}" type="text" name="subscription_duration" id="subscription_duration" value="{{ old('subscription_duration', $rechargePlan->subscription_duration) }}">
                @if($errors->has('subscription_duration'))
                    <span class="text-danger">{{ $errors->first('subscription_duration') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.rechargePlan.fields.subscription_duration_helper') }}</span>
            </div>
            <div class="form-group col-lg-12">
                <label for="discription">{{ trans('cruds.rechargePlan.fields.discription') }}</label>
                <textarea class="form-control {{ $errors->has('discription') ? 'is-invalid' : '' }}" name="discription" id="discription">{{ old('discription', $rechargePlan->discription) }}</textarea>
                @if($errors->has('discription'))
                    <span class="text-danger">{{ $errors->first('discription') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.rechargePlan.fields.discription_helper') }}</span>
            </div>
            </div>
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
@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.imeiMaster.title_singular') }}
    </div>

    <div class="card-body">
        @include('watermark')
        <form method="POST" action="{{ route("admin.imei-masters.update", [$imeiMaster->id]) }}" enctype="multipart/form-data" class="row">
            @method('PUT')
            @csrf
            <div class="form-group col-md-6">
                <label class="required" for="imei_model_id">{{ trans('cruds.imeiMaster.fields.imei_model') }}</label>
                <select class="form-control select2 {{ $errors->has('imei_model') ? 'is-invalid' : '' }}" name="imei_model_id" id="imei_model_id" required>
                    @foreach($imei_models as $id => $entry)
                        <option value="{{ $id }}" {{ (old('imei_model_id') ? old('imei_model_id') : $imeiMaster->imei_model->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('imei_model'))
                    <span class="text-danger">{{ $errors->first('imei_model') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.imeiMaster.fields.imei_model_helper') }}</span>
            </div>
            <div class="form-group col-md-6">
                <label class="required" for="imei_number">{{ trans('cruds.imeiMaster.fields.imei_number') }}</label>
                <input class="form-control {{ $errors->has('imei_number') ? 'is-invalid' : '' }}" type="text" name="imei_number" id="imei_number" value="{{ old('imei_number', $imeiMaster->imei_number) }}" required>
                @if($errors->has('imei_number'))
                    <span class="text-danger">{{ $errors->first('imei_number') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.imeiMaster.fields.imei_number_helper') }}</span>
            </div>
            <div class="form-group col-md-6">
                <label class="required">{{ trans('cruds.imeiMaster.fields.status') }}</label>
                <select class="form-control {{ $errors->has('status') ? 'is-invalid' : '' }}" name="status" id="status" required>
                    <option value disabled {{ old('status', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                    @foreach(App\Models\ImeiMaster::STATUS_SELECT as $key => $label)
                        <option value="{{ $key }}" {{ old('status', $imeiMaster->status) === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @if($errors->has('status'))
                    <span class="text-danger">{{ $errors->first('status') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.imeiMaster.fields.status_helper') }}</span>
            </div>
            <div class="form-group col-md-6">
                <label>{{ trans('cruds.imeiMaster.fields.product_status') }}</label>
                <select class="form-control {{ $errors->has('product_status') ? 'is-invalid' : '' }}" name="product_status" id="product_status">
                    <option value disabled {{ old('product_status', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                    @foreach(App\Models\ImeiMaster::PRODUCT_STATUS_SELECT as $key => $label)
                        <option value="{{ $key }}" {{ old('product_status', $imeiMaster->product_status) === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @if($errors->has('product_status'))
                    <span class="text-danger">{{ $errors->first('product_status') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.imeiMaster.fields.product_status_helper') }}</span>
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
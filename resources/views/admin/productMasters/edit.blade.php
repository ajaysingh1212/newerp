@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.productMaster.title_singular') }}
    </div>

    <div class="card-body">
        @include('watermark')
        <form method="POST" action="{{ route("admin.product-masters.update", [$productMaster->id]) }}" enctype="multipart/form-data" class="row">
            @method('PUT')
            @csrf
            <div class="card px-3">
                <h4 class= "text-center mt-2 py-2 bg-1">Edit Product Master</h4>
            <div class="row">
            <div class="form-group col-md-4">
                <label for="product_model_id">{{ trans('cruds.productMaster.fields.product_model') }}</label>
                <select class="form-control select2 {{ $errors->has('product_model') ? 'is-invalid' : '' }}" name="product_model_id" id="product_model_id">
                    @foreach($product_models as $id => $entry)
                        <option value="{{ $id }}" {{ (old('product_model_id') ? old('product_model_id') : $productMaster->product_model->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('product_model'))
                    <span class="text-danger">{{ $errors->first('product_model') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.productMaster.fields.product_model_helper') }}</span>
            </div>
            <div class="form-group col-md-4">
                <label class="required" for="imei_id">{{ trans('cruds.productMaster.fields.imei') }}</label>
                <select class="form-control select2 {{ $errors->has('imei') ? 'is-invalid' : '' }}" name="imei_id" id="imei_id" required>
                    @foreach($imeis as $id => $entry)
                        <option value="{{ $id }}" {{ (old('imei_id') ? old('imei_id') : $productMaster->imei->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('imei'))
                    <span class="text-danger">{{ $errors->first('imei') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.productMaster.fields.imei_helper') }}</span>
            </div>
            <div class="form-group col-md-4">
                <label for="vts_id">{{ trans('cruds.productMaster.fields.vts') }}</label>
                <select class="form-control select2 {{ $errors->has('vts') ? 'is-invalid' : '' }}" name="vts_id" id="vts_id">
                    @foreach($vts as $id => $entry)
                        <option value="{{ $id }}" {{ (old('vts_id') ? old('vts_id') : $productMaster->vts->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('vts'))
                    <span class="text-danger">{{ $errors->first('vts') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.productMaster.fields.vts_helper') }}</span>
            </div>
            <div class="form-group col-md-4">
                <label class="required" for="warranty">{{ trans('cruds.productMaster.fields.warranty') }}</label>
                <input class="form-control {{ $errors->has('warranty') ? 'is-invalid' : '' }}" type="text" name="warranty" id="warranty" value="{{ old('warranty', $productMaster->warranty) }}" required>
                @if($errors->has('warranty'))
                    <span class="text-danger">{{ $errors->first('warranty') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.productMaster.fields.warranty_helper') }}</span>
            </div>
            <div class="form-group col-md-4">
                <label class="required" for="subscription">{{ trans('cruds.productMaster.fields.subscription') }}</label>
                <input class="form-control {{ $errors->has('subscription') ? 'is-invalid' : '' }}" type="text" name="subscription" id="subscription" value="{{ old('subscription', $productMaster->subscription) }}" required>
                @if($errors->has('subscription'))
                    <span class="text-danger">{{ $errors->first('subscription') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.productMaster.fields.subscription_helper') }}</span>
            </div>
            <div class="form-group col-md-4">
                <label class="required" for="amc">{{ trans('cruds.productMaster.fields.amc') }}</label>
                <input class="form-control {{ $errors->has('amc') ? 'is-invalid' : '' }}" type="text" name="amc" id="amc" value="{{ old('amc', $productMaster->amc) }}" required>
                @if($errors->has('amc'))
                    <span class="text-danger">{{ $errors->first('amc') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.productMaster.fields.amc_helper') }}</span>
            </div>
            <div class="form-group col-md-12">
                <label class="required">{{ trans('cruds.productMaster.fields.status') }}</label>
                <select class="form-control {{ $errors->has('status') ? 'is-invalid' : '' }}" name="status" id="status" required>
                    <option value disabled {{ old('status', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                    @foreach(App\Models\ProductMaster::STATUS_SELECT as $key => $label)
                        <option value="{{ $key }}" {{ old('status', $productMaster->status) === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @if($errors->has('status'))
                    <span class="text-danger">{{ $errors->first('status') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.productMaster.fields.status_helper') }}</span>
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
@extends('layouts.admin')
@section('content')


<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('cruds.productModel.title_singular') }}
    </div>

    <div class="card-body">
        @include('watermark')
        <form method="POST" action="{{ route("admin.product-models.store") }}" enctype="multipart/form-data" class="row">
            @csrf
            <div class="card px-3">
                <h4 class= "text-center mt-2 py-2 bg-1"> Create Product Model</h4>
            <div class="row">
            <div class="form-group col-md-4">
                <label class="required" for="product_model">{{ trans('cruds.productModel.fields.product_model') }}</label>
                <input class="form-control {{ $errors->has('product_model') ? 'is-invalid' : '' }}" type="text" name="product_model" id="product_model" value="{{ old('product_model', '') }}" required>
                @if($errors->has('product_model'))
                    <span class="text-danger">{{ $errors->first('product_model') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.productModel.fields.product_model_helper') }}</span>
            </div>
            <div class="form-group col-md-4">
                <label class="required" for="warranty">{{ trans('cruds.productModel.fields.warranty') }}</label>
                <input class="form-control {{ $errors->has('warranty') ? 'is-invalid' : '' }}" type="text" name="warranty" id="warranty" value="{{ old('warranty', '') }}" required>
                @if($errors->has('warranty'))
                    <span class="text-danger">{{ $errors->first('warranty') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.productModel.fields.warranty_helper') }}</span>
            </div>
            <div class="form-group col-md-4">
                <label class="required" for="subscription">{{ trans('cruds.productModel.fields.subscription') }}</label>
                <input class="form-control {{ $errors->has('subscription') ? 'is-invalid' : '' }}" type="text" name="subscription" id="subscription" value="{{ old('subscription', '') }}" required>
                @if($errors->has('subscription'))
                    <span class="text-danger">{{ $errors->first('subscription') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.productModel.fields.subscription_helper') }}</span>
            </div>
            <div class="form-group col-md-4">
                <label class="required" for="amc">{{ trans('cruds.productModel.fields.amc') }}</label>
                <input class="form-control {{ $errors->has('amc') ? 'is-invalid' : '' }}" type="text" name="amc" id="amc" value="{{ old('amc', '') }}" required>
                @if($errors->has('amc'))
                    <span class="text-danger">{{ $errors->first('amc') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.productModel.fields.amc_helper') }}</span>
            </div>
            <div class="form-group col-md-4">
                <label class="required" for="mrp">{{ trans('cruds.productModel.fields.mrp') }}</label>
                <input class="form-control {{ $errors->has('mrp') ? 'is-invalid' : '' }}" type="text" name="mrp" id="mrp" value="{{ old('mrp', '') }}" required>
                @if($errors->has('mrp'))
                    <span class="text-danger">{{ $errors->first('mrp') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.productModel.fields.mrp_helper') }}</span>
            </div>
            <div class="form-group col-md-4">
                <label class="required" for="cnf_price">{{ trans('cruds.productModel.fields.cnf_price') }}</label>
                <input class="form-control {{ $errors->has('cnf_price') ? 'is-invalid' : '' }}" type="text" name="cnf_price" id="cnf_price" value="{{ old('cnf_price', '') }}" required>
                @if($errors->has('cnf_price'))
                    <span class="text-danger">{{ $errors->first('cnf_price') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.productModel.fields.cnf_price_helper') }}</span>
            </div>
            <div class="form-group  col-md-3">
                <label class="required" for="distributor_price">{{ trans('cruds.productModel.fields.distributor_price') }}</label>
                <input class="form-control {{ $errors->has('distributor_price') ? 'is-invalid' : '' }}" type="text" name="distributor_price" id="distributor_price" value="{{ old('distributor_price', '') }}" required>
                @if($errors->has('distributor_price'))
                    <span class="text-danger">{{ $errors->first('distributor_price') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.productModel.fields.distributor_price_helper') }}</span>
            </div>
            <div class="form-group col-md-3">
                <label class="required" for="dealer_price">{{ trans('cruds.productModel.fields.dealer_price') }}</label>
                <input class="form-control {{ $errors->has('dealer_price') ? 'is-invalid' : '' }}" type="text" name="dealer_price" id="dealer_price" value="{{ old('dealer_price', '') }}" required>
                @if($errors->has('dealer_price'))
                    <span class="text-danger">{{ $errors->first('dealer_price') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.productModel.fields.dealer_price_helper') }}</span>
            </div>
            <div class="form-group  col-md-3">
                <label class="required" for="customer_price">{{ trans('cruds.productModel.fields.customer_price') }}</label>
                <input class="form-control {{ $errors->has('customer_price') ? 'is-invalid' : '' }}" type="text" name="customer_price" id="customer_price" value="{{ old('customer_price', '') }}" required>
                @if($errors->has('customer_price'))
                    <span class="text-danger">{{ $errors->first('customer_price') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.productModel.fields.customer_price_helper') }}</span>
            </div>
            <div class="form-group col-md-3">
                <label class="required">{{ trans('cruds.productModel.fields.status') }}</label>
                <select class="form-control {{ $errors->has('status') ? 'is-invalid' : '' }}" name="status" id="status" required>
                    <option value disabled {{ old('status', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                    @foreach(App\Models\ProductModel::STATUS_SELECT as $key => $label)
                        <option value="{{ $key }}" {{ old('status', 'enable') === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @if($errors->has('status'))
                    <span class="text-danger">{{ $errors->first('status') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.productModel.fields.status_helper') }}</span>
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
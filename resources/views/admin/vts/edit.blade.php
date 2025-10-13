@extends('layouts.admin')
@section('content')

<style>
    .card .bg-1{
        background: #8776cc;
background: linear-gradient(18deg, rgba(135, 118, 204, 1) 13%, rgba(210, 231, 250, 1) 56%);
    }
</style>

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.vt.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.vts.update", [$vt->id]) }}" enctype="multipart/form-data" class="row">
            @method('PUT')
            @csrf
            <div class="card px-3">
                <h4 class= "text-center mt-2 py-2 bg-1"> Edit Vt</h4>
         <div class="row">
            <div class="form-group col-md-4">
                <label class="required" for="vts_number">{{ trans('cruds.vt.fields.vts_number') }}</label>
                <input class="form-control {{ $errors->has('vts_number') ? 'is-invalid' : '' }}" type="text" name="vts_number" id="vts_number" value="{{ old('vts_number', $vt->vts_number) }}" required>
                @if($errors->has('vts_number'))
                    <span class="text-danger">{{ $errors->first('vts_number') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.vt.fields.vts_number_helper') }}</span>
            </div>
            <div class="form-group col-md-4">
                <label class="required" for="sim_number">{{ trans('cruds.vt.fields.sim_number') }}</label>
                <input class="form-control {{ $errors->has('sim_number') ? 'is-invalid' : '' }}" type="text" name="sim_number" id="sim_number" value="{{ old('sim_number', $vt->sim_number) }}" required>
                @if($errors->has('sim_number'))
                    <span class="text-danger">{{ $errors->first('sim_number') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.vt.fields.sim_number_helper') }}</span>
            </div>
            <div class="form-group col-md-4">
                <label class="required" for="operator">{{ trans('cruds.vt.fields.operator') }}</label>
                <input class="form-control {{ $errors->has('operator') ? 'is-invalid' : '' }}" type="text" name="operator" id="operator" value="{{ old('operator', $vt->operator) }}" required>
                @if($errors->has('operator'))
                    <span class="text-danger">{{ $errors->first('operator') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.vt.fields.operator_helper') }}</span>
            </div>
            <div class="form-group col-md-6">
                <label class="required">{{ trans('cruds.vt.fields.product_status') }}</label>
                <select class="form-control {{ $errors->has('product_status') ? 'is-invalid' : '' }}" name="product_status" id="product_status" required>
                    <option value disabled {{ old('product_status', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                    @foreach(App\Models\Vt::PRODUCT_STATUS_SELECT as $key => $label)
                        <option value="{{ $key }}" {{ old('product_status', $vt->product_status) === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @if($errors->has('product_status'))
                    <span class="text-danger">{{ $errors->first('product_status') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.vt.fields.product_status_helper') }}</span>
            </div>
            <div class="form-group col-md-6">
                <label class="required">{{ trans('cruds.vt.fields.status') }}</label>
                <select class="form-control {{ $errors->has('status') ? 'is-invalid' : '' }}" name="status" id="status" required>
                    <option value disabled {{ old('status', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                    @foreach(App\Models\Vt::STATUS_SELECT as $key => $label)
                        <option value="{{ $key }}" {{ old('status', $vt->status) === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @if($errors->has('status'))
                    <span class="text-danger">{{ $errors->first('status') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.vt.fields.status_helper') }}</span>
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
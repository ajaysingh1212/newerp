@extends('layouts.admin')
@section('content')


<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.imeiModel.title_singular') }}
    </div>

    <div class="card-body">
        @include('watermark')
        <form method="POST" action="{{ route("admin.imei-models.update", [$imeiModel->id]) }}" enctype="multipart/form-data" >
            @method('PUT')
            @csrf
             <div class="card px-3">
                <h4 class= "text-center mt-2 py-2 bg-1"> Edit Imei Models</h4>
         <div class="row">
            <div class="form-group col-md-6">
                <label class="required" for="imei_model_number">{{ trans('cruds.imeiModel.fields.imei_model_number') }}</label>
                <input class="form-control {{ $errors->has('imei_model_number') ? 'is-invalid' : '' }}" type="text" name="imei_model_number" id="imei_model_number" value="{{ old('imei_model_number', $imeiModel->imei_model_number) }}" required>
                @if($errors->has('imei_model_number'))
                    <span class="text-danger">{{ $errors->first('imei_model_number') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.imeiModel.fields.imei_model_number_helper') }}</span>
            </div>
            <div class="form-group col-md-6">
                <label class="required">{{ trans('cruds.imeiModel.fields.status') }}</label>
                <select class="form-control {{ $errors->has('status') ? 'is-invalid' : '' }}" name="status" id="status" required>
                    <option value disabled {{ old('status', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                    @foreach(App\Models\ImeiModel::STATUS_SELECT as $key => $label)
                        <option value="{{ $key }}" {{ old('status', $imeiModel->status) === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @if($errors->has('status'))
                    <span class="text-danger">{{ $errors->first('status') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.imeiModel.fields.status_helper') }}</span>
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
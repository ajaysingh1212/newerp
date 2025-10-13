@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('cruds.state.title_singular') }}
    </div>

    <div class="card-body">
        @include('watermark')
        <form method="POST" action="{{ route("admin.states.store") }}" enctype="multipart/form-data">
            @csrf
            <div class="card px-3">
                <h4 class= "text-center mt-2 py-2 bg-1"> Add State</h4>
         <div class="row">
            <div class="form-group col-lg-6">
                <label class="required" for="state_name">{{ trans('cruds.state.fields.state_name') }}</label>
                <input class="form-control {{ $errors->has('state_name') ? 'is-invalid' : '' }}" type="text" name="state_name" id="state_name" value="{{ old('state_name', '') }}" required>
                @if($errors->has('state_name'))
                    <span class="text-danger">{{ $errors->first('state_name') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.state.fields.state_name_helper') }}</span>
            </div>
            <div class="form-group col-lg-6">
                <label class="required" for="country">{{ trans('cruds.state.fields.country') }}</label>
                <input class="form-control {{ $errors->has('country') ? 'is-invalid' : '' }}" type="text" name="country" id="country" value="{{ old('country', 'india') }}" required>
                @if($errors->has('country'))
                    <span class="text-danger">{{ $errors->first('country') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.state.fields.country_helper') }}</span>
            </div>
            <div class="form-group col-lg-12">
                <label class="required">{{ trans('cruds.state.fields.status') }}</label>
                <select class="form-control {{ $errors->has('status') ? 'is-invalid' : '' }}" name="status" id="status" required>
                    <option value disabled {{ old('status', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                    @foreach(App\Models\State::STATUS_SELECT as $key => $label)
                        <option value="{{ $key }}" {{ old('status', 'enable') === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @if($errors->has('status'))
                    <span class="text-danger">{{ $errors->first('status') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.state.fields.status_helper') }}</span>
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
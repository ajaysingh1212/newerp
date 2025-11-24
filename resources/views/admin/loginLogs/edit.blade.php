@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.loginLog.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.login-logs.update", [$loginLog->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="form-group">
                <label class="required" for="use_id">{{ trans('cruds.loginLog.fields.use') }}</label>
                <select class="form-control select2 {{ $errors->has('use') ? 'is-invalid' : '' }}" name="use_id" id="use_id" required>
                    @foreach($uses as $id => $entry)
                        <option value="{{ $id }}" {{ (old('use_id') ? old('use_id') : $loginLog->use->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('use'))
                    <span class="text-danger">{{ $errors->first('use') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.loginLog.fields.use_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="ip_address">{{ trans('cruds.loginLog.fields.ip_address') }}</label>
                <input class="form-control {{ $errors->has('ip_address') ? 'is-invalid' : '' }}" type="text" name="ip_address" id="ip_address" value="{{ old('ip_address', $loginLog->ip_address) }}">
                @if($errors->has('ip_address'))
                    <span class="text-danger">{{ $errors->first('ip_address') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.loginLog.fields.ip_address_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="device">{{ trans('cruds.loginLog.fields.device') }}</label>
                <input class="form-control {{ $errors->has('device') ? 'is-invalid' : '' }}" type="text" name="device" id="device" value="{{ old('device', $loginLog->device) }}">
                @if($errors->has('device'))
                    <span class="text-danger">{{ $errors->first('device') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.loginLog.fields.device_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="location">{{ trans('cruds.loginLog.fields.location') }}</label>
                <input class="form-control {{ $errors->has('location') ? 'is-invalid' : '' }}" type="text" name="location" id="location" value="{{ old('location', $loginLog->location) }}">
                @if($errors->has('location'))
                    <span class="text-danger">{{ $errors->first('location') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.loginLog.fields.location_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="logged_in_at">{{ trans('cruds.loginLog.fields.logged_in_at') }}</label>
                <input class="form-control {{ $errors->has('logged_in_at') ? 'is-invalid' : '' }}" type="text" name="logged_in_at" id="logged_in_at" value="{{ old('logged_in_at', $loginLog->logged_in_at) }}">
                @if($errors->has('logged_in_at'))
                    <span class="text-danger">{{ $errors->first('logged_in_at') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.loginLog.fields.logged_in_at_helper') }}</span>
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
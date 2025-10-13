@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.district.title_singular') }}
    </div>

    <div class="card-body">
        @include('watermark')
        <form method="POST" action="{{ route("admin.districts.update", [$district->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
             <div class="card px-3">
                <h4 class= "text-center mt-2 py-2 bg-1">Edit District</h4>
         <div class="row">
            <div class="form-group col-lg-4">
                <label class="required" for="districts">{{ trans('cruds.district.fields.districts') }}</label>
                <input class="form-control {{ $errors->has('districts') ? 'is-invalid' : '' }}" type="text" name="districts" id="districts" value="{{ old('districts', $district->districts) }}" required>
                @if($errors->has('districts'))
                    <span class="text-danger">{{ $errors->first('districts') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.district.fields.districts_helper') }}</span>
            </div>
            <div class="form-group col-lg-4">
                <label class="required" for="country">{{ trans('cruds.district.fields.country') }}</label>
                <input class="form-control {{ $errors->has('country') ? 'is-invalid' : '' }}" type="text" name="country" id="country" value="{{ old('country', $district->country) }}" required>
                @if($errors->has('country'))
                    <span class="text-danger">{{ $errors->first('country') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.district.fields.country_helper') }}</span>
            </div>
            <div class="form-group col-lg-4">
                <label class="required" for="select_state_id">{{ trans('cruds.district.fields.select_state') }}</label>
                <select class="form-control select2 {{ $errors->has('select_state') ? 'is-invalid' : '' }}" name="select_state_id" id="select_state_id" required>
                    @foreach($select_states as $id => $entry)
                        <option value="{{ $id }}" {{ (old('select_state_id') ? old('select_state_id') : $district->select_state->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('select_state'))
                    <span class="text-danger">{{ $errors->first('select_state') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.district.fields.select_state_helper') }}</span>
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
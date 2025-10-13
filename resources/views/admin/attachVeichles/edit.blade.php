@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.attachVeichle.title_singular') }}
    </div>

    <div class="card-body">
        @include('watermark')
        <form method="POST" action="{{ route("admin.attach-veichles.update", [$attachVeichle->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="card px-3">
                <h4 class= "text-center mt-2 py-2 bg-1"> Attach Vehicle</h4>
         <div class="row">
            <div class="form-group col-lg-6">
                <label class="required" for="select_user_id">{{ trans('cruds.attachVeichle.fields.select_user') }}</label>
                <select class="form-control select2 {{ $errors->has('select_user') ? 'is-invalid' : '' }}" name="select_user_id" id="select_user_id" required>
                    @foreach($select_users as $id => $entry)
                        <option value="{{ $id }}" {{ (old('select_user_id') ? old('select_user_id') : $attachVeichle->select_user->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('select_user'))
                    <span class="text-danger">{{ $errors->first('select_user') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.attachVeichle.fields.select_user_helper') }}</span>
            </div>
            <div class="form-group col-lg-6">
                <label for="vehicles">{{ trans('cruds.attachVeichle.fields.vehicle') }}</label>
                <!-- <div style="padding-bottom: 4px">
                    <span class="btn btn-info btn-xs select-all" style="border-radius: 0">{{ trans('global.select_all') }}</span>
                    <span class="btn btn-info btn-xs deselect-all" style="border-radius: 0">{{ trans('global.deselect_all') }}</span>
                </div> -->
                <select class="form-control select2 {{ $errors->has('vehicles') ? 'is-invalid' : '' }}" name="vehicles[]" id="vehicles" multiple>
                    @foreach($vehicles as $id => $vehicle)
                        <option value="{{ $id }}" {{ (in_array($id, old('vehicles', [])) || $attachVeichle->vehicles->contains($id)) ? 'selected' : '' }}>{{ $vehicle }}</option>
                    @endforeach
                </select>
                @if($errors->has('vehicles'))
                    <span class="text-danger">{{ $errors->first('vehicles') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.attachVeichle.fields.vehicle_helper') }}</span>
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
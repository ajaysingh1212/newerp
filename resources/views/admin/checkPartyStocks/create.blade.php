@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('cruds.checkPartyStock.title_singular') }}
    </div>

    <div class="card-body">
        @include('watermark')
        <form method="POST" action="{{ route("admin.check-party-stocks.store") }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label for="select_parties">{{ trans('cruds.checkPartyStock.fields.select_party') }}</label>
                <div style="padding-bottom: 4px">
                    <span class="btn btn-info btn-xs select-all" style="border-radius: 0">{{ trans('global.select_all') }}</span>
                    <span class="btn btn-info btn-xs deselect-all" style="border-radius: 0">{{ trans('global.deselect_all') }}</span>
                </div>
                <select class="form-control select2 {{ $errors->has('select_parties') ? 'is-invalid' : '' }}" name="select_parties[]" id="select_parties" multiple>
                    @foreach($select_parties as $id => $select_party)
                        <option value="{{ $id }}" {{ in_array($id, old('select_parties', [])) ? 'selected' : '' }}>{{ $select_party }}</option>
                    @endforeach
                </select>
                @if($errors->has('select_parties'))
                    <span class="text-danger">{{ $errors->first('select_parties') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.checkPartyStock.fields.select_party_helper') }}</span>
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
@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.appLink.title_singular') }}
    </div>

    <div class="card-body">
         @include('watermark')
        <form method="POST" action="{{ route("admin.app-links.update", [$appLink->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf 
             <div class="card px-3">
                <h4 class= "text-center mt-2 py-2 bg-1">Create App link</h4>
            <div class="row">
            <div class="form-group col-lg-6">
                <label class="required" for="title">{{ trans('cruds.appLink.fields.title') }}</label>
                <input class="form-control {{ $errors->has('title') ? 'is-invalid' : '' }}" type="text" name="title" id="title" value="{{ old('title', $appLink->title) }}" required>
                @if($errors->has('title'))
                    <span class="text-danger">{{ $errors->first('title') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.appLink.fields.title_helper') }}</span>
            </div>
            <div class="form-group col-lg-6">
                <label class="required" for="link">{{ trans('cruds.appLink.fields.link') }}</label>
                <input class="form-control {{ $errors->has('link') ? 'is-invalid' : '' }}" type="text" name="link" id="link" value="{{ old('link', $appLink->link) }}" required>
                @if($errors->has('link'))
                    <span class="text-danger">{{ $errors->first('link') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.appLink.fields.link_helper') }}</span>
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
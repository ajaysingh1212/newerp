@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.vehicleType.title_singular') }}
    </div>

    <div class="card-body">
        @include('watermark')
        <form method="POST" action="{{ route("admin.vehicle-types.update", [$vehicleType->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="card px-3">
                <h4 class= "text-center mt-2 py-2 bg-1">Edit Team Name</h4>
            <div class="row">
            <div class="form-group col-lg-12">
                <label class="required" for="vehicle_type">{{ trans('cruds.vehicleType.fields.vehicle_type') }}</label>
                <input class="form-control {{ $errors->has('vehicle_type') ? 'is-invalid' : '' }}" type="text" name="vehicle_type" id="vehicle_type" value="{{ old('vehicle_type', $vehicleType->vehicle_type) }}" required>
                @if($errors->has('vehicle_type'))
                    <span class="text-danger">{{ $errors->first('vehicle_type') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.vehicleType.fields.vehicle_type_helper') }}</span>
            </div>
            <div class="form-group col-lg-12">
                <label class="required" for="vehicle_icon">{{ trans('cruds.vehicleType.fields.vehicle_icon') }}</label>
                <div class="needsclick dropzone {{ $errors->has('vehicle_icon') ? 'is-invalid' : '' }}" id="vehicle_icon-dropzone">
                </div>
                @if($errors->has('vehicle_icon'))
                    <span class="text-danger">{{ $errors->first('vehicle_icon') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.vehicleType.fields.vehicle_icon_helper') }}</span>
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

@section('scripts')
<script>
    Dropzone.options.vehicleIconDropzone = {
    url: '{{ route('admin.vehicle-types.storeMedia') }}',
    maxFilesize: 20, // MB
    acceptedFiles: '.jpeg,.jpg,.png,.gif',
    maxFiles: 1,
    addRemoveLinks: true,
    headers: {
      'X-CSRF-TOKEN': "{{ csrf_token() }}"
    },
    params: {
      size: 20,
      width: 4096,
      height: 4096
    },
    success: function (file, response) {
      $('form').find('input[name="vehicle_icon"]').remove()
      $('form').append('<input type="hidden" name="vehicle_icon" value="' + response.name + '">')
    },
    removedfile: function (file) {
      file.previewElement.remove()
      if (file.status !== 'error') {
        $('form').find('input[name="vehicle_icon"]').remove()
        this.options.maxFiles = this.options.maxFiles + 1
      }
    },
    init: function () {
@if(isset($vehicleType) && $vehicleType->vehicle_icon)
      var file = {!! json_encode($vehicleType->vehicle_icon) !!}
          this.options.addedfile.call(this, file)
      this.options.thumbnail.call(this, file, file.preview ?? file.preview_url)
      file.previewElement.classList.add('dz-complete')
      $('form').append('<input type="hidden" name="vehicle_icon" value="' + file.file_name + '">')
      this.options.maxFiles = this.options.maxFiles - 1
@endif
    },
    error: function (file, response) {
        if ($.type(response) === 'string') {
            var message = response //dropzone sends it's own error messages in string
        } else {
            var message = response.errors.file
        }
        file.previewElement.classList.add('dz-error')
        _ref = file.previewElement.querySelectorAll('[data-dz-errormessage]')
        _results = []
        for (_i = 0, _len = _ref.length; _i < _len; _i++) {
            node = _ref[_i]
            _results.push(node.textContent = message)
        }

        return _results
    }
}

</script>
@endsection
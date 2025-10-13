@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header" style="background: linear-gradient(90deg, #515b66 0%, #c04de6 100%); color: white;">
        {{ trans('global.edit') }} {{ trans('cruds.activationRequest.title_singular') }}
    </div>

    <div class="card-body">
         @include('watermark')
        <form method="POST" action="{{ route("admin.activation-requests.update", [$activationRequest->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="card shadow-sm rounded">
             <div class="card-header" style="background: linear-gradient(90deg, #515b66 0%, #c04de6 100%); color: white;">
                <h5 class="mb-0">Party & Activation Details</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    
            <div class="form-group col-md-3">
                <label class="required" for="party_type_id">{{ trans('cruds.activationRequest.fields.party_type') }}</label>
                <select class="form-control select2 {{ $errors->has('party_type') ? 'is-invalid' : '' }}" name="party_type_id" id="party_type_id" required>
                    @foreach($party_types as $id => $entry)
                        <option value="{{ $id }}" {{ (old('party_type_id') ? old('party_type_id') : $activationRequest->party_type->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('party_type'))
                    <span class="text-danger">{{ $errors->first('party_type') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.activationRequest.fields.party_type_helper') }}</span>
            </div>
            <div class="form-group col-md-3">
                <label class="required" for="select_party_id">{{ trans('cruds.activationRequest.fields.select_party') }}</label>
                <select class="form-control select2 {{ $errors->has('select_party') ? 'is-invalid' : '' }}" name="select_party_id" id="select_party_id" required>
                    @foreach($select_parties as $id => $entry)
                        <option value="{{ $id }}" {{ (old('select_party_id') ? old('select_party_id') : $activationRequest->select_party->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('select_party'))
                    <span class="text-danger">{{ $errors->first('select_party') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.activationRequest.fields.select_party_helper') }}</span>
            </div>
          <div class="form-group col-md-3">
    <label for="product_id">Product SKU</label>
    <input type="text" class="form-control" value="{{ $activationRequest->product->sku ?? 'N/A' }}" readonly>
<input type="" name="product_id" value="{{ $activationRequest->product_id }}">

</div>


            <div class="form-group col-md-3">
                <label class="required" for="customer_name">{{ trans('cruds.activationRequest.fields.customer_name') }}</label>
                <input class="form-control {{ $errors->has('customer_name') ? 'is-invalid' : '' }}" type="text" name="customer_name" id="customer_name" value="{{ old('customer_name', $activationRequest->customer_name) }}" required>
                @if($errors->has('customer_name'))
                    <span class="text-danger">{{ $errors->first('customer_name') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.activationRequest.fields.customer_name_helper') }}</span>
            </div>
            </div>
            </div>
            </div>

             <div class="card shadow-sm rounded">
             <div class="card-header" style="background: linear-gradient(90deg, #515b66 0%, #c04de6 100%); color: white;">
                <h5 class="mb-0">Customer Details</h5>
            </div>
            <div class="card-body">
                <div class="row">
            <div class="form-group col-md-3">
                <label class="required" for="mobile_number">{{ trans('cruds.activationRequest.fields.mobile_number') }}</label>
                <input class="form-control {{ $errors->has('mobile_number') ? 'is-invalid' : '' }}" type="text" name="mobile_number" id="mobile_number" value="{{ old('mobile_number', $activationRequest->mobile_number) }}" required>
                @if($errors->has('mobile_number'))
                    <span class="text-danger">{{ $errors->first('mobile_number') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.activationRequest.fields.mobile_number_helper') }}</span>
            </div>
            <div class="form-group col-md-3">
                <label for="whatsapp_number">{{ trans('cruds.activationRequest.fields.whatsapp_number') }}</label>
                <input class="form-control {{ $errors->has('whatsapp_number') ? 'is-invalid' : '' }}" type="text" name="whatsapp_number" id="whatsapp_number" value="{{ old('whatsapp_number', $activationRequest->whatsapp_number) }}">
                @if($errors->has('whatsapp_number'))
                    <span class="text-danger">{{ $errors->first('whatsapp_number') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.activationRequest.fields.whatsapp_number_helper') }}</span>
            </div>
            <div class="form-group col-md-3">
                <label for="email">{{ trans('cruds.activationRequest.fields.email') }}</label>
                <input class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}" type="email" name="email" id="email" value="{{ old('email', $activationRequest->email) }}">
                @if($errors->has('email'))
                    <span class="text-danger">{{ $errors->first('email') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.activationRequest.fields.email_helper') }}</span>
            </div>

             <div class="form-group col-md-3">
                <label for="state_id">State</label>
                <input class="form-control {{ $errors->has('state_id') ? 'is-invalid' : '' }}" type="state_id" name="state_id" id="state_id" value="{{ old('state_id', $activationRequest->state_id) }}">
                @if($errors->has('state_id'))
                    <span class="text-danger">{{ $errors->first('state_id') }}</span>
                @endif
            </div>
             <div class="form-group col-md-3">
                <label for="disrict_id">Disrict</label>
                <input class="form-control {{ $errors->has('disrict_id') ? 'is-invalid' : '' }}" type="disrict_id" name="disrict_id" id="disrict_id" value="{{ old('disrict_id', $activationRequest->disrict_id) }}">
                @if($errors->has('disrict_id'))
                    <span class="text-danger">{{ $errors->first('disrict_id') }}</span>
                @endif
            </div>
           

          
            <div class="form-group col-md-8">
                <label for="address">{{ trans('cruds.activationRequest.fields.address') }}</label>
                <textarea class="form-control ckeditor {{ $errors->has('address') ? 'is-invalid' : '' }}" name="address" id="address">{!! old('address', $activationRequest->address) !!}</textarea>
                @if($errors->has('address'))
                    <span class="text-danger">{{ $errors->first('address') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.activationRequest.fields.address_helper') }}</span>
            </div>

            </div>
            </div>
            </div>
            <div class="card shadow-sm rounded">
             <div class="card-header" style="background: linear-gradient(90deg, #515b66 0%, #c04de6 100%); color: white;">
                <h5 class="mb-0">Vehicle Details</h5>
            </div>
            <div class="card-body">
                <div class="row">
            
            
            <div class="form-group col-md-3">
                <label class="required" for="vehicle_model">{{ trans('cruds.activationRequest.fields.vehicle_model') }}</label>
                <input class="form-control {{ $errors->has('vehicle_model') ? 'is-invalid' : '' }}" type="text" name="vehicle_model" id="vehicle_model" value="{{ old('vehicle_model', $activationRequest->vehicle_model) }}" required>
                @if($errors->has('vehicle_model'))
                    <span class="text-danger">{{ $errors->first('vehicle_model') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.activationRequest.fields.vehicle_model_helper') }}</span>
            </div>
            <div class="form-group col-md-3" >
                <label for="vehicle_type_id">{{ trans('cruds.activationRequest.fields.vehicle_type') }}</label>
                <select class="form-control select2 {{ $errors->has('vehicle_type') ? 'is-invalid' : '' }}" name="vehicle_type_id" id="vehicle_type_id">
                    @foreach($vehicle_types as $id => $entry)
                        <option value="{{ $id }}" {{ (old('vehicle_type_id') ? old('vehicle_type_id') : $activationRequest->vehicle_type->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('vehicle_type'))
                    <span class="text-danger">{{ $errors->first('vehicle_type') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.activationRequest.fields.vehicle_type_helper') }}</span>
            </div>
            <div class="form-group col-md-3">
                <label class="required" for="vehicle_reg_no">{{ trans('cruds.activationRequest.fields.vehicle_reg_no') }}</label>
                <input class="form-control {{ $errors->has('vehicle_reg_no') ? 'is-invalid' : '' }}" type="text" name="vehicle_reg_no" id="vehicle_reg_no" value="{{ old('vehicle_reg_no', $activationRequest->vehicle_reg_no) }}" required>
                @if($errors->has('vehicle_reg_no'))
                    <span class="text-danger">{{ $errors->first('vehicle_reg_no') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.activationRequest.fields.vehicle_reg_no_helper') }}</span>
            </div>
            <div class="form-group col-md-3">
                <label for="chassis_number">{{ trans('cruds.activationRequest.fields.chassis_number') }}</label>
                <input class="form-control {{ $errors->has('chassis_number') ? 'is-invalid' : '' }}" type="text" name="chassis_number" id="chassis_number" value="{{ old('chassis_number', $activationRequest->chassis_number) }}">
                @if($errors->has('chassis_number'))
                    <span class="text-danger">{{ $errors->first('chassis_number') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.activationRequest.fields.chassis_number_helper') }}</span>
            </div>
            <div class="form-group col-md-3">
                <label for="engine_number">{{ trans('cruds.activationRequest.fields.engine_number') }}</label>
                <input class="form-control {{ $errors->has('engine_number') ? 'is-invalid' : '' }}" type="text" name="engine_number" id="engine_number" value="{{ old('engine_number', $activationRequest->engine_number) }}">
                @if($errors->has('engine_number'))
                    <span class="text-danger">{{ $errors->first('engine_number') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.activationRequest.fields.engine_number_helper') }}</span>
            </div>
            <div class="form-group col-md-3">
                <label for="vehicle_color">{{ trans('cruds.activationRequest.fields.vehicle_color') }}</label>
                <input class="form-control {{ $errors->has('vehicle_color') ? 'is-invalid' : '' }}" type="text" name="vehicle_color" id="vehicle_color" value="{{ old('vehicle_color', $activationRequest->vehicle_color) }}">
                @if($errors->has('vehicle_color'))
                    <span class="text-danger">{{ $errors->first('vehicle_color') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.activationRequest.fields.vehicle_color_helper') }}</span>
            </div>
            </div>
            </div>
            </div>
<div class="card shadow-sm rounded mt-4">
    <div class="card-header  " style="background: linear-gradient(90deg, #515b66 0%, #c04de6 100%); color: white;">
        <h5 class="mb-0">Installation Details</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <!-- Request Date -->
            <div class="form-group col-md-4">
                <label for="request_date">{{ trans('cruds.activationRequest.fields.request_date') }}</label>
                <input class="form-control date {{ $errors->has('request_date') ? 'is-invalid' : '' }}" type="text" name="request_date" id="request_date" value="{{ old('request_date', $activationRequest->request_date) }}">
                @if($errors->has('request_date'))
                    <span class="text-danger">{{ $errors->first('request_date') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.activationRequest.fields.request_date_helper') }}</span>
            </div>

            <!-- Fitter Name -->
            <div class="form-group col-lg-4">
                <label for="fitter_name">Fitter Name</label>
                <input class="form-control {{ $errors->has('fitter_name') ? 'is-invalid' : '' }}" type="text" name="fitter_name" id="fitter_name" value=" {{ old('fitter_name', $activationRequest->fitter_name) }}">
                @if($errors->has('fitter_name'))
                    <span class="text-danger">{{ $errors->first('fitter_name') }}</span>
                @endif
            </div>

            <!-- Fitter Number -->
            <div class="form-group col-lg-4">
                <label for="fitter_number">Fitter Number</label>
                <input class="form-control {{ $errors->has('fitter_number') ? 'is-invalid' : '' }}" type="text" name="fitter_number" id="fitter_number" value="{{ old('fitter_number', $activationRequest->fitter_number) }}">
                @if($errors->has('fitter_number'))
                    <span class="text-danger">{{ $errors->first('fitter_number') }}</span>
                @endif
            </div>

            @php
                $isAdmin = auth()->user()->roles->contains('title', 'Admin');
            @endphp

@if($isAdmin)
    <div class="form-group mb-3 col-lg-4">
        <label for="app_link_id">Select App Link</label>
        <select name="app_link_id" class="form-control" required>
            <option value="">-- Select App Link --</option>
            @foreach($appLinks as $link)
                <option value="{{ $link->id }}" {{ old('app_link_id', $activationRequest->app_link_id) == $link->id ? 'selected' : '' }}>
                    {{ $link->title }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="form-group mb-3 col-lg-4">
        <label for="status">Select Status</label>
        <select name="status" id="status" class="form-control" required>
            <option value="">-- Select Status --</option>
            @foreach($statusOptions as $key => $label)
                <option value="{{ $key }}" {{ old('status', $activationRequest->status ?? '') == $key ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="form-group col-lg-4">
        <label for="user_id">User Id</label>
        <input class="form-control {{ $errors->has('user_id') ? 'is-invalid' : '' }}" type="text" name="user_id" id="user_id" value="{{ old('user_id', $activationRequest->user_id) }}">
        @if($errors->has('user_id'))
            <span class="text-danger">{{ $errors->first('user_id') }}</span>
        @endif
    </div>

    <div class="form-group col-lg-4">
        <label for="password">Password</label>
        <input class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}" type="text" name="password" id="password" value="{{ old('password', $activationRequest->password) }}">
        @if($errors->has('password'))
            <span class="text-danger">{{ $errors->first('password') }}</span>
        @endif
    </div>
@endif


        </div>
    </div>
</div>

            <div class="card shadow-sm rounded">
             <div class="card-header" style="background: linear-gradient(90deg, #515b66 0%, #c04de6 100%); color: white;">
                <h5 class="mb-0">Upload Documents & Photos</h5>
            </div>
            <div class="card-body">
                <div class="row">
            <div class="form-group col-md-6">
                <label class="required" for="id_proofs">{{ trans('cruds.activationRequest.fields.id_proofs') }}</label>
                <div class="needsclick dropzone {{ $errors->has('id_proofs') ? 'is-invalid' : '' }}" id="id_proofs-dropzone">
                </div>
                @if($errors->has('id_proofs'))
                    <span class="text-danger">{{ $errors->first('id_proofs') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.activationRequest.fields.id_proofs_helper') }}</span>
            </div>
            <div class="form-group col-md-6">
                <label for="customer_image">{{ trans('cruds.activationRequest.fields.customer_image') }}</label>
                <div class="needsclick dropzone {{ $errors->has('customer_image') ? 'is-invalid' : '' }}" id="customer_image-dropzone">
                </div>
                @if($errors->has('customer_image'))
                    <span class="text-danger">{{ $errors->first('customer_image') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.activationRequest.fields.customer_image_helper') }}</span>
            </div>
            <div class="form-group col-md-6">
                <label for="vehicle_photos">{{ trans('cruds.activationRequest.fields.vehicle_photos') }}</label>
                <div class="needsclick dropzone {{ $errors->has('vehicle_photos') ? 'is-invalid' : '' }}" id="vehicle_photos-dropzone">
                </div>
                @if($errors->has('vehicle_photos'))
                    <span class="text-danger">{{ $errors->first('vehicle_photos') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.activationRequest.fields.vehicle_photos_helper') }}</span>
            </div>
            <div class="form-group col-md-6">
                <label for="product_images">{{ trans('cruds.activationRequest.fields.product_images') }}</label>
                <div class="needsclick dropzone {{ $errors->has('product_images') ? 'is-invalid' : '' }}" id="product_images-dropzone">
                </div>
                @if($errors->has('product_images'))
                    <span class="text-danger">{{ $errors->first('product_images') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.activationRequest.fields.product_images_helper') }}</span>
            </div>
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
    $(document).ready(function () {
  function SimpleUploadAdapter(editor) {
    editor.plugins.get('FileRepository').createUploadAdapter = function(loader) {
      return {
        upload: function() {
          return loader.file
            .then(function (file) {
              return new Promise(function(resolve, reject) {
                // Init request
                var xhr = new XMLHttpRequest();
                xhr.open('POST', '{{ route('admin.activation-requests.storeCKEditorImages') }}', true);
                xhr.setRequestHeader('x-csrf-token', window._token);
                xhr.setRequestHeader('Accept', 'application/json');
                xhr.responseType = 'json';

                // Init listeners
                var genericErrorText = `Couldn't upload file: ${ file.name }.`;
                xhr.addEventListener('error', function() { reject(genericErrorText) });
                xhr.addEventListener('abort', function() { reject() });
                xhr.addEventListener('load', function() {
                  var response = xhr.response;

                  if (!response || xhr.status !== 201) {
                    return reject(response && response.message ? `${genericErrorText}\n${xhr.status} ${response.message}` : `${genericErrorText}\n ${xhr.status} ${xhr.statusText}`);
                  }

                  $('form').append('<input type="hidden" name="ck-media[]" value="' + response.id + '">');

                  resolve({ default: response.url });
                });

                if (xhr.upload) {
                  xhr.upload.addEventListener('progress', function(e) {
                    if (e.lengthComputable) {
                      loader.uploadTotal = e.total;
                      loader.uploaded = e.loaded;
                    }
                  });
                }

                // Send request
                var data = new FormData();
                data.append('upload', file);
                data.append('crud_id', '{{ $activationRequest->id ?? 0 }}');
                xhr.send(data);
              });
            })
        }
      };
    }
  }

  var allEditors = document.querySelectorAll('.ckeditor');
  for (var i = 0; i < allEditors.length; ++i) {
    ClassicEditor.create(
      allEditors[i], {
        extraPlugins: [SimpleUploadAdapter]
      }
    );
  }
});
</script>

<script>
    Dropzone.options.idProofsDropzone = {
    url: '{{ route('admin.activation-requests.storeMedia') }}',
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
      $('form').find('input[name="id_proofs"]').remove()
      $('form').append('<input type="hidden" name="id_proofs" value="' + response.name + '">')
    },
    removedfile: function (file) {
      file.previewElement.remove()
      if (file.status !== 'error') {
        $('form').find('input[name="id_proofs"]').remove()
        this.options.maxFiles = this.options.maxFiles + 1
      }
    },
    init: function () {
@if(isset($activationRequest) && $activationRequest->id_proofs)
      var file = {!! json_encode($activationRequest->id_proofs) !!}
          this.options.addedfile.call(this, file)
      this.options.thumbnail.call(this, file, file.preview ?? file.preview_url)
      file.previewElement.classList.add('dz-complete')
      $('form').append('<input type="hidden" name="id_proofs" value="' + file.file_name + '">')
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
<script>
    Dropzone.options.customerImageDropzone = {
    url: '{{ route('admin.activation-requests.storeMedia') }}',
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
      $('form').find('input[name="customer_image"]').remove()
      $('form').append('<input type="hidden" name="customer_image" value="' + response.name + '">')
    },
    removedfile: function (file) {
      file.previewElement.remove()
      if (file.status !== 'error') {
        $('form').find('input[name="customer_image"]').remove()
        this.options.maxFiles = this.options.maxFiles + 1
      }
    },
    init: function () {
@if(isset($activationRequest) && $activationRequest->customer_image)
      var file = {!! json_encode($activationRequest->customer_image) !!}
          this.options.addedfile.call(this, file)
      this.options.thumbnail.call(this, file, file.preview ?? file.preview_url)
      file.previewElement.classList.add('dz-complete')
      $('form').append('<input type="hidden" name="customer_image" value="' + file.file_name + '">')
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
<script>
    Dropzone.options.vehiclePhotosDropzone = {
    url: '{{ route('admin.activation-requests.storeMedia') }}',
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
      $('form').find('input[name="vehicle_photos"]').remove()
      $('form').append('<input type="hidden" name="vehicle_photos" value="' + response.name + '">')
    },
    removedfile: function (file) {
      file.previewElement.remove()
      if (file.status !== 'error') {
        $('form').find('input[name="vehicle_photos"]').remove()
        this.options.maxFiles = this.options.maxFiles + 1
      }
    },
    init: function () {
@if(isset($activationRequest) && $activationRequest->vehicle_photos)
      var file = {!! json_encode($activationRequest->vehicle_photos) !!}
          this.options.addedfile.call(this, file)
      this.options.thumbnail.call(this, file, file.preview ?? file.preview_url)
      file.previewElement.classList.add('dz-complete')
      $('form').append('<input type="hidden" name="vehicle_photos" value="' + file.file_name + '">')
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
<script>
    Dropzone.options.productImagesDropzone = {
    url: '{{ route('admin.activation-requests.storeMedia') }}',
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
      $('form').find('input[name="product_images"]').remove()
      $('form').append('<input type="hidden" name="product_images" value="' + response.name + '">')
    },
    removedfile: function (file) {
      file.previewElement.remove()
      if (file.status !== 'error') {
        $('form').find('input[name="product_images"]').remove()
        this.options.maxFiles = this.options.maxFiles + 1
      }
    },
    init: function () {
@if(isset($activationRequest) && $activationRequest->product_images)
      var file = {!! json_encode($activationRequest->product_images) !!}
          this.options.addedfile.call(this, file)
      this.options.thumbnail.call(this, file, file.preview ?? file.preview_url)
      file.previewElement.classList.add('dz-complete')
      $('form').append('<input type="hidden" name="product_images" value="' + file.file_name + '">')
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
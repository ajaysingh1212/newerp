@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('cruds.addCustomerVehicle.title_singular') }}
    </div>

    <div class="card-body">
         @include('watermark')
        <form method="POST" action="{{ route("admin.add-customer-vehicles.store") }}" enctype="multipart/form-data">
            @csrf
            <div class="card px-3">
                <h4 class= "text-center mt-2 py-2 bg-1">Add Customer Vehicle</h4>
            <div class="row">
            <div class="form-group col-lg-3">
                <label class="required" for="select_vehicle_type_id">{{ trans('cruds.addCustomerVehicle.fields.select_vehicle_type') }}</label>
                <select class="form-control select2 {{ $errors->has('select_vehicle_type') ? 'is-invalid' : '' }}" name="select_vehicle_type_id" id="select_vehicle_type_id" required>
                    @foreach($select_vehicle_types as $id => $entry)
                        <option value="{{ $id }}" {{ old('select_vehicle_type_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('select_vehicle_type'))
                    <span class="text-danger">{{ $errors->first('select_vehicle_type') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.addCustomerVehicle.fields.select_vehicle_type_helper') }}</span>
            </div>
            <div class="form-group col-lg-3">
                <label class="required" for="vehicle_number">{{ trans('cruds.addCustomerVehicle.fields.vehicle_number') }}</label>
                <input class="form-control {{ $errors->has('vehicle_number') ? 'is-invalid' : '' }}" 
                type="text" name="vehicle_number" id="vehicle_number" 
                value="{{ old('vehicle_number', '') }}" 
                required 
                style="text-transform: uppercase;" 
                oninput="this.value = this.value.toUpperCase()">

                @if($errors->has('vehicle_number'))
                    <span class="text-danger">{{ $errors->first('vehicle_number') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.addCustomerVehicle.fields.vehicle_number_helper') }}</span>
            </div>
            <div class="form-group col-lg-3">
                <label class="required" for="owners_name">{{ trans('cruds.addCustomerVehicle.fields.owners_name') }}</label>
                <input class="form-control {{ $errors->has('owners_name') ? 'is-invalid' : '' }}" type="text" name="owners_name" id="owners_name" value="{{ old('owners_name', '') }}" required>
                @if($errors->has('owners_name'))
                    <span class="text-danger">{{ $errors->first('owners_name') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.addCustomerVehicle.fields.owners_name_helper') }}</span>
            </div>
            <div class="form-group col-lg-3">
                <label class="required" for="insurance_expiry_date">{{ trans('cruds.addCustomerVehicle.fields.insurance_expiry_date') }}</label>
                <input class="form-control date {{ $errors->has('insurance_expiry_date') ? 'is-invalid' : '' }}" type="text" name="insurance_expiry_date" id="insurance_expiry_date" value="{{ old('insurance_expiry_date') }}" required>
                @if($errors->has('insurance_expiry_date'))
                    <span class="text-danger">{{ $errors->first('insurance_expiry_date') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.addCustomerVehicle.fields.insurance_expiry_date_helper') }}</span>
            </div>
            <div class="form-group col-lg-4">
                <label for="chassis_number">{{ trans('cruds.addCustomerVehicle.fields.chassis_number') }}</label>
                <input class="form-control {{ $errors->has('chassis_number') ? 'is-invalid' : '' }}" type="text" name="chassis_number" id="chassis_number" value="{{ old('chassis_number', '') }}">
                @if($errors->has('chassis_number'))
                    <span class="text-danger">{{ $errors->first('chassis_number') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.addCustomerVehicle.fields.chassis_number_helper') }}</span>
            </div>
            <div class="form-group col-lg-4">
                <label for="vehicle_model">{{ trans('cruds.addCustomerVehicle.fields.vehicle_model') }}</label>
                <input class="form-control {{ $errors->has('vehicle_model') ? 'is-invalid' : '' }}" type="text" name="vehicle_model" id="vehicle_model" value="{{ old('vehicle_model', '') }}">
                @if($errors->has('vehicle_model'))
                    <span class="text-danger">{{ $errors->first('vehicle_model') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.addCustomerVehicle.fields.vehicle_model_helper') }}</span>
            </div>
            <div class="form-group col-lg-4">
                <label for="vehicle_color">{{ trans('cruds.addCustomerVehicle.fields.vehicle_color') }}</label>
                <input class="form-control {{ $errors->has('vehicle_color') ? 'is-invalid' : '' }}" type="text" name="vehicle_color" id="vehicle_color" value="{{ old('vehicle_color', '') }}">
                @if($errors->has('vehicle_color'))
                    <span class="text-danger">{{ $errors->first('vehicle_color') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.addCustomerVehicle.fields.vehicle_color_helper') }}</span>
            </div>
            <div class="form-group col-lg-12">
                <label for="owner_image">{{ trans('cruds.addCustomerVehicle.fields.owner_image') }}</label>
                <input class="form-control {{ $errors->has('owner_image') ? 'is-invalid' : '' }}" type="text" name="owner_image" id="owner_image" value="{{ old('owner_image', '') }}">
                @if($errors->has('owner_image'))
                    <span class="text-danger">{{ $errors->first('owner_image') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.addCustomerVehicle.fields.owner_image_helper') }}</span>
            </div>
            <div class="form-group col-lg-12">
                <label class="" for="insurance">{{ trans('cruds.addCustomerVehicle.fields.insurance') }}</label>
                <div class="needsclick dropzone {{ $errors->has('insurance') ? 'is-invalid' : '' }}" id="insurance-dropzone">
                </div>
                @if($errors->has('insurance'))
                    <span class="text-danger">{{ $errors->first('insurance') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.addCustomerVehicle.fields.insurance_helper') }}</span>
            </div>
            <div class="form-group col-lg-12">
                <label class="" for="pollution">{{ trans('cruds.addCustomerVehicle.fields.pollution') }}</label>
                <div class="needsclick dropzone {{ $errors->has('pollution') ? 'is-invalid' : '' }}" id="pollution-dropzone">
                </div>
                @if($errors->has('pollution'))
                    <span class="text-danger">{{ $errors->first('pollution') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.addCustomerVehicle.fields.pollution_helper') }}</span>
            </div>
            <div class="form-group col-lg-12">
                <label class="" for="registration_certificate">{{ trans('cruds.addCustomerVehicle.fields.registration_certificate') }}</label>
                <div class="needsclick dropzone {{ $errors->has('registration_certificate') ? 'is-invalid' : '' }}" id="registration_certificate-dropzone">
                </div>
                @if($errors->has('registration_certificate'))
                    <span class="text-danger">{{ $errors->first('registration_certificate') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.addCustomerVehicle.fields.registration_certificate_helper') }}</span>
            </div>
            
            <div class="form-group col-lg-12">
                <label class="required" for="id_proofs">{{ trans('cruds.addCustomerVehicle.fields.id_proofs') }}</label>
                <div class="needsclick dropzone {{ $errors->has('id_proofs') ? 'is-invalid' : '' }}" id="id_proofs-dropzone">
                </div>
                @if($errors->has('id_proofs'))
                    <span class="text-danger">{{ $errors->first('id_proofs') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.addCustomerVehicle.fields.id_proofs_helper') }}</span>
            </div>
            <div class="form-group col-lg-12">
                <label for="vehicle_photos">{{ trans('cruds.addCustomerVehicle.fields.vehicle_photos') }}</label>
                <div class="needsclick dropzone {{ $errors->has('vehicle_photos') ? 'is-invalid' : '' }}" id="vehicle_photos-dropzone">
                </div>
                @if($errors->has('vehicle_photos'))
                    <span class="text-danger">{{ $errors->first('vehicle_photos') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.addCustomerVehicle.fields.vehicle_photos_helper') }}</span>
            </div>
            <div class="form-group col-lg-12">
                <label for="product_images">{{ trans('cruds.addCustomerVehicle.fields.product_images') }}</label>
                <div class="needsclick dropzone {{ $errors->has('product_images') ? 'is-invalid' : '' }}" id="product_images-dropzone">
                </div>
                @if($errors->has('product_images'))
                    <span class="text-danger">{{ $errors->first('product_images') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.addCustomerVehicle.fields.product_images_helper') }}</span>
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
    var uploadedInsuranceMap = {}
Dropzone.options.insuranceDropzone = {
    url: '{{ route('admin.add-customer-vehicles.storeMedia') }}',
    maxFilesize: 20, // MB
    acceptedFiles: '.jpeg,.jpg,.png,.gif',
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
      $('form').append('<input type="hidden" name="insurance[]" value="' + response.name + '">')
      uploadedInsuranceMap[file.name] = response.name
    },
    removedfile: function (file) {
      console.log(file)
      file.previewElement.remove()
      var name = ''
      if (typeof file.file_name !== 'undefined') {
        name = file.file_name
      } else {
        name = uploadedInsuranceMap[file.name]
      }
      $('form').find('input[name="insurance[]"][value="' + name + '"]').remove()
    },
    init: function () {
@if(isset($addCustomerVehicle) && $addCustomerVehicle->insurance)
      var files = {!! json_encode($addCustomerVehicle->insurance) !!}
          for (var i in files) {
          var file = files[i]
          this.options.addedfile.call(this, file)
          this.options.thumbnail.call(this, file, file.preview ?? file.preview_url)
          file.previewElement.classList.add('dz-complete')
          $('form').append('<input type="hidden" name="insurance[]" value="' + file.file_name + '">')
        }
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
    var uploadedPollutionMap = {}
Dropzone.options.pollutionDropzone = {
    url: '{{ route('admin.add-customer-vehicles.storeMedia') }}',
    maxFilesize: 20, // MB
    acceptedFiles: '.jpeg,.jpg,.png,.gif',
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
      $('form').append('<input type="hidden" name="pollution[]" value="' + response.name + '">')
      uploadedPollutionMap[file.name] = response.name
    },
    removedfile: function (file) {
      console.log(file)
      file.previewElement.remove()
      var name = ''
      if (typeof file.file_name !== 'undefined') {
        name = file.file_name
      } else {
        name = uploadedPollutionMap[file.name]
      }
      $('form').find('input[name="pollution[]"][value="' + name + '"]').remove()
    },
    init: function () {
@if(isset($addCustomerVehicle) && $addCustomerVehicle->pollution)
      var files = {!! json_encode($addCustomerVehicle->pollution) !!}
          for (var i in files) {
          var file = files[i]
          this.options.addedfile.call(this, file)
          this.options.thumbnail.call(this, file, file.preview ?? file.preview_url)
          file.previewElement.classList.add('dz-complete')
          $('form').append('<input type="hidden" name="pollution[]" value="' + file.file_name + '">')
        }
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
    var uploadedRegistrationCertificateMap = {}
Dropzone.options.registrationCertificateDropzone = {
    url: '{{ route('admin.add-customer-vehicles.storeMedia') }}',
    maxFilesize: 20, // MB
    acceptedFiles: '.jpeg,.jpg,.png,.gif',
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
      $('form').append('<input type="hidden" name="registration_certificate[]" value="' + response.name + '">')
      uploadedRegistrationCertificateMap[file.name] = response.name
    },
    removedfile: function (file) {
      console.log(file)
      file.previewElement.remove()
      var name = ''
      if (typeof file.file_name !== 'undefined') {
        name = file.file_name
      } else {
        name = uploadedRegistrationCertificateMap[file.name]
      }
      $('form').find('input[name="registration_certificate[]"][value="' + name + '"]').remove()
    },
    init: function () {
@if(isset($addCustomerVehicle) && $addCustomerVehicle->registration_certificate)
      var files = {!! json_encode($addCustomerVehicle->registration_certificate) !!}
          for (var i in files) {
          var file = files[i]
          this.options.addedfile.call(this, file)
          this.options.thumbnail.call(this, file, file.preview ?? file.preview_url)
          file.previewElement.classList.add('dz-complete')
          $('form').append('<input type="hidden" name="registration_certificate[]" value="' + file.file_name + '">')
        }
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
    Dropzone.options.idProofsDropzone = {
    url: '{{ route('admin.add-customer-vehicles.storeMedia') }}',
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
@if(isset($addCustomerVehicle) && $addCustomerVehicle->id_proofs)
      var file = {!! json_encode($addCustomerVehicle->id_proofs) !!}
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
    Dropzone.options.vehiclePhotosDropzone = {
    url: '{{ route('admin.add-customer-vehicles.storeMedia') }}',
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
@if(isset($addCustomerVehicle) && $addCustomerVehicle->vehicle_photos)
      var file = {!! json_encode($addCustomerVehicle->vehicle_photos) !!}
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
    url: '{{ route('admin.add-customer-vehicles.storeMedia') }}',
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
@if(isset($addCustomerVehicle) && $addCustomerVehicle->product_images)
      var file = {!! json_encode($addCustomerVehicle->product_images) !!}
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
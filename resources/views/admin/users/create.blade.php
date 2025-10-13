@extends('layouts.admin')
@section('content')

@php
    // Logged-in user ke roles ko fetch karo
    $userRoles = auth()->user()->roles->pluck('title')->toArray();
@endphp

@if(!in_array('Customer', $userRoles) && !in_array('Sharing', $userRoles))
<style>
    .card .bg-1{
        background: #8776cc;
background: linear-gradient(18deg, rgba(135, 118, 204, 1) 13%, rgba(210, 231, 250, 1) 56%);
    }
</style>
<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('cruds.user.title_singular') }}
    </div>

    <div class="card-body">
        @include('watermark')
        <form method="POST" action="{{ route("admin.users.store") }}" enctype="multipart/form-data">
            @csrf
            
            <div class="card px-3">
                <h4 class= "text-center mt-2 py-2 bg-1">Shop Detail</h4>
            <div class="row">
                
                    <div class="form-group col-lg-4">
                <label class="required" for="roles">{{ trans('cruds.user.fields.roles') }}</label>
                <!-- <div style="padding-bottom: 4px">
                    <span class="btn btn-info btn-xs select-all" style="border-radius: 0">{{ trans('global.select_all') }}</span>
                    <span class="btn btn-info btn-xs deselect-all" style="border-radius: 0">{{ trans('global.deselect_all') }}</span>
                </div> -->
                <select class="form-control select2 {{ $errors->has('roles') ? 'is-invalid' : '' }}" name="roles[]" id="roles" multiple required>
                    @foreach($roles as $id => $role)
                        <option value="{{ $id }}" {{ in_array($id, old('roles', [])) ? 'selected' : '' }}>{{ $role }}</option>
                    @endforeach
                </select>
                @if($errors->has('roles'))
                    <span class="text-danger">{{ $errors->first('roles') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.user.fields.roles_helper') }}</span>
            </div>
            <div class="form-group col-lg-4 ">
                <label class="required" for="name">{{ trans('cruds.user.fields.name') }}</label>
                <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text" name="name" id="name" value="{{ old('name', '') }}" required>
                @if($errors->has('name'))
                    <span class="text-danger">{{ $errors->first('name') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.user.fields.name_helper') }}</span>
            </div>
            <div class="form-group col-lg-4 companyNameField" id="">
                <label class="" for="company_name">{{ trans('cruds.user.fields.company_name') }}</label>
                <input class="form-control {{ $errors->has('company_name') ? 'is-invalid' : '' }}" type="text" name="company_name" id="company_name" value="{{ old('company_name', '') }}" >
                @if($errors->has('company_name'))
                    <span class="text-danger">{{ $errors->first('company_name') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.user.fields.company_name_helper') }}</span>
            </div>
            <div class="form-group col-lg-4">
                <label class="required" for="email">{{ trans('cruds.user.fields.email') }}</label>
                <input class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}" type="email" name="email" id="email" value="{{ old('email') }}" required>
                @if($errors->has('email'))
                    <span class="text-danger">{{ $errors->first('email') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.user.fields.email_helper') }}</span>
            </div>
            <div class="form-group col-lg-4 companyNameField">
                <label class="" for="gst_number">{{ trans('cruds.user.fields.gst_number') }}</label>
                <input class="form-control {{ $errors->has('gst_number') ? 'is-invalid' : '' }}" type="text" name="gst_number" id="gst_number" value="{{ old('gst_number', '') }}" >
                @if($errors->has('gst_number'))
                    <span class="text-danger">{{ $errors->first('gst_number') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.user.fields.gst_number_helper') }}</span>
            </div>
            <div class="form-group col-lg-4 companyNameField">
                <label for="date_inc">{{ trans('cruds.user.fields.date_inc') }}</label>
                <input class="form-control date {{ $errors->has('date_inc') ? 'is-invalid' : '' }}" type="text" name="date_inc" id="date_inc" value="{{ old('date_inc') }}">
                @if($errors->has('date_inc'))
                    <span class="text-danger">{{ $errors->first('date_inc') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.user.fields.date_inc_helper') }}</span>
            </div>
            <div class="form-group col-lg-4 companyNameField">
                <label for="date_joining">{{ trans('cruds.user.fields.date_joining') }}</label>
                <input class="form-control {{ $errors->has('date_joining') ? 'is-invalid' : '' }}" type="text" name="date_joining" id="date_joining" value="{{ old('date_joining', '') }}">
                @if($errors->has('date_joining'))
                    <span class="text-danger">{{ $errors->first('date_joining') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.user.fields.date_joining_helper') }}</span>
            </div>
            </div>
            </div>
            <div class="card px-3">
                <h4 class= "text-center mt-2 py-2 bg-1">User Information</h4>
            <div class="row">
            <div class="form-group col-lg-4">
                <label class="required" for="mobile_number">{{ trans('cruds.user.fields.mobile_number') }}</label>
                <input class="form-control {{ $errors->has('mobile_number') ? 'is-invalid' : '' }}" type="text" name="mobile_number" id="mobile_number" value="{{ old('mobile_number', '') }}" required>
                @if($errors->has('mobile_number'))
                    <span class="text-danger">{{ $errors->first('mobile_number') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.user.fields.mobile_number_helper') }}</span>
            </div>
            <div class="form-group col-lg-4">
                <label for="whatsapp_number">{{ trans('cruds.user.fields.whatsapp_number') }}</label>
                <input class="form-control {{ $errors->has('whatsapp_number') ? 'is-invalid' : '' }}" type="text" name="whatsapp_number" id="whatsapp_number" value="{{ old('whatsapp_number', '') }}">
                @if($errors->has('whatsapp_number'))
                    <span class="text-danger">{{ $errors->first('whatsapp_number') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.user.fields.whatsapp_number_helper') }}</span>
            </div>
            <div class="form-group col-lg-4">
                <label class="required" for="state_id">{{ trans('cruds.user.fields.state') }}</label>
                <select class="form-control select2 {{ $errors->has('state') ? 'is-invalid' : '' }}" name="state_id" id="state_id" >
                    @foreach($states as $id => $entry)
                        <option value="{{ $id }}" {{ old('state_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('state'))
                    <span class="text-danger">{{ $errors->first('state') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.user.fields.state_helper') }}</span>
            </div>
            <div class="form-group col-lg-4">
                <label class="required" for="district_id">{{ trans('cruds.user.fields.district') }}</label>
                <select class="form-control select2 {{ $errors->has('district') ? 'is-invalid' : '' }}" name="district_id" id="district_id" >
                    @foreach($districts as $id => $entry)
                        <option value="{{ $id }}" {{ old('district_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('district'))
                    <span class="text-danger">{{ $errors->first('district') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.user.fields.district_helper') }}</span>
            </div>
            <div class="form-group col-lg-4">
                <label for="pin_code">{{ trans('cruds.user.fields.pin_code') }}</label>
                <input class="form-control {{ $errors->has('pin_code') ? 'is-invalid' : '' }}" type="number" name="pin_code" id="pin_code" value="{{ old('pin_code', '') }}" step="1">
                @if($errors->has('pin_code'))
                    <span class="text-danger">{{ $errors->first('pin_code') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.user.fields.pin_code_helper') }}</span>
            </div>
            <div class="form-group col-lg-12">
                <label for="full_address">{{ trans('cruds.user.fields.full_address') }}</label>
                <textarea class="form-control ckeditor {{ $errors->has('full_address') ? 'is-invalid' : '' }}" name="full_address" id="full_address">{!! old('full_address') !!}</textarea>
                @if($errors->has('full_address'))
                    <span class="text-danger">{{ $errors->first('full_address') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.user.fields.full_address_helper') }}</span>
            </div>
            </div>
            </div>

            <div class="card px-3 companyNameField">
                <h4 class= "text-center bg-1 mt-2 py-2">Bank Details</h4>
            <div class="row">
            <div class="form-group col-lg-4 companyNameField">
                <label for="bank_name">{{ trans('cruds.user.fields.bank_name') }}</label>
                <input class="form-control {{ $errors->has('bank_name') ? 'is-invalid' : '' }}" type="text" name="bank_name" id="bank_name" value="{{ old('bank_name', '') }}">
                @if($errors->has('bank_name'))
                    <span class="text-danger">{{ $errors->first('bank_name') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.user.fields.bank_name_helper') }}</span>
            </div>
            <div class="form-group col-lg-4 companyNameField">
                <label for="branch_name">{{ trans('cruds.user.fields.branch_name') }}</label>
                <input class="form-control {{ $errors->has('branch_name') ? 'is-invalid' : '' }}" type="text" name="branch_name" id="branch_name" value="{{ old('branch_name', '') }}">
                @if($errors->has('branch_name'))
                    <span class="text-danger">{{ $errors->first('branch_name') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.user.fields.branch_name_helper') }}</span>
            </div>
            <div class="form-group col-lg-4 companyNameField">
                <label for="ifsc">{{ trans('cruds.user.fields.ifsc') }}</label>
                <input class="form-control {{ $errors->has('ifsc') ? 'is-invalid' : '' }}" type="text" name="ifsc" id="ifsc" value="{{ old('ifsc', '') }}">
                @if($errors->has('ifsc'))
                    <span class="text-danger">{{ $errors->first('ifsc') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.user.fields.ifsc_helper') }}</span>
            </div>
            <div class="form-group col-lg-4 companyNameField">
                <label for="ac_holder_name">{{ trans('cruds.user.fields.ac_holder_name') }}</label>
                <input class="form-control {{ $errors->has('ac_holder_name') ? 'is-invalid' : '' }}" type="text" name="ac_holder_name" id="ac_holder_name" value="{{ old('ac_holder_name', '') }}">
                @if($errors->has('ac_holder_name'))
                    <span class="text-danger">{{ $errors->first('ac_holder_name') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.user.fields.ac_holder_name_helper') }}</span>
            </div>
            <div class="form-group col-lg-4 companyNameField">
                <label for="pan_number">{{ trans('cruds.user.fields.pan_number') }}</label>
                <input class="form-control {{ $errors->has('pan_number') ? 'is-invalid' : '' }}" type="text" name="pan_number" id="pan_number" value="{{ old('pan_number', '') }}">
                @if($errors->has('pan_number'))
                    <span class="text-danger">{{ $errors->first('pan_number') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.user.fields.pan_number_helper') }}</span>
            </div>
            </div>
            </div>

            <div class="card px-3">
                <h4 class= "text-center bg-1 mt-2 py-2">Basic Details</h4>
            <div class="row">
                <div class="form-group col-lg-4 companyNameField">
                <label>{{ trans('cruds.user.fields.status') }}</label>
                <select class="form-control {{ $errors->has('status') ? 'is-invalid' : '' }}" name="status" id="status">
                    <option value disabled {{ old('status', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                    @foreach(App\Models\User::STATUS_SELECT as $key => $label)
                        <option value="{{ $key }}" {{ old('status', 'Enable') === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @if($errors->has('status'))
                    <span class="text-danger">{{ $errors->first('status') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.user.fields.status_helper') }}</span>
            </div>
            
            <div class="form-group col-lg-4">
                <label class="required" for="password">{{ trans('cruds.user.fields.password') }}</label>
                <input class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}" type="password" name="password" id="password" required>
                @if($errors->has('password'))
                    <span class="text-danger">{{ $errors->first('password') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.user.fields.password_helper') }}</span>
            </div>
            <div class="form-group col-lg-4 companyNameField">
                <label for="team_id">{{ trans('cruds.user.fields.team') }}</label>
                <select class="form-control select2 {{ $errors->has('team') ? 'is-invalid' : '' }}" name="team_id" id="team_id">
                    @foreach($teams as $id => $entry)
                        <option value="{{ $id }}" {{ old('team_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('team'))
                    <span class="text-danger">{{ $errors->first('team') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.user.fields.team_helper') }}</span>
            </div>
            </div>
            </div>

            <div class="card px-3">
                <h4 class= "text-center bg-1 mt-2 py-2">Attach ID Proof</h4>
            <div class="row">
            <div class="form-group col-lg-4">
                <label for="profile_image">{{ trans('cruds.user.fields.profile_image') }}</label>
                <div class="needsclick dropzone {{ $errors->has('profile_image') ? 'is-invalid' : '' }}" id="profile_image-dropzone">
                </div>
                @if($errors->has('profile_image'))
                    <span class="text-danger">{{ $errors->first('profile_image') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.user.fields.profile_image_helper') }}</span>
            </div>
            <div class="form-group col-lg-4 companyNameField">
                <label for="upload_signature">{{ trans('cruds.user.fields.upload_signature') }}</label>
                <div class="needsclick dropzone {{ $errors->has('upload_signature') ? 'is-invalid' : '' }}" id="upload_signature-dropzone">
                </div>
                @if($errors->has('upload_signature'))
                    <span class="text-danger">{{ $errors->first('upload_signature') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.user.fields.upload_signature_helper') }}</span>
            </div>
            <div class="form-group col-lg-4 companyNameField">
                <label for="upload_pan_aadhar">{{ trans('cruds.user.fields.upload_pan_aadhar') }}</label>
                <div class="needsclick dropzone {{ $errors->has('upload_pan_aadhar') ? 'is-invalid' : '' }}" id="upload_pan_aadhar-dropzone">
                </div>
                @if($errors->has('upload_pan_aadhar'))
                    <span class="text-danger">{{ $errors->first('upload_pan_aadhar') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.user.fields.upload_pan_aadhar_helper') }}</span>
            </div>
            <div class="form-group col-lg-4 companyNameField">
                <label for="passbook_statement">{{ trans('cruds.user.fields.passbook_statement') }}</label>
                <div class="needsclick dropzone {{ $errors->has('passbook_statement') ? 'is-invalid' : '' }}" id="passbook_statement-dropzone">
                </div>
                @if($errors->has('passbook_statement'))
                    <span class="text-danger">{{ $errors->first('passbook_statement') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.user.fields.passbook_statement_helper') }}</span>
            </div>
            <div class="form-group col-lg-4 companyNameField">
                <label for="shop_photo">{{ trans('cruds.user.fields.shop_photo') }}</label>
                <div class="needsclick dropzone {{ $errors->has('shop_photo') ? 'is-invalid' : '' }}" id="shop_photo-dropzone">
                </div>
                @if($errors->has('shop_photo'))
                    <span class="text-danger">{{ $errors->first('shop_photo') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.user.fields.shop_photo_helper') }}</span>
            </div>
            <div class="form-group col-lg-4 companyNameField">
                <label for="gst_certificate">{{ trans('cruds.user.fields.gst_certificate') }}</label>
                <div class="needsclick dropzone {{ $errors->has('gst_certificate') ? 'is-invalid' : '' }}" id="gst_certificate-dropzone">
                </div>
                @if($errors->has('gst_certificate'))
                    <span class="text-danger">{{ $errors->first('gst_certificate') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.user.fields.gst_certificate_helper') }}</span>
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
<script>
    $(document).ready(function() {
        function toggleCompanyField() {
            let selectedRoles = $('#roles').val();

            // Adjust the role ID or name as per your requirement
            if (selectedRoles.includes('2')) { // example: role ID 2 is "Seller"
                $('.companyNameField').hide();
            } else {
                $('.companyNameField').show();
                $('.company_name').val('');
            }
        }

        // Call once on load
        toggleCompanyField();

        // Attach change event
        $('#roles').on('change', function() {
            toggleCompanyField();
        });
    });
</script>

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
                xhr.open('POST', '{{ route('admin.users.storeCKEditorImages') }}', true);
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
                data.append('crud_id', '{{ $user->id ?? 0 }}');
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
    Dropzone.options.profileImageDropzone = {
    url: '{{ route('admin.users.storeMedia') }}',
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
      $('form').find('input[name="profile_image"]').remove()
      $('form').append('<input type="hidden" name="profile_image" value="' + response.name + '">')
    },
    removedfile: function (file) {
      file.previewElement.remove()
      if (file.status !== 'error') {
        $('form').find('input[name="profile_image"]').remove()
        this.options.maxFiles = this.options.maxFiles + 1
      }
    },
    init: function () {
@if(isset($user) && $user->profile_image)
      var file = {!! json_encode($user->profile_image) !!}
          this.options.addedfile.call(this, file)
      this.options.thumbnail.call(this, file, file.preview ?? file.preview_url)
      file.previewElement.classList.add('dz-complete')
      $('form').append('<input type="hidden" name="profile_image" value="' + file.file_name + '">')
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
    Dropzone.options.uploadSignatureDropzone = {
    url: '{{ route('admin.users.storeMedia') }}',
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
      $('form').find('input[name="upload_signature"]').remove()
      $('form').append('<input type="hidden" name="upload_signature" value="' + response.name + '">')
    },
    removedfile: function (file) {
      file.previewElement.remove()
      if (file.status !== 'error') {
        $('form').find('input[name="upload_signature"]').remove()
        this.options.maxFiles = this.options.maxFiles + 1
      }
    },
    init: function () {
@if(isset($user) && $user->upload_signature)
      var file = {!! json_encode($user->upload_signature) !!}
          this.options.addedfile.call(this, file)
      this.options.thumbnail.call(this, file, file.preview ?? file.preview_url)
      file.previewElement.classList.add('dz-complete')
      $('form').append('<input type="hidden" name="upload_signature" value="' + file.file_name + '">')
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
    Dropzone.options.uploadPanAadharDropzone = {
    url: '{{ route('admin.users.storeMedia') }}',
    maxFilesize: 2, // MB
    maxFiles: 1,
    addRemoveLinks: true,
    headers: {
      'X-CSRF-TOKEN': "{{ csrf_token() }}"
    },
    params: {
      size: 2
    },
    success: function (file, response) {
      $('form').find('input[name="upload_pan_aadhar"]').remove()
      $('form').append('<input type="hidden" name="upload_pan_aadhar" value="' + response.name + '">')
    },
    removedfile: function (file) {
      file.previewElement.remove()
      if (file.status !== 'error') {
        $('form').find('input[name="upload_pan_aadhar"]').remove()
        this.options.maxFiles = this.options.maxFiles + 1
      }
    },
    init: function () {
@if(isset($user) && $user->upload_pan_aadhar)
      var file = {!! json_encode($user->upload_pan_aadhar) !!}
          this.options.addedfile.call(this, file)
      file.previewElement.classList.add('dz-complete')
      $('form').append('<input type="hidden" name="upload_pan_aadhar" value="' + file.file_name + '">')
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
    Dropzone.options.passbookStatementDropzone = {
    url: '{{ route('admin.users.storeMedia') }}',
    maxFilesize: 20, // MB
    maxFiles: 1,
    addRemoveLinks: true,
    headers: {
      'X-CSRF-TOKEN': "{{ csrf_token() }}"
    },
    params: {
      size: 20
    },
    success: function (file, response) {
      $('form').find('input[name="passbook_statement"]').remove()
      $('form').append('<input type="hidden" name="passbook_statement" value="' + response.name + '">')
    },
    removedfile: function (file) {
      file.previewElement.remove()
      if (file.status !== 'error') {
        $('form').find('input[name="passbook_statement"]').remove()
        this.options.maxFiles = this.options.maxFiles + 1
      }
    },
    init: function () {
@if(isset($user) && $user->passbook_statement)
      var file = {!! json_encode($user->passbook_statement) !!}
          this.options.addedfile.call(this, file)
      file.previewElement.classList.add('dz-complete')
      $('form').append('<input type="hidden" name="passbook_statement" value="' + file.file_name + '">')
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
    Dropzone.options.shopPhotoDropzone = {
    url: '{{ route('admin.users.storeMedia') }}',
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
      $('form').find('input[name="shop_photo"]').remove()
      $('form').append('<input type="hidden" name="shop_photo" value="' + response.name + '">')
    },
    removedfile: function (file) {
      file.previewElement.remove()
      if (file.status !== 'error') {
        $('form').find('input[name="shop_photo"]').remove()
        this.options.maxFiles = this.options.maxFiles + 1
      }
    },
    init: function () {
@if(isset($user) && $user->shop_photo)
      var file = {!! json_encode($user->shop_photo) !!}
          this.options.addedfile.call(this, file)
      this.options.thumbnail.call(this, file, file.preview ?? file.preview_url)
      file.previewElement.classList.add('dz-complete')
      $('form').append('<input type="hidden" name="shop_photo" value="' + file.file_name + '">')
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
    Dropzone.options.gstCertificateDropzone = {
    url: '{{ route('admin.users.storeMedia') }}',
    maxFilesize: 20, // MB
    maxFiles: 1,
    addRemoveLinks: true,
    headers: {
      'X-CSRF-TOKEN': "{{ csrf_token() }}"
    },
    params: {
      size: 20
    },
    success: function (file, response) {
      $('form').find('input[name="gst_certificate"]').remove()
      $('form').append('<input type="hidden" name="gst_certificate" value="' + response.name + '">')
    },
    removedfile: function (file) {
      file.previewElement.remove()
      if (file.status !== 'error') {
        $('form').find('input[name="gst_certificate"]').remove()
        this.options.maxFiles = this.options.maxFiles + 1
      }
    },
    init: function () {
@if(isset($user) && $user->gst_certificate)
      var file = {!! json_encode($user->gst_certificate) !!}
          this.options.addedfile.call(this, file)
      file.previewElement.classList.add('dz-complete')
      $('form').append('<input type="hidden" name="gst_certificate" value="' + file.file_name + '">')
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

@else
<div class="card">
    <div class="card-header bg-warning text-white">
        Vehicle Sharing / Add Sharing User
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route('admin.vehicle-sharing.store') }}">
            @csrf

            {{-- Step 1: Select Vehicles --}}
            <h4 class="mb-3">Select Vehicles to Share</h4>
            <div class="row">
                @php
                    $vehicles = \App\Models\AddCustomerVehicle::where('user_id', auth()->id())->get();
                @endphp
                @foreach($vehicles as $vehicle)
                    @php
                        // Fetch already shared users
                        $sharedUsers = DB::table('vehicle_sharing')
                                        ->join('users', 'vehicle_sharing.sharing_user_id', '=', 'users.id')
                                        ->where('vehicle_sharing.vehicle_id', $vehicle->id)
                                        ->select('users.name', 'users.email')
                                        ->get();
                    @endphp
                    <div class="col-md-4">
                        <div class="card mb-3">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <span>{{ $vehicle->vehicle_number }}</span>
                                <input type="checkbox" name="vehicle_ids[]" value="{{ $vehicle->id }}">
                            </div>
                            <div class="card-body text-center">
                                {{-- Vehicle Image --}}
                                @if($vehicle->vehicle_photos && $vehicle->vehicle_photos->url)
                                    <img src="{{ $vehicle->vehicle_photos->url }}" alt="Vehicle Photo" class="img-fluid mb-2" height="100">
                                @else
                                    <img src="{{ asset('img/car.png') }}" alt="No Image" class="img-fluid mb-2">
                                @endif

                                <p class="mb-1"><strong>Owner:</strong> {{ $vehicle->owners_name }}</p>
                                <p class="mb-1"><strong>Type:</strong> {{ $vehicle->select_vehicle_type ? $vehicle->select_vehicle_type->name : '-' }}</p>
                                <p class="mb-1"><strong>Status:</strong> {{ $vehicle->status }}</p>

                                @if($sharedUsers->count())
                                    <div class="mt-2 p-2 border rounded bg-light">
                                        <strong>Shared With:</strong>
                                        <ul class="mb-0">
                                            @foreach($sharedUsers as $user)
                                                <li>{{ $user->name }} ({{ $user->email }})</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Step 2: Role --}}
            <div class="form-group mt-3">
                <label for="role">Select Role</label>
                <select name="role" id="role" class="form-control" required>
                    <option value="Sharing">Sharing</option>
                </select>
            </div>

            {{-- Step 3: User Details --}}
            <div class="form-group mt-3">
                <label for="name">Name</label>
                <input type="text" name="name" id="name" class="form-control" required>
            </div>

            <div class="form-group mt-3">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" class="form-control" required>
            </div>

            <div class="form-group mt-3">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>

            <div class="form-group mt-3">
                <label for="status">Status</label>
                <select name="status" id="status" class="form-control" required>
                    <option value="enable" selected>Enable</option>
                    <option value="disable">Disable</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary mt-4">Share Vehicles</button>
        </form>
    </div>
</div>


@endif

@endsection




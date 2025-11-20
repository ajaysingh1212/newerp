@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('cruds.registration.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.registrations.store") }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label for="reg">{{ trans('cruds.registration.fields.reg') }}</label>
                <input class="form-control {{ $errors->has('reg') ? 'is-invalid' : '' }}" type="text" name="reg" id="reg" value="{{ old('reg', '') }}">
                @if($errors->has('reg'))
                    <span class="text-danger">{{ $errors->first('reg') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.registration.fields.reg_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="investor_id">{{ trans('cruds.registration.fields.investor') }}</label>
                <select class="form-control select2 {{ $errors->has('investor') ? 'is-invalid' : '' }}" name="investor_id" id="investor_id" required>
                    @foreach($investors as $id => $entry)
                        <option value="{{ $id }}" {{ old('investor_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('investor'))
                    <span class="text-danger">{{ $errors->first('investor') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.registration.fields.investor_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="referral_code">{{ trans('cruds.registration.fields.referral_code') }}</label>
                <input class="form-control {{ $errors->has('referral_code') ? 'is-invalid' : '' }}" type="text" name="referral_code" id="referral_code" value="{{ old('referral_code', '') }}">
                @if($errors->has('referral_code'))
                    <span class="text-danger">{{ $errors->first('referral_code') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.registration.fields.referral_code_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="aadhaar_number">{{ trans('cruds.registration.fields.aadhaar_number') }}</label>
                <input class="form-control {{ $errors->has('aadhaar_number') ? 'is-invalid' : '' }}" type="text" name="aadhaar_number" id="aadhaar_number" value="{{ old('aadhaar_number', '') }}" required>
                @if($errors->has('aadhaar_number'))
                    <span class="text-danger">{{ $errors->first('aadhaar_number') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.registration.fields.aadhaar_number_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="pan_number">{{ trans('cruds.registration.fields.pan_number') }}</label>
                <input class="form-control {{ $errors->has('pan_number') ? 'is-invalid' : '' }}" type="text" name="pan_number" id="pan_number" value="{{ old('pan_number', '') }}" required>
                @if($errors->has('pan_number'))
                    <span class="text-danger">{{ $errors->first('pan_number') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.registration.fields.pan_number_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="dob">{{ trans('cruds.registration.fields.dob') }}</label>
                <input class="form-control date {{ $errors->has('dob') ? 'is-invalid' : '' }}" type="text" name="dob" id="dob" value="{{ old('dob') }}">
                @if($errors->has('dob'))
                    <span class="text-danger">{{ $errors->first('dob') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.registration.fields.dob_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required">{{ trans('cruds.registration.fields.gender') }}</label>
                <select class="form-control {{ $errors->has('gender') ? 'is-invalid' : '' }}" name="gender" id="gender" required>
                    <option value disabled {{ old('gender', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                    @foreach(App\Models\Registration::GENDER_SELECT as $key => $label)
                        <option value="{{ $key }}" {{ old('gender', '') === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @if($errors->has('gender'))
                    <span class="text-danger">{{ $errors->first('gender') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.registration.fields.gender_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="father_name">{{ trans('cruds.registration.fields.father_name') }}</label>
                <input class="form-control {{ $errors->has('father_name') ? 'is-invalid' : '' }}" type="text" name="father_name" id="father_name" value="{{ old('father_name', '') }}">
                @if($errors->has('father_name'))
                    <span class="text-danger">{{ $errors->first('father_name') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.registration.fields.father_name_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="address_line_1">{{ trans('cruds.registration.fields.address_line_1') }}</label>
                <textarea class="form-control ckeditor {{ $errors->has('address_line_1') ? 'is-invalid' : '' }}" name="address_line_1" id="address_line_1">{!! old('address_line_1') !!}</textarea>
                @if($errors->has('address_line_1'))
                    <span class="text-danger">{{ $errors->first('address_line_1') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.registration.fields.address_line_1_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="address_line_2">{{ trans('cruds.registration.fields.address_line_2') }}</label>
                <textarea class="form-control ckeditor {{ $errors->has('address_line_2') ? 'is-invalid' : '' }}" name="address_line_2" id="address_line_2">{!! old('address_line_2') !!}</textarea>
                @if($errors->has('address_line_2'))
                    <span class="text-danger">{{ $errors->first('address_line_2') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.registration.fields.address_line_2_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="pincode">{{ trans('cruds.registration.fields.pincode') }}</label>
                <input class="form-control {{ $errors->has('pincode') ? 'is-invalid' : '' }}" type="text" name="pincode" id="pincode" value="{{ old('pincode', '') }}" required>
                @if($errors->has('pincode'))
                    <span class="text-danger">{{ $errors->first('pincode') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.registration.fields.pincode_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="city">{{ trans('cruds.registration.fields.city') }}</label>
                <input class="form-control {{ $errors->has('city') ? 'is-invalid' : '' }}" type="text" name="city" id="city" value="{{ old('city', '') }}">
                @if($errors->has('city'))
                    <span class="text-danger">{{ $errors->first('city') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.registration.fields.city_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="state">{{ trans('cruds.registration.fields.state') }}</label>
                <input class="form-control {{ $errors->has('state') ? 'is-invalid' : '' }}" type="text" name="state" id="state" value="{{ old('state', '') }}">
                @if($errors->has('state'))
                    <span class="text-danger">{{ $errors->first('state') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.registration.fields.state_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="country">{{ trans('cruds.registration.fields.country') }}</label>
                <input class="form-control {{ $errors->has('country') ? 'is-invalid' : '' }}" type="text" name="country" id="country" value="{{ old('country', '') }}">
                @if($errors->has('country'))
                    <span class="text-danger">{{ $errors->first('country') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.registration.fields.country_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="bank_account_holder_name">{{ trans('cruds.registration.fields.bank_account_holder_name') }}</label>
                <input class="form-control {{ $errors->has('bank_account_holder_name') ? 'is-invalid' : '' }}" type="text" name="bank_account_holder_name" id="bank_account_holder_name" value="{{ old('bank_account_holder_name', '') }}" required>
                @if($errors->has('bank_account_holder_name'))
                    <span class="text-danger">{{ $errors->first('bank_account_holder_name') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.registration.fields.bank_account_holder_name_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="bank_account_number">{{ trans('cruds.registration.fields.bank_account_number') }}</label>
                <input class="form-control {{ $errors->has('bank_account_number') ? 'is-invalid' : '' }}" type="text" name="bank_account_number" id="bank_account_number" value="{{ old('bank_account_number', '') }}" required>
                @if($errors->has('bank_account_number'))
                    <span class="text-danger">{{ $errors->first('bank_account_number') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.registration.fields.bank_account_number_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="ifsc_code">{{ trans('cruds.registration.fields.ifsc_code') }}</label>
                <input class="form-control {{ $errors->has('ifsc_code') ? 'is-invalid' : '' }}" type="text" name="ifsc_code" id="ifsc_code" value="{{ old('ifsc_code', '') }}" required>
                @if($errors->has('ifsc_code'))
                    <span class="text-danger">{{ $errors->first('ifsc_code') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.registration.fields.ifsc_code_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="bank_name">{{ trans('cruds.registration.fields.bank_name') }}</label>
                <input class="form-control {{ $errors->has('bank_name') ? 'is-invalid' : '' }}" type="text" name="bank_name" id="bank_name" value="{{ old('bank_name', '') }}">
                @if($errors->has('bank_name'))
                    <span class="text-danger">{{ $errors->first('bank_name') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.registration.fields.bank_name_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="bank_branch">{{ trans('cruds.registration.fields.bank_branch') }}</label>
                <input class="form-control {{ $errors->has('bank_branch') ? 'is-invalid' : '' }}" type="text" name="bank_branch" id="bank_branch" value="{{ old('bank_branch', '') }}">
                @if($errors->has('bank_branch'))
                    <span class="text-danger">{{ $errors->first('bank_branch') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.registration.fields.bank_branch_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="pan_card_image">{{ trans('cruds.registration.fields.pan_card_image') }}</label>
                <div class="needsclick dropzone {{ $errors->has('pan_card_image') ? 'is-invalid' : '' }}" id="pan_card_image-dropzone">
                </div>
                @if($errors->has('pan_card_image'))
                    <span class="text-danger">{{ $errors->first('pan_card_image') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.registration.fields.pan_card_image_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="aadhaar_front_image">{{ trans('cruds.registration.fields.aadhaar_front_image') }}</label>
                <div class="needsclick dropzone {{ $errors->has('aadhaar_front_image') ? 'is-invalid' : '' }}" id="aadhaar_front_image-dropzone">
                </div>
                @if($errors->has('aadhaar_front_image'))
                    <span class="text-danger">{{ $errors->first('aadhaar_front_image') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.registration.fields.aadhaar_front_image_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="aadhaar_back_image">{{ trans('cruds.registration.fields.aadhaar_back_image') }}</label>
                <div class="needsclick dropzone {{ $errors->has('aadhaar_back_image') ? 'is-invalid' : '' }}" id="aadhaar_back_image-dropzone">
                </div>
                @if($errors->has('aadhaar_back_image'))
                    <span class="text-danger">{{ $errors->first('aadhaar_back_image') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.registration.fields.aadhaar_back_image_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="profile_image">{{ trans('cruds.registration.fields.profile_image') }}</label>
                <div class="needsclick dropzone {{ $errors->has('profile_image') ? 'is-invalid' : '' }}" id="profile_image-dropzone">
                </div>
                @if($errors->has('profile_image'))
                    <span class="text-danger">{{ $errors->first('profile_image') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.registration.fields.profile_image_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="signature_image">{{ trans('cruds.registration.fields.signature_image') }}</label>
                <div class="needsclick dropzone {{ $errors->has('signature_image') ? 'is-invalid' : '' }}" id="signature_image-dropzone">
                </div>
                @if($errors->has('signature_image'))
                    <span class="text-danger">{{ $errors->first('signature_image') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.registration.fields.signature_image_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required">{{ trans('cruds.registration.fields.income_range') }}</label>
                <select class="form-control {{ $errors->has('income_range') ? 'is-invalid' : '' }}" name="income_range" id="income_range" required>
                    <option value disabled {{ old('income_range', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                    @foreach(App\Models\Registration::INCOME_RANGE_SELECT as $key => $label)
                        <option value="{{ $key }}" {{ old('income_range', '') === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @if($errors->has('income_range'))
                    <span class="text-danger">{{ $errors->first('income_range') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.registration.fields.income_range_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required">{{ trans('cruds.registration.fields.occupation') }}</label>
                <select class="form-control {{ $errors->has('occupation') ? 'is-invalid' : '' }}" name="occupation" id="occupation" required>
                    <option value disabled {{ old('occupation', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                    @foreach(App\Models\Registration::OCCUPATION_SELECT as $key => $label)
                        <option value="{{ $key }}" {{ old('occupation', '') === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @if($errors->has('occupation'))
                    <span class="text-danger">{{ $errors->first('occupation') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.registration.fields.occupation_helper') }}</span>
            </div>
            <div class="form-group">
                <label>{{ trans('cruds.registration.fields.risk_profile') }}</label>
                <select class="form-control {{ $errors->has('risk_profile') ? 'is-invalid' : '' }}" name="risk_profile" id="risk_profile">
                    <option value disabled {{ old('risk_profile', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                    @foreach(App\Models\Registration::RISK_PROFILE_SELECT as $key => $label)
                        <option value="{{ $key }}" {{ old('risk_profile', '') === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @if($errors->has('risk_profile'))
                    <span class="text-danger">{{ $errors->first('risk_profile') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.registration.fields.risk_profile_helper') }}</span>
            </div>
            <div class="form-group">
                <label>{{ trans('cruds.registration.fields.investment_experience') }}</label>
                <select class="form-control {{ $errors->has('investment_experience') ? 'is-invalid' : '' }}" name="investment_experience" id="investment_experience">
                    <option value disabled {{ old('investment_experience', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                    @foreach(App\Models\Registration::INVESTMENT_EXPERIENCE_SELECT as $key => $label)
                        <option value="{{ $key }}" {{ old('investment_experience', '') === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @if($errors->has('investment_experience'))
                    <span class="text-danger">{{ $errors->first('investment_experience') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.registration.fields.investment_experience_helper') }}</span>
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
                xhr.open('POST', '{{ route('admin.registrations.storeCKEditorImages') }}', true);
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
                data.append('crud_id', '{{ $registration->id ?? 0 }}');
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
    var uploadedPanCardImageMap = {}
Dropzone.options.panCardImageDropzone = {
    url: '{{ route('admin.registrations.storeMedia') }}',
    maxFilesize: 20, // MB
    addRemoveLinks: true,
    headers: {
      'X-CSRF-TOKEN': "{{ csrf_token() }}"
    },
    params: {
      size: 20
    },
    success: function (file, response) {
      $('form').append('<input type="hidden" name="pan_card_image[]" value="' + response.name + '">')
      uploadedPanCardImageMap[file.name] = response.name
    },
    removedfile: function (file) {
      file.previewElement.remove()
      var name = ''
      if (typeof file.file_name !== 'undefined') {
        name = file.file_name
      } else {
        name = uploadedPanCardImageMap[file.name]
      }
      $('form').find('input[name="pan_card_image[]"][value="' + name + '"]').remove()
    },
    init: function () {
@if(isset($registration) && $registration->pan_card_image)
          var files =
            {!! json_encode($registration->pan_card_image) !!}
              for (var i in files) {
              var file = files[i]
              this.options.addedfile.call(this, file)
              file.previewElement.classList.add('dz-complete')
              $('form').append('<input type="hidden" name="pan_card_image[]" value="' + file.file_name + '">')
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
    Dropzone.options.aadhaarFrontImageDropzone = {
    url: '{{ route('admin.registrations.storeMedia') }}',
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
      $('form').find('input[name="aadhaar_front_image"]').remove()
      $('form').append('<input type="hidden" name="aadhaar_front_image" value="' + response.name + '">')
    },
    removedfile: function (file) {
      file.previewElement.remove()
      if (file.status !== 'error') {
        $('form').find('input[name="aadhaar_front_image"]').remove()
        this.options.maxFiles = this.options.maxFiles + 1
      }
    },
    init: function () {
@if(isset($registration) && $registration->aadhaar_front_image)
      var file = {!! json_encode($registration->aadhaar_front_image) !!}
          this.options.addedfile.call(this, file)
      file.previewElement.classList.add('dz-complete')
      $('form').append('<input type="hidden" name="aadhaar_front_image" value="' + file.file_name + '">')
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
    Dropzone.options.aadhaarBackImageDropzone = {
    url: '{{ route('admin.registrations.storeMedia') }}',
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
      $('form').find('input[name="aadhaar_back_image"]').remove()
      $('form').append('<input type="hidden" name="aadhaar_back_image" value="' + response.name + '">')
    },
    removedfile: function (file) {
      file.previewElement.remove()
      if (file.status !== 'error') {
        $('form').find('input[name="aadhaar_back_image"]').remove()
        this.options.maxFiles = this.options.maxFiles + 1
      }
    },
    init: function () {
@if(isset($registration) && $registration->aadhaar_back_image)
      var file = {!! json_encode($registration->aadhaar_back_image) !!}
          this.options.addedfile.call(this, file)
      file.previewElement.classList.add('dz-complete')
      $('form').append('<input type="hidden" name="aadhaar_back_image" value="' + file.file_name + '">')
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
    var uploadedProfileImageMap = {}
Dropzone.options.profileImageDropzone = {
    url: '{{ route('admin.registrations.storeMedia') }}',
    maxFilesize: 20, // MB
    addRemoveLinks: true,
    headers: {
      'X-CSRF-TOKEN': "{{ csrf_token() }}"
    },
    params: {
      size: 20
    },
    success: function (file, response) {
      $('form').append('<input type="hidden" name="profile_image[]" value="' + response.name + '">')
      uploadedProfileImageMap[file.name] = response.name
    },
    removedfile: function (file) {
      file.previewElement.remove()
      var name = ''
      if (typeof file.file_name !== 'undefined') {
        name = file.file_name
      } else {
        name = uploadedProfileImageMap[file.name]
      }
      $('form').find('input[name="profile_image[]"][value="' + name + '"]').remove()
    },
    init: function () {
@if(isset($registration) && $registration->profile_image)
          var files =
            {!! json_encode($registration->profile_image) !!}
              for (var i in files) {
              var file = files[i]
              this.options.addedfile.call(this, file)
              file.previewElement.classList.add('dz-complete')
              $('form').append('<input type="hidden" name="profile_image[]" value="' + file.file_name + '">')
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
    var uploadedSignatureImageMap = {}
Dropzone.options.signatureImageDropzone = {
    url: '{{ route('admin.registrations.storeMedia') }}',
    maxFilesize: 20, // MB
    addRemoveLinks: true,
    headers: {
      'X-CSRF-TOKEN': "{{ csrf_token() }}"
    },
    params: {
      size: 20
    },
    success: function (file, response) {
      $('form').append('<input type="hidden" name="signature_image[]" value="' + response.name + '">')
      uploadedSignatureImageMap[file.name] = response.name
    },
    removedfile: function (file) {
      file.previewElement.remove()
      var name = ''
      if (typeof file.file_name !== 'undefined') {
        name = file.file_name
      } else {
        name = uploadedSignatureImageMap[file.name]
      }
      $('form').find('input[name="signature_image[]"][value="' + name + '"]').remove()
    },
    init: function () {
@if(isset($registration) && $registration->signature_image)
          var files =
            {!! json_encode($registration->signature_image) !!}
              for (var i in files) {
              var file = files[i]
              this.options.addedfile.call(this, file)
              file.previewElement.classList.add('dz-complete')
              $('form').append('<input type="hidden" name="signature_image[]" value="' + file.file_name + '">')
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
@endsection
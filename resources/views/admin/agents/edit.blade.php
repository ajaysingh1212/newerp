@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.agent.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.agents.update", [$agent->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="form-group">
                <label class="required" for="full_name">{{ trans('cruds.agent.fields.full_name') }}</label>
                <input class="form-control {{ $errors->has('full_name') ? 'is-invalid' : '' }}" type="text" name="full_name" id="full_name" value="{{ old('full_name', $agent->full_name) }}" required>
                @if($errors->has('full_name'))
                    <span class="text-danger">{{ $errors->first('full_name') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.agent.fields.full_name_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="phone_number">{{ trans('cruds.agent.fields.phone_number') }}</label>
                <input class="form-control {{ $errors->has('phone_number') ? 'is-invalid' : '' }}" type="text" name="phone_number" id="phone_number" value="{{ old('phone_number', $agent->phone_number) }}" required>
                @if($errors->has('phone_number'))
                    <span class="text-danger">{{ $errors->first('phone_number') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.agent.fields.phone_number_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="whatsapp_number">{{ trans('cruds.agent.fields.whatsapp_number') }}</label>
                <input class="form-control {{ $errors->has('whatsapp_number') ? 'is-invalid' : '' }}" type="text" name="whatsapp_number" id="whatsapp_number" value="{{ old('whatsapp_number', $agent->whatsapp_number) }}">
                @if($errors->has('whatsapp_number'))
                    <span class="text-danger">{{ $errors->first('whatsapp_number') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.agent.fields.whatsapp_number_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="email">{{ trans('cruds.agent.fields.email') }}</label>
                <input class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}" type="text" name="email" id="email" value="{{ old('email', $agent->email) }}">
                @if($errors->has('email'))
                    <span class="text-danger">{{ $errors->first('email') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.agent.fields.email_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="pin_code">{{ trans('cruds.agent.fields.pin_code') }}</label>
                <input class="form-control {{ $errors->has('pin_code') ? 'is-invalid' : '' }}" type="number" name="pin_code" id="pin_code" value="{{ old('pin_code', $agent->pin_code) }}" step="1" required>
                @if($errors->has('pin_code'))
                    <span class="text-danger">{{ $errors->first('pin_code') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.agent.fields.pin_code_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="state">{{ trans('cruds.agent.fields.state') }}</label>
                <input class="form-control {{ $errors->has('state') ? 'is-invalid' : '' }}" type="text" name="state" id="state" value="{{ old('state', $agent->state) }}">
                @if($errors->has('state'))
                    <span class="text-danger">{{ $errors->first('state') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.agent.fields.state_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="city">{{ trans('cruds.agent.fields.city') }}</label>
                <input class="form-control {{ $errors->has('city') ? 'is-invalid' : '' }}" type="text" name="city" id="city" value="{{ old('city', $agent->city) }}">
                @if($errors->has('city'))
                    <span class="text-danger">{{ $errors->first('city') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.agent.fields.city_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="district">{{ trans('cruds.agent.fields.district') }}</label>
                <input class="form-control {{ $errors->has('district') ? 'is-invalid' : '' }}" type="text" name="district" id="district" value="{{ old('district', $agent->district) }}">
                @if($errors->has('district'))
                    <span class="text-danger">{{ $errors->first('district') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.agent.fields.district_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="present_address">{{ trans('cruds.agent.fields.present_address') }}</label>
                <textarea class="form-control ckeditor {{ $errors->has('present_address') ? 'is-invalid' : '' }}" name="present_address" id="present_address">{!! old('present_address', $agent->present_address) !!}</textarea>
                @if($errors->has('present_address'))
                    <span class="text-danger">{{ $errors->first('present_address') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.agent.fields.present_address_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="parmanent_address">{{ trans('cruds.agent.fields.parmanent_address') }}</label>
                <textarea class="form-control ckeditor {{ $errors->has('parmanent_address') ? 'is-invalid' : '' }}" name="parmanent_address" id="parmanent_address">{!! old('parmanent_address', $agent->parmanent_address) !!}</textarea>
                @if($errors->has('parmanent_address'))
                    <span class="text-danger">{{ $errors->first('parmanent_address') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.agent.fields.parmanent_address_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="aadhar_front">{{ trans('cruds.agent.fields.aadhar_front') }}</label>
                <div class="needsclick dropzone {{ $errors->has('aadhar_front') ? 'is-invalid' : '' }}" id="aadhar_front-dropzone">
                </div>
                @if($errors->has('aadhar_front'))
                    <span class="text-danger">{{ $errors->first('aadhar_front') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.agent.fields.aadhar_front_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="aadhar_back">{{ trans('cruds.agent.fields.aadhar_back') }}</label>
                <div class="needsclick dropzone {{ $errors->has('aadhar_back') ? 'is-invalid' : '' }}" id="aadhar_back-dropzone">
                </div>
                @if($errors->has('aadhar_back'))
                    <span class="text-danger">{{ $errors->first('aadhar_back') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.agent.fields.aadhar_back_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="pan_card">{{ trans('cruds.agent.fields.pan_card') }}</label>
                <div class="needsclick dropzone {{ $errors->has('pan_card') ? 'is-invalid' : '' }}" id="pan_card-dropzone">
                </div>
                @if($errors->has('pan_card'))
                    <span class="text-danger">{{ $errors->first('pan_card') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.agent.fields.pan_card_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="additional_document">{{ trans('cruds.agent.fields.additional_document') }}</label>
                <div class="needsclick dropzone {{ $errors->has('additional_document') ? 'is-invalid' : '' }}" id="additional_document-dropzone">
                </div>
                @if($errors->has('additional_document'))
                    <span class="text-danger">{{ $errors->first('additional_document') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.agent.fields.additional_document_helper') }}</span>
            </div>
            <div class="form-group">
                <label>{{ trans('cruds.agent.fields.status') }}</label>
                <select class="form-control {{ $errors->has('status') ? 'is-invalid' : '' }}" name="status" id="status">
                    <option value disabled {{ old('status', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                    @foreach(App\Models\Agent::STATUS_SELECT as $key => $label)
                        <option value="{{ $key }}" {{ old('status', $agent->status) === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @if($errors->has('status'))
                    <span class="text-danger">{{ $errors->first('status') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.agent.fields.status_helper') }}</span>
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
                xhr.open('POST', '{{ route('admin.agents.storeCKEditorImages') }}', true);
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
                data.append('crud_id', '{{ $agent->id ?? 0 }}');
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
    Dropzone.options.aadharFrontDropzone = {
    url: '{{ route('admin.agents.storeMedia') }}',
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
      $('form').find('input[name="aadhar_front"]').remove()
      $('form').append('<input type="hidden" name="aadhar_front" value="' + response.name + '">')
    },
    removedfile: function (file) {
      file.previewElement.remove()
      if (file.status !== 'error') {
        $('form').find('input[name="aadhar_front"]').remove()
        this.options.maxFiles = this.options.maxFiles + 1
      }
    },
    init: function () {
@if(isset($agent) && $agent->aadhar_front)
      var file = {!! json_encode($agent->aadhar_front) !!}
          this.options.addedfile.call(this, file)
      file.previewElement.classList.add('dz-complete')
      $('form').append('<input type="hidden" name="aadhar_front" value="' + file.file_name + '">')
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
    Dropzone.options.aadharBackDropzone = {
    url: '{{ route('admin.agents.storeMedia') }}',
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
      $('form').find('input[name="aadhar_back"]').remove()
      $('form').append('<input type="hidden" name="aadhar_back" value="' + response.name + '">')
    },
    removedfile: function (file) {
      file.previewElement.remove()
      if (file.status !== 'error') {
        $('form').find('input[name="aadhar_back"]').remove()
        this.options.maxFiles = this.options.maxFiles + 1
      }
    },
    init: function () {
@if(isset($agent) && $agent->aadhar_back)
      var file = {!! json_encode($agent->aadhar_back) !!}
          this.options.addedfile.call(this, file)
      file.previewElement.classList.add('dz-complete')
      $('form').append('<input type="hidden" name="aadhar_back" value="' + file.file_name + '">')
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
    Dropzone.options.panCardDropzone = {
    url: '{{ route('admin.agents.storeMedia') }}',
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
      $('form').find('input[name="pan_card"]').remove()
      $('form').append('<input type="hidden" name="pan_card" value="' + response.name + '">')
    },
    removedfile: function (file) {
      file.previewElement.remove()
      if (file.status !== 'error') {
        $('form').find('input[name="pan_card"]').remove()
        this.options.maxFiles = this.options.maxFiles + 1
      }
    },
    init: function () {
@if(isset($agent) && $agent->pan_card)
      var file = {!! json_encode($agent->pan_card) !!}
          this.options.addedfile.call(this, file)
      file.previewElement.classList.add('dz-complete')
      $('form').append('<input type="hidden" name="pan_card" value="' + file.file_name + '">')
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
    var uploadedAdditionalDocumentMap = {}
Dropzone.options.additionalDocumentDropzone = {
    url: '{{ route('admin.agents.storeMedia') }}',
    maxFilesize: 20, // MB
    addRemoveLinks: true,
    headers: {
      'X-CSRF-TOKEN': "{{ csrf_token() }}"
    },
    params: {
      size: 20
    },
    success: function (file, response) {
      $('form').append('<input type="hidden" name="additional_document[]" value="' + response.name + '">')
      uploadedAdditionalDocumentMap[file.name] = response.name
    },
    removedfile: function (file) {
      file.previewElement.remove()
      var name = ''
      if (typeof file.file_name !== 'undefined') {
        name = file.file_name
      } else {
        name = uploadedAdditionalDocumentMap[file.name]
      }
      $('form').find('input[name="additional_document[]"][value="' + name + '"]').remove()
    },
    init: function () {
@if(isset($agent) && $agent->additional_document)
          var files =
            {!! json_encode($agent->additional_document) !!}
              for (var i in files) {
              var file = files[i]
              this.options.addedfile.call(this, file)
              file.previewElement.classList.add('dz-complete')
              $('form').append('<input type="hidden" name="additional_document[]" value="' + file.file_name + '">')
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
@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.checkComplain.title_singular') }}
    </div>

    <div class="card-body">
        @include('watermark')
        <form method="POST" action="{{ route("admin.check-complains.update", [$checkComplain->id]) }}" enctype="multipart/form-data" class="row">
            @method('PUT')
            @csrf
            @php
                $user = auth()->user();
                $isCreator = $user->id === $checkComplain->created_by_id;
                $isAdmin = $user->roles()->where('title', 'admin')->exists(); // adjust title if needed
            @endphp

            <div class="form-group col-lg-6">
                <label class="required" for="select_complains">{{ trans('cruds.checkComplain.fields.select_complain') }}</label>

                <div style="padding-bottom: 4px">
                    <span class="btn btn-info btn-xs select-all" style="border-radius: 0" 
                        {{ !$isCreator ? 'disabled' : '' }}>
                        {{ trans('global.select_all') }}
                    </span>
                    <span class="btn btn-info btn-xs deselect-all" style="border-radius: 0"
                        {{ !$isCreator ? 'disabled' : '' }}>
                        {{ trans('global.deselect_all') }}
                    </span>
                </div>

                <select class="form-control select2 {{ $errors->has('select_complains') ? 'is-invalid' : '' }}"
                    name="select_complains[]"
                    id="select_complains"
                    multiple
                    {{ !$isCreator ? 'disabled' : '' }}>

                    @foreach($select_complains as $id => $select_complain)
                        <option value="{{ $id }}"
                            {{ (in_array($id, old('select_complains', [])) || $checkComplain->select_complains->contains($id)) ? 'selected' : '' }}>
                            {{ $select_complain }}
                        </option>
                    @endforeach
                </select>

                {{-- If admin, include hidden inputs so values still submit --}}
                @if($isAdmin && !$isCreator)
                    @foreach($checkComplain->select_complains as $selectedComplain)
                        <input type="hidden" name="select_complains[]" value="{{ $selectedComplain->id }}">
                    @endforeach
                @endif

                @if($errors->has('select_complains'))
                    <span class="text-danger">{{ $errors->first('select_complains') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.checkComplain.fields.select_complain_helper') }}</span>
            </div>





            <div class="form-group col-lg-6">
                <label class="required" for="ticket_number">{{ trans('cruds.checkComplain.fields.ticket_number') }}</label>
                <input class="form-control {{ $errors->has('ticket_number') ? 'is-invalid' : '' }}" type="text" name="ticket_number" id="ticket_number" value="{{ old('ticket_number', $checkComplain->ticket_number) }}" required readonly>
                @if($errors->has('ticket_number'))
                    <span class="text-danger">{{ $errors->first('ticket_number') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.checkComplain.fields.ticket_number_helper') }}</span>
            </div>

            <div class="form-group col-lg-4">
                <label class="required" for="vehicle_no">{{ trans('cruds.checkComplain.fields.vehicle_no') }}</label>
                <input class="form-control {{ $errors->has('vehicle_no') ? 'is-invalid' : '' }}" type="text" name="vehicle_no" id="vehicle_no" value="{{ old('vehicle_no', $checkComplain->vehicle_no) }}" required readonly>
                @if($errors->has('vehicle_no'))
                    <span class="text-danger">{{ $errors->first('vehicle_no') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.checkComplain.fields.vehicle_no_helper') }}</span>
            </div>
            <div class="form-group col-lg-4">
                <label class="required" for="customer_name">{{ trans('cruds.checkComplain.fields.customer_name') }}</label>
                <input class="form-control {{ $errors->has('customer_name') ? 'is-invalid' : '' }}" type="text" name="customer_name" id="customer_name" value="{{ old('customer_name', $checkComplain->customer_name) }}" required readonly>
                @if($errors->has('customer_name'))
                    <span class="text-danger">{{ $errors->first('customer_name') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.checkComplain.fields.customer_name_helper') }}</span>
            </div>
            <div class="form-group col-lg-4">
                <label class="required" for="phone_number">{{ trans('cruds.checkComplain.fields.phone_number') }}</label>
                <input class="form-control {{ $errors->has('phone_number') ? 'is-invalid' : '' }}" type="text" name="phone_number" id="phone_number" value="{{ old('phone_number', $checkComplain->phone_number) }}" required readonly>
                @if($errors->has('phone_number'))
                    <span class="text-danger">{{ $errors->first('phone_number') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.checkComplain.fields.phone_number_helper') }}</span>
            </div>
            <div class="form-group col-lg-12">
                <label for="reason">{{ trans('cruds.checkComplain.fields.reason') }}</label>
                <textarea class="form-control ckeditor {{ $errors->has('reason') ? 'is-invalid' : '' }}" name="reason" id="reason">{!! old('reason', $checkComplain->reason) !!}</textarea>
                @if($errors->has('reason'))
                    <span class="text-danger">{{ $errors->first('reason') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.checkComplain.fields.reason_helper') }}</span>
            </div>

            <div class="form-group col-lg-12">
                <label for="notes">{{ trans('cruds.checkComplain.fields.notes') }}</label>
                <textarea class="form-control ckeditor {{ $errors->has('notes') ? 'is-invalid' : '' }}" name="notes" id="notes">{!! old('notes', $checkComplain->notes) !!}</textarea>
                @if($errors->has('notes'))
                    <span class="text-danger">{{ $errors->first('notes') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.checkComplain.fields.notes_helper') }}</span>
            </div>
            <div class="form-group col-lg-12">
                <label for="attechment">{{ trans('cruds.checkComplain.fields.attechment') }}</label>
                <div class="needsclick dropzone {{ $errors->has('attechment') ? 'is-invalid' : '' }}" id="attechment-dropzone">
                </div>
                @if($errors->has('attechment'))
                    <span class="text-danger">{{ $errors->first('attechment') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.checkComplain.fields.attechment_helper') }}</span>
            </div>
            @php
                $isAdmin = auth()->user()->roles->contains('title', 'Admin');
            @endphp
            @if($isAdmin)
            <div class="form-group col-lg-12">
                <label for="admin_message">{{ trans('cruds.checkComplain.fields.admin_message') }}</label>
                <textarea class="form-control ckeditor {{ $errors->has('admin_message') ? 'is-invalid' : '' }}" name="admin_message" id="admin_message">{!! old('admin_message', $checkComplain->admin_message) !!}</textarea>
                @if($errors->has('admin_message'))
                    <span class="text-danger">{{ $errors->first('admin_message') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.checkComplain.fields.admin_message_helper') }}</span>
            </div>
            @endif
            @if($isAdmin)
                <div class="form-group col-lg-12">
                    <label class="required">{{ trans('cruds.checkComplain.fields.status') }}</label>
                    <select class="form-control {{ $errors->has('status') ? 'is-invalid' : '' }}" name="status" id="status" required>
                        <option value disabled {{ old('status', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                        @foreach(App\Models\CheckComplain::STATUS_SELECT as $key => $label)
                            <option value="{{ $key }}" {{ old('status', $checkComplain->status) === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @if($errors->has('status'))
                        <span class="text-danger">{{ $errors->first('status') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.checkComplain.fields.status_helper') }}</span>
                </div>
            @endif

            <div class="form-group col-lg-6">
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
                xhr.open('POST', '{{ route('admin.check-complains.storeCKEditorImages') }}', true);
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
                data.append('crud_id', '{{ $checkComplain->id ?? 0 }}');
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
    var uploadedAttechmentMap = {}
Dropzone.options.attechmentDropzone = {
    url: '{{ route('admin.check-complains.storeMedia') }}',
    maxFilesize: 50, // MB
    addRemoveLinks: true,
    headers: {
      'X-CSRF-TOKEN': "{{ csrf_token() }}"
    },
    params: {
      size: 50
    },
    success: function (file, response) {
      $('form').append('<input type="hidden" name="attechment[]" value="' + response.name + '">')
      uploadedAttechmentMap[file.name] = response.name
    },
    removedfile: function (file) {
      file.previewElement.remove()
      var name = ''
      if (typeof file.file_name !== 'undefined') {
        name = file.file_name
      } else {
        name = uploadedAttechmentMap[file.name]
      }
      $('form').find('input[name="attechment[]"][value="' + name + '"]').remove()
    },
    init: function () {
@if(isset($checkComplain) && $checkComplain->attechment)
          var files =
            {!! json_encode($checkComplain->attechment) !!}
              for (var i in files) {
              var file = files[i]
              this.options.addedfile.call(this, file)
              file.previewElement.classList.add('dz-complete')
              $('form').append('<input type="hidden" name="attechment[]" value="' + file.file_name + '">')
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
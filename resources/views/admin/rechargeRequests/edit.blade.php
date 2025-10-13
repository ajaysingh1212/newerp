@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.rechargeRequest.title_singular') }}
    </div>

    <div class="card-body">
      @include('watermark')
        <form method="POST" action="{{ route("admin.recharge-requests.update", [$rechargeRequest->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="form-group">
                <label for="user_id">{{ trans('cruds.rechargeRequest.fields.user') }}</label>
                <select class="form-control select2 {{ $errors->has('user') ? 'is-invalid' : '' }}" name="user_id" id="user_id">
                    @foreach($users as $id => $entry)
                        <option value="{{ $id }}" {{ (old('user_id') ? old('user_id') : $rechargeRequest->user->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('user'))
                    <span class="text-danger">{{ $errors->first('user') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.rechargeRequest.fields.user_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="vehicle_number">{{ trans('cruds.rechargeRequest.fields.vehicle_number') }}</label>
                <input class="form-control {{ $errors->has('vehicle_number') ? 'is-invalid' : '' }}" type="text" name="vehicle_number" id="vehicle_number" value="{{ old('vehicle_number', $rechargeRequest->vehicle_number) }}" required>
                @if($errors->has('vehicle_number'))
                    <span class="text-danger">{{ $errors->first('vehicle_number') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.rechargeRequest.fields.vehicle_number_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="product_id">{{ trans('cruds.rechargeRequest.fields.product') }}</label>
                <select class="form-control select2 {{ $errors->has('product') ? 'is-invalid' : '' }}" name="product_id" id="product_id">
                    @foreach($products as $id => $entry)
                        <option value="{{ $id }}" {{ (old('product_id') ? old('product_id') : $rechargeRequest->product->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('product'))
                    <span class="text-danger">{{ $errors->first('product') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.rechargeRequest.fields.product_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="select_recharge_id">{{ trans('cruds.rechargeRequest.fields.select_recharge') }}</label>
                <select class="form-control select2 {{ $errors->has('select_recharge') ? 'is-invalid' : '' }}" name="select_recharge_id" id="select_recharge_id">
                    @foreach($select_recharges as $id => $entry)
                        <option value="{{ $id }}" {{ (old('select_recharge_id') ? old('select_recharge_id') : $rechargeRequest->select_recharge->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('select_recharge'))
                    <span class="text-danger">{{ $errors->first('select_recharge') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.rechargeRequest.fields.select_recharge_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="notes">{{ trans('cruds.rechargeRequest.fields.notes') }}</label>
                <textarea class="form-control ckeditor {{ $errors->has('notes') ? 'is-invalid' : '' }}" name="notes" id="notes">{!! old('notes', $rechargeRequest->notes) !!}</textarea>
                @if($errors->has('notes'))
                    <span class="text-danger">{{ $errors->first('notes') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.rechargeRequest.fields.notes_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="attechment">{{ trans('cruds.rechargeRequest.fields.attechment') }}</label>
                <div class="needsclick dropzone {{ $errors->has('attechment') ? 'is-invalid' : '' }}" id="attechment-dropzone">
                </div>
                @if($errors->has('attechment'))
                    <span class="text-danger">{{ $errors->first('attechment') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.rechargeRequest.fields.attechment_helper') }}</span>
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
                xhr.open('POST', '{{ route('admin.recharge-requests.storeCKEditorImages') }}', true);
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
                data.append('crud_id', '{{ $rechargeRequest->id ?? 0 }}');
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
    Dropzone.options.attechmentDropzone = {
    url: '{{ route('admin.recharge-requests.storeMedia') }}',
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
      $('form').find('input[name="attechment"]').remove()
      $('form').append('<input type="hidden" name="attechment" value="' + response.name + '">')
    },
    removedfile: function (file) {
      file.previewElement.remove()
      if (file.status !== 'error') {
        $('form').find('input[name="attechment"]').remove()
        this.options.maxFiles = this.options.maxFiles + 1
      }
    },
    init: function () {
@if(isset($rechargeRequest) && $rechargeRequest->attechment)
      var file = {!! json_encode($rechargeRequest->attechment) !!}
          this.options.addedfile.call(this, file)
      file.previewElement.classList.add('dz-complete')
      $('form').append('<input type="hidden" name="attechment" value="' + file.file_name + '">')
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
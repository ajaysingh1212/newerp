@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.withdrawalRequest.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.withdrawal-requests.update", [$withdrawalRequest->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="form-group">
                <label class="required" for="select_investor_id">{{ trans('cruds.withdrawalRequest.fields.select_investor') }}</label>
                <select class="form-control select2 {{ $errors->has('select_investor') ? 'is-invalid' : '' }}" name="select_investor_id" id="select_investor_id" required>
                    @foreach($select_investors as $id => $entry)
                        <option value="{{ $id }}" {{ (old('select_investor_id') ? old('select_investor_id') : $withdrawalRequest->select_investor->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('select_investor'))
                    <span class="text-danger">{{ $errors->first('select_investor') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.withdrawalRequest.fields.select_investor_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="investment_id">{{ trans('cruds.withdrawalRequest.fields.investment') }}</label>
                <select class="form-control select2 {{ $errors->has('investment') ? 'is-invalid' : '' }}" name="investment_id" id="investment_id" required>
                    @foreach($investments as $id => $entry)
                        <option value="{{ $id }}" {{ (old('investment_id') ? old('investment_id') : $withdrawalRequest->investment->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('investment'))
                    <span class="text-danger">{{ $errors->first('investment') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.withdrawalRequest.fields.investment_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="amount">{{ trans('cruds.withdrawalRequest.fields.amount') }}</label>
                <input class="form-control {{ $errors->has('amount') ? 'is-invalid' : '' }}" type="number" name="amount" id="amount" value="{{ old('amount', $withdrawalRequest->amount) }}" step="0.01" required>
                @if($errors->has('amount'))
                    <span class="text-danger">{{ $errors->first('amount') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.withdrawalRequest.fields.amount_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required">{{ trans('cruds.withdrawalRequest.fields.type') }}</label>
                <select class="form-control {{ $errors->has('type') ? 'is-invalid' : '' }}" name="type" id="type" required>
                    <option value disabled {{ old('type', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                    @foreach(App\Models\WithdrawalRequest::TYPE_SELECT as $key => $label)
                        <option value="{{ $key }}" {{ old('type', $withdrawalRequest->type) === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @if($errors->has('type'))
                    <span class="text-danger">{{ $errors->first('type') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.withdrawalRequest.fields.type_helper') }}</span>
            </div>
            <div class="form-group">
                <label>{{ trans('cruds.withdrawalRequest.fields.status') }}</label>
                <select class="form-control {{ $errors->has('status') ? 'is-invalid' : '' }}" name="status" id="status">
                    <option value disabled {{ old('status', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                    @foreach(App\Models\WithdrawalRequest::STATUS_SELECT as $key => $label)
                        <option value="{{ $key }}" {{ old('status', $withdrawalRequest->status) === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @if($errors->has('status'))
                    <span class="text-danger">{{ $errors->first('status') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.withdrawalRequest.fields.status_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="processing_hours">{{ trans('cruds.withdrawalRequest.fields.processing_hours') }}</label>
                <input class="form-control {{ $errors->has('processing_hours') ? 'is-invalid' : '' }}" type="text" name="processing_hours" id="processing_hours" value="{{ old('processing_hours', $withdrawalRequest->processing_hours) }}">
                @if($errors->has('processing_hours'))
                    <span class="text-danger">{{ $errors->first('processing_hours') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.withdrawalRequest.fields.processing_hours_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="requested_at">{{ trans('cruds.withdrawalRequest.fields.requested_at') }}</label>
                <input class="form-control date {{ $errors->has('requested_at') ? 'is-invalid' : '' }}" type="text" name="requested_at" id="requested_at" value="{{ old('requested_at', $withdrawalRequest->requested_at) }}">
                @if($errors->has('requested_at'))
                    <span class="text-danger">{{ $errors->first('requested_at') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.withdrawalRequest.fields.requested_at_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="approved_at">{{ trans('cruds.withdrawalRequest.fields.approved_at') }}</label>
                <input class="form-control {{ $errors->has('approved_at') ? 'is-invalid' : '' }}" type="text" name="approved_at" id="approved_at" value="{{ old('approved_at', $withdrawalRequest->approved_at) }}">
                @if($errors->has('approved_at'))
                    <span class="text-danger">{{ $errors->first('approved_at') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.withdrawalRequest.fields.approved_at_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="notes">{{ trans('cruds.withdrawalRequest.fields.notes') }}</label>
                <textarea class="form-control ckeditor {{ $errors->has('notes') ? 'is-invalid' : '' }}" name="notes" id="notes">{!! old('notes', $withdrawalRequest->notes) !!}</textarea>
                @if($errors->has('notes'))
                    <span class="text-danger">{{ $errors->first('notes') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.withdrawalRequest.fields.notes_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="remarks">{{ trans('cruds.withdrawalRequest.fields.remarks') }}</label>
                <textarea class="form-control ckeditor {{ $errors->has('remarks') ? 'is-invalid' : '' }}" name="remarks" id="remarks">{!! old('remarks', $withdrawalRequest->remarks) !!}</textarea>
                @if($errors->has('remarks'))
                    <span class="text-danger">{{ $errors->first('remarks') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.withdrawalRequest.fields.remarks_helper') }}</span>
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
                xhr.open('POST', '{{ route('admin.withdrawal-requests.storeCKEditorImages') }}', true);
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
                data.append('crud_id', '{{ $withdrawalRequest->id ?? 0 }}');
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

@endsection
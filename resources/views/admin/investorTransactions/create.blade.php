@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('cruds.investorTransaction.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.investor-transactions.store") }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label class="required" for="investor_id">{{ trans('cruds.investorTransaction.fields.investor') }}</label>
                <select class="form-control select2 {{ $errors->has('investor') ? 'is-invalid' : '' }}" name="investor_id" id="investor_id" required>
                    @foreach($investors as $id => $entry)
                        <option value="{{ $id }}" {{ old('investor_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('investor'))
                    <span class="text-danger">{{ $errors->first('investor') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.investorTransaction.fields.investor_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="investment_id">{{ trans('cruds.investorTransaction.fields.investment') }}</label>
                <select class="form-control select2 {{ $errors->has('investment') ? 'is-invalid' : '' }}" name="investment_id" id="investment_id">
                    @foreach($investments as $id => $entry)
                        <option value="{{ $id }}" {{ old('investment_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('investment'))
                    <span class="text-danger">{{ $errors->first('investment') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.investorTransaction.fields.investment_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required">{{ trans('cruds.investorTransaction.fields.transaction_type') }}</label>
                <select class="form-control {{ $errors->has('transaction_type') ? 'is-invalid' : '' }}" name="transaction_type" id="transaction_type" required>
                    <option value disabled {{ old('transaction_type', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                    @foreach(App\Models\InvestorTransaction::TRANSACTION_TYPE_SELECT as $key => $label)
                        <option value="{{ $key }}" {{ old('transaction_type', '') === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @if($errors->has('transaction_type'))
                    <span class="text-danger">{{ $errors->first('transaction_type') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.investorTransaction.fields.transaction_type_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="amount">{{ trans('cruds.investorTransaction.fields.amount') }}</label>
                <input class="form-control {{ $errors->has('amount') ? 'is-invalid' : '' }}" type="number" name="amount" id="amount" value="{{ old('amount', '') }}" step="0.01" required>
                @if($errors->has('amount'))
                    <span class="text-danger">{{ $errors->first('amount') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.investorTransaction.fields.amount_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="narration">{{ trans('cruds.investorTransaction.fields.narration') }}</label>
                <textarea class="form-control ckeditor {{ $errors->has('narration') ? 'is-invalid' : '' }}" name="narration" id="narration">{!! old('narration') !!}</textarea>
                @if($errors->has('narration'))
                    <span class="text-danger">{{ $errors->first('narration') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.investorTransaction.fields.narration_helper') }}</span>
            </div>
            <div class="form-group">
                <label>{{ trans('cruds.investorTransaction.fields.status') }}</label>
                <select class="form-control {{ $errors->has('status') ? 'is-invalid' : '' }}" name="status" id="status">
                    <option value disabled {{ old('status', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                    @foreach(App\Models\InvestorTransaction::STATUS_SELECT as $key => $label)
                        <option value="{{ $key }}" {{ old('status', 'pending') === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @if($errors->has('status'))
                    <span class="text-danger">{{ $errors->first('status') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.investorTransaction.fields.status_helper') }}</span>
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
                xhr.open('POST', '{{ route('admin.investor-transactions.storeCKEditorImages') }}', true);
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
                data.append('crud_id', '{{ $investorTransaction->id ?? 0 }}');
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
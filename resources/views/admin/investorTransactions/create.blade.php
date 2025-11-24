@extends('layouts.admin')
@section('content')

<div class="max-w-5xl mx-auto py-8">
    <div class="bg-white shadow-xl rounded-2xl p-8 border border-gray-200">

        <!-- Header -->
        <div class="pb-4 border-b mb-6">
            <h2 class="text-2xl font-bold text-indigo-600">
                {{ trans('global.create') }} {{ trans('cruds.investorTransaction.title_singular') }}
            </h2>
        </div>

        <form method="POST" action="{{ route('admin.investor-transactions.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- Investor -->
                <div class="bg-blue-50 p-4 rounded-lg shadow-inner">
                    <label class="block text-sm font-semibold text-gray-700 mb-1 required">
                        {{ trans('cruds.investorTransaction.fields.investor') }}
                    </label>
                    <select name="investor_id" id="investor_id"
                        class="w-full rounded-lg border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        required>
                        @foreach($investors as $id => $entry)
                        <option value="{{ $id }}" {{ old('investor_id') == $id ? 'selected' : '' }}>
                            {{ $entry }}
                        </option>
                        @endforeach
                    </select>
                    @error('investor_id')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Investment -->
                <div class="bg-green-50 p-4 rounded-lg shadow-inner">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        {{ trans('cruds.investorTransaction.fields.investment') }}
                    </label>
                    <select name="investment_id" id="investment_id"
                        class="w-full rounded-lg border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @foreach($investments as $id => $entry)
                        <option value="{{ $id }}" {{ old('investment_id') == $id ? 'selected' : '' }}>
                            {{ $entry }}
                        </option>
                        @endforeach
                    </select>
                    @error('investment_id')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Transaction Type -->
                <div class="bg-yellow-50 p-4 rounded-lg shadow-inner">
                    <label class="block text-sm font-semibold text-gray-700 mb-1 required">
                        {{ trans('cruds.investorTransaction.fields.transaction_type') }}
                    </label>
                    <select name="transaction_type" id="transaction_type"
                        class="w-full rounded-lg border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        required>
                        <option value disabled selected>{{ trans('global.pleaseSelect') }}</option>
                        @foreach(App\Models\InvestorTransaction::TRANSACTION_TYPE_SELECT as $key => $label)
                        <option value="{{ $key }}" {{ old('transaction_type') == $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                        @endforeach
                    </select>
                    @error('transaction_type')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Amount -->
                <div class="bg-purple-50 p-4 rounded-lg shadow-inner">
                    <label class="block text-sm font-semibold text-gray-700 mb-1 required">
                        {{ trans('cruds.investorTransaction.fields.amount') }}
                    </label>
                    <input type="number" step="0.01" name="amount" id="amount"
                        value="{{ old('amount') }}"
                        class="w-full rounded-lg border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        required>
                    @error('amount')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Narration -->
                <div class="bg-blue-50 p-4 rounded-lg shadow-inner md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        {{ trans('cruds.investorTransaction.fields.narration') }}
                    </label>
                    <textarea name="narration" id="narration"
                        class="ckeditor w-full rounded-lg border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{!! old('narration') !!}</textarea>
                    @error('narration')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div class="bg-green-50 p-4 rounded-lg shadow-inner">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        {{ trans('cruds.investorTransaction.fields.status') }}
                    </label>
                    <select name="status" id="status"
                        class="w-full rounded-lg border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value disabled selected>{{ trans('global.pleaseSelect') }}</option>
                        @foreach(App\Models\InvestorTransaction::STATUS_SELECT as $key => $label)
                        <option value="{{ $key }}" {{ old('status', 'pending') == $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                        @endforeach
                    </select>
                    @error('status')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

            </div>

            <!-- Submit -->
            <div class="mt-8 text-right">
                <button type="submit"
                    class="bg-indigo-600 text-white px-6 py-2 rounded-lg shadow hover:bg-indigo-700 transition">
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
            editor.plugins.get('FileRepository').createUploadAdapter = function (loader) {
                return {
                    upload: function () {
                        return loader.file.then(function (file) {
                            return new Promise(function (resolve, reject) {

                                var xhr = new XMLHttpRequest();
                                xhr.open('POST', '{{ route('admin.investor-transactions.storeCKEditorImages') }}', true);
                                xhr.setRequestHeader('x-csrf-token', window._token);
                                xhr.setRequestHeader('Accept', 'application/json');
                                xhr.responseType = 'json';

                                xhr.addEventListener('error', function () { reject("Upload error"); });
                                xhr.addEventListener('abort', function () { reject(); });
                                xhr.addEventListener('load', function () {
                                    var response = xhr.response;

                                    if (!response || xhr.status !== 201) {
                                        return reject("Upload failed");
                                    }

                                    $('form').append('<input type="hidden" name="ck-media[]" value="' + response.id + '">');

                                    resolve({ default: response.url });
                                });

                                var data = new FormData();
                                data.append('upload', file);
                                data.append('crud_id', '{{ $investorTransaction->id ?? 0 }}');
                                xhr.send(data);
                            });
                        })
                    }
                };
            };
        }

        document.querySelectorAll('.ckeditor').forEach(editor => {
            ClassicEditor.create(editor, { extraPlugins: [SimpleUploadAdapter] });
        });
    });
</script>
@endsection

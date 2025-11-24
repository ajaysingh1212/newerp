@extends('layouts.admin')
@section('content')

<div class="max-w-5xl mx-auto py-8">
    <div class="bg-white shadow-xl rounded-2xl p-8 border border-gray-200">

        <!-- Header -->
        <div class="pb-4 border-b mb-6">
            <h2 class="text-2xl font-bold text-indigo-600">
                {{ trans('global.create') }} {{ trans('cruds.withdrawalRequest.title_singular') }}
            </h2>
        </div>

        <form method="POST" action="{{ route('admin.withdrawal-requests.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- Select Investor -->
                <div class="bg-blue-50 p-4 rounded-lg shadow-inner">
                    <label class="block text-sm font-semibold text-gray-700 mb-1 required">
                        {{ trans('cruds.withdrawalRequest.fields.select_investor') }}
                    </label>

                    <select name="select_investor_id" id="select_investor_id"
                        class="select2 w-full rounded-lg border-gray-300 shadow-sm px-3 py-2 text-sm
                               focus:border-indigo-500 focus:ring-indigo-500" required>

                        @foreach($select_investors as $id => $entry)
                            <option value="{{ $id }}" {{ old('select_investor_id') == $id ? 'selected' : '' }}>
                                {{ $entry }}
                            </option>
                        @endforeach
                    </select>

                    @error('select_investor')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Investment -->
                <div class="bg-blue-50 p-4 rounded-lg shadow-inner">
                    <label class="block text-sm font-semibold text-gray-700 mb-1 required">
                        {{ trans('cruds.withdrawalRequest.fields.investment') }}
                    </label>

                    <select name="investment_id" id="investment_id"
                        class="select2 w-full rounded-lg border-gray-300 shadow-sm px-3 py-2 text-sm
                               focus:border-indigo-500 focus:ring-indigo-500" required>

                        @foreach($investments as $id => $entry)
                            <option value="{{ $id }}" {{ old('investment_id') == $id ? 'selected' : '' }}>
                                {{ $entry }}
                            </option>
                        @endforeach
                    </select>

                    @error('investment')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Amount -->
                <div class="bg-green-50 p-4 rounded-lg shadow-inner">
                    <label class="block text-sm font-semibold text-gray-700 mb-1 required">
                        {{ trans('cruds.withdrawalRequest.fields.amount') }}
                    </label>

                    <input type="number" name="amount" id="amount" step="0.01"
                           value="{{ old('amount') }}"
                           class="w-full rounded-lg border-gray-300 shadow-sm px-3 py-2 text-sm
                                  focus:border-indigo-500 focus:ring-indigo-500" required>

                    @error('amount')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Type -->
                <div class="bg-green-50 p-4 rounded-lg shadow-inner">
                    <label class="block text-sm font-semibold text-gray-700 mb-1 required">
                        {{ trans('cruds.withdrawalRequest.fields.type') }}
                    </label>

                    <select name="type" id="type"
                        class="w-full rounded-lg border-gray-300 shadow-sm px-3 py-2 text-sm
                               focus:border-indigo-500 focus:ring-indigo-500" required>

                        <option value disabled selected>{{ trans('global.pleaseSelect') }}</option>

                        @foreach(App\Models\WithdrawalRequest::TYPE_SELECT as $key => $label)
                            <option value="{{ $key }}" {{ old('type','interest') == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>

                    @error('type')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Requested Date -->
                <div class="bg-purple-50 p-4 rounded-lg shadow-inner">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        {{ trans('cruds.withdrawalRequest.fields.requested_at') }}
                    </label>

                    <input type="text" name="requested_at" id="requested_at"
                           value="{{ old('requested_at') }}"
                           class="form-control date w-full rounded-lg border-gray-300 shadow-sm px-3 py-2 text-sm
                                  focus:border-indigo-500 focus:ring-indigo-500">

                    @error('requested_at')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Notes -->
                <div class="bg-yellow-50 p-4 rounded-lg shadow-inner md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        {{ trans('cruds.withdrawalRequest.fields.notes') }}
                    </label>

                    <textarea name="notes" id="notes"
                              class="ckeditor w-full rounded-lg border-gray-300 shadow-sm px-3 py-2 text-sm
                                     focus:border-indigo-500 focus:ring-indigo-500">{{ old('notes') }}</textarea>

                    @error('notes')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

            </div>

            <!-- Submit Button -->
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

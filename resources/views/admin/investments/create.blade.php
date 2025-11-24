@extends('layouts.admin')
@section('content')

<div class="max-w-5xl mx-auto py-8">
    <div class="bg-white shadow-xl rounded-2xl p-8 border border-gray-200">

        <!-- Header -->
        <div class="pb-4 border-b mb-6">
            <h2 class="text-2xl font-bold text-indigo-600">
                {{ trans('global.create') }} {{ trans('cruds.investment.title_singular') }}
            </h2>
        </div>

        <form method="POST" action="{{ route('admin.investments.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- Investor -->
                <div class="bg-blue-50 p-4 rounded-lg shadow-inner">
                    <label class="block text-sm font-semibold text-gray-700 mb-1 required">
                        {{ trans('cruds.investment.fields.select_investor') }}
                    </label>

                    <select name="select_investor_id" id="select_investor_id"
                        required
                        class="select2 w-full rounded-lg border-gray-300 shadow-sm px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">

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

                <!-- Plan -->
                <div class="bg-blue-50 p-4 rounded-lg shadow-inner">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        {{ trans('cruds.investment.fields.select_plan') }}
                    </label>

                    <select name="select_plan_id" id="select_plan_id"
                        class="select2 w-full rounded-lg border-gray-300 shadow-sm px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">

                        @foreach($select_plans as $id => $entry)
                            <option value="{{ $id }}" {{ old('select_plan_id') == $id ? 'selected' : '' }}>
                                {{ $entry }}
                            </option>
                        @endforeach
                    </select>

                    @error('select_plan')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Principal Amount -->
                <div class="bg-green-50 p-4 rounded-lg shadow-inner">
                    <label class="block text-sm font-semibold text-gray-700 mb-1 required">
                        {{ trans('cruds.investment.fields.principal_amount') }}
                    </label>

                    <input type="number" step="0.01" name="principal_amount" id="principal_amount"
                           value="{{ old('principal_amount') }}"
                           class="w-full rounded-lg border-gray-300 shadow-sm px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500" required>

                    @error('principal_amount')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Secure Interest Percent -->
                <div class="bg-green-50 p-4 rounded-lg shadow-inner">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        {{ trans('cruds.investment.fields.secure_interest_percent') }}
                    </label>

                    <input type="text" name="secure_interest_percent" id="secure_interest_percent"
                           value="{{ old('secure_interest_percent') }}"
                           class="w-full rounded-lg border-gray-300 shadow-sm px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">

                    @error('secure_interest_percent')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Market Interest -->
                <div class="bg-yellow-50 p-4 rounded-lg shadow-inner">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        {{ trans('cruds.investment.fields.market_interest_percent') }}
                    </label>

                    <input type="text" name="market_interest_percent" id="market_interest_percent"
                           value="{{ old('market_interest_percent') }}"
                           class="w-full rounded-lg border-gray-300 shadow-sm px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">

                    @error('market_interest_percent')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Total Interest -->
                <div class="bg-yellow-50 p-4 rounded-lg shadow-inner">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        {{ trans('cruds.investment.fields.total_interest_percent') }}
                    </label>

                    <input type="text" name="total_interest_percent" id="total_interest_percent"
                           value="{{ old('total_interest_percent') }}"
                           class="w-full rounded-lg border-gray-300 shadow-sm px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">

                    @error('total_interest_percent')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Start Date -->
                <div class="bg-purple-50 p-4 rounded-lg shadow-inner">
                    <label class="block text-sm font-semibold text-gray-700 mb-1 required">
                        {{ trans('cruds.investment.fields.start_date') }}
                    </label>

                    <input type="text" name="start_date" id="start_date"
                           value="{{ old('start_date') }}"
                           class="form-control date w-full rounded-lg border-gray-300 shadow-sm px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500" required>

                    @error('start_date')
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

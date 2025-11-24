@extends('layouts.admin')
@section('content')

<div class="max-w-5xl mx-auto py-8">
    <div class="bg-white shadow-xl rounded-2xl p-8 border border-gray-200">

        <!-- Header -->
        <div class="pb-4 border-b mb-6">
            <h2 class="text-2xl font-bold text-indigo-600">
                {{ trans('global.create') }} {{ trans('cruds.plan.title_singular') }}
            </h2>
        </div>

        <form method="POST" action="{{ route('admin.plans.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- Plan Name -->
                <div class="bg-blue-50 p-4 rounded-lg shadow-inner">
                    <label class="block text-sm font-semibold text-gray-700 mb-1 required">
                        {{ trans('cruds.plan.fields.plan_name') }}
                    </label>
                    <input type="text" name="plan_name" id="plan_name"
                        value="{{ old('plan_name') }}"
                        class="w-full rounded-lg border-gray-300 shadow-sm px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                        required>
                    @error('plan_name')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Secure Interest Percent -->
                <div class="bg-green-50 p-4 rounded-lg shadow-inner">
                    <label class="block text-sm font-semibold text-gray-700 mb-1 required">
                        {{ trans('cruds.plan.fields.secure_interest_percent') }}
                    </label>
                    <input type="text" name="secure_interest_percent"
                        class="w-full rounded-lg border-gray-300 shadow-sm px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                        value="{{ old('secure_interest_percent') }}" required>
                    @error('secure_interest_percent')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Market Interest Percent -->
                <div class="bg-yellow-50 p-4 rounded-lg shadow-inner">
                    <label class="block text-sm font-semibold text-gray-700 mb-1 required">
                        {{ trans('cruds.plan.fields.market_interest_percent') }}
                    </label>
                    <input type="text" name="market_interest_percent"
                        class="w-full rounded-lg border-gray-300 shadow-sm px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                        value="{{ old('market_interest_percent') }}" required>
                    @error('market_interest_percent')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Total Interest Percent -->
                <div class="bg-yellow-50 p-4 rounded-lg shadow-inner">
                    <label class="block text-sm font-semibold text-gray-700 mb-1 required">
                        {{ trans('cruds.plan.fields.total_interest_percent') }}
                    </label>
                    <input type="text" name="total_interest_percent"
                        class="w-full rounded-lg border-gray-300 shadow-sm px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                        value="{{ old('total_interest_percent') }}" required>
                    @error('total_interest_percent')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Payout Frequency -->
                <div class="bg-blue-50 p-4 rounded-lg shadow-inner">
                    <label class="block text-sm font-semibold text-gray-700 mb-1 required">
                        {{ trans('cruds.plan.fields.payout_frequency') }}
                    </label>
                    <select name="payout_frequency" id="payout_frequency"
                        class="w-full rounded-lg border-gray-300 shadow-sm px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                        required>
                        <option value disabled selected>{{ trans('global.pleaseSelect') }}</option>

                        @foreach(App\Models\Plan::PAYOUT_FREQUENCY_SELECT as $key => $label)
                            <option value="{{ $key }}"
                                {{ old('payout_frequency') == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('payout_frequency')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Min Invest Amount -->
                <div class="bg-green-50 p-4 rounded-lg shadow-inner">
                    <label class="block text-sm font-semibold text-gray-700 mb-1 required">
                        {{ trans('cruds.plan.fields.min_invest_amount') }}
                    </label>
                    <input type="number" step="0.01" name="min_invest_amount"
                        class="w-full rounded-lg border-gray-300 shadow-sm px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                        value="{{ old('min_invest_amount') }}" required>
                    @error('min_invest_amount')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Max Invest Amount -->
                <div class="bg-green-50 p-4 rounded-lg shadow-inner">
                    <label class="block text-sm font-semibold text-gray-700 mb-1 required">
                        {{ trans('cruds.plan.fields.max_invest_amount') }}
                    </label>
                    <input type="number" step="0.01" name="max_invest_amount"
                        class="w-full rounded-lg border-gray-300 shadow-sm px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                        value="{{ old('max_invest_amount') }}" required>
                    @error('max_invest_amount')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Lockin Days -->
                <div class="bg-purple-50 p-4 rounded-lg shadow-inner">
                    <label class="block text-sm font-semibold text-gray-700 mb-1 required">
                        {{ trans('cruds.plan.fields.lockin_days') }}
                    </label>
                    <input type="text" name="lockin_days"
                        class="w-full rounded-lg border-gray-300 shadow-sm px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                        value="{{ old('lockin_days') }}" required>
                    @error('lockin_days')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Withdraw Processing Hours -->
                <div class="bg-purple-50 p-4 rounded-lg shadow-inner">
                    <label class="block text-sm font-semibold text-gray-700 mb-1 required">
                        {{ trans('cruds.plan.fields.withdraw_processing_hours') }}
                    </label>
                    <input type="text" name="withdraw_processing_hours"
                        class="w-full rounded-lg border-gray-300 shadow-sm px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                        value="{{ old('withdraw_processing_hours') }}" required>
                    @error('withdraw_processing_hours')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div class="bg-blue-50 p-4 rounded-lg shadow-inner">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        {{ trans('cruds.plan.fields.status') }}
                    </label>
                    <select name="status"
                        class="w-full rounded-lg border-gray-300 shadow-sm px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value disabled selected>{{ trans('global.pleaseSelect') }}</option>
                        @foreach(App\Models\Plan::STATUS_SELECT as $key => $label)
                            <option value="{{ $key }}"
                                {{ old('status', 'Active') == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('status')
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

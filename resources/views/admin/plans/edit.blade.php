@extends('layouts.admin')
@section('content')

<div class="max-w-5xl mx-auto py-8">
    <div class="bg-white shadow-lg rounded-2xl p-8">

        <!-- Header -->
        <div class="flex justify-between items-center border-b pb-4 mb-6">
            <h2 class="text-2xl font-bold text-indigo-600 flex items-center gap-2">
                <i class="fas fa-chart-line"></i>
                {{ trans('global.edit') }} {{ trans('cruds.plan.title_singular') }}
            </h2>

            <a href="{{ route('admin.plans.index') }}"
               class="px-3 py-1 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 text-sm transition">
                ← {{ trans('global.back_to_list') }}
            </a>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('admin.plans.update', [$plan->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- Plan Name -->
                <div class="bg-blue-50 p-4 rounded-lg shadow-inner">
                    <label for="plan_name" class="block text-sm font-semibold text-gray-700 mb-1">
                        {{ trans('cruds.plan.fields.plan_name') }} <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="plan_name" id="plan_name"
                           value="{{ old('plan_name', $plan->plan_name) }}" required
                           class="w-full rounded-lg border-gray-300 px-3 py-2 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('plan_name')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Secure Interest Percent -->
                <div class="bg-blue-50 p-4 rounded-lg shadow-inner">
                    <label for="secure_interest_percent" class="block text-sm font-semibold text-gray-700 mb-1">
                        {{ trans('cruds.plan.fields.secure_interest_percent') }} <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="secure_interest_percent" id="secure_interest_percent"
                           value="{{ old('secure_interest_percent', $plan->secure_interest_percent) }}" required
                           class="w-full rounded-lg border-gray-300 px-3 py-2 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('secure_interest_percent')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Market Interest Percent -->
                <div class="bg-green-50 p-4 rounded-lg shadow-inner">
                    <label for="market_interest_percent" class="block text-sm font-semibold text-gray-700 mb-1">
                        {{ trans('cruds.plan.fields.market_interest_percent') }} <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="market_interest_percent" id="market_interest_percent"
                           value="{{ old('market_interest_percent', $plan->market_interest_percent) }}" required
                           class="w-full rounded-lg border-gray-300 px-3 py-2 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('market_interest_percent')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Total Interest Percent -->
                <div class="bg-green-50 p-4 rounded-lg shadow-inner">
                    <label for="total_interest_percent" class="block text-sm font-semibold text-gray-700 mb-1">
                        {{ trans('cruds.plan.fields.total_interest_percent') }} <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="total_interest_percent" id="total_interest_percent"
                           value="{{ old('total_interest_percent', $plan->total_interest_percent) }}" required
                           class="w-full rounded-lg border-gray-300 px-3 py-2 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('total_interest_percent')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Payout Frequency -->
                <div class="bg-yellow-50 p-4 rounded-lg shadow-inner">
                    <label for="payout_frequency" class="block text-sm font-semibold text-gray-700 mb-1">
                        {{ trans('cruds.plan.fields.payout_frequency') }} <span class="text-red-500">*</span>
                    </label>
                    <select name="payout_frequency" id="payout_frequency"
                            class="select2 w-full rounded-lg border-gray-300 shadow-sm px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value disabled>{{ trans('global.pleaseSelect') }}</option>
                        @foreach(App\Models\Plan::PAYOUT_FREQUENCY_SELECT as $key => $label)
                            <option value="{{ $key }}" {{ old('payout_frequency', $plan->payout_frequency) == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('payout_frequency')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Min Invest -->
                <div class="bg-blue-50 p-4 rounded-lg shadow-inner">
                    <label for="min_invest_amount" class="block text-sm font-semibold text-gray-700 mb-1">
                        {{ trans('cruds.plan.fields.min_invest_amount') }} <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="min_invest_amount" id="min_invest_amount"
                           value="{{ old('min_invest_amount', $plan->min_invest_amount) }}" step="0.01" required
                           class="w-full rounded-lg border-gray-300 px-3 py-2 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('min_invest_amount')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Max Invest -->
                <div class="bg-blue-50 p-4 rounded-lg shadow-inner">
                    <label for="max_invest_amount" class="block text-sm font-semibold text-gray-700 mb-1">
                        {{ trans('cruds.plan.fields.max_invest_amount') }} <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="max_invest_amount" id="max_invest_amount"
                           value="{{ old('max_invest_amount', $plan->max_invest_amount) }}" step="0.01" required
                           class="w-full rounded-lg border-gray-300 px-3 py-2 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('max_invest_amount')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Lockin Days -->
                <div class="bg-green-50 p-4 rounded-lg shadow-inner">
                    <label for="lockin_days" class="block text-sm font-semibold text-gray-700 mb-1">
                        {{ trans('cruds.plan.fields.lockin_days') }} <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="lockin_days" id="lockin_days"
                           value="{{ old('lockin_days', $plan->lockin_days) }}" required
                           class="w-full rounded-lg border-gray-300 px-3 py-2 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('lockin_days')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Withdraw Processing Hours -->
                <div class="bg-green-50 p-4 rounded-lg shadow-inner">
                    <label for="withdraw_processing_hours" class="block text-sm font-semibold text-gray-700 mb-1">
                        {{ trans('cruds.plan.fields.withdraw_processing_hours') }} <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="withdraw_processing_hours" id="withdraw_processing_hours"
                           value="{{ old('withdraw_processing_hours', $plan->withdraw_processing_hours) }}" required
                           class="w-full rounded-lg border-gray-300 px-3 py-2 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('withdraw_processing_hours')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div class="bg-yellow-50 p-4 rounded-lg shadow-inner">
                    <label for="status" class="block text-sm font-semibold text-gray-700 mb-1">
                        {{ trans('cruds.plan.fields.status') }}
                    </label>
                    <select name="status" id="status"
                        class="select2 w-full rounded-lg border-gray-300 px-3 py-2 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @foreach(App\Models\Plan::STATUS_SELECT as $key => $label)
                            <option value="{{ $key }}" {{ old('status', $plan->status) == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('status')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

            </div>

            <!-- Actions -->
            <div class="flex justify-end gap-3 pt-6 border-t mt-6">
                <a href="{{ route('admin.plans.index') }}"
                   class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg shadow-sm hover:bg-gray-200 transition">
                    {{ trans('global.cancel') }}
                </a>

                <button type="submit"
                        class="px-5 py-2 bg-indigo-600 text-white font-medium rounded-lg shadow-md hover:bg-indigo-700 transition">
                    <i class="fas fa-save mr-1"></i> {{ trans('global.save') }}
                </button>
            </div>

        </form>
    </div>
</div>

@endsection

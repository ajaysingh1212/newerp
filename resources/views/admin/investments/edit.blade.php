@extends('layouts.admin')
@section('content')

<div class="max-w-5xl mx-auto py-8">
    <div class="bg-white shadow-lg rounded-2xl p-8">

        <!-- Header -->
        <div class="flex justify-between items-center border-b pb-4 mb-6">
            <h2 class="text-2xl font-bold text-indigo-600 flex items-center gap-2">
                <i class="fas fa-chart-line"></i>
                {{ trans('global.edit') }} {{ trans('cruds.investment.title_singular') }}
            </h2>

            <a href="{{ route('admin.investments.index') }}"
               class="px-3 py-1 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 text-sm transition">
                ← {{ trans('global.back_to_list') }}
            </a>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('admin.investments.update', [$investment->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- Select Investor -->
                <div class="bg-blue-50 p-4 rounded-lg shadow-inner">
                    <label for="select_investor_id" class="block text-sm font-semibold text-gray-700 mb-1">
                        {{ trans('cruds.investment.fields.select_investor') }} <span class="text-red-500">*</span>
                    </label>
                    <select name="select_investor_id" id="select_investor_id"
                        class="select2 w-full rounded-lg border-gray-300 shadow-sm px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @foreach($select_investors as $id => $entry)
                            <option value="{{ $id }}" 
                                {{ (old('select_investor_id', $investment->select_investor->id ?? '') == $id) ? 'selected' : '' }}>
                                {{ $entry }}
                            </option>
                        @endforeach
                    </select>
                    @error('select_investor')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Select Plan -->
                <div class="bg-blue-50 p-4 rounded-lg shadow-inner">
                    <label for="select_plan_id" class="block text-sm font-semibold text-gray-700 mb-1">
                        {{ trans('cruds.investment.fields.select_plan') }}
                    </label>
                    <select name="select_plan_id" id="select_plan_id"
                        class="select2 w-full rounded-lg border-gray-300 shadow-sm px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @foreach($select_plans as $id => $entry)
                            <option value="{{ $id }}" 
                                {{ (old('select_plan_id', $investment->select_plan->id ?? '') == $id) ? 'selected' : '' }}>
                                {{ $entry }}
                            </option>
                        @endforeach
                    </select>
                    @error('select_plan')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Principal Amount -->
                <div class="bg-green-50 p-4 rounded-lg shadow-inner">
                    <label for="principal_amount" class="block text-sm font-semibold text-gray-700 mb-1">
                        {{ trans('cruds.investment.fields.principal_amount') }} <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="principal_amount" id="principal_amount" step="0.01"
                        value="{{ old('principal_amount', $investment->principal_amount) }}" required
                        class="w-full rounded-lg border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    
                    @error('principal_amount')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Secure Interest Percent -->
                <div class="bg-green-50 p-4 rounded-lg shadow-inner">
                    <label for="secure_interest_percent" class="block text-sm font-semibold text-gray-700 mb-1">
                        {{ trans('cruds.investment.fields.secure_interest_percent') }}
                    </label>
                    <input type="text" name="secure_interest_percent" id="secure_interest_percent"
                        value="{{ old('secure_interest_percent', $investment->secure_interest_percent) }}"
                        class="w-full rounded-lg border-gray-300 px-3 py-2 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('secure_interest_percent')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Market Interest Percent -->
                <div class="bg-blue-50 p-4 rounded-lg shadow-inner">
                    <label for="market_interest_percent" class="block text-sm font-semibold text-gray-700 mb-1">
                        {{ trans('cruds.investment.fields.market_interest_percent') }}
                    </label>
                    <input type="text" name="market_interest_percent" id="market_interest_percent"
                        value="{{ old('market_interest_percent', $investment->market_interest_percent) }}"
                        class="w-full rounded-lg border-gray-300 px-3 py-2 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('market_interest_percent')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Total Interest Percent -->
                <div class="bg-blue-50 p-4 rounded-lg shadow-inner">
                    <label for="total_interest_percent" class="block text-sm font-semibold text-gray-700 mb-1">
                        {{ trans('cruds.investment.fields.total_interest_percent') }}
                    </label>
                    <input type="text" name="total_interest_percent" id="total_interest_percent"
                        value="{{ old('total_interest_percent', $investment->total_interest_percent) }}"
                        class="w-full rounded-lg border-gray-300 px-3 py-2 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('total_interest_percent')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Start Date -->
                <div class="bg-yellow-50 p-4 rounded-lg shadow-inner">
                    <label for="start_date" class="block text-sm font-semibold text-gray-700 mb-1">
                        {{ trans('cruds.investment.fields.start_date') }} <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="start_date" id="start_date"
                        value="{{ old('start_date', $investment->start_date) }}"
                        class="date w-full rounded-lg border-gray-300 px-3 py-2 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                    @error('start_date')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

            </div>

            <!-- Actions -->
            <div class="flex justify-end gap-3 pt-6 border-t mt-6">
                <a href="{{ route('admin.investments.index') }}"
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

@extends('layouts.admin')
@section('content')

<div class="max-w-5xl mx-auto py-8">
    <div class="bg-white shadow-xl rounded-2xl p-8 border border-gray-200">

        <!-- Header -->
        <div class="pb-4 border-b mb-6">
            <h2 class="text-2xl font-bold text-indigo-600">
                {{ trans('global.create') }} {{ trans('cruds.monthlyPayoutRecord.title_singular') }}
            </h2>
        </div>

        <form method="POST" action="{{ route('admin.monthly-payout-records.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- Investment -->
                <div class="bg-blue-50 p-4 rounded-lg shadow-inner">
                    <label class="block text-sm font-semibold text-gray-700 mb-1 required">
                        {{ trans('cruds.monthlyPayoutRecord.fields.investment') }}
                    </label>
                    <select name="investment_id" id="investment_id"
                        class="w-full rounded-lg border-gray-300 shadow-sm px-3 py-2 text-sm select2 focus:border-indigo-500 focus:ring-indigo-500"
                        required>
                        <option value disabled selected>{{ trans('global.pleaseSelect') }}</option>
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

                <!-- Investor -->
                <div class="bg-green-50 p-4 rounded-lg shadow-inner">
                    <label class="block text-sm font-semibold text-gray-700 mb-1 required">
                        {{ trans('cruds.monthlyPayoutRecord.fields.investor') }}
                    </label>
                    <select name="investor_id" id="investor_id"
                        class="w-full rounded-lg border-gray-300 shadow-sm px-3 py-2 text-sm select2 focus:border-indigo-500 focus:ring-indigo-500"
                        required>
                        <option value disabled selected>{{ trans('global.pleaseSelect') }}</option>
                        @foreach($investors as $id => $entry)
                            <option value="{{ $id }}" {{ old('investor_id') == $id ? 'selected' : '' }}>
                                {{ $entry }}
                            </option>
                        @endforeach
                    </select>
                    @error('investor')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Secure Interest Amount -->
                <div class="bg-yellow-50 p-4 rounded-lg shadow-inner">
                    <label class="block text-sm font-semibold text-gray-700 mb-1 required">
                        {{ trans('cruds.monthlyPayoutRecord.fields.secure_interest_amount') }}
                    </label>
                    <input type="text" name="secure_interest_amount" id="secure_interest_amount"
                        value="{{ old('secure_interest_amount') }}"
                        class="w-full rounded-lg border-gray-300 shadow-sm px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                        required>
                    @error('secure_interest_amount')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Market Interest Amount -->
                <div class="bg-yellow-50 p-4 rounded-lg shadow-inner">
                    <label class="block text-sm font-semibold text-gray-700 mb-1 required">
                        {{ trans('cruds.monthlyPayoutRecord.fields.market_interest_amount') }}
                    </label>
                    <input type="text" name="market_interest_amount" id="market_interest_amount"
                        value="{{ old('market_interest_amount') }}"
                        class="w-full rounded-lg border-gray-300 shadow-sm px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                        required>
                    @error('market_interest_amount')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Total Payout Amount -->
                <div class="bg-green-50 p-4 rounded-lg shadow-inner">
                    <label class="block text-sm font-semibold text-gray-700 mb-1 required">
                        {{ trans('cruds.monthlyPayoutRecord.fields.total_payout_amount') }}
                    </label>
                    <input type="number" step="0.01" name="total_payout_amount" id="total_payout_amount"
                        value="{{ old('total_payout_amount') }}"
                        class="w-full rounded-lg border-gray-300 shadow-sm px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                        required>
                    @error('total_payout_amount')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Month For -->
                <div class="bg-purple-50 p-4 rounded-lg shadow-inner">
                    <label class="block text-sm font-semibold text-gray-700 mb-1 required">
                        {{ trans('cruds.monthlyPayoutRecord.fields.month_for') }}
                    </label>
                    <input type="text" name="month_for" id="month_for"
                        value="{{ old('month_for') }}"
                        class="w-full rounded-lg border-gray-300 shadow-sm px-3 py-2 text-sm date focus:border-indigo-500 focus:ring-indigo-500"
                        required>
                    @error('month_for')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div class="bg-blue-50 p-4 rounded-lg shadow-inner">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        {{ trans('cruds.monthlyPayoutRecord.fields.status') }}
                    </label>
                    <select name="status"
                        class="w-full rounded-lg border-gray-300 shadow-sm px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value disabled selected>{{ trans('global.pleaseSelect') }}</option>

                        @foreach(App\Models\MonthlyPayoutRecord::STATUS_SELECT as $key => $label)
                            <option value="{{ $key }}"
                                {{ old('status', 'pending') == $key ? 'selected' : '' }}>
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

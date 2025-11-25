@extends('layouts.admin')
@section('content')

<div class="content">
    <div class="p-6 max-w-4xl mx-auto">

        <!-- Back Button -->
        <div class="mb-4">
            <a href="{{ route('admin.monthly-payout-records.index') }}" 
               class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                {{ trans('global.back_to_list') }}
            </a>
        </div>

        <!-- Monthly Payout Record Card -->
        <div class="bg-white shadow-lg rounded-xl p-6 text-sm">
            <h2 class="text-2xl font-bold mb-6 text-blue-600">
                {{ trans('global.show') }} {{ trans('cruds.monthlyPayoutRecord.title') }}
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- ID -->
                <div class="bg-blue-50 p-4 rounded-lg shadow-inner">
                    <span class="text-gray-500 font-semibold block mb-1">
                        {{ trans('cruds.monthlyPayoutRecord.fields.id') }}
                    </span>
                    <span class="text-gray-800 font-medium">{{ $monthlyPayoutRecord->id }}</span>
                </div>

                <!-- Investment -->
                <div class="bg-green-50 p-4 rounded-lg shadow-inner">
                    <span class="text-gray-500 font-semibold block mb-1">
                        {{ trans('cruds.monthlyPayoutRecord.fields.investment') }}
                    </span>
                    <span class="text-gray-800 font-medium">
                        {{ $monthlyPayoutRecord->investment->principal_amount ?? '' }}
                    </span>
                </div>

                <!-- Investor -->
                <div class="bg-yellow-50 p-4 rounded-lg shadow-inner">
                    <span class="text-gray-500 font-semibold block mb-1">
                        {{ trans('cruds.monthlyPayoutRecord.fields.investor') }}
                    </span>
                    <span class="text-gray-800 font-medium">
                        {{ $monthlyPayoutRecord->investor->reg ?? '' }}
                    </span>
                </div>

                <!-- Secure Interest Amount -->
                <div class="bg-purple-50 p-4 rounded-lg shadow-inner">
                    <span class="text-gray-500 font-semibold block mb-1">
                        {{ trans('cruds.monthlyPayoutRecord.fields.secure_interest_amount') }}
                    </span>
                    <span class="text-gray-800 font-medium">
                        {{ $monthlyPayoutRecord->secure_interest_amount }}
                    </span>
                </div>

                <!-- Market Interest Amount -->
                <div class="bg-pink-50 p-4 rounded-lg shadow-inner">
                    <span class="text-gray-500 font-semibold block mb-1">
                        {{ trans('cruds.monthlyPayoutRecord.fields.market_interest_amount') }}
                    </span>
                    <span class="text-gray-800 font-medium">
                        {{ $monthlyPayoutRecord->market_interest_amount }}
                    </span>
                </div>

                <!-- Total Payout Amount -->
                <div class="bg-indigo-50 p-4 rounded-lg shadow-inner">
                    <span class="text-gray-500 font-semibold block mb-1">
                        {{ trans('cruds.monthlyPayoutRecord.fields.total_payout_amount') }}
                    </span>
                    <span class="text-gray-800 font-medium">
                        {{ $monthlyPayoutRecord->total_payout_amount }}
                    </span>
                </div>

                <!-- Month For -->
                <div class="bg-red-50 p-4 rounded-lg shadow-inner">
                    <span class="text-gray-500 font-semibold block mb-1">
                        {{ trans('cruds.monthlyPayoutRecord.fields.month_for') }}
                    </span>
                    <span class="text-gray-800 font-medium">
                        {{ $monthlyPayoutRecord->month_for }}
                    </span>
                </div>

                <!-- Status -->
                <div class="bg-teal-50 p-4 rounded-lg shadow-inner">
                    <span class="text-gray-500 font-semibold block mb-1">
                        {{ trans('cruds.monthlyPayoutRecord.fields.status') }}
                    </span>
                    <span class="text-gray-800 font-medium">
                        {{ App\Models\MonthlyPayoutRecord::STATUS_SELECT[$monthlyPayoutRecord->status] ?? '' }}
                    </span>
                </div>

            </div>

            <!-- Back Button Bottom -->
            <div class="mt-6">
                <a href="{{ route('admin.monthly-payout-records.index') }}" 
                   class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>

        </div>
    </div>
</div>

@endsection

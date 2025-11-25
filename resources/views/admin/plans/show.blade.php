@extends('layouts.admin')
@section('content')
<div class="content">

    <div class="p-6 max-w-4xl mx-auto">

        <!-- Back Button -->
        <div class="mb-4">
            <a href="{{ route('admin.plans.index') }}" 
               class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                {{ trans('global.back_to_list') }}
            </a>
        </div>

        <!-- Plan Details Card -->
        <div class="bg-white shadow-lg rounded-xl p-6 text-sm">
            <h2 class="text-2xl font-bold mb-6 text-blue-600">
                {{ trans('global.show') }} {{ trans('cruds.plan.title') }}
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- ID -->
                <div class="bg-blue-50 p-4 rounded-lg shadow-inner">
                    <span class="text-gray-500 font-semibold block mb-1">
                        {{ trans('cruds.plan.fields.id') }}
                    </span>
                    <span class="text-gray-800 font-medium">{{ $plan->id }}</span>
                </div>

                <!-- Plan Name -->
                <div class="bg-green-50 p-4 rounded-lg shadow-inner">
                    <span class="text-gray-500 font-semibold block mb-1">
                        {{ trans('cruds.plan.fields.plan_name') }}
                    </span>
                    <span class="text-gray-800 font-medium">{{ $plan->plan_name }}</span>
                </div>

                <!-- Secure Interest % -->
                <div class="bg-yellow-50 p-4 rounded-lg shadow-inner">
                    <span class="text-gray-500 font-semibold block mb-1">
                        {{ trans('cruds.plan.fields.secure_interest_percent') }}
                    </span>
                    <span class="text-gray-800 font-medium">{{ $plan->secure_interest_percent }}</span>
                </div>

                <!-- Market Interest % -->
                <div class="bg-purple-50 p-4 rounded-lg shadow-inner">
                    <span class="text-gray-500 font-semibold block mb-1">
                        {{ trans('cruds.plan.fields.market_interest_percent') }}
                    </span>
                    <span class="text-gray-800 font-medium">{{ $plan->market_interest_percent }}</span>
                </div>

                <!-- Total Interest % -->
                <div class="bg-pink-50 p-4 rounded-lg shadow-inner">
                    <span class="text-gray-500 font-semibold block mb-1">
                        {{ trans('cruds.plan.fields.total_interest_percent') }}
                    </span>
                    <span class="text-gray-800 font-medium">{{ $plan->total_interest_percent }}</span>
                </div>

                <!-- Payout Frequency -->
                <div class="bg-indigo-50 p-4 rounded-lg shadow-inner">
                    <span class="text-gray-500 font-semibold block mb-1">
                        {{ trans('cruds.plan.fields.payout_frequency') }}
                    </span>
                    <span class="text-gray-800 font-medium">
                        {{ App\Models\Plan::PAYOUT_FREQUENCY_SELECT[$plan->payout_frequency] ?? '' }}
                    </span>
                </div>

                <!-- Min Invest Amount -->
                <div class="bg-red-50 p-4 rounded-lg shadow-inner">
                    <span class="text-gray-500 font-semibold block mb-1">
                        {{ trans('cruds.plan.fields.min_invest_amount') }}
                    </span>
                    <span class="text-gray-800 font-medium">{{ $plan->min_invest_amount }}</span>
                </div>

                <!-- Max Invest Amount -->
                <div class="bg-teal-50 p-4 rounded-lg shadow-inner">
                    <span class="text-gray-500 font-semibold block mb-1">
                        {{ trans('cruds.plan.fields.max_invest_amount') }}
                    </span>
                    <span class="text-gray-800 font-medium">{{ $plan->max_invest_amount }}</span>
                </div>

                <!-- Lock-in Days -->
                <div class="bg-orange-50 p-4 rounded-lg shadow-inner">
                    <span class="text-gray-500 font-semibold block mb-1">
                        {{ trans('cruds.plan.fields.lockin_days') }}
                    </span>
                    <span class="text-gray-800 font-medium">{{ $plan->lockin_days }}</span>
                </div>

                <!-- Withdraw Processing Hours -->
                <div class="bg-blue-100 p-4 rounded-lg shadow-inner">
                    <span class="text-gray-500 font-semibold block mb-1">
                        {{ trans('cruds.plan.fields.withdraw_processing_hours') }}
                    </span>
                    <span class="text-gray-800 font-medium">{{ $plan->withdraw_processing_hours }}</span>
                </div>

                <!-- Status -->
                <div class="bg-lime-50 p-4 rounded-lg shadow-inner">
                    <span class="text-gray-500 font-semibold block mb-1">
                        {{ trans('cruds.plan.fields.status') }}
                    </span>
                    <span class="text-gray-800 font-medium">
                        {{ App\Models\Plan::STATUS_SELECT[$plan->status] ?? '' }}
                    </span>
                </div>

            </div>

            <!-- Bottom Back Button -->
            <div class="mt-6">
                <a href="{{ route('admin.plans.index') }}" 
                   class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>

    <!-- RELATED DATA (Tabs) -->
    <div class="max-w-4xl mx-auto mt-6">
        <div class="bg-white shadow-lg rounded-xl p-6">

            <h3 class="text-xl font-bold mb-4 text-blue-600">
                {{ trans('global.relatedData') }}
            </h3>

            <div class="border-b mb-4">
                <ul class="flex flex-wrap text-sm font-medium">
                    <li class="mr-4">
                        <a class="pb-2 border-b-2 border-blue-500 text-blue-600"
                           href="#select_plan_investments">
                            {{ trans('cruds.investment.title') }}
                        </a>
                    </li>
                </ul>
            </div>

            <div id="select_plan_investments">
                @includeIf('admin.plans.relationships.selectPlanInvestments', [
                    'investments' => $plan->selectPlanInvestments
                ])
            </div>

        </div>
    </div>

</div>
@endsection

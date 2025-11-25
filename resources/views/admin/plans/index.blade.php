@extends('layouts.admin')
@section('content')

<div class="max-w-7xl mx-auto py-6">
    <div class="bg-white shadow-lg rounded-2xl p-6">

        <!-- Header -->
        <div class="flex justify-between items-center mb-6 border-b pb-4">
            <h2 class="text-xl font-bold text-indigo-700 flex items-center gap-2">
                <i class="fas fa-coins text-indigo-600"></i>
                {{ trans('cruds.plan.title_singular') }} {{ trans('global.list') }}
            </h2>

            <div class="flex gap-2 items-center">
                @can('plan_create')
                    <!-- Add New -->
                    <a href="{{ route('admin.plans.create') }}"
                        class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg shadow">
                        <i class="fas fa-plus mr-1"></i> {{ trans('global.add') }} {{ trans('cruds.plan.title_singular') }}
                    </a>

                    <!-- CSV Import -->
                    <button 
                        data-toggle="modal" 
                        data-target="#csvImportModal"
                        class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-medium rounded-lg shadow">
                        <i class="fas fa-file-csv mr-1"></i> {{ trans('global.app_csvImport') }}
                    </button>

                    @include('csvImport.modal', [
                        'model' => 'Plan', 
                        'route' => 'admin.plans.parseCsvImport'
                    ])
                @endcan

                <!-- Search -->
                <input type="text" id="planSearch" placeholder="Search plans..."
                    class="px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-blue-200 focus:border-blue-500 text-sm w-64">
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 datatable-Plan">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 w-10"></th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-black uppercase">
                            {{ trans('cruds.plan.fields.id') }}
                        </th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-black uppercase">
                            {{ trans('cruds.plan.fields.plan_name') }}
                        </th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-black uppercase">
                            {{ trans('cruds.plan.fields.secure_interest_percent') }}
                        </th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-black uppercase">
                            {{ trans('cruds.plan.fields.market_interest_percent') }}
                        </th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-black uppercase ">
                            {{ trans('cruds.plan.fields.total_interest_percent') }}
                        </th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-black uppercase ">
                            {{ trans('cruds.plan.fields.payout_frequency') }}
                        </th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-black uppercase ">
                            {{ trans('cruds.plan.fields.min_invest_amount') }}
                        </th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-black uppercase ">
                            {{ trans('cruds.plan.fields.max_invest_amount') }}
                        </th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-black uppercase ">
                            {{ trans('cruds.plan.fields.lockin_days') }}
                        </th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-black uppercase ">
                            {{ trans('cruds.plan.fields.withdraw_processing_hours') }}
                        </th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-black uppercase ">
                            {{ trans('cruds.plan.fields.status') }}
                        </th>
                        <th class="px-4 py-3 text-center text-sm font-medium text-black uppercase">
                            {{ trans('global.actions') }}
                        </th>
                    </tr>
                </thead>

                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($plans as $plan)
                        <tr data-entry-id="{{ $plan->id }}" class="hover:bg-gray-50">
                            <td class="px-4 py-3"></td>
                            <td class="px-4 py-3 text-s text-gray-700">{{ $plan->id }}</td>
                            <td class="px-4 py-3 text-s  text-gray-900">{{ $plan->plan_name }}</td>
                            <td class="px-4 py-3 text-s text-gray-700">{{ $plan->secure_interest_percent }}</td>
                            <td class="px-4 py-3 text-s text-gray-700">{{ $plan->market_interest_percent }}</td>
                            <td class="px-4 py-3 text-s text-gray-700">{{ $plan->total_interest_percent }}</td>
                            <td class="px-4 py-3 text-s text-gray-700">
                                {{ App\Models\Plan::PAYOUT_FREQUENCY_SELECT[$plan->payout_frequency] ?? '' }}
                            </td>
                            <td class="px-4 py-3 text-s text-gray-700">{{ $plan->min_invest_amount }}</td>
                            <td class="px-4 py-3 text-s text-gray-700">{{ $plan->max_invest_amount }}</td>
                            <td class="px-4 py-3 text-s text-gray-700">{{ $plan->lockin_days }}</td>
                            <td class="px-4 py-3 text-s text-gray-700">{{ $plan->withdraw_processing_hours }}</td>
                            <td class="px-4 py-3 text-s text-gray-700">
                                {{ App\Models\Plan::STATUS_SELECT[$plan->status] ?? '' }}
                            </td>

                            <td class="px-4 py-3 text-center space-x-1">
                                @can('plan_show')
                                    <a href="{{ route('admin.plans.show', $plan->id) }}"
                                       class="px-2 py-1 bg-blue-600 text-white rounded text-xs hover:bg-blue-700">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                @endcan

                                @can('plan_edit')
                                    <a href="{{ route('admin.plans.edit', $plan->id) }}"
                                       class="px-2 py-1 bg-indigo-600 text-white rounded text-xs hover:bg-indigo-700">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                @endcan

                                @can('plan_delete')
                                    <form action="{{ route('admin.plans.destroy', $plan->id) }}" method="POST"
                                        onsubmit="return confirm('{{ trans('global.areYouSure') }}');"
                                        class="inline-block">
                                        @method('DELETE')
                                        @csrf
                                        <button class="px-2 py-1 bg-red-600 text-white rounded text-xs hover:bg-red-700">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @endforeach
                </tbody>

            </table>
        </div>
    </div>
</div>

@endsection

@section('scripts')
@parent
<script>
    $(function () {
        let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)

        @can('plan_delete')
        let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
        let deleteButton = {
            text: deleteButtonTrans,
            url: "{{ route('admin.plans.massDestroy') }}",
            className: 'btn-danger',
            action: function (e, dt, node, config) {
                var ids = $.map(dt.rows({ selected: true }).nodes(), function (entry) {
                    return $(entry).data('entry-id')
                });

                if (ids.length === 0) {
                    alert('{{ trans('global.datatables.zero_selected') }}')
                    return
                }

                if (confirm('{{ trans('global.areYouSure') }}')) {
                    $.ajax({
                        headers: {'x-csrf-token': _token},
                        method: 'POST',
                        url: config.url,
                        data: { ids: ids, _method: 'DELETE' }
                    }).done(function () { location.reload() })
                }
            }
        }
        dtButtons.push(deleteButton)
        @endcan

        let table = $('.datatable-Plan').DataTable({
            buttons: dtButtons,
            orderCellsTop: true,
            order: [[1, 'desc']],
            pageLength: 25,
        });

        // Search filter
        $('#planSearch').on('keyup', function () {
            table.search(this.value).draw();
        });

        $('a[data-toggle="tab"]').on('shown.bs.tab click', function () {
            $($.fn.dataTable.tables(true)).DataTable().columns.adjust();
        });
    })
</script>
@endsection

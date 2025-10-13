@extends('layouts.admin')
@section('content')
@can('check_complain_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('admin.check-complains.create') }}">
                {{ trans('global.add') }} {{ trans('cruds.checkComplain.title_singular') }}
            </a>
            <button class="btn btn-warning" data-toggle="modal" data-target="#csvImportModal">
                {{ trans('global.app_csvImport') }}
            </button>
            @include('csvImport.modal', ['model' => 'CheckComplain', 'route' => 'admin.check-complains.parseCsvImport'])
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        {{ trans('cruds.checkComplain.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        @include('watermark')
        <table class="table table-bordered table-striped table-hover ajaxTable datatable datatable-CheckComplain">
            <thead>
                <tr>
                    <th width="10"></th>
                    <th>{{ trans('cruds.checkComplain.fields.id') }}</th>
                    <th>{{ trans('cruds.checkComplain.fields.select_complain') }}</th>
                    <th>{{ trans('cruds.checkComplain.fields.ticket_number') }}</th>
                    <th>Name</th>
                    @php
                        $isCustomer = auth()->user()->roles->contains(function ($role) {
                            return in_array($role->title, ['Customer', 'Admin']);
                        });
                    @endphp
                    @if($isCustomer)
                    <th>{{ trans('cruds.checkComplain.fields.vehicle_no') }}</th>
                    <th>{{ trans('cruds.checkComplain.fields.customer_name') }}</th>
                    <th>{{ trans('cruds.checkComplain.fields.phone_number') }}</th>
                    @endif

                    <th>{{ trans('cruds.checkComplain.fields.status') }}</th>
                    <th>{{ trans('cruds.checkComplain.fields.attechment') }}</th>
                    <th>{{ trans('cruds.checkComplain.fields.admin_message') }}</th>

                    <th>Status Duration</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
        </table>

    </div>
</div>



@endsection
@section('scripts')
@parent
<script>
    $(function () {
        let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
        @can('check_complain_delete')
        let deleteButtonTrans = '{{ trans('global.datatables.delete') }}';
        let deleteButton = {
            text: deleteButtonTrans,
            url: "{{ route('admin.check-complains.massDestroy') }}",
            className: 'btn-danger',
            action: function (e, dt, node, config) {
                var ids = $.map(dt.rows({ selected: true }).data(), function (entry) {
                    return entry.id
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
                    })
                    .done(function () { location.reload() })
                }
            }
        }
        dtButtons.push(deleteButton)
        @endcan

        let dtOverrideGlobals = {
            buttons: dtButtons,
            processing: true,
            serverSide: true,
            retrieve: true,
            aaSorting: [],
            ajax: "{{ route('admin.check-complains.index') }}",
            columns: [
                { data: 'placeholder', name: 'placeholder' },
                { data: 'id', name: 'id' },
                { data: 'select_complain', name: 'select_complains.title' },
                { data: 'ticket_number', name: 'ticket_number' },
                { data: 'created_by_name', name: 'created_by.name' },
                    @php
                    $isCustomer = auth()->user()->roles->contains(function ($role) {
                    return in_array($role->title, ['Customer', 'Admin']);
                    });
                    @endphp
                    @if ($isCustomer)
                        { data: 'vehicle_no', name: 'vehicle_no' },
                        { data: 'customer_name', name: 'customer_name' },
                        { data: 'phone_number', name: 'phone_number' },
                    @endif
                

                { data: 'status', name: 'status' },
                { data: 'attechment', name: 'attechment', sortable: false, searchable: false },
                { data: 'admin_message', name: 'admin_message' },   // NEW

                { data: 'status_duration', name: 'status_duration', sortable: false, searchable: false }, // NEW
                { data: 'actions', name: '{{ trans('global.actions') }}' }
            ],
            orderCellsTop: true,
            order: [[1, 'desc']],
            pageLength: 100,
        };

        let table = $('.datatable-CheckComplain').DataTable(dtOverrideGlobals);

        $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
            $($.fn.dataTable.tables(true)).DataTable()
                .columns.adjust();
        });

    });
</script>
<style>
    .blink {
        animation: blinker 1s linear infinite;
        font-weight: bold;
    }

    @keyframes blinker {
        50% {
            opacity: 0;
        }
    }
</style>

@endsection

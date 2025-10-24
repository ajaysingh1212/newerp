@extends('layouts.admin')
@section('content')

@can('recharge_request_create')
<div style="margin-bottom: 10px;" class="row">
    <div class="col-lg-12">
        <a class="btn btn-success" href="{{ route('admin.recharge-requests.create') }}">
            {{ trans('global.add') }} {{ trans('cruds.rechargeRequest.title_singular') }}
        </a>
        <button class="btn btn-warning" data-toggle="modal" data-target="#csvImportModal">
            {{ trans('global.app_csvImport') }}
        </button>
        @include('csvImport.modal', ['model' => 'RechargeRequest', 'route' => 'admin.recharge-requests.parseCsvImport'])
    </div>
</div>
@endcan

<div class="card">
    <div class="card-header">
        {{ trans('cruds.rechargeRequest.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">

        <!-- FILTERS -->
        <form method="GET" action="{{ route('admin.recharge-requests.index') }}" class="mb-3 row">
            <div class="col-md-3">
                <select name="date_filter" class="form-control">
                    <option value="">-- Select Period --</option>
                    <option value="today" {{ request('date_filter')=='today'?'selected':'' }}>Today</option>
                    <option value="yesterday" {{ request('date_filter')=='yesterday'?'selected':'' }}>Yesterday</option>
                    <option value="this_week" {{ request('date_filter')=='this_week'?'selected':'' }}>This Week</option>
                    <option value="this_month" {{ request('date_filter')=='this_month'?'selected':'' }}>This Month</option>
                    <option value="last_3_months" {{ request('date_filter')=='last_3_months'?'selected':'' }}>Last 3 Months</option>
                    <option value="last_6_months" {{ request('date_filter')=='last_6_months'?'selected':'' }}>Last 6 Months</option>
                    <option value="last_1_year" {{ request('date_filter')=='last_1_year'?'selected':'' }}>Last 1 Year</option>
                </select>
            </div>
            <div class="col-md-3">
                <input type="date" name="from_date" value="{{ request('from_date') }}" class="form-control" placeholder="From Date">
            </div>
            <div class="col-md-3">
                <input type="date" name="to_date" value="{{ request('to_date') }}" class="form-control" placeholder="To Date">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary">Find</button>
            </div>
        </form>

        <!-- TOTAL PLAN AMOUNT -->
        <div class="mb-3" style="padding:10px; background-color:#f8f9fa; border:1px solid #ddd;">
            {{-- @php
                dd($totalAmount);
            @endphp --}}
            <h5>Total Plan Amount: <strong>₹{{ number_format($totalAmount, 2) }}</strong></h5>
        </div>

        @include('watermark')

        <table class="table table-bordered table-striped table-hover datatable">
            <thead>
                <tr>
                    <th width="10"></th>
                    <th>{{ trans('cruds.rechargeRequest.fields.id') }}</th>
                    <th>{{ trans('cruds.rechargeRequest.fields.date') }}</th>
                    <th>{{ trans('cruds.rechargeRequest.fields.user') }}</th>
                    <th>{{ trans('cruds.rechargeRequest.fields.vehicle_number') }}</th>
                    <th>{{ trans('cruds.rechargeRequest.fields.select_recharge') }}</th>
                    <th>{{ trans('cruds.rechargePlan.fields.plan_name') }}</th>
                    <th>Plan Amount</th>
                    <th>{{ trans('cruds.rechargeRequest.fields.vehicle_status') }}</th>
                    <th>{{ trans('cruds.rechargeRequest.fields.payment_status') }}</th>
                    <th>{{ trans('cruds.rechargeRequest.fields.attechment') }}</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rechargeRequests as $request)
                <tr>
                    <td></td>
                    <td>{{ $request->id }}</td>
                    <td>{{ $request->created_at?->format('d-m-Y H:i') }}</td>
                    <td>{{ $request->user?->name ?? '' }} <br> {{ $request->user?->mobile_number ?? '' }}</td>
                    <td><span style="text-transform: uppercase;">{{ $request->vehicle_number }}</span></td>
                    <td>{{ $request->select_recharge?->type ?? '' }}</td>
                    <td>{{ $request->select_recharge?->plan_name ?? '' }}</td>
                    <td>₹{{ $request->payment_amount ?? 0 }}</td>
                    <td>{{ $request->vehicle_status }}</td>
                    <td>{{ ucfirst($request->payment_status) }}</td>
                    <td>
                        @if($request->attechment)
                        <a href="{{ $request->attechment->getUrl() }}" target="_blank">{{ trans('global.downloadFile') }}</a>
                        @endif
                    </td>
                    <td>
                        @can('recharge_request_show')
                        <a class="btn btn-xs btn-primary" href="{{ route('admin.recharge-requests.show', $request->id) }}">
                            {{ trans('global.view') }}
                        </a>
                        @endcan
                        @can('recharge_request_edit')
                        <a class="btn btn-xs btn-info" href="{{ route('admin.recharge-requests.edit', $request->id) }}">
                            {{ trans('global.edit') }}
                        </a>
                        @endcan
                        @can('recharge_request_delete')
                        <form action="{{ route('admin.recharge-requests.destroy', $request->id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <input type="submit" class="btn btn-xs btn-danger" value="{{ trans('global.delete') }}">
                        </form>
                        @endcan
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

    </div>

</div>

@endsection

@section('scripts')
@parent
<script>
    $(function () {
        $('.datatable').DataTable({
            pageLength: 100,
            order: [[1, 'desc']],
        });
    });
</script>
@endsection

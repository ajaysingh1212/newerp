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

<div class="card shadow-sm border-0">
    <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">{{ trans('cruds.checkComplain.title_singular') }} {{ trans('global.list') }}</h5>
    </div>

    <div class="card-body">
        @include('watermark')

        {{-- ✅ Filter Form --}}
        <form method="GET" action="{{ route('admin.check-complains.index') }}" class="mb-4">
            <div class="row align-items-end">
                <div class="col-md-3">
                    <label><strong>Quick Range</strong></label>
                    <select name="range" class="form-control">
                        <option value="">-- Select Range --</option>
                        <option value="today" {{ request('range') == 'today' ? 'selected' : '' }}>Today</option>
                        <option value="yesterday" {{ request('range') == 'yesterday' ? 'selected' : '' }}>Yesterday</option>
                        <option value="this_week" {{ request('range') == 'this_week' ? 'selected' : '' }}>This Week</option>
                        <option value="this_month" {{ request('range') == 'this_month' ? 'selected' : '' }}>This Month</option>
                        <option value="last_3_months" {{ request('range') == 'last_3_months' ? 'selected' : '' }}>Last 3 Months</option>
                        <option value="last_6_months" {{ request('range') == 'last_6_months' ? 'selected' : '' }}>Last 6 Months</option>
                        <option value="this_year" {{ request('range') == 'this_year' ? 'selected' : '' }}>This Year</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label><strong>From Date</strong></label>
                    <input type="date" name="from_date" value="{{ request('from_date') }}" class="form-control">
                </div>

                <div class="col-md-2">
                    <label><strong>To Date</strong></label>
                    <input type="date" name="to_date" value="{{ request('to_date') }}" class="form-control">
                </div>

                <div class="col-md-2">
                    <label><strong>Status</strong></label>
                    <select name="status" class="form-control">
                        <option value="">All</option>
                        <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                        <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
                        <option value="reject" {{ request('status') == 'reject' ? 'selected' : '' }}>Rejected</option>
                        <option value="solved" {{ request('status') == 'solved' ? 'selected' : '' }}>Solved</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary mt-3">Filter</button>
                    <a href="{{ route('admin.check-complains.index') }}" class="btn btn-secondary mt-3">Reset</a>
                </div>
            </div>
        </form>

        {{-- ✅ DataTable (No AJAX, Pure Blade Loop) --}}
        <table class="table table-bordered  table-hover datatable datatable-CheckComplain">
            <thead>
                <tr>
                    <th width="10"></th>
                    <th>ID</th>
                    <th>Complain</th>
                    <th>Ticket Number</th>
                    <th>Created By</th>
                    @php
                        $isCustomer = auth()->user()->roles->contains(function ($role) {
                            return in_array($role->title, ['Customer', 'Admin']);
                        });
                    @endphp
                    @if($isCustomer)
                        <th>Vehicle No</th>
                        <th>Customer Name</th>
                        <th>Phone Number</th>
                    @endif
                    <th>Status</th>
                    <th>Attachment</th>
                    <th>Admin Message</th>
                    <th>Status Duration</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($checkComplains as $row)
                    <tr>
                        <td></td>
                        <td>{{ $row->id }}</td>
                        <td class="space-x-1 space-y-1">
    <div class="flex flex-wrap gap-1">
        @foreach($row->select_complains as $comp)
            <span class="badge badge-info">
                {{ $comp->title }}
            </span>
        @endforeach
    </div>
</td>

                        <td>{{ $row->ticket_number }} <br>
                            {{ $row->created_at->format('d-m-Y H:i') }}
                        </td>
                        <td>
                            <strong>Name:</strong> {{ $row->created_by->name ?? '-' }}<br>
                            <strong>Mobile:</strong> {{ $row->created_by->mobile_number ?? '-' }}<br>
                             <strong>Role:</strong>
                                    {{ $row->created_by?->roles->pluck('title')->implode(', ') ?? '-' }}
                        </td>
                        @if($isCustomer)
                            <td>{{ $row->vehicle_no }}</td>
                            <td>{{ $row->customer_name }}</td>
                            <td>{{ $row->phone_number }}</td>
                        @endif
                        <td>
                            @if($row->status == 'Pending')
                                <span class="badge badge-danger">Pending</span>
                            @elseif($row->status == 'processing')
                                <span class="badge badge-warning">Processing</span>
                            @elseif($row->status == 'reject')
                                <span class="badge badge-secondary">Rejected</span>
                            @else
                                <span class="badge badge-success">Solved</span>
                            @endif
                           
                        </td>
                        <td>
                            @foreach($row->attechment as $media)
                                <a href="{{ $media->getUrl() }}" target="_blank">{{ trans('global.downloadFile') }}</a><br>
                            @endforeach
                        </td>
                        <td>{!! strip_tags($row->admin_message) ?? '-' !!}</td>
                        <td>
                            @php
                                $now = \Carbon\Carbon::now();
                                $created = $row->created_at;
                                $updated = $row->updated_at;
                                $status = $row->status;
                            @endphp

                            @if ($status === 'Pending')
                                <span class="text-danger blink">Pending since {{ $created->diffInDays($now) }} days</span>
                            @elseif ($status === 'processing')
                                <span class="text-danger blink">Processing since {{ $updated->diffInDays($now) }} days</span>
                            @elseif ($status === 'reject')
                                <span class="text-danger blink">Rejected {{ $updated->diffInDays($now) }} days ago</span>
                            @elseif ($status === 'solved')
                                <span class="text-success">Solved {{ $updated->diffInDays($now) }} days ago</span>
                            @else
                                -
                            @endif
                             {{ $row->updated_at ?? '' }}
                        </td>
                        <td>
                            @include('partials.datatablesActions', [
                                'viewGate' => 'check_complain_show',
                                'editGate' => 'check_complain_edit',
                                'deleteGate' => 'check_complain_delete',
                                'crudRoutePart' => 'check-complains',
                                'row' => $row
                            ])
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="13" class="text-center text-muted">No data found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
@parent
<script>
    $(function () {
        $('.datatable-CheckComplain').DataTable({
            order: [[1, 'desc']],
            pageLength: 50
        });
    });
</script>

<style>
    .blink {
        animation: blinker 1s linear infinite;
        font-weight: bold;
    }
    @keyframes blinker {
        50% { opacity: 0; }
    }
</style>
@endsection

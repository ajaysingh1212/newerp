@extends('layouts.admin')

@section('content')
<div class="container">
    <h1 class="mb-4">KYC Recharges</h1>

    <!-- 🔹 Filter Section -->
    <div class="card mb-3 shadow-sm">
        <div class="card-body">
            <form id="filterForm" method="GET" class="row g-3 align-items-end">
                <div class="col-md-2">
                    <label class="form-label">Quick Filter</label>
                    <select name="filter_type" id="filter_type" class="form-control">
                        <option value="">-- Select --</option>
                        <option value="today" {{ request('filter_type') == 'today' ? 'selected' : '' }}>Today</option>
                        <option value="yesterday" {{ request('filter_type') == 'yesterday' ? 'selected' : '' }}>Yesterday</option>
                        <option value="7_days" {{ request('filter_type') == '7_days' ? 'selected' : '' }}>Last 7 Days</option>
                        <option value="15_days" {{ request('filter_type') == '15_days' ? 'selected' : '' }}>Last 15 Days</option>
                        <option value="1_month" {{ request('filter_type') == '1_month' ? 'selected' : '' }}>Last 1 Month</option>
                        <option value="custom" {{ request('filter_type') == 'custom' ? 'selected' : '' }}>Custom Range</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">From Date</label>
                    <input type="date" name="from_date" id="from_date" class="form-control"
                        value="{{ request('from_date') }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label">To Date</label>
                    <input type="date" name="to_date" id="to_date" class="form-control"
                        value="{{ request('to_date') }}">
                </div>

                <div class="col-md-2">
                    <label class="form-label">Payment Status</label>
                    <select name="payment_status" id="payment_status" class="form-control">
                        <option value="">-- All --</option>
                        <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="completed" {{ request('payment_status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="failed" {{ request('payment_status') == 'failed' ? 'selected' : '' }}>Failed</option>
                        <option value="refunded" {{ request('payment_status') == 'refunded' ? 'selected' : '' }}>Refunded</option>
                    </select>
                </div>

                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary w-100">Apply</button>
                </div>

                <div class="col-md-1">
                    <a href="{{ route('admin.kyc-recharges.index') }}" class="btn btn-secondary w-100">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- 🔹 Filter Summary + Total Amount -->
    @if(request('filter_type'))
        <div class="alert alert-info py-2 mb-3">
            <strong>Filter:</strong> 
            {{ ucfirst(str_replace('_', ' ', request('filter_type'))) }}
            @if(request('filter_type') == 'custom' && request('from_date') && request('to_date'))
                ({{ request('from_date') }} to {{ request('to_date') }})
            @endif
            — Showing <strong>{{ $recharges->count() }}</strong> records,
            <strong>Total Amount:</strong> {{ number_format($totalAmount, 2) }}
        </div>
    @endif

    <!-- 🔹 Table -->
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>All Recharges</span>
            <a href="{{ route('admin.kyc-recharges.create') }}" class="btn btn-primary btn-sm">Create New</a>
        </div>
        <div class="card-body">
            <!-- Total Amount Above Table -->
            <div class="alert alert-success py-2 mb-3">
                <strong>Total Amount:</strong> {{ number_format($totalAmount, 2) }}
            </div>

            <table class="table table-bordered table-striped table-hover datatable datatable-KycRecharge">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Payment Date</th>
                        <th>User Details</th>
                        <th>Vehicle Number</th>
                        <th>Title</th>
                        <th>Payment Status</th>
                        <th>Vehicle Status</th>
                        <th>Description</th>
                        <th>Amount</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recharges as $recharge)
                    <tr>
                        <td>{{ $recharge->id }}</td>
                        <td>{{ $recharge->created_at ? $recharge->created_at->format('d-m-Y H:i') : '-' }}</td>
                        <td>
                            {{ $recharge->user->name ?? '-' }}<br>
                            {{ $recharge->user->email ?? '-' }}<br>
                            {{ $recharge->user->mobile_number ?? '-' }}
                        </td>
                        <td>{{ $recharge->vehicle_number }}</td>
                        <td>{{ $recharge->title }}</td>
                        <td>
                            <span class="badge bg-{{ $recharge->payment_status == 'completed' ? 'success' : ($recharge->payment_status == 'failed' ? 'danger' : 'warning') }}">
                                {{ ucfirst($recharge->payment_status) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-{{ $recharge->vehicle_status == 'live' ? 'info' : 'secondary' }}">
                                {{ ucfirst($recharge->vehicle_status) }}
                            </span>
                        </td>
                        <td>{{$recharge->description}}</td>
                        <td>{{ number_format($recharge->payment_amount, 2) }}</td>
                        <td>
                            @can('kyc_recharge_show')
                                <a href="{{ route('admin.kyc-recharges.show', $recharge->id) }}" class="btn btn-sm btn-info">View</a>
                            @endcan
                            @can('kyc_recharge_edit')
                                <a href="{{ route('admin.kyc-recharges.edit', $recharge->id) }}" class="btn btn-sm btn-warning">Edit</a>
                            @endcan
                            @can('kyc_recharge_delete')
                                <form action="{{ route('admin.kyc-recharges.destroy', $recharge->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="text-center">No KYC Recharges found.</td></tr>
                    @endforelse
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
    $('.datatable-KycRecharge').DataTable({
        order: [[0, 'desc']],
        pageLength: 25
    });

    function toggleDateInputs() {
        const filter = $('#filter_type').val();
        const fromDate = $('#from_date');
        const toDate = $('#to_date');

        if (!fromDate.length || !toDate.length) return;

        if (filter === 'custom') {
            fromDate.prop('disabled', false);
            toDate.prop('disabled', false);
        } else {
            fromDate.val('').prop('disabled', true);
            toDate.val('').prop('disabled', true);
        }
    }

    toggleDateInputs();
    $('#filter_type').on('change', toggleDateInputs);
});
</script>
@endsection

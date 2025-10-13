@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Stock History Records</h4>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Customer Name</th>
                            <th>Mobile Number</th>
                            <th>Email</th>
                            <th>State</th>
                            <th>District</th>
                            <th>Full Address</th>
                            <th>Request Date</th>
                            <th>Fitter Name</th>
                            <th>Vehicle Reg No</th>
                            <th>Product ID</th>
                            <th>action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stockHistories as $history)
                            <tr>
                                <td>{{ $history->id }}</td>
                                <td>{{ $history->customer_name }}</td>
                                <td>{{ $history->mobile_number }}</td>
                                <td>{{ $history->email }}</td>
                                <td>{{ $history->state }}</td>
                                <td>{{ $history->district }}</td>
                                <td>{{ $history->full_address }}</td>
                                <td>{{ $history->request_date }}</td>
                                <td>{{ $history->fitter_name }}</td>
                                <td>{{ $history->vehicle_reg_no }}</td>
                                <td>{{ $history->product_id }}</td>
                                <td>
                                    <a href="{{ route('admin.reports.stock-history.show', $history->id) }}" class="btn btn-primary btn-sm">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center">No stock history records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

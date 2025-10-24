@extends('layouts.admin')

@section('content')
<div class="container">
    <h1 class="mb-4">KYC Recharges</h1>

    <!-- List Table -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>All Recharges</span>
            <a href="{{ route('admin.kyc-recharges.create') }}" class="btn btn-primary btn-sm">Create New</a>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped table-hover datatable datatable-KycRecharge">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User ID</th>
                        <th>Vehicle Number</th>
                        <th>Title</th>
                        <th>Payment Status</th>
                        <th>Amount</th>
                        <th>Payment Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recharges as $recharge)
                    <tr>
                        <td>{{ $recharge->id }}</td>
                        <td>{{ $recharge->user->name }} <br> {{$recharge->user->email}} </td>
                        <td>{{ $recharge->vehicle_number }}</td>
                        <td>{{ $recharge->title }}</td>
                        <td>{{ ucfirst($recharge->payment_status) }}</td>
                        <td>{{ number_format($recharge->payment_amount, 2) }}</td>
                        <td>{{ $recharge->created_at ? \Carbon\Carbon::parse($recharge->created_at)->format('d-m-Y H:i') : '-' }}</td>
                        <td>
    @can('kyc_recharge_show')
        <a href="{{ route('admin.kyc-recharges.show', $recharge->id) }}" class="btn btn-sm btn-info">
            {{ __('View') }}
        </a>
    @endcan

    @can('kyc_recharge_edit')
        <a href="{{ route('admin.kyc-recharges.edit', $recharge->id) }}" class="btn btn-sm btn-warning">
            {{ __('Edit') }}
        </a>
    @endcan

    @can('kyc_recharge_delete')
        <form action="{{ route('admin.kyc-recharges.destroy', $recharge->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('{{ __('Are you sure you want to delete this recharge?') }}');">
            @csrf
            @method('DELETE')
            <button class="btn btn-sm btn-danger">{{ __('Delete') }}</button>
        </form>
    @endcan
</td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center">No KYC Recharges found.</td>
                    </tr>
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
            "order": [[ 0, "desc" ]],
            "pageLength": 25,
        });
    });
</script>
@endsection

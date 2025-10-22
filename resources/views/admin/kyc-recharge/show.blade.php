@extends('layouts.admin')

@section('content')
<div class="container">
    <h1 class="mb-4">KYC Recharge Details</h1>

    <div class="card">
        <div class="card-body">
            <p><strong>ID:</strong> {{ $recharge->id }}</p>
            <p><strong>User:</strong> {{ $recharge->user->name ?? 'N/A' }}</p>
            <p><strong>Vehicle Number:</strong> {{ $recharge->vehicle_number }}</p>
            <p><strong>Title:</strong> {{ $recharge->title }}</p>
            <p><strong>Description:</strong> {{ $recharge->description }}</p>
            <p><strong>Payment Status:</strong> 
                <span class="badge bg-{{ $recharge->payment_status == 'paid' ? 'success' : 'warning' }}">
                    {{ ucfirst($recharge->payment_status) }}
                </span>
            </p>
            <p><strong>Payment Method:</strong> {{ $recharge->payment_method ?? 'N/A' }}</p>
            <p><strong>Payment Amount:</strong> ₹{{ number_format($recharge->payment_amount, 2) }}</p>
            <p><strong>Payment Date:</strong> {{ $recharge->payment_date ? $recharge->payment_date->format('d M Y') : 'N/A' }}</p>
            <p><strong>Location:</strong> {{ $recharge->location ?? 'N/A' }}</p>
            <p><strong>Latitude:</strong> {{ $recharge->latitude ?? 'N/A' }}</p>
            <p><strong>Longitude:</strong> {{ $recharge->longitude ?? 'N/A' }}</p>

            @if($recharge->getFirstMediaUrl('kyc_recharge_images'))
                <div class="mt-3">
                    <strong>Captured Image:</strong><br>
                    <img src="{{ $recharge->getFirstMediaUrl('kyc_recharge_images') }}" 
                         alt="KYC Image" class="img-fluid rounded" style="max-width: 300px;">
                </div>
            @endif
        </div>

        <div class="card-footer">
            <a href="{{ route('admin.kyc-recharge.edit', $recharge->id) }}" class="btn btn-warning">Edit</a>
            <a href="{{ route('admin.kyc-recharge.index') }}" class="btn btn-secondary">Back to List</a>
        </div>
    </div>
</div>
@endsection

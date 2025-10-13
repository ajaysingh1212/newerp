@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">KYC Recharge Details</h1>

    <div class="card">
        <div class="card-body">
            <p><strong>ID:</strong> {{ $recharge->id }}</p>
            <p><strong>User ID:</strong> {{ $recharge->user_id }}</p>
            <p><strong>Vehicle Number:</strong> {{ $recharge->vehicle_number }}</p>
            <p><strong>Title:</strong> {{ $recharge->title }}</p>
            <p><strong>Description:</strong> {{ $recharge->description }}</p>
            <p><strong>Payment Status:</strong> {{ $recharge->payment_status }}</p>
            <p><strong>Payment Method:</strong> {{ $recharge->payment_method }}</p>
            <p><strong>Payment Amount:</strong> {{ $recharge->payment_amount }}</p>
            <p><strong>Payment Date:</strong> {{ $recharge->payment_date }}</p>
        </div>
        <div class="card-footer">
            <a href="{{ route('kyc-recharge.edit', $recharge->id) }}" class="btn btn-warning">Edit</a>
            <a href="{{ route('kyc-recharge.index') }}" class="btn btn-secondary">Back to List</a>
        </div>
    </div>
</div>
@endsection

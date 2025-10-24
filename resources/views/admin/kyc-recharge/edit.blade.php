@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">View Recharge Details</h1>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.kyc-recharge.update', $recharge->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label>Title</label>
                    <input type="text" name="title" class="form-control" 
                           value="{{ $recharge->title }}" readonly>
                </div>

                <div class="mb-3">
                    <label>Description</label>
                    <textarea name="description" class="form-control" readonly>{{ $recharge->description }}</textarea>
                </div>

                <div class="mb-3">
                    <label>Payment Status</label>
                    <select name="payment_status" class="form-control" disabled>
                        <option value="pending" {{ $recharge->payment_status == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="completed" {{ $recharge->payment_status == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="failed" {{ $recharge->payment_status == 'failed' ? 'selected' : '' }}>Failed</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label>Vehicle Status</label>
                    <select name="vehicle_status" class="form-control">
                        <option value="processing" {{ $recharge->vehicle_status == 'processing' ? 'selected' : '' }}>Processing</option>
                        <option value="live" {{ $recharge->vehicle_status == 'live' ? 'selected' : '' }}>Live</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label>Payment Method</label>
                    <input type="text" name="payment_method" class="form-control" 
                           value="{{ $recharge->payment_method }}" readonly>
                </div>

                <div class="mb-3">
                    <label>Payment Amount</label>
                    <input type="number" step="0.01" name="payment_amount" 
                           class="form-control" value="{{ $recharge->payment_amount }}" readonly>
                </div>

                <div class="mb-3">
                    <label>Payment Date</label>
                    <input type="datetime-local" name="payment_date" 
                           class="form-control" 
                           value="{{ \Carbon\Carbon::parse($recharge->payment_date)->format('Y-m-d\TH:i') }}" readonly>
                </div>

                <button class="btn btn-success">Update Vehicle Status</button>
            </form>
        </div>
    </div>
</div>
@endsection

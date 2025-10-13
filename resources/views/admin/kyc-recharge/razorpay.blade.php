@extends('layouts.admin')

@section('content')
<div class="container text-center">
    <h2>Pay for KYC Recharge</h2>
    <p>Amount: ₹{{ $recharge->payment_amount }}</p>

    <form action="{{ route('admin.kyc-recharges.payment-callback', $recharge->id) }}" method="POST">
        @csrf
        <script
            src="https://checkout.razorpay.com/v1/checkout.js"
            data-key="{{ env('RAZORPAY_KEY_ID') }}"
            data-amount="{{ $recharge->payment_amount * 100 }}"
            data-currency="INR"
            data-order_id="{{ $recharge->razorpay_order_id }}"
            data-buttontext="Pay Now"
            data-name="Your Company"
            data-description="KYC Recharge Payment"
            data-prefill.name="{{ Auth::user()->name }}"
            data-prefill.email="{{ Auth::user()->email }}"
            data-theme.color="#F37254">
        </script>
    </form>
</div>
@endsection

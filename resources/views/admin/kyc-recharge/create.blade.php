@extends('layouts.admin')

@section('content')
<div class="container">
    <h1 class="mb-4">Add New KYC Recharge</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Form for creating recharge -->
    <form id="kycRechargeForm" method="POST">
        @csrf

        <!-- Hidden user_id (logged-in user) -->
        <input type="hidden" name="user_id" value="{{ Auth::id() }}">
        <input type="hidden" name="created_by_id" value="{{ Auth::id() }}">

       <div class="mb-3">
    <label>Vehicle Number</label>
    <select name="vehicle_number" class="form-control select2" required>
        <option value="">-- Select Vehicle Number --</option>
        @foreach($vehicles as $vehicle)
            <option value="{{ $vehicle->vehicle_number }}">{{ $vehicle->vehicle_number }}</option>
        @endforeach
    </select>
</div>


        <div class="mb-3">
            <label>Title</label>
            <input type="text" name="title" class="form-control" placeholder="Enter Title" value="KYC Recharge" required readonly>
        </div>

        <div class="mb-3">
            <label>Description</label>
            <textarea name="description" class="form-control" placeholder="Enter Description"></textarea>
        </div>

        <div class="mb-3">
            <label>Payment Amount (INR)</label>
            <input type="number" step="0.01" name="payment_amount" id="payment_amount" class="form-control" placeholder="Enter Amount" value="299" readonly required>
        </div>

        <button type="button" class="btn btn-success" id="payButton">Create & Pay</button>
    </form>
</div>

@endsection

@section('scripts')
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
document.getElementById('payButton').onclick = function(e) {
    e.preventDefault();

    var amount = document.getElementById('payment_amount').value;
    if(!amount || amount <= 0){
        alert('Enter a valid amount');
        return;
    }

    // Send AJAX to store recharge in DB with pending status
    fetch("{{ route('admin.kyc-recharges.store') }}", {
        method: "POST",
        headers: {
            'X-CSRF-TOKEN': "{{ csrf_token() }}",
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            user_id: "{{ Auth::id() }}",
            created_by_id: "{{ Auth::id() }}",
            vehicle_number: document.querySelector('[name="vehicle_number"]').value,
            title: document.querySelector('[name="title"]').value,
            description: document.querySelector('[name="description"]').value,
            payment_amount: amount,
            payment_status: 'pending'
        })
    })
    .then(res => res.json())
    .then(data => {
        // Initialize Razorpay checkout
        var options = {
            "key": "{{ env('RAZORPAY_KEY_ID') }}",
            "amount": data.payment_amount * 100, // paise
            "currency": "INR",
            "name": "Your Company Name",
            "description": "KYC Recharge Payment",
            "order_id": data.razorpay_order_id, 
            "handler": function (response){
                // On successful payment, update recharge
                fetch("/admin/kyc-recharges/" + data.id + "/payment-callback", {
                    method: "POST",
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}",
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        razorpay_payment_id: response.razorpay_payment_id,
                        razorpay_order_id: response.razorpay_order_id,
                        razorpay_signature: response.razorpay_signature
                    })
                })
                .then(() => window.location.href = "{{ route('admin.kyc-recharges.index') }}");
            },
            "prefill": {
                "name": "{{ Auth::user()->name }}",
                "email": "{{ Auth::user()->email }}"
            },
            "theme": {
                "color": "#F37254"
            }
        };
        var rzp1 = new Razorpay(options);
        rzp1.open();
    })
    .catch(err => {
        console.log(err);
        alert('Something went wrong. Try again.');
    });
}
</script>
@endsection

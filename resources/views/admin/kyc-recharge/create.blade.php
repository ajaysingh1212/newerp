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

    <form id="kycRechargeForm" method="POST">
        @csrf

        <!-- Hidden user_id and created_by_id -->
        <input type="hidden" name="user_id" value="{{ Auth::id() }}">
        <input type="hidden" name="created_by_id" value="{{ Auth::id() }}">
        <input type="hidden" name="vehicle_number" id="vehicle_number"
               value="{{ $selectedVehicle->vehicle_number ?? '' }}">

        <!-- Vehicle Number Select -->
        <div class="mb-3">
            <label>Vehicle Number</label>
            <select name="vehicle_id" id="vehicle_id" class="form-control select2" required>
                <option value="">-- Select Vehicle Number --</option>
                @foreach($vehicles as $vehicle)
                    <option value="{{ $vehicle->id }}"
                            data-number="{{ $vehicle->vehicle_number }}"
                            {{ isset($selectedVehicle) && $selectedVehicle->id == $vehicle->id ? 'selected' : '' }}>
                        {{ $vehicle->vehicle_number }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Title -->
        <div class="mb-3">
            <label>Title</label>
            <input type="text" name="title" class="form-control" value="KYC Recharge" readonly required>
        </div>

        <!-- Description -->
        <div class="mb-3">
            <label>Description</label>
            <textarea name="description" class="form-control" placeholder="Enter Description"></textarea>
        </div>

        <!-- Payment Amount -->
        <div class="mb-3">
            <label>Payment Amount (INR)</label>
            <input type="number" step="0.01" name="payment_amount" id="payment_amount" 
                   class="form-control" value="299" readonly required>
        </div>

        <button type="button" class="btn btn-success" id="payButton">Create & Pay</button>
    </form>
</div>
@endsection

@section('scripts')
<!-- Razorpay Checkout -->
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>

<script>
$(document).ready(function() {
    const vehicleSelect = $('#vehicle_id');
    const vehicleNumberInput = $('#vehicle_number');

    // On page load, set hidden input if pre-selected
    if(vehicleSelect.val()){
        const selectedOption = vehicleSelect.find('option:selected');
        vehicleNumberInput.val(selectedOption.data('number'));
    }

    // Listen for Select2 change
    vehicleSelect.on('change', function(){
        const selectedOption = $(this).find('option:selected');
        vehicleNumberInput.val(selectedOption.data('number') || '');
    });

    $('#payButton').on('click', function(e) {
        e.preventDefault();

        const amount = $('#payment_amount').val();
        if(!amount || amount <= 0){
            alert('Enter a valid amount');
            return;
        }

        if(!vehicleNumberInput.val()){
            alert('Select a vehicle first');
            return;
        }

        // Store recharge with pending status
        fetch("{{ route('admin.kyc-recharges.store') }}", {
            method: "POST",
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}",
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                user_id: "{{ Auth::id() }}",
                created_by_id: "{{ Auth::id() }}",
                vehicle_number: vehicleNumberInput.val(),
                title: $('[name="title"]').val(),
                description: $('[name="description"]').val(),
                payment_amount: amount,
                payment_status: 'pending'
            })
        })
        .then(res => res.json())
        .then(data => {
            const options = {
                "key": "{{ env('RAZORPAY_KEY_ID') }}",
                "amount": data.payment_amount * 100,
                "currency": "INR",
                "name": "Your Company Name",
                "description": "KYC Recharge Payment",
                "order_id": data.razorpay_order_id, 
                "handler": function (response){
                    // Callback to update payment
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
                "theme": { "color": "#F37254" }
            };
            const rzp1 = new Razorpay(options);
            rzp1.open();
        })
        .catch(err => {
            console.error(err);
            alert('Something went wrong. Try again.');
        });
    });
});
</script>
@endsection

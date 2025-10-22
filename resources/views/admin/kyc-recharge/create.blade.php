@extends('layouts.admin')

@section('content')
<div class="container">
    <h1 class="mb-4">Add New KYC Recharge</h1>

    <div id="formErrors" class="alert alert-danger d-none">
        <ul></ul>
    </div>

    <form id="kycRechargeForm" method="POST" enctype="multipart/form-data">
        @csrf

        <input type="hidden" name="user_id" value="{{ Auth::id() }}">
        <input type="hidden" name="created_by_id" value="{{ Auth::id() }}">
        <input type="hidden" name="vehicle_number" id="vehicle_number" value="{{ $selectedVehicle->vehicle_number ?? '' }}">
        <input type="hidden" name="image_base64" id="captured_image">

        <!-- Vehicle Number -->
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

        <!-- Camera Capture -->
        <div class="mb-3">
            <label>Take Photo</label>
            <div class="border p-3 rounded text-center">
                <video id="camera" autoplay playsinline width="100%" height="250" class="rounded"></video>
                <canvas id="snapshot" class="d-none"></canvas>
                <br>
                <button type="button" id="captureBtn" class="btn btn-primary mt-2">Capture Photo</button>
            </div>
        </div>

        <!-- Location -->
        <div class="mb-3">
            <label>Location</label>
            <input type="text" name="location" id="location" class="form-control" placeholder="Auto Detecting Location...">
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label>Latitude</label>
                <input type="text" name="latitude" id="latitude" class="form-control" readonly>
            </div>
            <div class="col-md-6 mb-3">
                <label>Longitude</label>
                <input type="text" name="longitude" id="longitude" class="form-control" readonly>
            </div>
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

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBgRXfXiK8KHfSnKtunSIpGpKNmLNGNUzM&libraries=geometry"></script>

<script>
$(document).ready(function() {
    const vehicleSelect = $('#vehicle_id');
    const vehicleNumberInput = $('#vehicle_number');
    const formErrorsDiv = $('#formErrors');

    // Pre-select vehicle number
    if(vehicleSelect.val()){
        vehicleNumberInput.val(vehicleSelect.find('option:selected').data('number'));
    }

    vehicleSelect.on('change', function(){
        vehicleNumberInput.val($(this).find('option:selected').data('number') || '');
    });

    // Auto detect location
    if(navigator.geolocation){
        navigator.geolocation.getCurrentPosition(
            function(position){
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                $('#latitude').val(lat);
                $('#longitude').val(lng);

                const geocoder = new google.maps.Geocoder();
                geocoder.geocode({ location: { lat: lat, lng: lng } }, function(results, status){
                    if(status === "OK" && results[0]){
                        $('#location').val(results[0].formatted_address);
                    } else {
                        $('#location').val("Location not found");
                    }
                });
            },
            function(){
                $('#location').val("Location access denied");
            }
        );
    } else {
        $('#location').val("Geolocation not supported");
    }

    // Camera access
    const video = document.getElementById('camera');
    const canvas = document.getElementById('snapshot');
    const captureBtn = document.getElementById('captureBtn');
    const imageInput = document.getElementById('captured_image');

    navigator.mediaDevices.getUserMedia({ video: true })
        .then(stream => video.srcObject = stream)
        .catch(err => alert('Camera access denied or unavailable.'));

    captureBtn.addEventListener('click', () => {
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        canvas.getContext('2d').drawImage(video, 0, 0);
        imageInput.value = canvas.toDataURL('image/png'); // Base64
        alert('Photo captured successfully!');
    });

    // Razorpay payment
    $('#payButton').on('click', async function(e){
        e.preventDefault();
        formErrorsDiv.addClass('d-none').find('ul').html('');
        const formData = new FormData($('#kycRechargeForm')[0]);

        try {
            // Send POST request to Laravel
            const res = await fetch("{{ route('admin.kyc-recharges.store') }}", {
                method: "POST",
                headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
                body: formData
            });

            if(res.status === 422){ // validation error
                const data = await res.json();
                const ul = Object.values(data.error).flat().map(err => `<li>${err}</li>`).join('');
                formErrorsDiv.find('ul').html(ul).removeClass('d-none');
                return;
            }

            if(!res.ok){
                const text = await res.text();
                console.error('Server Response:', text);
                alert('Server error while creating payment.');
                return;
            }

            const data = await res.json();

            // Razorpay options
            const options = {
                key: "{{ env('RAZORPAY_KEY_ID') }}",
                amount: data.payment_amount * 100,
                currency: "INR",
                name: "Your Company Name",
                description: "KYC Recharge Payment",
                order_id: data.razorpay_order_id,
                handler: async function(response){
                    try {
                        // Payment callback
                        const callbackUrl = "{{ route('admin.kyc-recharges.payment-callback-json', ['id' => 'REPLACE_ID']) }}".replace('REPLACE_ID', data.id);
                        const callbackRes = await fetch(callbackUrl, {
                            method: "POST",
                            headers: {
                                'X-CSRF-TOKEN': "{{ csrf_token() }}",
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify(response)
                        });

                        if(!callbackRes.ok){
                            const text = await callbackRes.text();
                            console.error('Callback Response:', text);
                            alert('Payment callback error. Check console.');
                            return;
                        }

                        const result = await callbackRes.json();
                        if(result.success){
                            window.location.href = result.redirect;
                        } else {
                            alert('Payment callback failed.');
                        }

                    } catch(err){
                        console.error('Callback error:', err);
                        alert('Payment callback failed. See console.');
                    }
                },
                prefill: { 
                    name: "{{ addslashes(Auth::user()->name) }}", 
                    email: "{{ addslashes(Auth::user()->email) }}" 
                },
                theme: { color: "#F37254" }
            };

            const rzp1 = new Razorpay(options);
            rzp1.open();

        } catch(err){
            console.error('Payment creation error:', err);
            alert('Something went wrong. Check console.');
        }
    });
});
</script>
@endsection

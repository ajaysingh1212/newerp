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

    <form id="kycRechargeForm" method="POST" enctype="multipart/form-data">
        @csrf

        <input type="hidden" name="user_id" value="{{ Auth::id() }}">
        <input type="hidden" name="created_by_id" value="{{ Auth::id() }}">
        <input type="hidden" name="vehicle_number" id="vehicle_number"
               value="{{ $selectedVehicle->vehicle_number ?? '' }}">
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
@endsection

@section('scripts')
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBgRXfXiK8KHfSnKtunSIpGpKNmLNGNUzM&libraries=geometry"></script>

<script>
$(document).ready(function() {
    const vehicleSelect = $('#vehicle_id');
    const vehicleNumberInput = $('#vehicle_number');

    // Pre-select vehicle
    if(vehicleSelect.val()){
        const selectedOption = vehicleSelect.find('option:selected');
        vehicleNumberInput.val(selectedOption.data('number'));
    }

    // On vehicle change
    vehicleSelect.on('change', function(){
        const selectedOption = $(this).find('option:selected');
        vehicleNumberInput.val(selectedOption.data('number') || '');
    });

    // AUTO LOCATION
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function(position) {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;

                $('#latitude').val(lat);
                $('#longitude').val(lng);

                const geocoder = new google.maps.Geocoder();
                geocoder.geocode({ location: { lat: lat, lng: lng } }, function(results, status) {
                    if (status === "OK" && results[0]) {
                        $('#location').val(results[0].formatted_address);
                    } else {
                        $('#location').val("Location not found");
                    }
                });
            },
            function() {
                $('#location').val("Location access denied");
            }
        );
    } else {
        $('#location').val("Geolocation not supported");
    }

    // CAMERA ACCESS
    const video = document.getElementById('camera');
    const canvas = document.getElementById('snapshot');
    const captureBtn = document.getElementById('captureBtn');
    const imageInput = document.getElementById('captured_image');

    navigator.mediaDevices.getUserMedia({ video: true })
        .then(stream => { video.srcObject = stream; })
        .catch(err => alert('Camera access denied or unavailable.'));

    captureBtn.addEventListener('click', () => {
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        canvas.getContext('2d').drawImage(video, 0, 0);
        imageInput.value = canvas.toDataURL('image/png'); // save base64
        alert('Photo captured successfully!');
    });

    // RAZORPAY PAYMENT
    $('#payButton').on('click', function(e) {
        e.preventDefault();
        const formData = new FormData($('#kycRechargeForm')[0]);

        fetch("{{ route('admin.kyc-recharges.store') }}", {
            method: "POST",
            headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if(data.error){ 
                alert(data.error); 
                return; 
            }

            const options = {
                "key": "{{ env('RAZORPAY_KEY_ID') }}",
                "amount": data.payment_amount * 100,
                "currency": "INR",
                "name": "Your Company Name",
                "description": "KYC Recharge Payment",
                "order_id": data.razorpay_order_id,
                "handler": function (response){
                    // ✅ Use fetch without parsing JSON from redirect
                    fetch("/admin/kyc-recharges/" + data.id + "/payment-callback-json", {
                        method: "POST",
                        headers: {
                            'X-CSRF-TOKEN': "{{ csrf_token() }}",
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(response)
                    })
                    .then(res => res.json())
                    .then(result => {
                        if(result.success){
                            window.location.href = result.redirect;
                        } else {
                            alert('Payment callback failed.');
                        }
                    })
                    .catch(err => alert('Payment callback error.'));
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
            alert('Something went wrong while creating payment.'); 
        });
    });
});
</script>

@endsection

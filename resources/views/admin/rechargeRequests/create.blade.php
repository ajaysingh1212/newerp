@extends('layouts.admin')

@section('content')
<style>
  /* Card with shadow and smooth hover effect */
  .vehicle-card {
    border: 1px solid #ddd;
    border-radius: 0.5rem;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    background: #fff;
    position: relative;
  }
  .vehicle-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 15px rgba(0,0,0,0.15);
  }

  .status-pending { border-left: 6px solid #adb5bd; }
  .status-active { border-left: 6px solid #4ade80; }
  .status-suspend { border-left: 6px solid #f87171; }

  .status-badge {
    position: absolute;
    top: 12px;
    right: 12px;
    padding: 0.25em 0.75em;
    border-radius: 12px;
    font-weight: 600;
    font-size: 0.8rem;
    color: #fff;
  }
  .status-pending .status-badge { background-color: #6c757d; }
  .status-active .status-badge { background-color: #22c55e; }
  .status-suspend .status-badge { background-color: #ef4444; }

  .vehicle-image { width: 100%; height: 150px; object-fit: cover; border-radius: 0.5rem 0.5rem 0 0; }

  .form-check-input { width: 1.8em; height: 1.8em; cursor: pointer; border-radius: 0.35rem; border: 2px solid #ccc; transition: border-color 0.3s, background-color 0.3s; }
  .form-check-input:checked { background-color: #4ade80; border-color: #4ade80; }
  .form-check-input:focus { box-shadow: 0 0 5px rgba(74, 222, 128, 0.6); }
  .form-check-label { user-select: none; font-weight: 500; cursor: pointer; margin-left: 0.5em; }

  .vehicle-card-body { padding: 1rem; }
</style>

@php
    $loggedInUser = auth()->user();
    $loggedInUserRole = $loggedInUser->roles->first()?->title ?? null;
    $user = auth()->user();
    $role = strtolower(optional($user->roles()->first())->title);

    $dealerCommission = $role === 'dealer' ? \App\Models\Commission::where('dealer_id', $user->id)->sum('dealer_commission') : 0;
    $distributorCommission = $role === 'distributer' ? \App\Models\Commission::where('distributor_id', $user->id)->sum('distributor_commission') : 0;
    $totalCommission = $dealerCommission + $distributorCommission;

    $redeemedAmount = \App\Models\RechargeRequest::where('created_by_id', $user->id)->sum('redeem_amount');
    $totalCommission = $totalCommission - $redeemedAmount;
@endphp

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('cruds.rechargeRequest.title_singular') }}
    </div>

    <div class="card-body">
        @include('watermark')
        <form method="POST" action="{{ route('admin.recharge-requests.store') }}" enctype="multipart/form-data" id="rechargeForm">
            @csrf

            {{-- User & Vehicle Selection --}}
            <div class="card px-3 mb-4">
                <h4 class="text-center mt-2 py-2 bg-1">Choose User and Vehicle</h4>
                <div class="row">

                    {{-- User --}}
                    <div class="form-group col-lg-6">
                        <label class="required" for="user_id">Select User</label>
                        @if($loggedInUserRole === 'Customer')
                            <input type="hidden" name="user_id" value="{{ $loggedInUser->id }}">
                            <input type="text" class="form-control" value="{{ $loggedInUser->name }}" readonly>
                        @else
                            <select class="form-control select2" name="user_id" id="user_id" required>
                                <option value="">Please select</option>
                                @foreach($userOptions as $id => $name)
                                    <option value="{{ $id }}" {{ old('user_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </select>
                        @endif
                    </div>

                    {{-- Vehicle --}}
                    <div class="form-group col-lg-6">
                        <label class="required" for="vehicle_id">Vehicle Number</label>
                        <select class="form-control select2" name="vehicle_id" id="vehicle_id" required>
                            <option value="">Please select</option>
                        </select>
                        <input type="hidden" name="vehicle_number" id="vehicle_number">
                        <input type="hidden" name="selected_vehicle_number" id="selected_vehicle_number">
                        <input type="hidden" name="selected_vehicle_id" id="selected_vehicle_id">
                    </div>
                </div>

                {{-- Vehicle Details Card --}}
                <div id="vehicle-details-card" class="card shadow-lg border-0 rounded-4 col-lg-4 p-0 mb-4" style="display: none;">
                    <div id="vehicle_photo_container" class="p-3"></div>
                    <div class="card-body pt-0">
                        <h4 class="card-title text-primary alert alert-success" id="detail_vehicle_number"></h4>
                        <div class="row">
                            <div class="col-lg-4"><p><strong>Model:</strong> <span id="detail_vehicle_model"></span></p></div>
                            <div class="col-lg-4"><p><strong>Type ID:</strong> <span id="detail_vehicle_type"></span></p></div>
                            <div class="col-lg-4"><p><strong>Status:</strong> <span id="detail_vehicle_activated"></span></p></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Recharge Plan --}}
            <div class="card px-3 mb-4">
                <h4 class="text-center mt-2 py-2 bg-1">Choose Recharge Plan</h4>
                <div class="row">
                    <div class="form-group col-lg-6">
                        <label class="required" for="recharge_plan_id">Select Recharge Plan</label>
                        <select class="form-control select2" name="recharge_plan_id" id="recharge_plan_id" required>
                            <option value="">-- Select a Plan --</option>
                            @foreach($select_recharges as $id => $label)
                                <option value="{{ $id }}" {{ old('recharge_plan_id') == $id ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>

                        {{-- Redeem --}}
                        @if(in_array($role, ['dealer', 'distributer']))
                            <div class="form-group mt-2">
                                <p>Total Available Commission: ₹<span id="total_commission">{{ number_format($totalCommission, 2) }}</span></p>
                                <label for="redeem_amount">Redeem Amount (₹)</label>
                                <input type="number" name="redeem_amount" id="redeem_amount" class="form-control" value="{{ old('redeem_amount', 0) }}" min="0" step="0.01">
                                <div id="redeem-error" class="text-danger mt-1"></div>
                            </div>
                        @else
                            <input type="hidden" name="redeem_amount" value="0">
                        @endif

                        <div class="form-group alert alert-info mt-2">
                            <h5>Final Amount to Pay: ₹<span id="final_amount_display">0.00</span></h5>
                            <input type="hidden" id="amount_in_paise" name="amount_in_paise" value="0">
                        </div>
                    </div>

                    {{-- Plan Details --}}
                    <div class="col-lg-6">
                        <div id="recharge-plan-details" class="card mt-3" style="display: none;">
                            <h4 class="text-center mt-2 py-2 bg-1">Recharge Plan Details</h4>
                            <div class="card-body" id="planDetailsContent"></div>

                            <input type="hidden" name="type" id="plan_type">
                            <input type="hidden" name="amc_duration" id="plan_amc_duration">
                            <input type="hidden" name="warranty_duration" id="plan_warranty_duration">
                            <input type="hidden" name="subscription_duration" id="plan_subscription_duration">
                            <input type="hidden" id="plan_price" name="price">
                            <textarea name="description" id="plan_description" class="d-none"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Additional Details --}}
            <div class="card px-3 mb-4">
                <h4 class="text-center mt-2 py-2 bg-1">Additional Details</h4>
                <div class="row">
                    <div class="form-group col-lg-12">
                        <label for="notes">Notes</label>
                        <textarea class="form-control ckeditor" name="notes" id="notes">{!! old('notes') !!}</textarea>
                    </div>

                    <div class="form-group col-lg-12">
                        <label for="attechment">Attachment</label>
                        <div class="needsclick dropzone" id="attechment-dropzone"></div>
                    </div>
                </div>
            </div>

            <input type="hidden" name="payment_status" id="payment_status" value="pending">

            <div class="form-group">
                <button id="rzp-button1" type="button" class="btn btn-primary">Pay Now</button>
            </div>
        </form>
    </div>
</div>

{{-- Razorpay & jQuery --}}
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function () {

    // 🚗 Vehicle data
    let vehiclesData = {}, vehicleImages = {};
    const role = "{{ $loggedInUserRole }}";
    const uid = "{{ $loggedInUser->id }}";

    // Load vehicles for Customers on page load
    if (role.toLowerCase() === 'customer') loadCustomerVehicles(uid);

    // User selection change
    $('#user_id').on('change', function () {
        const id = $(this).val();
        if (id) loadCustomerVehicles(id);
        else resetVehicleForm();
    });

    // Fetch vehicles for selected user
    function loadCustomerVehicles(userId) {
        $.get(`/admin/get-customer-vehicles/${userId}`, function (res) {
            vehiclesData = res.vehicles || {};
            vehicleImages = res.vehicleImages || {};
            $('#vehicle_id').empty().append('<option value="">Please select</option>');
            for (const id in vehiclesData) {
                const v = vehiclesData[id];
                $('#vehicle_id').append(`<option value="${v.id}" data-number="${v.vehicle_number}">${v.vehicle_number.toUpperCase()}</option>`);
            }
            resetVehicleForm();
        });
    }

    // Reset vehicle selection
    function resetVehicleForm() {
        $('#vehicle_number, #selected_vehicle_number, #selected_vehicle_id').val('');
        $('#vehicle-details-card').hide();
        $('#vehicle_photo_container').empty();
    }

    // Vehicle selection change
    $('#vehicle_id').on('change', function () {
        const vid = $(this).val();
        const vehicle = vehiclesData[vid];
        if (vehicle) {
            $('#vehicle_number').val(vehicle.vehicle_number);
            $('#selected_vehicle_number').val(vehicle.vehicle_number);
            $('#selected_vehicle_id').val(vid);
            $('#detail_vehicle_number').text(vehicle.vehicle_number);
            $('#detail_vehicle_model').text(vehicle.vehicle_model);
            $('#detail_vehicle_type').text(vehicle.select_vehicle_type_id);
            $('#detail_vehicle_activated').text(vehicle.activated ? 'Activated' : 'Not Activated');

            const img = vehicleImages[vid];
            $('#vehicle_photo_container').html(
                img ? `<img src="${img}" class="img-fluid w-100" style="max-height:300px;">` 
                    : '<p class="text-center mt-3">No photo available.</p>'
            );

            $('#vehicle-details-card').show();
        } else {
            resetVehicleForm();
        }
    });

    // Recharge plan selection
    $('#recharge_plan_id').on('change', function () {
        const planId = $(this).val();
        if (!planId) {
            $('#recharge-plan-details').hide();
            $('#plan_price').val(0);
            updateFinalAmount();
            return;
        }

        $.get(`/admin/recharge-plan-details/${planId}`, function (data) {
            let html = '<div class="row">';
            if (data.plan_name) html += `<div class="col-lg-6"><p><strong>Plan Name:</strong> ${data.plan_name}</p></div>`;
            if (data.type) html += `<div class="col-lg-6"><p><strong>Type:</strong> ${data.type}</p></div>`;
            if (data.amc_duration) html += `<div class="col-lg-6"><p><strong>AMC Duration:</strong> ${data.amc_duration}</p></div>`;
            if (data.warranty_duration) html += `<div class="col-lg-6"><p><strong>Warranty Duration:</strong> ${data.warranty_duration}</p></div>`;
            if (data.subscription_duration) html += `<div class="col-lg-6"><p><strong>Subscription Duration:</strong> ${data.subscription_duration}</p></div>`;
            if (data.price) html += `<div class="col-lg-6"><p><strong>Price:</strong> ₹${data.price}</p></div>`;
            if (data.description) html += `<div class="col-lg-12"><p><strong>Description:</strong> ${data.description}</p></div>`;
            html += '</div>';

            $('#planDetailsContent').html(html);
            $('#recharge-plan-details').show();

            // Hidden fields
            $('#plan_type').val(data.type || '');
            $('#plan_amc_duration').val(data.amc_duration || '');
            $('#plan_warranty_duration').val(data.warranty_duration || '');
            $('#plan_subscription_duration').val(data.subscription_duration || '');
            $('#plan_price').val(data.price || '');
            $('#plan_description').val(data.description || '');

            updateFinalAmount();
        });
    });

    // Redeem amount input
    $('#redeem_amount').on('input', updateFinalAmount);

    // Update final amount dynamically
    function updateFinalAmount() {
        const planPrice = parseFloat($('#plan_price').val() || 0);
        let redeemAmount = parseFloat($('#redeem_amount').val() || 0);
        const totalCommission = parseFloat($('#total_commission').text().replace(/,/g, '') || 0);

        if (redeemAmount > totalCommission) {
            redeemAmount = totalCommission;
            $('#redeem_amount').val(redeemAmount.toFixed(2));
            $('#redeem-error').text('Redeem amount cannot exceed your total commission');
        } else if (redeemAmount > planPrice) {
            redeemAmount = planPrice;
            $('#redeem_amount').val(redeemAmount.toFixed(2));
            $('#redeem-error').text('Redeem amount cannot exceed the plan price');
        } else {
            $('#redeem-error').text('');
        }

        const finalAmount = planPrice - redeemAmount;
        $('#final_amount_display').text(finalAmount.toFixed(2));
        $('#amount_in_paise').val(Math.round(finalAmount * 100));
    }

    // Pay button click
    $('#rzp-button1').on('click', function (e) {
        e.preventDefault();
        const form = document.getElementById('rechargeForm');
        const amount = parseInt($('#amount_in_paise').val() || 0);

        // Admin: skip payment gateway
        if (role.toLowerCase() === 'admin') {
            $('#payment_status').val('success');
            form.submit();
            return;
        }

        // Wallet payment if final amount is zero
        if (amount === 0) {
            $('#payment_status').val('success');
            const randomId = 'pay_' + Math.random().toString(36).substr(2, 10);
            const walletMethod = 'wallet';
            const mockFields = {
                razorpay_payment_id: randomId,
                razorpay_order_id: 'order_' + Math.random().toString(36).substr(2, 10),
                razorpay_signature: btoa(randomId + '|wallet')
            };
            for (const field in mockFields) {
                $('<input>').attr({type:'hidden', name:field, value:mockFields[field]}).appendTo(form);
            }
            $('<input>').attr({type:'hidden', name:'payment_method', value:walletMethod}).appendTo(form);
            form.submit();
            return;
        }

        // Razorpay payment for normal users
        const options = {
            key: "{{ config('services.razorpay.key') }}",
            amount: amount,
            currency: "INR",
            name: "EEMOTRACK INDIA",
            description: "Recharge Plan",
            handler: function (response) {
                ['razorpay_payment_id','razorpay_order_id','razorpay_signature'].forEach(f => {
                    $('<input>').attr({type:'hidden', name:f, value:response[f]}).appendTo(form);
                });
                form.submit();
            },
            prefill: {
                name: "{{ auth()->user()->name }}",
                email: "{{ auth()->user()->email }}"
            },
            theme: { color: "#528FF0" }
        };
        const rzp = new Razorpay(options);
        rzp.open();
    });

});
</script>

@endsection

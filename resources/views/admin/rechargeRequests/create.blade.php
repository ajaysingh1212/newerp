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

  /* Accent border for status */
  .status-pending {
    border-left: 6px solid #adb5bd; /* muted gray */
  }
  .status-active {
    border-left: 6px solid #4ade80; /* light green */
  }
  .status-suspend {
    border-left: 6px solid #f87171; /* light red */
  }

  /* Status badge */
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
  .status-pending .status-badge {
    background-color: #6c757d; /* gray */
  }
  .status-active .status-badge {
    background-color: #22c55e; /* green */
  }
  .status-suspend .status-badge {
    background-color: #ef4444; /* red */
  }

  /* Image styling */
  .vehicle-image {
    width: 100%;
    height: 150px;
    object-fit: cover;
    border-top-left-radius: 0.5rem;
    border-top-right-radius: 0.5rem;
  }

  /* Checkbox styling */
  .form-check-input {
    width: 1.8em;
    height: 1.8em;
    cursor: pointer;
    border-radius: 0.35rem;
    border: 2px solid #ccc;
    transition: border-color 0.3s ease, background-color 0.3s ease;
  }
  .form-check-input:checked {
    background-color: #4ade80; /* green */
    border-color: #4ade80;
  }
  .form-check-input:focus {
    box-shadow: 0 0 5px rgba(74, 222, 128, 0.6);
  }
  .form-check-label {
    user-select: none;
    font-weight: 500;
    cursor: pointer;
    margin-left: 0.5em;
  }

  /* Card body padding */
  .vehicle-card-body {
    padding: 1rem;
  }
</style>
                        @php
                        $loggedInUser = auth()->user();
                        $loggedInUserRole = $loggedInUser->roles->first()?->title ?? null;
                        $user = auth()->user();
                        $role = strtolower(optional($user->roles()->first())->title);

                        $dealerCommission = 0;
                        $distributorCommission = 0;

                        if ($role === 'dealer') {
                            $dealerCommission = \App\Models\Commission::where('dealer_id', $user->id)->sum('dealer_commission');
                        }

                        if ($role === 'distributer') {
                            $distributorCommission = \App\Models\Commission::where('distributor_id', $user->id)->sum('distributor_commission');
                        }

                        $totalCommission = $dealerCommission + $distributorCommission;

                        // Fetch total redeemed amount from recharge_requests table
                        $redeemedAmount = \App\Models\RechargeRequest::where('created_by_id', $user->id)->sum('redeem_amount');

                        // Calculate remaining commission
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

<div class="card px-3">
                <h4 class= "text-center mt-2 py-2 bg-1">Choose User and Vehicle</h4>
            <div class="row">


    {{-- User Selection --}}
    <div class="form-group col-lg-6">
      <div class="row">
        <div class="form-group col-lg-12">
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
    <div class="form-group col-lg-12">
        <label class="required" for="vehicle_id">Vehicle Number</label>
        <select class="form-control select2" name="vehicle_id" id="vehicle_id" required>
            <option value="">Please select</option>
        </select>
        <input type="hidden" name="vehicle_number" id="vehicle_number">
        <input type="hidden" name="selected_vehicle_number" id="selected_vehicle_number">
        <input type="hidden" name="selected_vehicle_id" id="selected_vehicle_id">
    </div>
    </div>
    </div>
    

    {{-- Vehicle Details --}}
    <div id="vehicle-details-card" class="card shadow-lg border-0 rounded-4 col-lg-4 p-0 mb-4" style="display: none;">
        <div id="vehicle_photo_container" class="p-3"></div>
        <div class="card-body pt-0">
            <h4 class="card-title text-primary alert alert-success" id="detail_vehicle_number"></h4>
            <div class="row">
                <div class="col-lg-4"><p><strong>Model:</strong><br> <span id="detail_vehicle_model"></span></p></div>
                <div class="col-lg-4"><p><strong>Type ID:</strong><br> <span id="detail_vehicle_type"></span></p></div>
                <div class="col-lg-4"><p><strong>Status:</strong><br> <span id="detail_vehicle_activated"></span></p></div>
            </div>
        </div>
    </div>

    </div>
    </div>

    
<div class="card px-3">
                <h4 class= "text-center mt-2 py-2 bg-1">Choose Recharge Plan</h4>
            <div class="row">


    {{-- Recharge Plan --}}
    <div class="form-group col-lg-6">
        <label class="required" for="recharge_plan_id">Select Recharge Plan</label>
        <select class="form-control select2" name="recharge_plan_id" id="recharge_plan_id" required>
            <option value="">-- Select a Plan --</option>
            @foreach($select_recharges as $id => $label)
                <option value="{{ $id }}" {{ old('recharge_plan_id') == $id ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>

         {{-- Redeem Section --}}
    @if(in_array($role, ['dealer', 'distributer']))
        <div class="form-group">
            <p>Total Available Commission: ₹<span id="total_commission">{{ number_format($totalCommission, 2) }}</span></p>
            <label for="redeem_amount">Redeem Amount (₹)</label>
            <input type="number" name="redeem_amount" id="redeem_amount" class="form-control" 
                   value="{{ old('redeem_amount', 0) }}" min="0" step="0.01">
            <div id="redeem-error" class="text-danger mt-1"></div>
        </div>
    @else
        <input type="hidden" name="redeem_amount" value="0">
    @endif

    {{-- Final Amount Display --}}
    <div class="form-group alert alert-info">
        <h5>Final Amount to Pay: ₹<span id="final_amount_display">0.00</span></h5>
        <input type="hidden" id="amount_in_paise" name="amount_in_paise" value="0">
    </div>

    </div>

    {{-- Plan Details --}}
    <div class="col-lg-6">
    <div id="recharge-plan-details" class="card mt-3" style="display: none;">
                <h4 class= "text-center mt-2 py-2 bg-1">Recharge Plan Details</h4>
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

   
    
  
<div class="card px-3">
                <h4 class= "text-center mt-2 py-2 bg-1">Additional Details</h4>
            <div class="row">
    {{-- Notes --}}
    <div class="form-group col-lg-12">
        <label for="notes">Notes</label>
        <textarea class="form-control ckeditor" name="notes" id="notes">{!! old('notes') !!}</textarea>
    </div>

    {{-- Attachment --}}
    <div class="form-group col-lg-12">
        <label for="attechment">Attachment</label>
        <div class="needsclick dropzone" id="attechment-dropzone"></div>
    </div>
    </div>
</div>


    {{-- Payment Status --}}
    <input type="hidden" name="payment_status" id="payment_status" value="pending">

    {{-- Pay Button --}}
    <div class="form-group">
        <button id="rzp-button1" type="button" class="btn btn-primary">Pay Now</button>
    </div>
</form>

{{-- Razorpay --}}
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    let vehiclesData = {}, vehicleImages = {};

    $(document).ready(function () {
        const role = "{{ $loggedInUserRole }}";
        const uid = "{{ $loggedInUser->id }}";

        if (role === 'Customer') loadCustomerVehicles(uid);

        $('#user_id').on('change', function () {
            let id = $(this).val();
            if (id) loadCustomerVehicles(id);
            else resetVehicleForm();
        });

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

        function resetVehicleForm() {
            $('#vehicle_number, #selected_vehicle_number, #selected_vehicle_id').val('');
            $('#vehicle-details-card').hide();
            $('#vehicle_photo_container').empty();
        }

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
                $('#vehicle_photo_container').html(img
                    ? `<img src="${img}" class="img-fluid w-100" style="max-height: 300px;">`
                    : '<p class="text-center mt-3">No photo available.</p>'
                );

                $('#vehicle-details-card').show();
            } else resetVehicleForm();
        });

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

                $('#plan_type').val(data.type || '');
                $('#plan_amc_duration').val(data.amc_duration || '');
                $('#plan_warranty_duration').val(data.warranty_duration || '');
                $('#plan_subscription_duration').val(data.subscription_duration || '');
                $('#plan_price').val(data.price || '');
                $('#plan_description').val(data.description || '');

                updateFinalAmount();
            });
        });

        $('#redeem_amount').on('input', function () {
            updateFinalAmount();
        });

        updateFinalAmount();
    });

    function updateFinalAmount() {
        const planPrice = parseFloat($('#plan_price').val()) || 0;
        let redeemAmount = parseFloat($('#redeem_amount').val()) || 0;
        const totalCommission = parseFloat($('#total_commission').text().replace(/,/g, '')) || 0;

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

    document.getElementById('rzp-button1').onclick = function (e) {
        e.preventDefault();

        const redeemAmount = parseFloat(document.getElementById('redeem_amount').value) || 0;
        const price = parseFloat(document.getElementById('plan_price').value) || 0;

        const amount = parseInt($('#amount_in_paise').val());
        const form = document.getElementById('rechargeForm');

        if (redeemAmount < 0 || redeemAmount > price) {
            alert('Invalid redeem amount.');
            return;
        }

        if (amount === 0) {
            document.getElementById('payment_status').value = 'success';

            const randomId = 'pay_' + Math.random().toString(36).substr(2, 10);
            const walletMethod = 'wallet';

            const mockFields = {
                razorpay_payment_id: randomId,
                razorpay_order_id: 'order_' + Math.random().toString(36).substr(2, 10),
                razorpay_signature: btoa(randomId + '|wallet')
            };

            for (const field in mockFields) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = field;
                input.value = mockFields[field];
                form.appendChild(input);
            }

            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = 'payment_method';
            methodInput.value = walletMethod;
            form.appendChild(methodInput);

            form.submit();
            return;
        }

        const options = {
            key: "{{ config('services.razorpay.key') }}",
            amount: amount,
            currency: "INR",
            name: "EEMOTRACK INDIA",
            description: "Recharge Payment",
            image: "{{ asset('images/logo.png') }}",
            handler: function (response) {
                document.getElementById('payment_status').value = 'success';

                ['razorpay_payment_id', 'razorpay_order_id', 'razorpay_signature'].forEach(field => {
                    if (response[field]) {
                        let input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = field;
                        input.value = response[field];
                        form.appendChild(input);
                    }
                });

                form.submit();
            },
            prefill: {
                name: "{{ auth()->user()->name ?? '' }}",
                email: "{{ auth()->user()->email ?? '' }}",
                contact: "{{ auth()->user()->phone ?? '' }}"
            },
            theme: {
                color: "#3399cc"
            }
        };

        const rzp1 = new Razorpay(options);

        rzp1.on('payment.failed', function (response) {
            alert("Payment failed: " + response.error.description);
            document.getElementById('payment_status').value = 'failed';
            form.submit();
        });

        rzp1.open();
    };
</script>


@endsection

@section('scripts')
<script>
    $(document).ready(function () {
  function SimpleUploadAdapter(editor) {
    editor.plugins.get('FileRepository').createUploadAdapter = function(loader) {
      return {
        upload: function() {
          return loader.file
            .then(function (file) {
              return new Promise(function(resolve, reject) {
                // Init request
                var xhr = new XMLHttpRequest();
                xhr.open('POST', '{{ route('admin.recharge-requests.storeCKEditorImages') }}', true);
                xhr.setRequestHeader('x-csrf-token', window._token);
                xhr.setRequestHeader('Accept', 'application/json');
                xhr.responseType = 'json';

                // Init listeners
                var genericErrorText = `Couldn't upload file: ${ file.name }.`;
                xhr.addEventListener('error', function() { reject(genericErrorText) });
                xhr.addEventListener('abort', function() { reject() });
                xhr.addEventListener('load', function() {
                  var response = xhr.response;

                  if (!response || xhr.status !== 201) {
                    return reject(response && response.message ? `${genericErrorText}\n${xhr.status} ${response.message}` : `${genericErrorText}\n ${xhr.status} ${xhr.statusText}`);
                  }

                  $('form').append('<input type="hidden" name="ck-media[]" value="' + response.id + '">');

                  resolve({ default: response.url });
                });

                if (xhr.upload) {
                  xhr.upload.addEventListener('progress', function(e) {
                    if (e.lengthComputable) {
                      loader.uploadTotal = e.total;
                      loader.uploaded = e.loaded;
                    }
                  });
                }

                // Send request
                var data = new FormData();
                data.append('upload', file);
                data.append('crud_id', '{{ $rechargeRequest->id ?? 0 }}');
                xhr.send(data);
              });
            })
        }
      };
    }
  }

  var allEditors = document.querySelectorAll('.ckeditor');
  for (var i = 0; i < allEditors.length; ++i) {
    ClassicEditor.create(
      allEditors[i], {
        extraPlugins: [SimpleUploadAdapter]
      }
    );
  }
});
</script>

<script>
    Dropzone.options.attechmentDropzone = {
    url: '{{ route('admin.recharge-requests.storeMedia') }}',
    maxFilesize: 20, // MB
    maxFiles: 1,
    addRemoveLinks: true,
    headers: {
      'X-CSRF-TOKEN': "{{ csrf_token() }}"
    },
    params: {
      size: 20
    },
    success: function (file, response) {
      $('form').find('input[name="attechment"]').remove()
      $('form').append('<input type="hidden" name="attechment" value="' + response.name + '">')
    },
    removedfile: function (file) {
      file.previewElement.remove()
      if (file.status !== 'error') {
        $('form').find('input[name="attechment"]').remove()
        this.options.maxFiles = this.options.maxFiles + 1
      }
    },
    init: function () {
@if(isset($rechargeRequest) && $rechargeRequest->attechment)
      var file = {!! json_encode($rechargeRequest->attechment) !!}
          this.options.addedfile.call(this, file)
      file.previewElement.classList.add('dz-complete')
      $('form').append('<input type="hidden" name="attechment" value="' + file.file_name + '">')
      this.options.maxFiles = this.options.maxFiles - 1
@endif
    },
     error: function (file, response) {
         if ($.type(response) === 'string') {
             var message = response //dropzone sends it's own error messages in string
         } else {
             var message = response.errors.file
         }
         file.previewElement.classList.add('dz-error')
         _ref = file.previewElement.querySelectorAll('[data-dz-errormessage]')
         _results = []
         for (_i = 0, _len = _ref.length; _i < _len; _i++) {
             node = _ref[_i]
             _results.push(node.textContent = message)
         }

         return _results
     }
}
</script>
@endsection
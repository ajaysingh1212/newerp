@extends('layouts.admin')
@section('content')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('cruds.activationRequest.title_singular') }}
    </div>


    <div class="card-body">
         @include('watermark')
        <form method="POST" action="{{ route("admin.activation-requests.store") }}" enctype="multipart/form-data" class="row">
            @csrf
            <!-- Party Type Dropdown -->
          <div class="card shadow-sm rounded">
    <div class="card-header text-white" style="background: linear-gradient(90deg, #515b66 0%, #c04de6 100%);">
        <h5 class="mb-0">Party & Activation Details</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <!-- Party Type -->
            <div class="form-group col-lg-3">
                <label for="party_type">Party Type</label>
                <select id="party_type" class="form-control select2" name="party_type_id">
                    @foreach($party_types as $id => $title)
                        <option value="{{ $id }}">{{ $title }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Select Party -->
            <div class="form-group col-lg-3">
                <label for="select_party">Select Party</label>
                <select id="select_party" class="form-control select2" name="select_party_id">
                    @foreach($select_parties as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Product -->
            <div class="form-group col-lg-3">
                <label class="required" for="product_id">{{ trans('cruds.activationRequest.fields.product') }}</label>
                <select class="form-control select2 {{ $errors->has('product') ? 'is-invalid' : '' }}" name="product_id" id="product_id" required>
                    <option value="">{{ trans('global.pleaseSelect') }}</option>
                    @foreach($select_products as $id => $entry)
                        <option value="{{ $id }}" {{ old('product_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('product'))
                    <span class="text-danger">{{ $errors->first('product') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.activationRequest.fields.product_helper') }}</span>
            </div>

            <!-- Select Customer -->
            <div class="form-group col-lg-3">
                <label for="select_user">Select Customer</label>
                <select id="select_user" class="form-control select2" name="customer_name">
                    <option value="">Select User</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        // When select_party is changed, check if party_type is 'Customer'
        $('#select_party').on('change', function () {
            const selectedPartyId = $(this).val();
            const selectedPartyType = $('#party_type option:selected').text().trim().toLowerCase();

            if (selectedPartyType === 'customer') {
                $('#select_user').val(selectedPartyId).trigger('change');
            } else {
                $('#select_user').val('').trigger('change');
            }
        });
    });
</script>

<!-- jQuery and Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />


<script>
$(document).ready(function() {
    // Initialize Select2
    $('.select2').select2({
        placeholder: "{{ trans('global.pleaseSelect') }}",
        allowClear: true
    });


    // Initialize variables
    const pleaseSelectText = "{{ trans('global.pleaseSelect') }}";
    const loadingText = "{{ trans('global.loading') }}";
   
    // Party Type Change Handler
    $('#party_type').on('change', function() {
        const roleId = $(this).val();
        const $partySelect = $('#select_party');
       
        if (roleId) {
            $partySelect.html(`<option value="">${loadingText}</option>`);
           
            $.ajax({
                url: "{{ route('admin.get.users.by.roles') }}",
                type: "GET",
                data: { role_id: roleId },
                success: function(data) {
                    $partySelect.html(`<option value="">${pleaseSelectText}</option>`);
                   
                    if (data.options && data.options.length > 0) {
                        data.options.forEach(function(user) {
                            $partySelect.append(
                                $(`<option></option>`).val(user.id).text(`${user.name} (${user.mobile_number})`)
                            );
                        });
                    }
                   
                    // Reset product dropdown when party type changes
                    $('#product_id').html(`<option value="">${pleaseSelectText}</option>`);
                },
                error: function(xhr) {
                    $partySelect.html(`<option value="">${pleaseSelectText}</option>`);
                    console.error('Error:', xhr.responseText);
                    alert('Error fetching parties. Please try again.');
                }
            });
        } else {
            $partySelect.html(`<option value="">${pleaseSelectText}</option>`);
            $('#product_id').html(`<option value="">${pleaseSelectText}</option>`);
        }
    });


    // Party Selection Change Handler
    $('#select_party').on('change', function() {
        const partyId = $(this).val();
        const $productSelect = $('#product_id');
       
        if (!partyId) {
            $productSelect.html(`<option value="">${pleaseSelectText}</option>`).trigger('change');
            return;
        }


        $productSelect.html(`<option value="">${loadingText}</option>`).trigger('change');
       
        $.ajax({
            url: '{{ route("admin.getPartyProducts") }}',
            type: 'GET',
            data: { user_id: partyId },
            success: function(response) {
                $productSelect.html(`<option value="">${pleaseSelectText}</option>`);
               
                if (response && response.length > 0) {
                    response.forEach(function(item) {
                        $productSelect.append(
                            $(`<option></option>`).val(item.id).text(item.text)
                        );
                    });
                } else {
                    alert('No products available for the selected party.');
                }
               
                $productSelect.trigger('change');
            },
            error: function(xhr) {
                $productSelect.html(`<option value="">${pleaseSelectText}</option>`).trigger('change');
                console.error('Error:', xhr.responseText);
                alert('Unable to fetch products for this party.');
            }
        });
    });
});
</script>
</div>      






<div class="row mt-5" id="user_detail_card" style="display: none;">
    <!-- Customer Information Card -->
    <div class="col-lg-6 ">
        <div class="card border-0 shadow rounded-4">
            <div class="card-header bg-gradient-primary text-white rounded-top-4 py-3" style="background: linear-gradient(90deg, #515b66 0%, #0056b3 100%);">
                <h5 class="mb-0"><i class="fas fa-user me-2"></i>Customer Information</h5>
            </div>
            <div class="card-body p-4">
                <table class="table table-striped table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 180px;">Mobile Number</th>
                            <th>District</th>
                            <th>Email</th>
                            <th>State</th>
                            <th>Full Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td id="mobile_number_text">-</td>
                            <td id="district_text">-</td>
                            <td id="email_text">-</td>
                            <td id="state_text">-</td>
                            <td id="full_address_text">-</td>

                            <!-- Hidden Inputs -->
                            <input type="hidden" name="mobile_number" id="mobile_number" value="">
                            <input type="hidden" name="email" id="email" value="">
                            <input type="hidden" name="address" id="full_address" value="">
                            <input type="hidden" name="state_id" id="state" value="">
                            <input type="hidden" name="disrict_id" id="district" value="">
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Vehicle Information Table -->
    <div class="col-lg-6">
        <div id="vehicle_detail_table_container" style="display:none;">
            <div class="card border-0 shadow rounded-4">
                <div class="card-header bg-gradient-success text-white rounded-top-4 py-3" style="background: linear-gradient(90deg, #515b66 0%, #0056b3 100%);">
                    <h5 class="mb-0"><i class="fas fa-car me-2"></i>Vehicle Information</h5>
                </div>
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered align-middle" id="vehicle_table">
                            <thead class="table-light">
                                <tr class="text-center">
                                    <th>Select</th>
                                    
                                    <th>Vehicle Number</th>
                                    <th>Owner Name</th>
                                    <th>Model</th>
                                    <th>Color</th>
                                    <th>Chassis Number</th>
                                    <th>Engine Number</th>
                                    
                                    <th>Vehicle Photo</th>
                                    <th>ID Proof(s)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Vehicle rows will be appended dynamically -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<!-- Form Inputs -->
 <div class="card shadow-sm rounded mt-4">
    <div class="card-header text-white" style="background: linear-gradient(90deg, #515b66 0%, #c04de6 100%);">
        <h5 class="mb-0">Vehicle Information</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <!-- Vehicle Model -->
            <div class="form-group col-lg-3">
                <label for="vehicle_model">Vehicle Model</label>
                <input type="text" id="vehicle_model" name="vehicle_model" class="form-control {{ $errors->has('vehicle_model') ? 'is-invalid' : '' }}">
                @if($errors->has('vehicle_model'))
                    <span class="text-danger">{{ $errors->first('vehicle_model') }}</span>
                @endif
                <span class="help-block">Enter the model of the vehicle.</span>
            </div>

            <!-- Vehicle Registration Number -->
            <div class="form-group col-lg-3">
                <label for="vehicle_reg_no">Vehicle Registration Number</label>
                <input type="text" id="vehicle_reg_no" name="vehicle_reg_no" class="form-control {{ $errors->has('vehicle_reg_no') ? 'is-invalid' : '' }}" style="text-transform: uppercase;">
                @if($errors->has('vehicle_reg_no'))
                    <span class="text-danger">{{ $errors->first('vehicle_reg_no') }}</span>
                @endif
                <span class="help-block">Enter the registration number of the vehicle.</span>
            </div>

            <!-- Chassis Number -->
            <div class="form-group col-lg-3">
                <label for="chassis_number">Chassis Number</label>
                <input type="text" id="chassis_number" name="chassis_number" class="form-control {{ $errors->has('chassis_number') ? 'is-invalid' : '' }}">
                @if($errors->has('chassis_number'))
                    <span class="text-danger">{{ $errors->first('chassis_number') }}</span>
                @endif
                <span class="help-block">Enter the chassis number of the vehicle.</span>
            </div>

            <!-- Vehicle Color -->
            <div class="form-group col-lg-3">
                <label for="vehicle_color">Vehicle Color</label>
                <input type="text" id="vehicle_color" name="vehicle_color" class="form-control {{ $errors->has('vehicle_color') ? 'is-invalid' : '' }}">
                @if($errors->has('vehicle_color'))
                    <span class="text-danger">{{ $errors->first('vehicle_color') }}</span>
                @endif
                <span class="help-block">Enter the color of the vehicle.</span>
            </div>

            <!-- Engine Number -->
            <div class="form-group col-lg-3">
                <label for="engine_number">Engine Number</label>
                <input type="text" id="engine_number" name="engine_number" class="form-control {{ $errors->has('engine_number') ? 'is-invalid' : '' }}">
                @if($errors->has('engine_number'))
                    <span class="text-danger">{{ $errors->first('engine_number') }}</span>
                @endif
                <span class="help-block">Enter the engine number of the vehicle.</span>
            </div>

            <!-- Vehicle Type -->
            <div class="form-group col-lg-3">
                <label for="select_vehicle_type_id">Vehicle Type</label>
                <input type="text" id="select_vehicle_type_id" name="vehicle_type_id" class="form-control {{ $errors->has('select_vehicle_type_id') ? 'is-invalid' : '' }}">
                @if($errors->has('select_vehicle_type_id'))
                    <span class="text-danger">{{ $errors->first('select_vehicle_type_id') }}</span>
                @endif
                <span class="help-block">Enter the type of the vehicle.</span>
            </div>
        </div>
    </div>









<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


<script>
$(document).ready(function () {
    $('#select_user').on('change', function () {
        let userId = $(this).val();

        // Reset form and vehicle table
        $('#vehicle_model, #vehicle_reg_no, #chassis_number, #vehicle_color, #engine_number, #select_vehicle_type_id, #id_proofs').val('');
        $('#id_proof_preview').empty();
        $('#vehicle_detail_table_container').hide();
        $('#vehicle_table tbody').empty();

        if (userId) {
            $.ajax({
                url: '{{ route("admin.get.user.details", ":id") }}'.replace(':id', userId),
                type: 'GET',
                success: function (response) {
                    $('#user_detail_card').show();

                    // Populate user info
                    $('#mobile_number_text').text(response.mobile_number || '-');
                    $('#mobile_number').val(response.mobile_number || '');
                    $('#email_text').text(response.email || '-');
                    $('#email').val(response.email || '');
                    $('#full_address_text').text(response.address || '-');
                    $('#full_address').val(response.address || '');
                    $('#state_text').text(response.state || '-');
                    $('#state').val(response.state || '');
                    $('#district_text').text(response.district || '-');
                    $('#district').val(response.district || '');

                    if (response.vehicles && response.vehicles.length > 0) {
                        $('#vehicle_detail_table_container').show();

                        response.vehicles.forEach(function (vehicle) {
                            // Check if vehicle is already activated
                            const isActive = vehicle.status === 'Activated' ? 'data-active="1"' : 'data-active="0"';

                            let vehiclePhotoHtml = vehicle.vehicle_photo
                                ? `<img src="${vehicle.vehicle_photo}" alt="Vehicle Photo" style="max-height: 80px;">`
                                : 'No photo';

                            let idProofImagesHtml = '';
                            if (vehicle.id_proofs && vehicle.id_proofs.length > 0) {
                                vehicle.id_proofs.forEach(function (proof) {
                                    idProofImagesHtml += `<img src="${proof.url}" alt="ID Proof" style="max-height: 80px; margin-right: 5px;">`;
                                });
                            } else {
                                idProofImagesHtml = 'No ID proofs';
                            }

                            let rowHtml = `
                                <tr>
                                    <td class="text-center">
                                        <input type="radio" name="select_vehicle" class="select-vehicle-radio" 
                                            data-vehicle='${JSON.stringify(vehicle)}' ${isActive}>
                                    </td>
                                    <td>${vehicle.vehicle_number || '-'}</td>
                                    <td>${vehicle.owners_name || '-'}</td>
                                    <td>${vehicle.vehicle_model || '-'}</td>
                                    <td>${vehicle.vehicle_color || '-'}</td>
                                    <td>${vehicle.chassis_number || '-'}</td>
                                    <td>${vehicle.engine_number || '-'}</td>
                                    <td>${vehiclePhotoHtml}</td>
                                    <td>${idProofImagesHtml}</td>
                                </tr>
                            `;
                            $('#vehicle_table tbody').append(rowHtml);
                        });
                    } else {
                        $('#vehicle_detail_table_container').hide();
                    }
                },
                error: function () {
                    alert('User data could not be retrieved.');
                    $('#user_detail_card').hide();
                    $('#vehicle_detail_table_container').hide();
                }
            });
        } else {
            $('#user_detail_card').hide();
            $('#vehicle_detail_table_container').hide();
            $('#mobile_number_text, #email_text, #full_address_text, #state_text, #district_text').text('-');
            $('#vehicle_table tbody').empty();
        }
    });

    // Vehicle selection handler
    $(document).on('change', '.select-vehicle-radio', function () {
        const vehicle = $(this).data('vehicle');
        const isActive = $(this).data('Activated') == 1;

        if (isActive) {
            alert('This vehicle is already activated.');
            $(this).prop('checked', false);
            return;
        }

        // Populate form fields if not activated
        $('#vehicle_model').val(vehicle.vehicle_model || '');
        $('#vehicle_reg_no').val(vehicle.vehicle_number || '');
        $('#chassis_number').val(vehicle.chassis_number || '');
        $('#vehicle_color').val(vehicle.vehicle_color || '');
        $('#engine_number').val(vehicle.engine_number || '');
        $('#select_vehicle_type_id').val(vehicle.select_vehicle_type_name || '');

        let idProofUrls = [];
        let idProofHtml = '';
        if (vehicle.id_proofs && vehicle.id_proofs.length > 0) {
            vehicle.id_proofs.forEach(function (proof) {
                idProofUrls.push(proof.url);
                idProofHtml += `<img src="${proof.url}" alt="ID Proof" style="max-height: 100px; margin-right: 5px;">`;
            });
            $('#id_proofs').val(idProofUrls.join(','));
            $('#id_proof_preview').html(idProofHtml);
        } else {
            $('#id_proofs').val('');
            $('#id_proof_preview').html('<span class="text-muted">No ID Proof Available</span>');
        }
    });
});

</script>
</div>
</div>




<div class="card shadow-sm rounded mt-4">
    <div class="card-header  text-white" style="background: linear-gradient(90deg, #515b66 0%, #c04de6 100%);">
        <h5 class="mb-0">Installation Details</h5>
    </div>
    <div class="card-body">
        <div class="row">
         <!-- Request Date -->
<div class="form-group col-lg-4">
    <label for="request_date">{{ trans('cruds.activationRequest.fields.request_date') }}</label>
    <input class="form-control date {{ $errors->has('request_date') ? 'is-invalid' : '' }}" type="text" name="request_date" id="request_date" value="">
    @if($errors->has('request_date'))
        <span class="text-danger">{{ $errors->first('request_date') }}</span>
    @endif
    <span class="help-block">{{ trans('cruds.activationRequest.fields.request_date_helper') }}</span>

    {{-- Hidden Fields for amc, warranty, subscription --}}
    <input type="hidden" name="amc" id="amc">
    <input type="hidden" name="warranty" id="warranty">
    <input type="hidden" name="subscription" id="subscription">
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const requestDateInput = document.getElementById('request_date');
        const amcInput = document.getElementById('amc');
        const warrantyInput = document.getElementById('warranty');
        const subscriptionInput = document.getElementById('subscription');

        function updateHiddenFields(value) {
            amcInput.value = value;
            warrantyInput.value = value;
            subscriptionInput.value = value;
        }

        // Update on input/change
        requestDateInput.addEventListener('input', function () {
            updateHiddenFields(this.value);
        });

        // Also update immediately on page load
        if (requestDateInput.value) {
            updateHiddenFields(requestDateInput.value);
        }
    });
</script>




            <!-- Fitter Name -->
            <div class="form-group col-lg-4">
                <label for="fitter_name">Fitter Name</label>
                <input class="form-control {{ $errors->has('fitter_name') ? 'is-invalid' : '' }}" type="text" name="fitter_name" id="fitter_name" value="">
                @if($errors->has('fitter_name'))
                    <span class="text-danger">{{ $errors->first('fitter_name') }}</span>
                @endif
            </div>

            <!-- Fitter Number -->
            <div class="form-group col-lg-4">
                <label for="fitter_number">Fitter Number</label>
                <input class="form-control {{ $errors->has('fitter_number') ? 'is-invalid' : '' }}" type="text" name="fitter_number" id="fitter_number" value="">
                @if($errors->has('fitter_number'))
                    <span class="text-danger">{{ $errors->first('fitter_number') }}</span>
                @endif
            </div>


        </div>
    </div>
</div>



           <div class="card shadow-sm rounded mt-4">
    <div class="card-header bg-info text-white">
        <h5 class="mb-0">Upload Documents & Photos</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <!-- Customer Image -->
            <div class="form-group col-lg-6">
                <label for="customer_image">{{ trans('cruds.activationRequest.fields.customer_image') }}</label>
                <div class="needsclick dropzone {{ $errors->has('customer_image') ? 'is-invalid' : '' }}" id="customer_image-dropzone"></div>
                @if($errors->has('customer_image'))
                    <span class="text-danger">{{ $errors->first('customer_image') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.activationRequest.fields.customer_image_helper') }}</span>
            </div>

            <!-- Vehicle Photos -->
            <div class="form-group col-lg-6">
                <label for="vehicle_photos">{{ trans('cruds.activationRequest.fields.vehicle_photos') }}</label>
                <div class="needsclick dropzone {{ $errors->has('vehicle_photos') ? 'is-invalid' : '' }}" id="vehicle_photos-dropzone"></div>
                @if($errors->has('vehicle_photos'))
                    <span class="text-danger">{{ $errors->first('vehicle_photos') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.activationRequest.fields.vehicle_photos_helper') }}</span>
            </div>

            <!-- Product Images -->
            <div class="form-group col-lg-6">
                <label for="product_images">{{ trans('cruds.activationRequest.fields.product_images') }}</label>
                <div class="needsclick dropzone {{ $errors->has('product_images') ? 'is-invalid' : '' }}" id="product_images-dropzone"></div>
                @if($errors->has('product_images'))
                    <span class="text-danger">{{ $errors->first('product_images') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.activationRequest.fields.product_images_helper') }}</span>
            </div>

            <!-- ID Proofs -->
            <div class="form-group col-lg-6">
                <label class="required" for="id_proofs">{{ trans('cruds.activationRequest.fields.id_proofs') }}</label>
                <div class="needsclick dropzone {{ $errors->has('id_proofs') ? 'is-invalid' : '' }}" id="id_proofs-dropzone"></div>
                @if($errors->has('id_proofs'))
                    <span class="text-danger">{{ $errors->first('id_proofs') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.activationRequest.fields.id_proofs_helper') }}</span>
            </div>

            <!-- Submit Button -->
            <div class="form-group col-lg-12 mt-3 text-end">
                <button class="btn btn-danger" type="submit">
                    {{ trans('global.save') }}
                </button>
            </div>
        </div>
    </div>
</div>

        </form>
    </div>
</div>






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
                xhr.open('POST', '{{ route('admin.activation-requests.storeCKEditorImages') }}', true);
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
                data.append('crud_id', '{{ $activationRequest->id ?? 0 }}');
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
    Dropzone.options.customerImageDropzone = {
    url: '{{ route('admin.activation-requests.storeMedia') }}',
    maxFilesize: 20, // MB
    acceptedFiles: '.jpeg,.jpg,.png,.gif',
    maxFiles: 1,
    addRemoveLinks: true,
    headers: {
      'X-CSRF-TOKEN': "{{ csrf_token() }}"
    },
    params: {
      size: 20,
      width: 4096,
      height: 4096
    },
    success: function (file, response) {
      $('form').find('input[name="customer_image"]').remove()
      $('form').append('<input type="hidden" name="customer_image" value="' + response.name + '">')
    },
    removedfile: function (file) {
      file.previewElement.remove()
      if (file.status !== 'error') {
        $('form').find('input[name="customer_image"]').remove()
        this.options.maxFiles = this.options.maxFiles + 1
      }
    },
    init: function () {
@if(isset($activationRequest) && $activationRequest->customer_image)
      var file = {!! json_encode($activationRequest->customer_image) !!}
          this.options.addedfile.call(this, file)
      this.options.thumbnail.call(this, file, file.preview ?? file.preview_url)
      file.previewElement.classList.add('dz-complete')
      $('form').append('<input type="hidden" name="customer_image" value="' + file.file_name + '">')
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
<script>
    Dropzone.options.vehiclePhotosDropzone = {
    url: '{{ route('admin.activation-requests.storeMedia') }}',
    maxFilesize: 20, // MB
    acceptedFiles: '.jpeg,.jpg,.png,.gif',
    maxFiles: 1,
    addRemoveLinks: true,
    headers: {
      'X-CSRF-TOKEN': "{{ csrf_token() }}"
    },
    params: {
      size: 20,
      width: 4096,
      height: 4096
    },
    success: function (file, response) {
      $('form').find('input[name="vehicle_photos"]').remove()
      $('form').append('<input type="hidden" name="vehicle_photos" value="' + response.name + '">')
    },
    removedfile: function (file) {
      file.previewElement.remove()
      if (file.status !== 'error') {
        $('form').find('input[name="vehicle_photos"]').remove()
        this.options.maxFiles = this.options.maxFiles + 1
      }
    },
    init: function () {
@if(isset($activationRequest) && $activationRequest->vehicle_photos)
      var file = {!! json_encode($activationRequest->vehicle_photos) !!}
          this.options.addedfile.call(this, file)
      this.options.thumbnail.call(this, file, file.preview ?? file.preview_url)
      file.previewElement.classList.add('dz-complete')
      $('form').append('<input type="hidden" name="vehicle_photos" value="' + file.file_name + '">')
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
<script>
    Dropzone.options.productImagesDropzone = {
    url: '{{ route('admin.activation-requests.storeMedia') }}',
    maxFilesize: 20, // MB
    acceptedFiles: '.jpeg,.jpg,.png,.gif',
    maxFiles: 1,
    addRemoveLinks: true,
    headers: {
      'X-CSRF-TOKEN': "{{ csrf_token() }}"
    },
    params: {
      size: 20,
      width: 4096,
      height: 4096
    },
    success: function (file, response) {
      $('form').find('input[name="product_images"]').remove()
      $('form').append('<input type="hidden" name="product_images" value="' + response.name + '">')
    },
    removedfile: function (file) {
      file.previewElement.remove()
      if (file.status !== 'error') {
        $('form').find('input[name="product_images"]').remove()
        this.options.maxFiles = this.options.maxFiles + 1
      }
    },
    init: function () {
@if(isset($activationRequest) && $activationRequest->product_images)
      var file = {!! json_encode($activationRequest->product_images) !!}
          this.options.addedfile.call(this, file)
      this.options.thumbnail.call(this, file, file.preview ?? file.preview_url)
      file.previewElement.classList.add('dz-complete')
      $('form').append('<input type="hidden" name="product_images" value="' + file.file_name + '">')
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


<script>
    Dropzone.options.idProofsDropzone = {
    url: '{{ route('admin.activation-requests.storeMedia') }}',
    maxFilesize: 20, // MB
    acceptedFiles: '.jpeg,.jpg,.png,.gif',
    maxFiles: 1,
    addRemoveLinks: true,
    headers: {
      'X-CSRF-TOKEN': "{{ csrf_token() }}"
    },
    params: {
      size: 20,
      width: 4096,
      height: 4096
    },
    success: function (file, response) {
      $('form').find('input[name="id_proofs"]').remove()
      $('form').append('<input type="hidden" name="id_proofs" value="' + response.name + '">')
    },
    removedfile: function (file) {
      file.previewElement.remove()
      if (file.status !== 'error') {
        $('form').find('input[name="id_proofs"]').remove()
        this.options.maxFiles = this.options.maxFiles + 1
      }
    },
    init: function () {
@if(isset($activationRequest) && $activationRequest->id_proofs)
      var file = {!! json_encode($activationRequest->id_proofs) !!}
          this.options.addedfile.call(this, file)
      this.options.thumbnail.call(this, file, file.preview ?? file.preview_url)
      file.previewElement.classList.add('dz-complete')
      $('form').append('<input type="hidden" name="id_proofs" value="' + file.file_name + '">')
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

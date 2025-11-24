@extends('layouts.admin')
@section('content')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

<style>
/* ---------- PREMIUM IPO/SMI THEME ---------- */

/* STEP HEADER */
.step-header {
    display: flex;
    justify-content: space-between;
    margin-bottom: 35px;
}
.step-item {
    flex: 1;
    text-align: center;
    padding-bottom: 12px;
    position: relative;
    font-weight: 600;
    color: #c2c5cc;
    font-size: 15px;
    text-transform: uppercase;
}
.step-item.active { color: #007bff; }
.step-item.completed { color: #28a745; }

.step-item::after {
    content: "";
    height: 3px;
    width: 100%;
    background: #ddd;
    position: absolute;
    bottom: 0;
    left: 0;
}
.step-item.active::after { background: #007bff; }
.step-item.completed::after { background: #28a745; }

/* CARD DESIGN */
.step-card {
    padding: 28px;
    border-radius: 15px;
    background: #ffffff;
    box-shadow: 0px 4px 18px rgba(0,0,0,0.08);
}

/* INPUTS */
label {
    font-weight: 600;
    color: #292929;
}

.required-star::after {
    content: " *";
    color: red;
    font-weight: bold;
}

.form-control,
.select2-selection {
    border-radius: 10px !important;
    padding: 10px !important;
    border-color: #d2d2d2 !important;
}

.form-control:focus,
.select2-selection:focus {
    box-shadow: 0px 0px 5px rgba(0, 139, 255, 0.35);
    border-color: #007bff !important;
}

/* BUTTONS */
.btn-nav {
    padding: 10px 28px;
    border-radius: 10px;
    font-size: 15px;
    font-weight: 600;
}

/* Dropzone */
.dropzone {
    border: 2px dashed #007bff !important;
    border-radius: 12px !important;
    background: #f8fbff !important;
}
.dz-message {
    font-weight: 500;
    color: #555;
}
</style>

<div class="card">
    <div class="card-header">
        <strong>SMI Investor Registration</strong>
    </div>

    <div class="card-body">

        <!-- STEP HEADER -->
        <div class="step-header">
            <div class="step-item active" id="stepIndicator1">Personal Details</div>
            <div class="step-item" id="stepIndicator2">Address</div>
            <div class="step-item" id="stepIndicator3">Bank Details</div>
            <div class="step-item" id="stepIndicator4">KYC Upload</div>
            <div class="step-item" id="stepIndicator5">Financial Info</div>
        </div>

        <form method="POST" action="{{ route('admin.registrations.store') }}" enctype="multipart/form-data">
            @csrf

            <!-- ================= STEP 1 ================= -->
            <div class="step-card animate__animated animate__fadeIn" id="step1">

                <div class="row">

                    <div class="col-md-4">
                        <label class="required-star">Registration No.</label>
                        <input class="form-control" name="reg" id="reg_no" readonly>
                    </div>

                    <div class="col-md-4">
                        <label class="required-star">Investor</label>
                        <select class="form-control select2" name="investor_id" required>
                            @foreach($investors as $id => $entry)
                                <option value="{{ $id }}">{{ $entry }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label>Referral Code</label>
                        <input class="form-control" name="referral_code">
                    </div>

                    <div class="col-md-4">
                        <label class="required-star">Aadhaar Number</label>
                        <input class="form-control" name="aadhaar_number" required>
                    </div>

                    <div class="col-md-4">
                        <label class="required-star">PAN Number</label>
                        <input class="form-control" name="pan_number" required>
                    </div>

                    <div class="col-md-4">
                        <label>Date of Birth</label>
                        <input class="form-control date" name="dob">
                    </div>

                    <div class="col-md-4">
                        <label class="required-star">Gender</label>
                        <select class="form-control" name="gender" required>
                            <option value="">Please select</option>
                            @foreach(App\Models\Registration::GENDER_SELECT as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-8">
                        <label>Father Name</label>
                        <input class="form-control" name="father_name">
                    </div>

                </div>

                <br>
                <button type="button" class="btn btn-primary btn-nav float-right" onclick="nextStep(1)">
                    Next →
                </button>

            </div>

            <!-- ================= STEP 2 ================= -->
            <div class="step-card animate__animated animate__fadeIn d-none" id="step2">

                <div class="row">

                    <div class="col-md-6">
                        <label class="required-star">Address Line 1</label>
                        <textarea class="form-control ckeditor" name="address_line_1" required></textarea>
                    </div>

                    <div class="col-md-6">
                        <label>Address Line 2</label>
                        <textarea class="form-control ckeditor" name="address_line_2"></textarea>
                    </div>

                    <div class="col-md-4">
                        <label class="required-star">Pincode</label>
                        <input class="form-control" name="pincode" required>
                    </div>

                    <div class="col-md-4">
                        <label>City</label>
                        <input class="form-control" name="city">
                    </div>

                    <div class="col-md-4">
                        <label>State</label>
                        <input class="form-control" name="state">
                    </div>

                    <div class="col-md-12 mt-2">
                        <label class="required-star">Country</label>
                        <input class="form-control" name="country" required>
                    </div>

                </div>

                <br>
                <button type="button" class="btn btn-secondary btn-nav" onclick="prevStep(2)">← Back</button>
                <button type="button" class="btn btn-primary btn-nav float-right" onclick="nextStep(2)">Next →</button>

            </div>

            <!-- ================= STEP 3 ================= -->
            <div class="step-card animate__animated animate__fadeIn d-none" id="step3">

                <div class="row">

                    <div class="col-md-6">
                        <label class="required-star">Account Holder Name</label>
                        <input class="form-control" name="bank_account_holder_name" required>
                    </div>

                    <div class="col-md-6">
                        <label class="required-star">Account Number</label>
                        <input class="form-control" name="bank_account_number" required>
                    </div>

                    <div class="col-md-4">
                        <label class="required-star">IFSC Code</label>
                        <input class="form-control" name="ifsc_code" required>
                    </div>

                    <div class="col-md-4">
                        <label>Bank Name</label>
                        <input class="form-control" name="bank_name">
                    </div>

                    <div class="col-md-4">
                        <label>Branch</label>
                        <input class="form-control" name="bank_branch">
                    </div>

                </div>

                <br>
                <button type="button" class="btn btn-secondary btn-nav" onclick="prevStep(3)">← Back</button>
                <button type="button" class="btn btn-primary btn-nav float-right" onclick="nextStep(3)">Next →</button>

            </div>

            <!-- ================= STEP 4 ================= -->
            <div class="step-card animate__animated animate__fadeIn d-none" id="step4">

                <div class="row">

                    <div class="col-md-6">
                        <label class="required-star">PAN Card Image</label>
                        <div class="dropzone" id="pan_card_image-dropzone"></div>
                    </div>

                    <div class="col-md-6">
                        <label class="required-star">Aadhaar Front</label>
                        <div class="dropzone" id="aadhaar_front_image-dropzone"></div>
                    </div>

                    <div class="col-md-6 mt-3">
                        <label class="required-star">Aadhaar Back</label>
                        <div class="dropzone" id="aadhaar_back_image-dropzone"></div>
                    </div>

                    <div class="col-md-6 mt-3">
                        <label>Profile Photo</label>
                        <div class="dropzone" id="profile_image-dropzone"></div>
                    </div>

                    <div class="col-md-6 mt-3">
                        <label>Signature</label>
                        <div class="dropzone" id="signature_image-dropzone"></div>
                    </div>

                </div>

                <br>
                <button type="button" class="btn btn-secondary btn-nav" onclick="prevStep(4)">← Back</button>
                <button type="button" class="btn btn-primary btn-nav float-right" onclick="nextStep(4)">Next →</button>

            </div>

            <!-- ================= STEP 5 ================= -->
            <div class="step-card animate__animated animate__fadeIn d-none" id="step5">

                <div class="row">

                    <div class="col-md-6">
                        <label class="required-star">Income Range</label>
                        <select class="form-control" name="income_range" required>
                            @foreach(App\Models\Registration::INCOME_RANGE_SELECT as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="required-star">Occupation</label>
                        <select class="form-control" name="occupation" required>
                            @foreach(App\Models\Registration::OCCUPATION_SELECT as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 mt-3">
                        <label>Risk Profile</label>
                        <select class="form-control" name="risk_profile">
                            @foreach(App\Models\Registration::RISK_PROFILE_SELECT as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 mt-3">
                        <label>Investment Experience</label>
                        <select class="form-control" name="investment_experience">
                            @foreach(App\Models\Registration::INVESTMENT_EXPERIENCE_SELECT as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                </div>

                <br>
                <button type="button" class="btn btn-secondary btn-nav" onclick="prevStep(5)">← Back</button>
                <button class="btn btn-success btn-nav float-right" type="submit">
                    Submit Registration ✔
                </button>

            </div>

        </form>
    </div>
</div>

@endsection

@section('scripts')

<script>
/* ------------------ AUTO GENERATE 10 DIGIT REG NO ------------------ */
function generateRegNo() {
    let num = Math.floor(1000000000 + Math.random() * 9000000000);  
    document.getElementById("reg_no").value = num;
}
generateRegNo();

/* ------------------ STEP FORM LOGIC ------------------ */
let currentStep = 1;

function showStep(step) {
    document.querySelectorAll(".step-card").forEach(e => e.classList.add("d-none"));
    document.getElementById("step" + step).classList.remove("d-none");

    document.querySelectorAll(".step-item").forEach((e, i) => {
        e.classList.remove("active", "completed");
        if (i + 1 < step) e.classList.add("completed");
        if (i + 1 === step) e.classList.add("active");
    });
}

function nextStep(step) {
    currentStep = step + 1;
    showStep(currentStep);
}
function prevStep(step) {
    currentStep = step - 1;
    showStep(currentStep);
}
showStep(1);

/* ------------------ CKEDITOR UPLOAD ------------------ */
function SimpleUploadAdapter(editor) {
    editor.plugins.get('FileRepository').createUploadAdapter = function (loader) {
        return {
            upload: function () {
                return loader.file.then(function (file) {
                    return new Promise(function (resolve, reject) {
                        var xhr = new XMLHttpRequest();
                        xhr.open('POST', '{{ route('admin.registrations.storeCKEditorImages') }}', true);
                        xhr.setRequestHeader('x-csrf-token', window._token);
                        xhr.setRequestHeader('Accept', 'application/json');
                        xhr.responseType = 'json';

                        xhr.addEventListener('load', function () {
                            var response = xhr.response;
                            $('form').append('<input type="hidden" name="ck-media[]" value="' + response.id + '">');
                            resolve({ default: response.url });
                        });

                        var data = new FormData();
                        data.append('upload', file);
                        data.append('crud_id', 0);
                        xhr.send(data);
                    });
                });
            }
        };
    };
}

document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll('.ckeditor').forEach(function (el) {
        ClassicEditor.create(el, { extraPlugins: [SimpleUploadAdapter] });
    });
});

/* ------------------ DROPZONE CONFIG ------------------ */

// PAN MULTIPLE
Dropzone.options.panCardImageDropzone = {
    url: '{{ route('admin.registrations.storeMedia') }}',
    maxFilesize: 20,
    addRemoveLinks: true,
    headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
    success: function (file, response) {
        $('form').append('<input type="hidden" name="pan_card_image[]" value="' + response.name + '">')
    }
};

// Aadhaar front
Dropzone.options.aadhaarFrontImageDropzone = {
    url: '{{ route('admin.registrations.storeMedia') }}',
    maxFilesize: 20,
    maxFiles: 1,
    addRemoveLinks: true,
    headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
    success: function (file, response) {
        $('form').append('<input type="hidden" name="aadhaar_front_image" value="' + response.name + '">')
    }
};

// Aadhaar back
Dropzone.options.aadhaarBackImageDropzone = {
    url: '{{ route('admin.registrations.storeMedia') }}',
    maxFilesize: 20,
    maxFiles: 1,
    addRemoveLinks: true,
    headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
    success: function (file, response) {
        $('form').append('<input type="hidden" name="aadhaar_back_image" value="' + response.name + '">')
    }
};

// Profile (multi)
Dropzone.options.profileImageDropzone = {
    url: '{{ route('admin.registrations.storeMedia') }}',
    maxFilesize: 20,
    addRemoveLinks: true,
    headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
    success: function (file, response) {
        $('form').append('<input type="hidden" name="profile_image[]" value="' + response.name + '">')
    }
};

// Signature (multi)
Dropzone.options.signatureImageDropzone = {
    url: '{{ route('admin.registrations.storeMedia') }}',
    maxFilesize: 20,
    addRemoveLinks: true,
    headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
    success: function (file, response) {
        $('form').append('<input type="hidden" name="signature_image[]" value="' + response.name + '">')
    }
};

</script>
@endsection

@extends('layouts.admin')
@section('content')

<style>
/* ---------- STEP WIZARD ---------- */
.step-wizard {
    display: flex;
    justify-content: space-between;
    margin-bottom: 25px;
    position: relative;
}
.step-wizard::before {
    content: "";
    position: absolute;
    top: 18px;
    left: 0;
    width: 100%;
    height: 3px;
    background: #dcdcdc;
    z-index: 0;
}
.step {
    z-index: 1;
    text-align: center;
    width: 25%;
}
.step .circle {
    width: 35px;
    height: 35px;
    background: #bbb;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    margin: 0 auto 5px;
    font-weight: bold;
}
.step.active .circle {
    background: #0d6efd;
}
.step.completed .circle {
    background: #198754;
}

/* ---------- FORM STYLING ---------- */
.step-content { display: none; }
.step-content.active { display: block; }

.form-section-title {
    font-weight: bold;
    font-size: 18px;
    border-left: 4px solid #0d6efd;
    padding-left: 10px;
    margin: 20px 0 15px 0;
}

.form-group-row {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
}
.form-group-row .col-4 {
    flex: 1;
    min-width: 30%;
}

/* ---------- BUTTONS ---------- */
.btn-next, .btn-prev {
    padding: 10px 20px;
    font-size: 15px;
    border-radius: 6px;
}
</style>


<div class="card shadow-sm">
    <div class="card-header bg-primary text-white">
        <strong>Create Agent (Multi-Step Form)</strong>
    </div>

    <div class="card-body">

        <!-- 🚀 STEP WIZARD -->
        <div class="step-wizard mb-4">
            <div class="step step-1 active">
                <div class="circle">1</div>
                Basic Info
            </div>
            <div class="step step-2">
                <div class="circle">2</div>
                Location
            </div>
            <div class="step step-3">
                <div class="circle">3</div>
                Address
            </div>
            <div class="step step-4">
                <div class="circle">4</div>
                Documents
            </div>
        </div>

        <form method="POST" action="{{ route('admin.agents.store') }}" enctype="multipart/form-data">
            @csrf

            <!-- ⭐ STEP 1 -->
            <div class="step-content step-content-1 active">
                <div class="form-section-title">Basic Information</div>

                <div class="form-group-row">
                    <div class="col-4 form-group">
                        <label class="required">Full Name</label>
                        <input class="form-control" type="text" name="full_name" id="full_name"
                               value="{{ old('full_name') }}" required>
                    </div>

                    <div class="col-4 form-group">
                        <label class="required">Phone Number</label>
                        <input class="form-control" type="text" name="phone_number" id="phone_number"
                               value="{{ old('phone_number') }}" required>
                    </div>

                    <div class="col-4 form-group">
                        <label>WhatsApp Number</label>
                        <input class="form-control" type="text" name="whatsapp_number" id="whatsapp_number"
                               value="{{ old('whatsapp_number') }}">
                    </div>
                </div>

                <div class="form-group-row">
                    <div class="col-4 form-group">
                        <label>Email</label>
                        <input class="form-control" type="text" name="email" id="email"
                               value="{{ old('email') }}">
                    </div>

                    <div class="col-4 form-group">
                        <label class="required">Pin Code</label>
                        <input class="form-control" type="number" name="pin_code" id="pin_code"
                               value="{{ old('pin_code') }}" required>
                    </div>

                    <div class="col-4 form-group">
                        <label>Status</label>
                        <select name="status" id="status" class="form-control">
                            @foreach(App\Models\Agent::STATUS_SELECT as $key => $label)
                                <option value="{{ $key }}" {{ old('status', 'active') == $key ? 'selected':'' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <button type="button" class="btn btn-primary btn-next float-end">Next →</button>
            </div>

            <!-- ⭐ STEP 2 -->
            <div class="step-content step-content-2">
                <div class="form-section-title">Location Details</div>

                <div class="form-group-row">
                    <div class="col-4 form-group">
                        <label>State</label>
                        <input class="form-control" type="text" name="state" id="state" value="{{ old('state') }}">
                    </div>

                    <div class="col-4 form-group">
                        <label>City</label>
                        <input class="form-control" type="text" name="city" id="city" value="{{ old('city') }}">
                    </div>

                    <div class="col-4 form-group">
                        <label>District</label>
                        <input class="form-control" type="text" name="district" id="district" value="{{ old('district') }}">
                    </div>
                </div>

                <button type="button" class="btn btn-secondary btn-prev">← Back</button>
                <button type="button" class="btn btn-primary btn-next float-end">Next →</button>
            </div>

            <!-- ⭐ STEP 3 -->
            <div class="step-content step-content-3">
                <div class="form-section-title">Address Details</div>

                <div class="form-group">
                    <label>Present Address</label>
                    <textarea name="present_address" id="present_address"
                              class="form-control ckeditor">{{ old('present_address') }}</textarea>
                </div>

                <div class="form-group">
                    <label>Permanent Address</label>
                    <textarea name="parmanent_address" id="parmanent_address"
                              class="form-control ckeditor">{{ old('parmanent_address') }}</textarea>
                </div>

                <button type="button" class="btn btn-secondary btn-prev">← Back</button>
                <button type="button" class="btn btn-primary btn-next float-end">Next →</button>
            </div>

            <!-- ⭐ STEP 4 -->
            <div class="step-content step-content-4">
                <div class="form-section-title">Document Uploads</div>

                <div class="form-group">
                    <label>Aadhar Front</label>
                    <div class="dropzone needsclick" id="aadhar_front-dropzone"></div>
                </div>

                <div class="form-group">
                    <label>Aadhar Back</label>
                    <div class="dropzone needsclick" id="aadhar_back-dropzone"></div>
                </div>

                <div class="form-group">
                    <label>PAN Card</label>
                    <div class="dropzone needsclick" id="pan_card-dropzone"></div>
                </div>

                <div class="form-group">
                    <label>Additional Document</label>
                    <div class="dropzone needsclick" id="additional_document-dropzone"></div>
                </div>

                <button type="button" class="btn btn-secondary btn-prev">← Back</button>

                <button type="submit" class="btn btn-success float-end">
                    Save Agent
                </button>
            </div>

        </form>

    </div>
</div>

@endsection


@section('scripts')

<!-- ⭐ MULTI STEP LOGIC -->
<script>
let step = 1;

function showStep(n) {
    document.querySelectorAll('.step-content').forEach(s => s.classList.remove('active'));
    document.querySelector('.step-content-' + n).classList.add('active');

    document.querySelectorAll('.step').forEach((el, index) => {
        el.classList.remove('active', 'completed');

        if (index + 1 < n) el.classList.add('completed');
        if (index + 1 === n) el.classList.add('active');
    });
}

document.querySelectorAll('.btn-next').forEach(btn => {
    btn.addEventListener('click', () => {
        step++;
        showStep(step);
    });
});

document.querySelectorAll('.btn-prev').forEach(btn => {
    btn.addEventListener('click', () => {
        step--;
        showStep(step);
    });
});
</script>

<!-- ⭐ PINCODE AUTO-FILL -->
<script>
document.getElementById('pin_code').addEventListener('keyup', function () {
    const pin = this.value;

    if (pin.length === 6) {
        fetch(`https://api.postalpincode.in/pincode/${pin}`)
            .then(response => response.json())
            .then(data => {
                if (data[0].Status === "Success") {
                    let post = data[0].PostOffice[0];

                    document.getElementById('state').value = post.State;
                    document.getElementById('city').value = post.Region;
                    document.getElementById('district').value = post.District;

                    document.getElementById('parmanent_address').value =
                        `${post.Name}, ${post.District}, ${post.State}, ${pin}`;
                }
            })
            .catch(err => console.log("Pincode API Error", err));
    }
});
</script>

<!-- ⭐ CKEDITOR (SINGLE CLEAN WORKING VERSION) -->
<script>
$(document).ready(function () {

    function SimpleUploadAdapter(editor) {
        editor.plugins.get('FileRepository').createUploadAdapter = function (loader) {
            return {
                upload: function () {
                    return loader.file.then(function (file) {
                        return new Promise(function (resolve, reject) {

                            var xhr = new XMLHttpRequest();
                            xhr.open('POST', '{{ route('admin.agents.storeCKEditorImages') }}', true);
                            xhr.setRequestHeader('x-csrf-token', window._token);
                            xhr.responseType = 'json';

                            xhr.onload = function () {
                                if (xhr.status === 201) {
                                    $('form').append(
                                        '<input type="hidden" name="ck-media[]" value="' + xhr.response.id + '">'
                                    );
                                    resolve({ default: xhr.response.url });
                                } else {
                                    reject('Upload Error');
                                }
                            };

                            var data = new FormData();
                            data.append('upload', file);
                            xhr.send(data);
                        });
                    });
                }
            };
        };
    }

    document.querySelectorAll('.ckeditor').forEach(el => {
        ClassicEditor.create(el, {
            extraPlugins: [SimpleUploadAdapter]
        });
    });

});
</script>

<!-- ⭐ DROPZONE – AADHAR FRONT -->
<script>
Dropzone.options.aadharFrontDropzone = {
    url: '{{ route('admin.agents.storeMedia') }}',
    maxFilesize: 20,
    maxFiles: 1,
    addRemoveLinks: true,
    headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
    params: { size: 20 },

    success: function (file, response) {
        $('form').find('input[name="aadhar_front"]').remove()
        $('form').append('<input type="hidden" name="aadhar_front" value="' + response.name + '">')
    },

    removedfile: function (file) {
        file.previewElement.remove();
        $('form').find('input[name="aadhar_front"]').remove()
        this.options.maxFiles++;
    }
}
</script>

<!-- ⭐ DROPZONE – AADHAR BACK -->
<script>
Dropzone.options.aadharBackDropzone = {
    url: '{{ route('admin.agents.storeMedia') }}',
    maxFilesize: 20,
    maxFiles: 1,
    addRemoveLinks: true,
    headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
    params: { size: 20 },

    success: function (file, response) {
        $('form').find('input[name="aadhar_back"]').remove()
        $('form').append('<input type="hidden" name="aadhar_back" value="' + response.name + '">')
    },

    removedfile: function (file) {
        file.previewElement.remove();
        $('form').find('input[name="aadhar_back"]').remove()
        this.options.maxFiles++;
    }
}
</script>

<!-- ⭐ DROPZONE – PAN -->
<script>
Dropzone.options.panCardDropzone = {
    url: '{{ route('admin.agents.storeMedia') }}',
    maxFilesize: 20,
    maxFiles: 1,
    addRemoveLinks: true,
    headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
    params: { size: 20 },

    success: function (file, response) {
        $('form').find('input[name="pan_card"]').remove()
        $('form').append('<input type="hidden" name="pan_card" value="' + response.name + '">')
    },

    removedfile: function (file) {
        file.previewElement.remove();
        $('form').find('input[name="pan_card"]').remove()
        this.options.maxFiles++;
    }
}
</script>

<!-- ⭐ DROPZONE – ADDITIONAL DOCUMENTS -->
<script>
var uploadedAdditionalDocumentMap = {}

Dropzone.options.additionalDocumentDropzone = {
    url: '{{ route('admin.agents.storeMedia') }}',
    maxFilesize: 20,
    addRemoveLinks: true,
    headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
    params: { size: 20 },

    success: function (file, response) {
        $('form').append('<input type="hidden" name="additional_document[]" value="' + response.name + '">')
        uploadedAdditionalDocumentMap[file.name] = response.name
    },

    removedfile: function (file) {
        file.previewElement.remove();
        var name = uploadedAdditionalDocumentMap[file.name];
        $('form').find('input[name="additional_document[]"][value="' + name + '"]').remove()
    }
}
</script>

@endsection

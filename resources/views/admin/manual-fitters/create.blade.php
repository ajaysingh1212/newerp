@extends('layouts.admin')

@section('content')
<style>
    .mf-wizard-card { border: none; border-radius: 16px; box-shadow: 0 10px 30px rgba(124,58,237,.1); overflow: hidden; }
    .mf-wizard-header { background: linear-gradient(135deg, #7C3AED 0%, #6366F1 100%); padding: 24px; color: #fff; }
    .mf-steps { display: flex; list-style: none; padding: 0; margin: 20px 0 0; }
    .mf-steps li { flex: 1; text-align: center; position: relative; color: #C4B5FD; font-weight: 600; font-size: .85rem; }
    .mf-steps li .circle {
        width: 36px; height: 36px; border-radius: 50%; background: rgba(255,255,255,.15);
        display: flex; align-items: center; justify-content: center; margin: 0 auto 6px;
        border: 2px solid rgba(255,255,255,.3); transition: .3s;
    }
    .mf-steps li.active .circle { background: #fff; color: #6D28D9; border-color: #fff; transform: scale(1.1); }
    .mf-steps li.done .circle { background: #A7F3D0; color: #065F46; border-color: #A7F3D0; }
    .mf-steps li.active, .mf-steps li.done { color: #fff; }
    .mf-step-pane { display: none; animation: fadeInStep .35s ease; }
    .mf-step-pane.active { display: block; }
    @keyframes fadeInStep { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
    .mf-form-group label { font-weight: 600; color: #4B5563; font-size: .85rem; }
    .mf-form-group .required::after { content: ' *'; color: #EF4444; }
    .mf-btn-nav { background: linear-gradient(135deg, #7C3AED, #6366F1); border: none; color: #fff; border-radius: 8px; padding: 10px 24px; }
    .mf-btn-outline { border: 1px solid #C4B5FD; color: #6D28D9; background: #fff; border-radius: 8px; padding: 10px 24px; }
    @media (max-width: 576px) { .mf-steps li span { display: none; } }
</style>

<div class="card mf-wizard-card animate__animated animate__fadeIn">
    <div class="mf-wizard-header">
        <h4 class="mb-0"><i class="fas fa-user-hard-hat mr-2"></i>New Fitter</h4>
        <small>Manual Entry &raquo; Fitter &raquo; Naya fitter add karein</small>
        <ul class="mf-steps">
            <li class="active" data-step="1"><div class="circle">1</div><span>Basic Details</span></li>
            <li data-step="2"><div class="circle">2</div><span>Contact Details</span></li>
            <li data-step="3"><div class="circle">3</div><span>Address Details</span></li>
        </ul>
    </div>

    <div class="card-body p-4">
        @if($errors->any())
            <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
        @endif

        <form action="{{ route('admin.manual-fitters.store') }}" method="POST" enctype="multipart/form-data" id="fitterForm">
            @csrf

            {{-- STEP 1: Basic Details --}}
            <div class="mf-step-pane active" data-pane="1">
                <div class="row">
                    <div class="col-md-4 mf-form-group mb-3">
                        <label class="required">Name</label>
                        <input type="text" name="name" class="form-control" required value="{{ old('name') }}">
                    </div>
                    <div class="col-md-4 mf-form-group mb-3">
                        <label class="required">Phone</label>
                        <input type="text" name="phone" class="form-control" required value="{{ old('phone') }}">
                    </div>
                    <div class="col-md-4 mf-form-group mb-3">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}">
                    </div>
                    <div class="col-md-4 mf-form-group mb-3">
                        <label>Date of Birth</label>
                        <input type="date" name="dob" class="form-control" value="{{ old('dob') }}">
                    </div>
                    <div class="col-md-4 mf-form-group mb-3">
                        <label>Gender</label>
                        <select name="gender" class="form-control">
                            <option value="">Select</option>
                            <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                            <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                            <option value="Other" {{ old('gender') == 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                    <div class="col-md-4 mf-form-group mb-3">
                        <label>Photo</label>
                        <input type="file" name="photo" class="form-control" accept="image/*">
                    </div>
                </div>
            </div>

            {{-- STEP 2: Contact Details --}}
            <div class="mf-step-pane" data-pane="2">
                <div class="row">
                    <div class="col-md-4 mf-form-group mb-3">
                        <label>Alternate Phone</label>
                        <input type="text" name="alternate_phone" class="form-control" value="{{ old('alternate_phone') }}">
                    </div>
                    <div class="col-md-4 mf-form-group mb-3">
                        <label>WhatsApp Number</label>
                        <input type="text" name="whatsapp_number" class="form-control" value="{{ old('whatsapp_number') }}">
                    </div>
                    <div class="col-md-4 mf-form-group mb-3">
                        <label>Aadhar Number</label>
                        <input type="text" name="aadhar_number" class="form-control" value="{{ old('aadhar_number') }}">
                    </div>
                    <div class="col-md-6 mf-form-group mb-3">
                        <label>ID Proof</label>
                        <input type="file" name="id_proof" class="form-control" accept="image/*,.pdf">
                    </div>
                    <div class="col-md-6 mf-form-group mb-3">
                        <label>Landmark</label>
                        <input type="text" name="landmark" class="form-control" value="{{ old('landmark') }}">
                    </div>
                    <div class="col-md-12 mf-form-group mb-3">
                        <label>Address</label>
                        <textarea name="address" rows="2" class="form-control">{{ old('address') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- STEP 3: Address Details --}}
            <div class="mf-step-pane" data-pane="3">
                <div class="row">
                    <div class="col-md-3 mf-form-group mb-3">
                        <label class="required">State</label>
                        <select name="state" id="stateSelect" class="form-control" required>
                            <option value="">Select State</option>
                        </select>
                    </div>
                    <div class="col-md-3 mf-form-group mb-3">
                        <label class="required">District</label>
                        <select name="district" id="districtSelect" class="form-control" required disabled>
                            <option value="">Select District</option>
                        </select>
                    </div>
                    <div class="col-md-3 mf-form-group mb-3">
                        <label class="required">City</label>
                        <select name="city" id="citySelect" class="form-control" required disabled>
                            <option value="">Select City</option>
                        </select>
                    </div>
                    <div class="col-md-3 mf-form-group mb-3">
                        <label class="required">Pincode</label>
                        <input type="text" name="pincode" id="pincodeInput" class="form-control" required value="{{ old('pincode') }}">
                    </div>
                    <div class="col-md-4 mf-form-group mb-3">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="active" selected>Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between mt-4">
                <button type="button" class="mf-btn-outline" id="prevBtn" style="display:none;"><i class="fas fa-arrow-left mr-1"></i> Back</button>
                <div class="ml-auto">
                    <button type="button" class="mf-btn-nav" id="nextBtn">Next <i class="fas fa-arrow-right ml-1"></i></button>
                    <button type="submit" class="mf-btn-nav" id="submitBtn" style="display:none;"><i class="fas fa-check mr-1"></i> Save Fitter</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
(function() {
    const totalSteps = 3;
    let current = 1;
    const steps = document.querySelectorAll('.mf-steps li');
    const panes = document.querySelectorAll('.mf-step-pane');
    const nextBtn = document.getElementById('nextBtn');
    const prevBtn = document.getElementById('prevBtn');
    const submitBtn = document.getElementById('submitBtn');

    function goToStep(n) {
        panes.forEach(p => p.classList.remove('active'));
        document.querySelector(`.mf-step-pane[data-pane="${n}"]`).classList.add('active');
        steps.forEach(s => {
            const step = parseInt(s.dataset.step);
            s.classList.remove('active', 'done');
            if (step < n) s.classList.add('done');
            if (step === n) s.classList.add('active');
        });
        prevBtn.style.display = n === 1 ? 'none' : 'inline-block';
        nextBtn.style.display = n === totalSteps ? 'none' : 'inline-block';
        submitBtn.style.display = n === totalSteps ? 'inline-block' : 'none';
        current = n;
    }

    function validateStep(n) {
        const pane = document.querySelector(`.mf-step-pane[data-pane="${n}"]`);
        const requiredFields = pane.querySelectorAll('[required]');
        for (const field of requiredFields) {
            if (!field.value) { field.reportValidity(); return false; }
        }
        return true;
    }

    nextBtn.addEventListener('click', () => { if (validateStep(current) && current < totalSteps) goToStep(current + 1); });
    prevBtn.addEventListener('click', () => { if (current > 1) goToStep(current - 1); });

    // ---------- State -> District -> City -> Pincode (from public JSON) ----------
    const stateSelect = document.getElementById('stateSelect');
    const districtSelect = document.getElementById('districtSelect');
    const citySelect = document.getElementById('citySelect');
    const pincodeInput = document.getElementById('pincodeInput');
    let locationData = [];

    fetch("{{ asset('assets/data/india-states-districts-cities.json') }}")
        .then(res => res.json())
        .then(data => {
            locationData = data;
            data.forEach(s => {
                stateSelect.innerHTML += `<option value="${s.state}">${s.state}</option>`;
            });
        })
        .catch(err => console.error('Location data load failed', err));

    stateSelect.addEventListener('change', function () {
        districtSelect.innerHTML = '<option value="">Select District</option>';
        citySelect.innerHTML = '<option value="">Select City</option>';
        citySelect.disabled = true;
        pincodeInput.value = '';

        const stateObj = locationData.find(s => s.state === this.value);
        if (!stateObj) { districtSelect.disabled = true; return; }

        stateObj.districts.forEach(d => {
            districtSelect.innerHTML += `<option value="${d.district}">${d.district}</option>`;
        });
        districtSelect.disabled = false;
    });

    districtSelect.addEventListener('change', function () {
        citySelect.innerHTML = '<option value="">Select City</option>';
        pincodeInput.value = '';

        const stateObj = locationData.find(s => s.state === stateSelect.value);
        const districtObj = stateObj ? stateObj.districts.find(d => d.district === this.value) : null;
        if (!districtObj) { citySelect.disabled = true; return; }

        districtObj.cities.forEach(c => {
            citySelect.innerHTML += `<option value="${c.city}" data-pincode="${c.pincode}">${c.city}</option>`;
        });
        citySelect.disabled = false;
    });

    citySelect.addEventListener('change', function () {
        const selected = this.options[this.selectedIndex];
        pincodeInput.value = selected ? (selected.dataset.pincode || '') : '';
    });
})();
</script>
@endsection

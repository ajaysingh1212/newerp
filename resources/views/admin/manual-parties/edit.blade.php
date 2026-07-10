@extends('layouts.admin')

@section('content')
<style>
    .me-wizard-card { border: none; border-radius: 16px; box-shadow: 0 10px 30px rgba(124,58,237,.1); overflow: hidden; }
    .me-wizard-header { background: linear-gradient(135deg, #7C3AED 0%, #6366F1 100%); padding: 24px; color: #fff; }
    .me-steps { display: flex; list-style: none; padding: 0; margin: 20px 0 0; }
    .me-steps li { flex: 1; text-align: center; position: relative; color: #C4B5FD; font-weight: 600; font-size: .85rem; }
    .me-steps li .circle {
        width: 36px; height: 36px; border-radius: 50%; background: rgba(255,255,255,.15);
        display: flex; align-items: center; justify-content: center; margin: 0 auto 6px;
        border: 2px solid rgba(255,255,255,.3); transition: .3s;
    }
    .me-steps li.active .circle { background: #fff; color: #6D28D9; border-color: #fff; transform: scale(1.1); }
    .me-steps li.done .circle { background: #A7F3D0; color: #065F46; border-color: #A7F3D0; }
    .me-steps li.active, .me-steps li.done { color: #fff; }
    .me-step-pane { display: none; animation: fadeInStep .35s ease; }
    .me-step-pane.active { display: block; }
    @keyframes fadeInStep { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
    .me-form-group label { font-weight: 600; color: #4B5563; font-size: .85rem; }
    .me-form-group .required::after { content: ' *'; color: #EF4444; }
    .me-btn-nav { background: linear-gradient(135deg, #7C3AED, #6366F1); border: none; color: #fff; border-radius: 8px; padding: 10px 24px; }
    .me-btn-outline { border: 1px solid #C4B5FD; color: #6D28D9; background: #fff; border-radius: 8px; padding: 10px 24px; }
</style>

<div class="card me-wizard-card animate__animated animate__fadeIn">
    <div class="me-wizard-header">
        <h4 class="mb-0"><i class="fas fa-user-tie mr-2"></i>Edit Party</h4>
        <small>Manual Entry &raquo; Party &raquo; {{ $manualParty->name }}</small>
        <ul class="me-steps">
            <li class="active" data-step="1"><div class="circle">1</div><span>Basic Info</span></li>
            <li data-step="2"><div class="circle">2</div><span>Location</span></li>
            <li data-step="3"><div class="circle">3</div><span>GST / PAN</span></li>
            <li data-step="4"><div class="circle">4</div><span>Account</span></li>
        </ul>
    </div>

    <div class="card-body p-4">
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif

        <form action="{{ route('admin.manual-parties.update', $manualParty->id) }}" method="POST" id="partyForm">
            @csrf @method('PUT')

            <div class="me-step-pane active" data-pane="1">
                <div class="row">
                    <div class="col-md-4 me-form-group mb-3">
                        <label class="required">Party Name</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $manualParty->name) }}" required>
                    </div>
                    <div class="col-md-4 me-form-group mb-3">
                        <label class="required">Phone</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $manualParty->phone) }}" required>
                    </div>
                    <div class="col-md-4 me-form-group mb-3">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $manualParty->email) }}">
                    </div>
                </div>
            </div>

            <div class="me-step-pane" data-pane="2">
                <div class="row">
                    <div class="col-md-4 me-form-group mb-3">
                        <label class="required">State</label>
                        <select name="state" id="state_select" class="form-control" required>
                            <option value="">Select State</option>
                        </select>
                    </div>
                    <div class="col-md-4 me-form-group mb-3">
                        <label class="required">District</label>
                        <select name="district" id="district_select" class="form-control" required disabled>
                            <option value="">Select State First</option>
                        </select>
                    </div>
                    <div class="col-md-4 me-form-group mb-3">
                        <label class="required">City</label>
                        <select name="city" id="city_select" class="form-control" required disabled>
                            <option value="">Select District First</option>
                        </select>
                    </div>
                    <div class="col-md-4 me-form-group mb-3">
                        <label>Pincode</label>
                        <input type="text" name="pincode" id="pincode" class="form-control" value="{{ old('pincode', $manualParty->pincode) }}">
                    </div>
                    <div class="col-md-8 me-form-group mb-3">
                        <label>Address</label>
                        <input type="text" name="address" class="form-control" value="{{ old('address', $manualParty->address) }}">
                    </div>
                </div>
            </div>

            <div class="me-step-pane" data-pane="3">
                <div class="row">
                    <div class="col-md-6 me-form-group mb-3">
                        <label>GST Number</label>
                        <input type="text" name="gst_number" class="form-control" value="{{ old('gst_number', $manualParty->gst_number) }}">
                    </div>
                    <div class="col-md-6 me-form-group mb-3">
                        <label>PAN Number</label>
                        <input type="text" name="pan_number" class="form-control" value="{{ old('pan_number', $manualParty->pan_number) }}">
                    </div>
                </div>
            </div>

            <div class="me-step-pane" data-pane="4">
                <div class="row">
                    <div class="col-md-6 me-form-group mb-3">
                        <label>Bank Name</label>
                        <input type="text" name="bank_name" class="form-control" value="{{ old('bank_name', $manualParty->bank_name) }}">
                    </div>
                    <div class="col-md-6 me-form-group mb-3">
                        <label>Account Holder Name</label>
                        <input type="text" name="account_holder_name" class="form-control" value="{{ old('account_holder_name', $manualParty->account_holder_name) }}">
                    </div>
                    <div class="col-md-4 me-form-group mb-3">
                        <label>Account Number</label>
                        <input type="text" name="account_number" class="form-control" value="{{ old('account_number', $manualParty->account_number) }}">
                    </div>
                    <div class="col-md-4 me-form-group mb-3">
                        <label>IFSC Code</label>
                        <input type="text" name="ifsc_code" class="form-control" value="{{ old('ifsc_code', $manualParty->ifsc_code) }}">
                    </div>
                    <div class="col-md-4 me-form-group mb-3">
                        <label>Branch Name</label>
                        <input type="text" name="branch_name" class="form-control" value="{{ old('branch_name', $manualParty->branch_name) }}">
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between mt-4">
                <button type="button" class="me-btn-outline" id="prevBtn" style="display:none;"><i class="fas fa-arrow-left mr-1"></i> Back</button>
                <div class="ml-auto">
                    <button type="button" class="me-btn-nav" id="nextBtn">Next <i class="fas fa-arrow-right ml-1"></i></button>
                    <button type="submit" class="me-btn-nav" id="submitBtn" style="display:none;"><i class="fas fa-check mr-1"></i> Update Party</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
(function() {
    const totalSteps = 4;
    let current = 1;
    const steps = document.querySelectorAll('.me-steps li');
    const panes = document.querySelectorAll('.me-step-pane');
    const nextBtn = document.getElementById('nextBtn');
    const prevBtn = document.getElementById('prevBtn');
    const submitBtn = document.getElementById('submitBtn');

    function goToStep(n) {
        panes.forEach(p => p.classList.remove('active'));
        document.querySelector(`.me-step-pane[data-pane="${n}"]`).classList.add('active');
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
        const pane = document.querySelector(`.me-step-pane[data-pane="${n}"]`);
        const requiredFields = pane.querySelectorAll('[required]');
        for (const field of requiredFields) {
            if (!field.value) { field.reportValidity(); return false; }
        }
        return true;
    }

    nextBtn.addEventListener('click', () => { if (validateStep(current) && current < totalSteps) goToStep(current + 1); });
    prevBtn.addEventListener('click', () => { if (current > 1) goToStep(current - 1); });

    // ---------- JSON-based Cascading State -> District -> City (with pre-fill) ----------
    const stateSelect = document.getElementById('state_select');
    const districtSelect = document.getElementById('district_select');
    const citySelect = document.getElementById('city_select');
    const pincodeInput = document.getElementById('pincode');

    // Existing saved values (Blade se aa rahe hain)
    const existingState = @json(old('state', $manualParty->state));
    const existingDistrict = @json(old('district', $manualParty->district));
    const existingCity = @json(old('city', $manualParty->city));

    let locationData = [];

    function fillDistricts(stateName, selectDistrict) {
        districtSelect.innerHTML = '<option value="">Select District</option>';
        const stateObj = locationData.find(s => s.state === stateName);
        if (stateObj && stateObj.districts) {
            stateObj.districts.forEach(d => {
                const sel = d.district === selectDistrict ? 'selected' : '';
                districtSelect.innerHTML += `<option value="${d.district}" ${sel}>${d.district}</option>`;
            });
            districtSelect.disabled = false;
        }
    }

    function fillCities(stateName, districtName, selectCity) {
        citySelect.innerHTML = '<option value="">Select City</option>';
        const stateObj = locationData.find(s => s.state === stateName);
        const districtObj = stateObj ? stateObj.districts.find(d => d.district === districtName) : null;
        if (districtObj && districtObj.cities) {
            districtObj.cities.forEach(c => {
                const cityName = c.city;
                const sel = cityName === selectCity ? 'selected' : '';
                citySelect.innerHTML += `<option value="${cityName}" data-pincode="${c.pincode ?? ''}" ${sel}>${cityName}</option>`;
            });
            citySelect.disabled = false;
        }
    }

    fetch('{{ asset('assets/data/india-states-districts-cities.json') }}')
        .then(res => res.json())
        .then(data => {
            locationData = data;

            stateSelect.innerHTML = '<option value="">Select State</option>';
            data.forEach(s => {
                const sel = s.state === existingState ? 'selected' : '';
                stateSelect.innerHTML += `<option value="${s.state}" ${sel}>${s.state}</option>`;
            });

            if (existingState) {
                fillDistricts(existingState, existingDistrict);
            }
            if (existingState && existingDistrict) {
                fillCities(existingState, existingDistrict, existingCity);
            }
        })
        .catch(() => {
            stateSelect.innerHTML = '<option value="">Unable to load states</option>';
        });

    stateSelect.addEventListener('change', function () {
        citySelect.innerHTML = '<option value="">Select District First</option>';
        citySelect.disabled = true;

        if (!this.value) {
            districtSelect.innerHTML = '<option value="">Select State First</option>';
            districtSelect.disabled = true;
            return;
        }
        fillDistricts(this.value, null);
    });

    districtSelect.addEventListener('change', function () {
        if (!this.value) {
            citySelect.innerHTML = '<option value="">Select District First</option>';
            citySelect.disabled = true;
            return;
        }
        fillCities(stateSelect.value, this.value, null);
    });

    citySelect.addEventListener('change', function () {
        const selected = this.options[this.selectedIndex];
        const pin = selected ? selected.getAttribute('data-pincode') : '';
        if (pin) pincodeInput.value = pin;
    });
})();
</script>
@endsection

@extends('layouts.admin')

@section('content')
<style>
    .mf-card { border: none; border-radius: 16px; box-shadow: 0 10px 30px rgba(124,58,237,.1); overflow: hidden; }
    .mf-header { background: linear-gradient(135deg, #7C3AED 0%, #6366F1 100%); padding: 24px; color: #fff; }
    .mf-form-group label { font-weight: 600; color: #4B5563; font-size: .85rem; }
    .mf-form-group .required::after { content: ' *'; color: #EF4444; }
    .mf-btn-nav { background: linear-gradient(135deg, #7C3AED, #6366F1); border: none; color: #fff; border-radius: 8px; padding: 10px 24px; }
</style>

<div class="card mf-card animate__animated animate__fadeIn">
    <div class="mf-header">
        <h4 class="mb-0"><i class="fas fa-user-hard-hat mr-2"></i>Edit Fitter</h4>
        <small>Manual Entry &raquo; Fitter &raquo; #{{ $manualFitter->id }}</small>
    </div>

    <div class="card-body p-4">
        @if($errors->any())
            <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
        @endif

        <form action="{{ route('admin.manual-fitters.update', $manualFitter->id) }}" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')

            <h6 class="text-uppercase text-muted mb-3">Basic Details</h6>
            <div class="row">
                <div class="col-md-4 mf-form-group mb-3">
                    <label class="required">Name</label>
                    <input type="text" name="name" class="form-control" required value="{{ old('name', $manualFitter->name) }}">
                </div>
                <div class="col-md-4 mf-form-group mb-3">
                    <label class="required">Phone</label>
                    <input type="text" name="phone" class="form-control" required value="{{ old('phone', $manualFitter->phone) }}">
                </div>
                <div class="col-md-4 mf-form-group mb-3">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $manualFitter->email) }}">
                </div>
                <div class="col-md-4 mf-form-group mb-3">
                    <label>Date of Birth</label>
                    <input type="date" name="dob" class="form-control" value="{{ old('dob', optional($manualFitter->dob)->format('Y-m-d')) }}">
                </div>
                <div class="col-md-4 mf-form-group mb-3">
                    <label>Gender</label>
                    <select name="gender" class="form-control">
                        <option value="">Select</option>
                        @foreach(['Male','Female','Other'] as $g)
                            <option value="{{ $g }}" {{ old('gender', $manualFitter->gender) == $g ? 'selected' : '' }}>{{ $g }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mf-form-group mb-3">
                    <label>Photo</label>
                    @if($manualFitter->photo_path)
                        <div class="mb-2"><a href="{{ Storage::url($manualFitter->photo_path) }}" target="_blank">View current photo</a></div>
                    @endif
                    <input type="file" name="photo" class="form-control" accept="image/*">
                </div>
            </div>

            <h6 class="text-uppercase text-muted mb-3 mt-3">Contact Details</h6>
            <div class="row">
                <div class="col-md-4 mf-form-group mb-3">
                    <label>Alternate Phone</label>
                    <input type="text" name="alternate_phone" class="form-control" value="{{ old('alternate_phone', $manualFitter->alternate_phone) }}">
                </div>
                <div class="col-md-4 mf-form-group mb-3">
                    <label>WhatsApp Number</label>
                    <input type="text" name="whatsapp_number" class="form-control" value="{{ old('whatsapp_number', $manualFitter->whatsapp_number) }}">
                </div>
                <div class="col-md-4 mf-form-group mb-3">
                    <label>Aadhar Number</label>
                    <input type="text" name="aadhar_number" class="form-control" value="{{ old('aadhar_number', $manualFitter->aadhar_number) }}">
                </div>
                <div class="col-md-6 mf-form-group mb-3">
                    <label>ID Proof</label>
                    @if($manualFitter->id_proof_path)
                        <div class="mb-2"><a href="{{ Storage::url($manualFitter->id_proof_path) }}" target="_blank">View current file</a></div>
                    @endif
                    <input type="file" name="id_proof" class="form-control" accept="image/*,.pdf">
                </div>
                <div class="col-md-6 mf-form-group mb-3">
                    <label>Landmark</label>
                    <input type="text" name="landmark" class="form-control" value="{{ old('landmark', $manualFitter->landmark) }}">
                </div>
                <div class="col-md-12 mf-form-group mb-3">
                    <label>Address</label>
                    <textarea name="address" rows="2" class="form-control">{{ old('address', $manualFitter->address) }}</textarea>
                </div>
            </div>

            <h6 class="text-uppercase text-muted mb-3 mt-3">Address Details</h6>
            <div class="row">
                <div class="col-md-3 mf-form-group mb-3">
                    <label class="required">State</label>
                    <select name="state" id="stateSelect" class="form-control" required data-selected="{{ old('state', $manualFitter->state) }}">
                        <option value="">Select State</option>
                    </select>
                </div>
                <div class="col-md-3 mf-form-group mb-3">
                    <label class="required">District</label>
                    <select name="district" id="districtSelect" class="form-control" required disabled data-selected="{{ old('district', $manualFitter->district) }}">
                        <option value="">Select District</option>
                    </select>
                </div>
                <div class="col-md-3 mf-form-group mb-3">
                    <label class="required">City</label>
                    <select name="city" id="citySelect" class="form-control" required disabled data-selected="{{ old('city', $manualFitter->city) }}">
                        <option value="">Select City</option>
                    </select>
                </div>
                <div class="col-md-3 mf-form-group mb-3">
                    <label class="required">Pincode</label>
                    <input type="text" name="pincode" id="pincodeInput" class="form-control" required value="{{ old('pincode', $manualFitter->pincode) }}">
                </div>
                <div class="col-md-4 mf-form-group mb-3">
                    <label>Status</label>
                    <select name="status" class="form-control">
                        <option value="active" {{ old('status', $manualFitter->status) == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $manualFitter->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
            </div>

            <div class="mt-3">
                <button type="submit" class="mf-btn-nav"><i class="fas fa-check mr-1"></i> Update Fitter</button>
                <a href="{{ route('admin.manual-fitters.index') }}" class="btn btn-light ml-2">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
(function () {
    const stateSelect = document.getElementById('stateSelect');
    const districtSelect = document.getElementById('districtSelect');
    const citySelect = document.getElementById('citySelect');
    const pincodeInput = document.getElementById('pincodeInput');
    let locationData = [];

    function fillDistricts(stateName, preselectDistrict) {
        districtSelect.innerHTML = '<option value="">Select District</option>';
        const stateObj = locationData.find(s => s.state === stateName);
        if (!stateObj) { districtSelect.disabled = true; return; }
        stateObj.districts.forEach(d => {
            const sel = d.district === preselectDistrict ? 'selected' : '';
            districtSelect.innerHTML += `<option value="${d.district}" ${sel}>${d.district}</option>`;
        });
        districtSelect.disabled = false;
    }

    function fillCities(stateName, districtName, preselectCity) {
        citySelect.innerHTML = '<option value="">Select City</option>';
        const stateObj = locationData.find(s => s.state === stateName);
        const districtObj = stateObj ? stateObj.districts.find(d => d.district === districtName) : null;
        if (!districtObj) { citySelect.disabled = true; return; }
        districtObj.cities.forEach(c => {
            const sel = c.city === preselectCity ? 'selected' : '';
            citySelect.innerHTML += `<option value="${c.city}" data-pincode="${c.pincode}" ${sel}>${c.city}</option>`;
        });
        citySelect.disabled = false;
    }

    fetch("{{ asset('assets/data/india-states-districts-cities.json') }}")
        .then(res => res.json())
        .then(data => {
            locationData = data;
            const selectedState = stateSelect.dataset.selected;
            const selectedDistrict = districtSelect.dataset.selected;
            const selectedCity = citySelect.dataset.selected;

            data.forEach(s => {
                const sel = s.state === selectedState ? 'selected' : '';
                stateSelect.innerHTML += `<option value="${s.state}" ${sel}>${s.state}</option>`;
            });

            if (selectedState) {
                fillDistricts(selectedState, selectedDistrict);
            }
            if (selectedState && selectedDistrict) {
                fillCities(selectedState, selectedDistrict, selectedCity);
            }
        })
        .catch(err => console.error('Location data load failed', err));

    stateSelect.addEventListener('change', function () {
        pincodeInput.value = '';
        fillDistricts(this.value, null);
        citySelect.innerHTML = '<option value="">Select City</option>';
        citySelect.disabled = true;
    });

    districtSelect.addEventListener('change', function () {
        pincodeInput.value = '';
        fillCities(stateSelect.value, this.value, null);
    });

    citySelect.addEventListener('change', function () {
        const selected = this.options[this.selectedIndex];
        pincodeInput.value = selected ? (selected.dataset.pincode || '') : pincodeInput.value;
    });
})();
</script>
@endsection

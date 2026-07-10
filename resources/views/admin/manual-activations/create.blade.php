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
    .me-doc-row { border: 1px dashed #C4B5FD; border-radius: 10px; padding: 12px; margin-bottom: 10px; background: #FAF5FF; }
    .me-add-doc { border: 1px dashed #7C3AED; color: #6D28D9; background: #fff; border-radius: 8px; padding: 8px 16px; }
    @media (max-width: 576px) { .me-steps li span { display: none; } }
</style>

<div class="card me-wizard-card animate__animated animate__fadeIn">
    <div class="me-wizard-header">
        <h4 class="mb-0"><i class="fas fa-bolt mr-2"></i>New Activation</h4>
        <small>Manual Entry &raquo; Activation &raquo; Naya activation entry karein</small>
        <ul class="me-steps">
            <li class="active" data-step="1"><div class="circle">1</div><span>Party & Product</span></li>
            <li data-step="2"><div class="circle">2</div><span>Customer</span></li>
            <li data-step="3"><div class="circle">3</div><span>Vehicle</span></li>
            <li data-step="4"><div class="circle">4</div><span>Documents</span></li>
        </ul>
    </div>

    <div class="card-body p-4">
        @if($errors->any())
            <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
        @endif

        <form action="{{ route('admin.manual-activations.store') }}" method="POST" enctype="multipart/form-data" id="activationForm">
            @csrf

            {{-- STEP 1: Party, Fitter, Product, Fitting Date --}}
            <div class="me-step-pane active" data-pane="1">
                <div class="row">
                    <div class="col-md-3 me-form-group mb-3">
                        <label class="required">Party</label>
                        <select name="manual_party_id" id="partySelect" class="form-control" required>
                            <option value="">Select Party</option>
                            @foreach($parties as $party)
                                <option value="{{ $party->id }}" {{ old('manual_party_id') == $party->id ? 'selected' : '' }}>{{ $party->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 me-form-group mb-3" id="fitterWrapper" style="{{ old('manual_party_id') ? '' : 'display:none;' }}">
                        <label class="required">Manual Fitter</label>
                        <select name="manual_fitter_id" id="fitterSelect" class="form-control" required {{ old('manual_party_id') ? '' : 'disabled' }}>
                            <option value="">Select Fitter</option>
                            @foreach($fitters as $fitter)
                                <option value="{{ $fitter->id }}" {{ old('manual_fitter_id') == $fitter->id ? 'selected' : '' }}>{{ $fitter->name }}{{ $fitter->phone ? ' - '.$fitter->phone : '' }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 me-form-group mb-3">
                        <label class="required">Product</label>
                        <select name="manual_product_id" class="form-control" required>
                            <option value="">Select Product</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" {{ old('manual_product_id') == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 me-form-group mb-3">
                        <label class="required">Fitting Date</label>
                        <input type="date" name="fitting_date" class="form-control" required value="{{ old('fitting_date', date('Y-m-d')) }}">
                    </div>
                </div>
            </div>

            {{-- STEP 2: Customer Details --}}
            <div class="me-step-pane" data-pane="2">
                <div class="row">
                    <div class="col-md-4 me-form-group mb-3">
                        <label>Customer Name</label>
                        <input type="text" name="customer_name" class="form-control" value="{{ old('customer_name') }}">
                    </div>
                    <div class="col-md-4 me-form-group mb-3">
                        <label>Customer Phone</label>
                        <input type="text" name="customer_phone" class="form-control" value="{{ old('customer_phone') }}">
                    </div>
                    <div class="col-md-4 me-form-group mb-3">
                        <label>Customer Email</label>
                        <input type="email" name="customer_email" class="form-control" value="{{ old('customer_email') }}">
                    </div>
                    <div class="col-md-12 me-form-group mb-3">
                        <label>Customer Address</label>
                        <textarea name="customer_address" rows="2" class="form-control">{{ old('customer_address') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- STEP 3: Vehicle Details --}}
            <div class="me-step-pane" data-pane="3">
                <div class="row">
                    <div class="col-md-4 me-form-group mb-3">
                        <label>Vehicle Number</label>
                        <input type="text" name="vehicle_number" class="form-control" value="{{ old('vehicle_number') }}">
                    </div>
                    <div class="col-md-4 me-form-group mb-3">
                        <label>Vehicle Model</label>
                        <input type="text" name="vehicle_model" class="form-control" value="{{ old('vehicle_model') }}">
                    </div>
                    <div class="col-md-4 me-form-group mb-3">
                        <label>Vehicle Color</label>
                        <input type="text" name="vehicle_color" class="form-control" value="{{ old('vehicle_color') }}">
                    </div>
                    <div class="col-md-6 me-form-group mb-3">
                        <label>Chassis Number</label>
                        <input type="text" name="vehicle_chassis_number" class="form-control" value="{{ old('vehicle_chassis_number') }}">
                    </div>
                    <div class="col-md-6 me-form-group mb-3">
                        <label>Engine Number</label>
                        <input type="text" name="vehicle_engine_number" class="form-control" value="{{ old('vehicle_engine_number') }}">
                    </div>
                </div>
            </div>

            {{-- STEP 4: Documents --}}
            <div class="me-step-pane" data-pane="4">
                <div class="row">
                    <div class="col-md-6 me-form-group mb-3">
                        <label>Aadhar Card - Front</label>
                        <input type="file" name="aadhar_front" class="form-control" accept="image/*,.pdf">
                    </div>
                    <div class="col-md-6 me-form-group mb-3">
                        <label>Aadhar Card - Back</label>
                        <input type="file" name="aadhar_back" class="form-control" accept="image/*,.pdf">
                    </div>
                </div>

                <hr>
                <label class="d-block mb-2" style="font-weight:600; color:#4B5563;">Extra Documents (optional)</label>
                <div id="documentsWrapper"></div>
                <button type="button" class="me-add-doc" id="addDocBtn"><i class="fas fa-plus mr-1"></i> Add Document</button>
            </div>

            <div class="d-flex justify-content-between mt-4">
                <button type="button" class="me-btn-outline" id="prevBtn" style="display:none;"><i class="fas fa-arrow-left mr-1"></i> Back</button>
                <div class="ml-auto">
                    <button type="button" class="me-btn-nav" id="nextBtn">Next <i class="fas fa-arrow-right ml-1"></i></button>
                    <button type="submit" class="me-btn-nav" id="submitBtn" style="display:none;"><i class="fas fa-check mr-1"></i> Save Activation</button>
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
            if (field.disabled) continue;
            if (!field.value) { field.reportValidity(); return false; }
        }
        return true;
    }

    nextBtn.addEventListener('click', () => { if (validateStep(current) && current < totalSteps) goToStep(current + 1); });
    prevBtn.addEventListener('click', () => { if (current > 1) goToStep(current - 1); });

    // Reveal Manual Fitter select only after a Party has been chosen
    const partySelect = document.getElementById('partySelect');
    const fitterWrapper = document.getElementById('fitterWrapper');
    const fitterSelect = document.getElementById('fitterSelect');
    partySelect.addEventListener('change', function () {
        if (this.value) {
            fitterWrapper.style.display = 'block';
            fitterSelect.disabled = false;
        } else {
            fitterWrapper.style.display = 'none';
            fitterSelect.disabled = true;
            fitterSelect.value = '';
        }
    });

    // Dynamic extra documents repeater
    let docIndex = 0;
    const wrapper = document.getElementById('documentsWrapper');
    document.getElementById('addDocBtn').addEventListener('click', () => {
        const row = document.createElement('div');
        row.className = 'me-doc-row row align-items-end animate__animated animate__fadeIn';
        row.innerHTML = `
            <div class="col-md-5 mb-2">
                <label style="font-size:.8rem; font-weight:600;">Document Name</label>
                <input type="text" name="document_names[${docIndex}]" class="form-control" placeholder="e.g. Driving Licence">
            </div>
            <div class="col-md-5 mb-2">
                <label style="font-size:.8rem; font-weight:600;">File</label>
                <input type="file" name="document_files[${docIndex}]" class="form-control" accept="image/*,.pdf">
            </div>
            <div class="col-md-2 mb-2">
                <button type="button" class="btn btn-outline-danger btn-block remove-doc"><i class="fas fa-trash"></i></button>
            </div>`;
        wrapper.appendChild(row);
        row.querySelector('.remove-doc').addEventListener('click', () => row.remove());
        docIndex++;
    });
})();
</script>
@endsection

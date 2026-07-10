@extends('layouts.admin')

@section('content')
<style>
    .me-wizard-card { border: none; border-radius: 16px; box-shadow: 0 10px 30px rgba(124,58,237,.1); overflow: hidden; }
    .me-wizard-header { background: linear-gradient(135deg, #7C3AED 0%, #6366F1 100%); padding: 24px; color: #fff; }
    .me-form-group label { font-weight: 600; color: #4B5563; font-size: .85rem; }
    .me-form-group .required::after { content: ' *'; color: #EF4444; }
    .me-btn-nav { background: linear-gradient(135deg, #7C3AED, #6366F1); border: none; color: #fff; border-radius: 8px; padding: 10px 24px; }
    .me-doc-chip { display: inline-flex; align-items: center; gap: 8px; background: #F5F3FF; border-radius: 20px; padding: 6px 14px; margin: 4px; font-size: .8rem; }
</style>

<div class="card me-wizard-card animate__animated animate__fadeIn">
    <div class="me-wizard-header">
        <h4 class="mb-0"><i class="fas fa-bolt mr-2"></i>Edit Activation</h4>
        <small>Manual Entry &raquo; Activation &raquo; #{{ $manualActivation->id }}</small>
    </div>

    <div class="card-body p-4">
        @if($errors->any())
            <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
        @endif

        <form action="{{ route('admin.manual-activations.update', $manualActivation->id) }}" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')

            <h6 class="text-uppercase text-muted mb-3">Party, Fitter & Product</h6>
            <div class="row">
                <div class="col-md-3 me-form-group mb-3">
                    <label class="required">Party</label>
                    <select name="manual_party_id" class="form-control" required>
                        @foreach($parties as $party)
                            <option value="{{ $party->id }}" {{ old('manual_party_id', $manualActivation->manual_party_id) == $party->id ? 'selected' : '' }}>{{ $party->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 me-form-group mb-3">
                    <label class="required">Manual Fitter</label>
                    <select name="manual_fitter_id" class="form-control" required>
                        <option value="">Select Fitter</option>
                        @foreach($fitters as $fitter)
                            <option value="{{ $fitter->id }}" {{ old('manual_fitter_id', $manualActivation->manual_fitter_id) == $fitter->id ? 'selected' : '' }}>{{ $fitter->name }}{{ $fitter->phone ? ' - '.$fitter->phone : '' }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 me-form-group mb-3">
                    <label class="required">Product</label>
                    <select name="manual_product_id" class="form-control" required>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" {{ old('manual_product_id', $manualActivation->manual_product_id) == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 me-form-group mb-3">
                    <label class="required">Fitting Date</label>
                    <input type="date" name="fitting_date" class="form-control" required value="{{ old('fitting_date', optional($manualActivation->fitting_date)->format('Y-m-d')) }}">
                </div>
            </div>

            <h6 class="text-uppercase text-muted mb-3 mt-3">Customer Details</h6>
            <div class="row">
                <div class="col-md-4 me-form-group mb-3">
                    <label>Customer Name</label>
                    <input type="text" name="customer_name" class="form-control" value="{{ old('customer_name', $manualActivation->customer_name) }}">
                </div>
                <div class="col-md-4 me-form-group mb-3">
                    <label>Customer Phone</label>
                    <input type="text" name="customer_phone" class="form-control" value="{{ old('customer_phone', $manualActivation->customer_phone) }}">
                </div>
                <div class="col-md-4 me-form-group mb-3">
                    <label>Customer Email</label>
                    <input type="email" name="customer_email" class="form-control" value="{{ old('customer_email', $manualActivation->customer_email) }}">
                </div>
                <div class="col-md-12 me-form-group mb-3">
                    <label>Customer Address</label>
                    <textarea name="customer_address" rows="2" class="form-control">{{ old('customer_address', $manualActivation->customer_address) }}</textarea>
                </div>
            </div>

            <h6 class="text-uppercase text-muted mb-3 mt-3">Vehicle Details</h6>
            <div class="row">
                <div class="col-md-4 me-form-group mb-3">
                    <label>Vehicle Number</label>
                    <input type="text" name="vehicle_number" class="form-control" value="{{ old('vehicle_number', $manualActivation->vehicle_number) }}">
                </div>
                <div class="col-md-4 me-form-group mb-3">
                    <label>Vehicle Model</label>
                    <input type="text" name="vehicle_model" class="form-control" value="{{ old('vehicle_model', $manualActivation->vehicle_model) }}">
                </div>
                <div class="col-md-4 me-form-group mb-3">
                    <label>Vehicle Color</label>
                    <input type="text" name="vehicle_color" class="form-control" value="{{ old('vehicle_color', $manualActivation->vehicle_color) }}">
                </div>
                <div class="col-md-6 me-form-group mb-3">
                    <label>Chassis Number</label>
                    <input type="text" name="vehicle_chassis_number" class="form-control" value="{{ old('vehicle_chassis_number', $manualActivation->vehicle_chassis_number) }}">
                </div>
                <div class="col-md-6 me-form-group mb-3">
                    <label>Engine Number</label>
                    <input type="text" name="vehicle_engine_number" class="form-control" value="{{ old('vehicle_engine_number', $manualActivation->vehicle_engine_number) }}">
                </div>
            </div>

            <h6 class="text-uppercase text-muted mb-3 mt-3">Documents</h6>
            <div class="row">
                <div class="col-md-6 me-form-group mb-3">
                    <label>Aadhar Card - Front</label>
                    @if($manualActivation->aadhar_front_path)
                        <div class="mb-2"><a href="{{ Storage::url($manualActivation->aadhar_front_path) }}" target="_blank">View current file</a></div>
                    @endif
                    <input type="file" name="aadhar_front" class="form-control" accept="image/*,.pdf">
                </div>
                <div class="col-md-6 me-form-group mb-3">
                    <label>Aadhar Card - Back</label>
                    @if($manualActivation->aadhar_back_path)
                        <div class="mb-2"><a href="{{ Storage::url($manualActivation->aadhar_back_path) }}" target="_blank">View current file</a></div>
                    @endif
                    <input type="file" name="aadhar_back" class="form-control" accept="image/*,.pdf">
                </div>
            </div>

            @if($manualActivation->documents->count())
                <div class="mb-3">
                    <label style="font-weight:600; color:#4B5563;">Existing Extra Documents</label><br>
                    @foreach($manualActivation->documents as $doc)
                        <span class="me-doc-chip">
                            <a href="{{ Storage::url($doc->file_path) }}" target="_blank">{{ $doc->document_name }}</a>
                            <button type="button" class="btn btn-sm btn-link text-danger p-0 delete-doc" data-id="{{ $doc->id }}">&times;</button>
                        </span>
                    @endforeach
                </div>
            @endif

            <div id="documentsWrapper"></div>
            <button type="button" class="btn btn-outline-primary btn-sm mb-3" id="addDocBtn"><i class="fas fa-plus mr-1"></i> Add More Document</button>

            <div class="mt-3">
                <button type="submit" class="me-btn-nav"><i class="fas fa-check mr-1"></i> Update Activation</button>
                <a href="{{ route('admin.manual-activations.index') }}" class="btn btn-light ml-2">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
(function () {
    let docIndex = 0;
    const wrapper = document.getElementById('documentsWrapper');
    document.getElementById('addDocBtn').addEventListener('click', () => {
        const row = document.createElement('div');
        row.className = 'row align-items-end mb-2';
        row.innerHTML = `
            <div class="col-md-5"><input type="text" name="document_names[${docIndex}]" class="form-control" placeholder="Document Name"></div>
            <div class="col-md-5"><input type="file" name="document_files[${docIndex}]" class="form-control" accept="image/*,.pdf"></div>
            <div class="col-md-2"><button type="button" class="btn btn-outline-danger btn-block remove-doc"><i class="fas fa-trash"></i></button></div>`;
        wrapper.appendChild(row);
        row.querySelector('.remove-doc').addEventListener('click', () => row.remove());
        docIndex++;
    });

    document.querySelectorAll('.delete-doc').forEach(btn => {
        btn.addEventListener('click', function () {
            if (!confirm('Delete this document?')) return;
            const id = this.dataset.id;
            fetch(`{{ url('admin/manual-activations/documents') }}/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            }).then(() => this.closest('.me-doc-chip').remove());
        });
    });
})();
</script>
@endsection

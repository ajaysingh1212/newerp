@extends('layouts.admin')

@section('content')
<style>
    .me-card { border: none; border-radius: 14px; box-shadow: 0 6px 24px rgba(124,58,237,.08); }
    .me-header { background: linear-gradient(135deg, #7C3AED 0%, #6366F1 100%); padding: 20px 24px; color: #fff; border-radius: 14px 14px 0 0; }
    .me-info-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px dashed #E5E7EB; }
    .me-info-row .label { color: #6B7280; font-weight: 600; font-size: .85rem; }
    .me-info-row .value { color: #111827; font-weight: 500; }
    .me-doc-chip { display: inline-flex; align-items: center; gap: 8px; background: #F5F3FF; border-radius: 20px; padding: 6px 14px; margin: 4px; font-size: .8rem; }
</style>

<div class="card me-card animate__animated animate__fadeIn">
    <div class="me-header">
        <h4 class="mb-0"><i class="fas fa-bolt mr-2"></i>Activation #{{ $manualActivation->id }}</h4>
        <small>{{ optional($manualActivation->fitting_date)->format('d-m-Y') }}</small>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h6 class="text-uppercase text-muted mb-2">Party & Product</h6>
                <div class="me-info-row"><span class="label">Party</span><span class="value">{{ optional($manualActivation->party)->name }}</span></div>
                <div class="me-info-row"><span class="label">Product</span><span class="value">{{ optional($manualActivation->product)->name }}</span></div>

                <h6 class="text-uppercase text-muted mb-2 mt-4">Customer</h6>
                <div class="me-info-row"><span class="label">Name</span><span class="value">{{ $manualActivation->customer_name ?? '-' }}</span></div>
                <div class="me-info-row"><span class="label">Phone</span><span class="value">{{ $manualActivation->customer_phone ?? '-' }}</span></div>
                <div class="me-info-row"><span class="label">Email</span><span class="value">{{ $manualActivation->customer_email ?? '-' }}</span></div>
                <div class="me-info-row"><span class="label">Address</span><span class="value">{{ $manualActivation->customer_address ?? '-' }}</span></div>
            </div>
            <div class="col-md-6">
                <h6 class="text-uppercase text-muted mb-2">Vehicle</h6>
                <div class="me-info-row"><span class="label">Number</span><span class="value">{{ $manualActivation->vehicle_number ?? '-' }}</span></div>
                <div class="me-info-row"><span class="label">Model</span><span class="value">{{ $manualActivation->vehicle_model ?? '-' }}</span></div>
                <div class="me-info-row"><span class="label">Color</span><span class="value">{{ $manualActivation->vehicle_color ?? '-' }}</span></div>
                <div class="me-info-row"><span class="label">Chassis No.</span><span class="value">{{ $manualActivation->vehicle_chassis_number ?? '-' }}</span></div>
                <div class="me-info-row"><span class="label">Engine No.</span><span class="value">{{ $manualActivation->vehicle_engine_number ?? '-' }}</span></div>

                <h6 class="text-uppercase text-muted mb-2 mt-4">Documents</h6>
                @if($manualActivation->aadhar_front_path)
                    <span class="me-doc-chip"><a href="{{ Storage::url($manualActivation->aadhar_front_path) }}" target="_blank">Aadhar Front</a></span>
                @endif
                @if($manualActivation->aadhar_back_path)
                    <span class="me-doc-chip"><a href="{{ Storage::url($manualActivation->aadhar_back_path) }}" target="_blank">Aadhar Back</a></span>
                @endif
                @foreach($manualActivation->documents as $doc)
                    <span class="me-doc-chip"><a href="{{ Storage::url($doc->file_path) }}" target="_blank">{{ $doc->document_name }}</a></span>
                @endforeach
            </div>
        </div>

        <a href="{{ route('admin.manual-activations.index') }}" class="btn btn-secondary mt-3"><i class="fas fa-arrow-left mr-1"></i> Back to Dashboard</a>
    </div>
</div>
@endsection

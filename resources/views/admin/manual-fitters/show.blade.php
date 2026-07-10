@extends('layouts.admin')

@section('content')
<style>
    .mf-card { border: none; border-radius: 14px; box-shadow: 0 6px 24px rgba(124,58,237,.08); }
    .mf-header { background: linear-gradient(135deg, #7C3AED 0%, #6366F1 100%); padding: 20px 24px; color: #fff; border-radius: 14px 14px 0 0; }
    .mf-info-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px dashed #E5E7EB; }
    .mf-info-row .label { color: #6B7280; font-weight: 600; font-size: .85rem; }
    .mf-info-row .value { color: #111827; font-weight: 500; }
    .mf-badge-active { background: #A7F3D0; color: #065F46; padding: 4px 12px; border-radius: 12px; font-size: .75rem; font-weight: 700; }
    .mf-badge-inactive { background: #FECACA; color: #991B1B; padding: 4px 12px; border-radius: 12px; font-size: .75rem; font-weight: 700; }
</style>

<div class="card mf-card animate__animated animate__fadeIn">
    <div class="mf-header d-flex justify-content-between align-items-center">
        <div>
            <h4 class="mb-0"><i class="fas fa-user-hard-hat mr-2"></i>{{ $manualFitter->name }}</h4>
            <small>Fitter #{{ $manualFitter->id }}</small>
        </div>
        <span class="{{ $manualFitter->status == 'active' ? 'mf-badge-active' : 'mf-badge-inactive' }}">
            {{ ucfirst($manualFitter->status) }}
        </span>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h6 class="text-uppercase text-muted mb-2">Basic Details</h6>
                <div class="mf-info-row"><span class="label">Name</span><span class="value">{{ $manualFitter->name }}</span></div>
                <div class="mf-info-row"><span class="label">Phone</span><span class="value">{{ $manualFitter->phone }}</span></div>
                <div class="mf-info-row"><span class="label">Email</span><span class="value">{{ $manualFitter->email ?? '-' }}</span></div>
                <div class="mf-info-row"><span class="label">DOB</span><span class="value">{{ optional($manualFitter->dob)->format('d-m-Y') ?? '-' }}</span></div>
                <div class="mf-info-row"><span class="label">Gender</span><span class="value">{{ $manualFitter->gender ?? '-' }}</span></div>

                <h6 class="text-uppercase text-muted mb-2 mt-4">Contact Details</h6>
                <div class="mf-info-row"><span class="label">Alternate Phone</span><span class="value">{{ $manualFitter->alternate_phone ?? '-' }}</span></div>
                <div class="mf-info-row"><span class="label">WhatsApp</span><span class="value">{{ $manualFitter->whatsapp_number ?? '-' }}</span></div>
                <div class="mf-info-row"><span class="label">Aadhar No.</span><span class="value">{{ $manualFitter->aadhar_number ?? '-' }}</span></div>
                <div class="mf-info-row"><span class="label">Landmark</span><span class="value">{{ $manualFitter->landmark ?? '-' }}</span></div>
                <div class="mf-info-row"><span class="label">Address</span><span class="value">{{ $manualFitter->address ?? '-' }}</span></div>
            </div>
            <div class="col-md-6">
                <h6 class="text-uppercase text-muted mb-2">Address Details</h6>
                <div class="mf-info-row"><span class="label">State</span><span class="value">{{ $manualFitter->state }}</span></div>
                <div class="mf-info-row"><span class="label">District</span><span class="value">{{ $manualFitter->district }}</span></div>
                <div class="mf-info-row"><span class="label">City</span><span class="value">{{ $manualFitter->city }}</span></div>
                <div class="mf-info-row"><span class="label">Pincode</span><span class="value">{{ $manualFitter->pincode }}</span></div>

                <h6 class="text-uppercase text-muted mb-2 mt-4">Documents</h6>
                @if($manualFitter->photo_path)
                    <div class="mb-2"><a href="{{ Storage::url($manualFitter->photo_path) }}" target="_blank"><i class="fas fa-image mr-1"></i> Photo</a></div>
                @endif
                @if($manualFitter->id_proof_path)
                    <div class="mb-2"><a href="{{ Storage::url($manualFitter->id_proof_path) }}" target="_blank"><i class="fas fa-file-alt mr-1"></i> ID Proof</a></div>
                @endif
                @if(!$manualFitter->photo_path && !$manualFitter->id_proof_path)
                    <div class="text-muted">No documents uploaded.</div>
                @endif
            </div>
        </div>

        <a href="{{ route('admin.manual-fitters.index') }}" class="btn btn-secondary mt-3"><i class="fas fa-arrow-left mr-1"></i> Back to List</a>
    </div>
</div>
@endsection

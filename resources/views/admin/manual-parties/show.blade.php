@extends('layouts.admin')

@section('content')
<style>
    .me-card { border: none; border-radius: 14px; box-shadow: 0 6px 24px rgba(124,58,237,.08); }
    .me-header { background: linear-gradient(135deg, #7C3AED 0%, #6366F1 100%); padding: 20px 24px; color: #fff; border-radius: 14px 14px 0 0; }
    .me-info-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px dashed #E5E7EB; }
    .me-info-row .label { color: #6B7280; font-weight: 600; font-size: .85rem; }
    .me-info-row .value { color: #111827; font-weight: 500; }
</style>

<div class="card me-card animate__animated animate__fadeIn">
    <div class="me-header">
        <h4 class="mb-0"><i class="fas fa-user-tie mr-2"></i>{{ $manualParty->name }}</h4>
        <small>Party Details</small>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h6 class="text-uppercase text-muted mb-2">Basic Info</h6>
                <div class="me-info-row"><span class="label">Phone</span><span class="value">{{ $manualParty->phone }}</span></div>
                <div class="me-info-row"><span class="label">Email</span><span class="value">{{ $manualParty->email ?? '-' }}</span></div>
                <div class="me-info-row"><span class="label">Status</span><span class="value">{{ ucfirst($manualParty->status) }}</span></div>

                <h6 class="text-uppercase text-muted mb-2 mt-4">Location</h6>
                <div class="me-info-row"><span class="label">State</span><span class="value">{{ $manualParty->state ?? '-' }}</span></div>
                <div class="me-info-row"><span class="label">District</span><span class="value">{{ $manualParty->district ?? '-' }}</span></div>
                <div class="me-info-row"><span class="label">City</span><span class="value">{{ $manualParty->city ?? '-' }}</span></div>
                <div class="me-info-row"><span class="label">Pincode</span><span class="value">{{ $manualParty->pincode ?? '-' }}</span></div>
                <div class="me-info-row"><span class="label">Address</span><span class="value">{{ $manualParty->address ?? '-' }}</span></div>
            </div>
            <div class="col-md-6">
                <h6 class="text-uppercase text-muted mb-2">GST / PAN</h6>
                <div class="me-info-row"><span class="label">GST Number</span><span class="value">{{ $manualParty->gst_number ?? '-' }}</span></div>
                <div class="me-info-row"><span class="label">PAN Number</span><span class="value">{{ $manualParty->pan_number ?? '-' }}</span></div>

                <h6 class="text-uppercase text-muted mb-2 mt-4">Account Details</h6>
                <div class="me-info-row"><span class="label">Bank Name</span><span class="value">{{ $manualParty->bank_name ?? '-' }}</span></div>
                <div class="me-info-row"><span class="label">Account Holder</span><span class="value">{{ $manualParty->account_holder_name ?? '-' }}</span></div>
                <div class="me-info-row"><span class="label">Account Number</span><span class="value">{{ $manualParty->account_number ?? '-' }}</span></div>
                <div class="me-info-row"><span class="label">IFSC</span><span class="value">{{ $manualParty->ifsc_code ?? '-' }}</span></div>
                <div class="me-info-row"><span class="label">Branch</span><span class="value">{{ $manualParty->branch_name ?? '-' }}</span></div>
            </div>
        </div>

        @if($manualParty->activations->count())
            <h6 class="text-uppercase text-muted mb-2 mt-4">Recent Activations</h6>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead><tr><th>Date</th><th>Product</th><th>Customer</th><th>Vehicle No.</th></tr></thead>
                    <tbody>
                        @foreach($manualParty->activations as $a)
                            <tr>
                                <td>{{ optional($a->fitting_date)->format('d-m-Y') }}</td>
                                <td>{{ optional($a->product)->name }}</td>
                                <td>{{ $a->customer_name }}</td>
                                <td>{{ $a->vehicle_number }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <a href="{{ route('admin.manual-parties.index') }}" class="btn btn-secondary mt-3"><i class="fas fa-arrow-left mr-1"></i> Back</a>
    </div>
</div>
@endsection

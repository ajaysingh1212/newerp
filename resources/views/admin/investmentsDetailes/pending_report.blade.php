@extends('layouts.admin')

@section('content')

<style>
    .table thead { background:#f3f4f6; font-weight:600; }
    .section-title { font-size:18px; font-weight:600; margin-top:20px; }
    .badge-active { background:#4ade80; color:#064e3a; }
    .badge-pending { background:#fbbf24; color:#92400e; }
</style>
<style>
/* =====================================================
   PERFECT UNIVERSAL PRINT FIX – WORKS WITH ADMIN LAYOUT
===================================================== */

@media print {

    /* Hide EVERYTHING from layout (sidebar/header/footer) */
    body * {
        visibility: hidden !important;
    }

    /* Show only printArea properly */
    #printArea, #printArea * {
        visibility: visible !important;
    }

    /* Make printArea full page */
    #printArea {
        position: absolute;
        top: 0;
        left: 0;
        width: 100% !important;
        padding: 0 !important;
        margin: 0 !important;
        background: white !important;
    }

    /* Remove container-fluid width limit */
    .container-fluid {
        width: 100% !important;
        max-width: 100% !important;
        padding: 0 !important;
        margin: 0 !important;
    }

    /* PRINT CARD SHOULD BE FULL WIDTH */
    .print-card {
        width: 100% !important;
        max-width: 100% !important;
        margin: 0 0 20px 0 !important;
        page-break-after: always !important;
    }

    /* Fix tables */
    table {
        width: 100% !important;
        border-collapse: collapse !important;
    }

    th, td {
        border: 1px solid #ccc !important;
        padding: 6px !important;
        font-size: 12px !important;
    }

    /* Keep header colors */
    .card-header {
        background: #0d6efd !important;
        color: white !important;
        -webkit-print-color-adjust: exact !important;
    }

    .table thead {
        background: #e5e7eb !important;
        -webkit-print-color-adjust: exact !important;
    }

    .badge-active {
        background:#4ade80 !important;
        -webkit-print-color-adjust: exact !important;
    }

    .badge-pending {
        background:#fbbf24 !important;
        -webkit-print-color-adjust: exact !important;
    }

    /* Hide print button */
    .no-print {
        display: none !important;
    }

    /* A4 page settings */
    @page {
        size: A4;
        margin: 10mm;
    }
}
</style>
@php
    // Group by Registration ID
    $registrationIds = $registrations->pluck('id');
@endphp

<div id="printArea" class="container-fluid">

@foreach($registrationIds as $regId)

    @php
        $reg = $registrations->where('id', $regId)->first();
        $investorName = $reg->investor->name ?? 'Unknown';

        $invRows = $investments->where('select_investor_id', $regId);
        $wdRows  = $withdrawals->where('select_investor_id', $regId);
    @endphp

    <div class="card shadow-lg mb-4 border-0 print-card">
        <div class="card-header bg-primary text-white">
            <h4 class="m-0">
                👤 Investor: {{ $investorName }}
                <small class="text-light">(Registration ID: {{ $regId }})</small>
            </h4>
        </div>

        <div class="card-body">

            <!-- REGISTRATION DETAILS -->
            <h5 class="section-title text-primary">🧍 Registration Details</h5>

            <table class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th>Reg ID</th>
                    <th>Aadhaar</th>
                    <th>PAN</th>
                    <th>KYC</th>
                    <th>Bank Acc No</th>
                    <th>IFSC</th>
                    <th>Bank</th>
                    <th>Branch</th>
                </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $reg->id }} - {{ $reg->reg }}</td>
                        <td>{{ $reg->aadhaar_number }}</td>
                        <td>{{ $reg->pan_number }}</td>
                        <td><span class="badge badge-active">{{ $reg->kyc_status }}</span></td>
                        <td>{{ $reg->bank_account_number }}</td>
                        <td>{{ $reg->ifsc_code }}</td>
                        <td>{{ $reg->bank_name }}</td>
                        <td>{{ $reg->bank_branch }}</td>
                    </tr>
                </tbody>
            </table>

            <!-- INVESTMENT DETAILS -->
            <h5 class="section-title text-primary">💼 Investment Details</h5>

            <table class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th>Inv ID</th>
                    <th>Principal</th>
                    <th>Status</th>
                    <th>Start Date</th>
                </tr>
                </thead>
                <tbody>
                @forelse($invRows as $i)
                    <tr>
                        <td>{{ $i->id }}</td>
                        <td>₹{{ number_format($i->principal_amount) }}</td>
                        <td>{{ $i->status }}</td>
                        <td>{{ $i->start_date }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center">No Investments</td></tr>
                @endforelse
                </tbody>
            </table>

            <!-- WITHDRAWALS -->
            <h5 class="section-title text-primary">💸 Pending Withdrawals</h5>

            <table class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th>WD ID</th>
                    <th>Amount</th>
                    <th>Type</th>
                    <th>Requested At</th>
                    <th>Status</th>
                </tr>
                </thead>
                <tbody>
                @forelse($wdRows as $w)
                    <tr>
                        <td>{{ $w->id }}</td>
                        <td>₹{{ number_format($w->amount) }}</td>
                        <td>{{ $w->type }}</td>
                        <td>{{ $w->requested_at }}</td>
                        <td><span class="badge badge-pending">{{ $w->status }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center">No Pending Withdrawals</td></tr>
                @endforelse
                </tbody>
            </table>

        </div>
    </div>

@endforeach

</div>

<!-- PRINT BUTTON -->
<div class="text-end mb-3 no-print">
    <button onclick="printReport()" class="btn btn-danger">
        🖨 Print / Download PDF
    </button>
</div>

@endsection

<script>
function printReport() {
    window.print();
}
</script>


    

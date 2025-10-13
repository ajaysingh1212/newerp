@extends('layouts.admin')

@section('content')

  @php
    use Carbon\Carbon;

    $warrantyMonths = $product->warranty ?? 0;
    $requestDate = $history->request_date ?? null;

    $daysRemaining = 'N/A';

    if ($warrantyMonths && $requestDate) {
        $warrantyEnd = Carbon::parse($requestDate)->addMonths($warrantyMonths);
        $daysRemaining = Carbon::now()->diffInDays($warrantyEnd, false); // false = negative if expired
    }

    $subscriptionMonths = $product->subscription ?? 0;
    $subscriptionDaysRemaining = 'N/A';

    if ($subscriptionMonths && $requestDate) {
        $subscriptionEnd = Carbon::parse($requestDate)->addMonths($subscriptionMonths);
        $subscriptionDaysRemaining = Carbon::now()->diffInDays($subscriptionEnd, false);
    }

    $amcMonths = $product->amc ?? 0;
    $requestDate = $history->request_date ?? null;

    $amcDaysRemaining = 'N/A';

    if ($amcMonths && $requestDate) {
        $amcEnd = Carbon::parse($requestDate)->addMonths($amcMonths);
        $amcDaysRemaining = Carbon::now()->diffInDays($amcEnd, false);
    }
@endphp

<style>
    .blinking-days {
        animation: blinker 1s linear infinite;
    }

    @keyframes blinker {
        50% { opacity: 0; 
        color:black;}
    }
    
    .action-buttons {
        margin-top: 20px;
        text-align: center;
    }
    
    .action-buttons .btn {
        margin: 0 10px;
        padding: 10px 20px;
        font-size: 16px;
    }
</style>


<div class="container mt-4">
    <div class="row">
        <div class="col-lg-6">
            <div class="row">
        {{-- Activation Request Detail --}}
        <div class="col-md-12 mb-1">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">Activation Request Detail</div>
                <div class="card-body">
                    <p><strong>Request Date:</strong> {{ $history->request_date }}</p>
                    <p><strong>Status:</strong> 
                        <span class="badge bg-success">{{ ucfirst($history->status ?? 'N/A') }}</span>
                    </p>
                    <p><strong>Order No:</strong> {{ $history->activation_request_id ?? 'N/A' }}</p>
                    <p><strong>Activation Date:</strong> {{ $history->request_date ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        {{-- Customer Detail --}}
        <div class="col-md-12 mb-1">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">Customer Detail</div>
                <div class="card-body">
                    <p><strong>Name:</strong> {{ $user->name }}</p>
                    
                    <p><strong>Mobile No:</strong> {{ $user->mobile_number }}</p>
                    <p><strong>WhatsApp No:</strong> {{ $user->whatsapp_number }}</p>
                    <p><strong>Email:</strong> {{ $user->email }}</p>
                    <p><strong>Address:</strong> {!! $user->full_address !!}</p>
                    <p><strong>District:</strong> {{ $user->district->districts ?? 'N/A' }}</p>
                    <p><strong>State:</strong> {{ $user->state->state_name ?? 'N/A' }}</p>
                    <p><strong>PIN Code:</strong> {{ $user->pin_code }}</p>
                </div>
            </div>
        </div>
       
        {{-- Vehicle Detail --}}
        <div class="col-md-12 mb-1">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">Vehicle Detail</div>
                <div class="card-body">
                    <p><strong>Vehicle Type:</strong> {{ $vehicle->select_vehicle_type_id ?? 'N/A' }}</p>
                    <p><strong>Model:</strong> {{ $vehicle->vehicle_model ?? ''}}</p>
                    <p><strong>Reg. No:</strong> {{ $vehicle->vehicle_number ?? ''}}</p>
                    <p><strong>Chassis No:</strong> {{ $vehicle->chassis_number ?? ''}}</p>
                    <p><strong>Engine No:</strong> {{ $vehicle->engine_number ?? ''}}</p>
                    <p><strong>Vehicle Color:</strong> {{ $vehicle->vehicle_color ?? '' }}</p>
                </div>
            </div>
        </div>   
</div>
        </div>

        <div class="col-lg-6">
        <div class="row">

         {{-- Product Detail --}}
        <div class="col-md-12 mb-1">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">Product Detail</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6">
                              <p><strong>Product Model:</strong> {{ $product->product_model->product_model ?? 'N/A' }}</p>
                    <p><strong>IMEI No:</strong> {{ $product->imei->imei_number ?? 'N/A' }}</p>
                    <p><strong>VTS No:</strong> {{ $product->vts->vts_number ?? 'N/A' }}</p>
                    <p><strong>SIM No:</strong> {{ $product->vts->sim_number ?? 'N/A' }}</p>
                        </div>
                        <div class="col-lg-6">
                            <p><strong>Operator:</strong> {{ $product->vts->operator ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
            
              {{-- Warranty Detail --}}
        <div class="col-md-12 mb-1">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">Warranty Detail</div>
                <div class="card-body">
<p>
    <strong>Warranty:</strong> {{ $product->warranty ?? 'N/A' }} Months
    @if (is_numeric($daysRemaining))
        <span class="blinking-days" style="float: right; color: green; font-weight: bold;">
            {{ $daysRemaining > 0 ? $daysRemaining . ' Days Left' : 'Expired' }}
        </span>
    @endif
</p>
                </div>
            </div>
        </div>

        {{-- Subscription Detail --}}
        <div class="col-md-12 mb-1">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">Subscription Detail</div>
                <div class="card-body">
                   <p>
    <strong>Subscription:</strong> {{ $product->subscription ?? 'N/A' }} Months
    @if (is_numeric($subscriptionDaysRemaining))
        <span class="blinking-days" style="float: right; color: green; font-weight: bold;">
            {{ $subscriptionDaysRemaining > 0 ? $subscriptionDaysRemaining . ' Days Left' : 'Expired' }}
        </span>
    @endif
</p>
                </div>
            </div>
        </div>

        {{-- AMC Detail --}}
        <div class="col-md-12 mb-1">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">AMC Detail</div>
                <div class="card-body">
                   <p>
    <strong>AMC:</strong> {{ $product->amc ?? 'N/A' }} Months
    @if(is_numeric($amcDaysRemaining))
        <span class="blinking-days" style="float: right; color: green; font-weight: bold;">
            {{ $amcDaysRemaining }} days 
            {{ $amcDaysRemaining > 0 ? 'Left' : 'Expired' }}
        </span>
    @endif
</p>
                </div>
            </div>
        </div>

        {{-- Employee Detail --}}
        <div class="col-md-12 mb-1">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">Employee Detail</div>
                <div class="card-body">
                    <p><strong>Name:</strong> {{ $createdBy->name ?? 'N/A' }}</p>
                    <p><strong>Mobile No:</strong> {{ $createdBy->mobile_number ?? 'N/A' }}</p>
                    <p><strong>WhatsApp No:</strong> {{ $createdBy->email ?? 'N/A' }}</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-12 action-buttons">
        <button id="downloadInvoice" class="btn btn-success">Download Invoice</button>
        <button id="printInvoice" class="btn btn-success">Print</button>
    </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('downloadInvoice').addEventListener('click', function () {
        const id = '{{ $history->id ?? 0 }}';

        if (id == 0) {
            alert("Invalid history ID");
            return;
        }

        const url = '{{ route("admin.reports.download", ":id") }}'.replace(':id', id);
        window.location.href = url; // No need for form
    });

    document.getElementById('printInvoice').addEventListener('click', function () {
        const id = '{{ $history->id ?? 0 }}';
        const url = '{{ route("admin.reports.print", ":id") }}'.replace(':id', id);
        window.open(url, '_blank').focus();
    });
});
</script>

@endsection
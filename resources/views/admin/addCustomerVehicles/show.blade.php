@extends('layouts.admin')
@section('content')
<div class="card">
    <div class="card-header">
        Vehicle Details
    </div>

    <div class="card-body">
         @include('watermark')
        <table class="table table-bordered">
            <tr><th>ID</th><td>{{ $addCustomerVehicle->id }}</td></tr>
            <tr><th>Vehicle Number</th><td>{{ $addCustomerVehicle->vehicle_number }}</td></tr>
            <tr><th>Vehicle Model</th><td>{{ $addCustomerVehicle->vehicle_model }}</td></tr>
            <tr><th>Vehicle Type</th><td>{{ $addCustomerVehicle->select_vehicle_type->vehicle_type ?? '' }}</td></tr>
            <tr><th>Vehicle Color</th><td>{{ $addCustomerVehicle->vehicle_color }}</td></tr>
            <tr><th>Request Date</th><td>{{ \Carbon\Carbon::parse($addCustomerVehicle->request_date)->format('Y-m-d') }}</td></tr>

            {{-- Product Master --}}
            <tr><th>Product Model</th><td>{{ $addCustomerVehicle->product_master->product_model->product_model ?? '' }}</td></tr>
            <tr><th>IMEI Number</th><td>{{ $addCustomerVehicle->product_master->imei->imei_number ?? '' }}</td></tr>
            <tr><th>VTS Info</th>
                <td>
                    @if($addCustomerVehicle->product_master && $addCustomerVehicle->product_master->vts)
                        {{ $addCustomerVehicle->product_master->vts->vts_number }} -
                        {{ $addCustomerVehicle->product_master->vts->sim_number }}
                        ({{ $addCustomerVehicle->product_master->vts->operator }})
                    @endif
                </td>
            </tr>
@php
    use Carbon\Carbon;

    function calculateRemainingDays($months, $requestDate) {
        if (!$months || !$requestDate) return null;

        $expiry = Carbon::parse($requestDate)->addMonths($months);
        $now = Carbon::now();
        $daysLeft = $now->diffInDays($expiry, false);

        if ($daysLeft > 0) {
            return "<span class='blink'>{$daysLeft} days left</span>";
        } else {
            return "<span style='color:red;'>Expired</span>";
        }
    }
@endphp
<style>
@keyframes blink {
    50% { opacity: 0; }
}
.blink {
    animation: blink 1s step-start 0s infinite;
    color: green;
    font-weight: bold;
}
</style>

<tr>
    <th>Subscription</th>
    <td>
        {{ $addCustomerVehicle->product_master->subscription ?? '' }} months <br/>
        {!! calculateRemainingDays($addCustomerVehicle->product_master->subscription ?? 0, $addCustomerVehicle->request_date) !!}
    </td>
</tr>

<tr>
    <th>AMC</th>
    <td>
        {{ $addCustomerVehicle->product_master->amc ?? '' }} months <br/>
        {!! calculateRemainingDays($addCustomerVehicle->product_master->amc ?? 0, $addCustomerVehicle->request_date) !!}
    </td>
</tr>

<tr>
    <th>Warranty</th>
    <td>
        {{ $addCustomerVehicle->product_master->warranty ?? '' }} months <br/>
        {!! calculateRemainingDays($addCustomerVehicle->product_master->warranty ?? 0, $addCustomerVehicle->request_date) !!}
    </td>
</tr>

            {{-- Images --}}
            <tr>
                <th>Owner Image</th>
                <td>
                    @foreach($addCustomerVehicle->getMedia('customer_image') as $media)
                        <a href="{{ $media->getUrl() }}" target="_blank">
                            <img src="{{ $media->getUrl('thumb') }}" width="50" height="50">
                        </a>
                    @endforeach
                </td>
            </tr>

            <tr>
                <th>Insurance</th>
                <td>
                    @foreach($addCustomerVehicle->getMedia('insurance') as $media)
                        <a href="{{ $media->getUrl() }}" target="_blank">
                            <img src="{{ $media->getUrl('thumb') }}" width="50" height="50">
                        </a>
                    @endforeach
                </td>
            </tr>

            <tr>
                <th>Pollution</th>
                <td>
                    @foreach($addCustomerVehicle->getMedia('pollution') as $media)
                        <a href="{{ $media->getUrl() }}" target="_blank">
                            <img src="{{ $media->getUrl('thumb') }}" width="50" height="50">
                        </a>
                    @endforeach
                </td>
            </tr>

            <tr>
                <th>RC</th>
                <td>
                    @foreach($addCustomerVehicle->getMedia('registration_certificate') as $media)
                        <a href="{{ $media->getUrl() }}" target="_blank">
                            <img src="{{ $media->getUrl('thumb') }}" width="50" height="50">
                        </a>
                    @endforeach
                </td>
            </tr>

            <tr>
                <th>ID Proofs</th>
                <td>
                    @foreach($addCustomerVehicle->getMedia('id_proofs') as $media)
                        <a href="{{ $media->getUrl() }}" target="_blank">
                            <img src="{{ $media->getUrl('thumb') }}" width="50" height="50">
                        </a>
                    @endforeach
                </td>
            </tr>

            <tr>
                <th>Vehicle Photos</th>
                <td>
                    @foreach($addCustomerVehicle->getMedia('vehicle_photos') as $media)
                        <a href="{{ $media->getUrl() }}" target="_blank">
                            <img src="{{ $media->getUrl('thumb') }}" width="50" height="50">
                        </a>
                    @endforeach
                </td>
            </tr>

            <tr>
                <th>Product Images</th>
                <td>
                    @foreach($addCustomerVehicle->getMedia('product_images') as $media)
                        <a href="{{ $media->getUrl() }}" target="_blank">
                            <img src="{{ $media->getUrl('thumb') }}" width="50" height="50">
                        </a>
                    @endforeach
                </td>
            </tr>
        </table>
    </div>
</div>
@endsection

@extends('layouts.admin')

@section('content')
<div class="container">
    <h1 class="mb-4">KYC Recharge Details</h1>

    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>ID</th>
                        <td>{{ $recharge->id }}</td>
                        <th>User Name</th>
                        <td>{{ $recharge->user->name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td>{{ $recharge->user->email ?? 'N/A' }}</td>
                        <th>Number</th>
                        <td>{{ $recharge->user->number ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Address</th>
                        <td colspan="3">{{ $recharge->user->full_address ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Vehicle Number</th>
                        <td>{{ $recharge->vehicle_number }}</td>
                        <th>Title</th>
                        <td>{{ $recharge->title }}</td>
                    </tr>
                    <tr>
                        <th>Description</th>
                        <td colspan="3">{{ $recharge->description }}</td>
                    </tr>
                    <tr>
                        <th>Transaction Id</th>
                        <td>{{ $recharge->razorpay_order_id }}</td>
                        <th>Payment Status</th>
                        <td>
                            <span class="badge bg-{{ $recharge->payment_status == 'paid' ? 'success' : 'warning' }}">
                                {{ ucfirst($recharge->payment_status) }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Payment Method</th>
                        <td>{{ $recharge->payment_method ?? 'N/A' }}</td>
                        <th>Payment Amount</th>
                        <td>₹{{ number_format($recharge->payment_amount, 2) }}</td>
                    </tr>
                    <tr>
                        <th>Payment Date</th>
                        <td>{{ $recharge->payment_date ? \Carbon\Carbon::parse($recharge->payment_date)->format('d M Y') : 'N/A' }}</td>
                        <th>Location</th>
                        <td>{{ $recharge->location ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Latitude</th>
                        <td>{{ $recharge->latitude ?? 'N/A' }}</td>
                        <th>Longitude</th>
                        <td>{{ $recharge->longitude ?? 'N/A' }}</td>
                    </tr>
                </tbody>
            </table>

            {{-- KYC Image --}}
            <div class="mt-4">
                <h5>KYC Image</h5>
                @if($recharge->getFirstMediaUrl('kyc_recharge_images'))
                    <img src="{{ $recharge->getFirstMediaUrl('kyc_recharge_images') }}" 
                         alt="KYC Image" class="img-fluid rounded" style="max-width: 300px;">
                @else
                    <img src="{{ asset('images/add-image.png') }}" 
                         alt="Add KYC Image" class="img-fluid rounded" style="max-width: 300px;">
                @endif
            </div>

            {{-- Vehicle Media --}}
            @if($recharge->vehicle)
                @php
                    $vehicle = $recharge->vehicle;
                    $mediaTypes = [
                        'Vehicle Image' => $vehicle->vehicle_photos,
                        'ID Proof' => $vehicle->id_proofs,
                        'Product Image' => $vehicle->product_images,
                        'Insurance' => $vehicle->insurance->first(),
                        'Pollution' => $vehicle->pollution->first(),
                        'Registration Certificate' => $vehicle->registration_certificate->first(),
                    ];
                @endphp

                <div class="mt-4">
                  
    <h5>Vehicle Media</h5>
    <table class="table table-bordered">
        <tbody>
            @php
                $chunks = collect($mediaTypes)->chunk(3); // 3 images per row
            @endphp

            @foreach($chunks as $chunk)
                <tr>
                    @foreach($chunk as $label => $file)
                        <td class="text-center">
                            @if($file)
                                <img src="{{ $file->getUrl() }}" 
                                     alt="{{ $label }}" class="img-fluid rounded" style="max-width: 150px;">
                            @else
                                <img src="{{ asset('images/add-image.png') }}" 
                                     alt="Add {{ $label }}" class="img-fluid rounded" style="max-width: 150px;">
                            @endif
                            <div class="mt-2">{{ $label }}</div>
                        </td>
                    @endforeach
                    {{-- Fill empty columns if last row has less than 3 --}}
                    @for($i = $chunk->count(); $i < 3; $i++)
                        <td></td>
                    @endfor
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

                   
            @endif

        </div>
    </div>
</div>
@endsection

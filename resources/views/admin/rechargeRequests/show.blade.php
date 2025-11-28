@extends('layouts.admin')
@section('content')

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>KYC Recharge Invoice - {{ $recharge->id }}</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Poppins', sans-serif; background:#f1f5f9; }

        @media print {
            .no-print { display: none !important; }
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }

        .invoice-box {
            background:white;
            border-radius:12px;
            padding:25px;
            box-shadow:0 4px 20px rgba(0,0,0,0.07);
        }

        .title-bar {
            background: linear-gradient(90deg, #033a91, #0c58d9);
            color:white;
            padding:14px 22px;
            border-radius:10px;
            display:flex;
            justify-content:space-between;
            align-items:center;
        }
    </style>
</head>

<body class="p-4">

<div class="max-w-4xl mx-auto">

    <!-- COMPANY HEADER -->
    <div class="bg-white shadow-md rounded-xl p-6 border border-blue-100 mb-4">
        <div class="text-center">
            <h1 class="text-3xl font-extrabold text-blue-900">EEMOTRACK</h1>
            <p class="text-sm text-gray-700 mt-1 font-medium">Authorized Dealer & Service Center</p>
            <p class="text-xs text-gray-500">1st Floor, Kamla Bhattacharya Road, Patna, Bihar - 800001</p>
            <p class="text-xs text-gray-500">Phone: 78578 68055 | Email: info@eemotrack.com</p>
            <p class="text-xs text-gray-600 font-semibold mt-1">GSTIN: 10AQFPK9218D1ZA</p>
        </div>
    </div>

    <!-- INVOICE TITLE BAR -->
    <div class="title-bar mb-4">
        <div>
            <h2 class="text-lg font-bold tracking-wide">RECHARGE INVOICE</h2>
            <p class="text-xs text-blue-100">Original Copy</p>
        </div>

        <div class="text-right">
            <p class="text-xs font-semibold">Invoice No.</p>
            <p class="text-sm font-bold">#{{ $recharge->id }}</p>
        </div>
    </div>

    <!-- STATUS BOX -->
    <div class="invoice-box mb-4">
        <p class="text-sm font-semibold">Payment Status:</p>
        <p class="px-3 py-1 inline-block rounded-full text-xs font-bold 
            {{ $recharge->payment_status == 'success' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
            {{ ucfirst($recharge->payment_status) }}
        </p>
    </div>

    <!-- USER & VEHICLE DETAILS -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">

        <!-- USER DETAILS -->
        <div class="invoice-box">
            <h3 class="font-semibold text-blue-800 mb-3">User Information</h3>
            <table class="text-xs w-full">
                <tr><td class="py-1 font-medium">Name</td><td>{{ $recharge->user->name ?? 'N/A' }}</td></tr>
                <tr><td class="py-1 font-medium">Email</td><td>{{ $recharge->user->email ?? 'N/A' }}</td></tr>
                <tr><td class="py-1 font-medium">Phone</td><td>{{ $recharge->user->number ?? 'N/A' }}</td></tr>
                <tr><td class="py-1 font-medium">Address</td><td>{{ $recharge->user->full_address ?? 'N/A' }}</td></tr>
            </table>
        </div>

        <!-- VEHICLE + PAYMENT -->
        <div class="invoice-box">
            <h3 class="font-semibold text-blue-800 mb-3">Vehicle & Payment</h3>

            <table class="text-xs w-full">
                <tr><td class="py-1 font-medium">Vehicle Number</td><td>{{ $recharge->vehicle_number }}</td></tr>
                <tr><td class="py-1 font-medium">Title</td><td>{{ $recharge->title }}</td></tr>
                <tr><td class="py-1 font-medium">Description</td><td>{{ $recharge->description }}</td></tr>
                <tr><td class="py-1 font-medium">Transaction ID</td><td>{{ $recharge->razorpay_order_id ?? 'N/A' }}</td></tr>
                <tr><td class="py-1 font-medium">Method</td><td>{{ $recharge->payment_method }}</td></tr>
                <tr><td class="py-1 font-medium">Amount</td><td>₹{{ number_format($recharge->payment_amount,2) }}</td></tr>
                <tr><td class="py-1 font-medium">Payment Date</td>
                    <td>{{ $recharge->payment_date ? \Carbon\Carbon::parse($recharge->payment_date)->format('d M Y') : 'N/A' }}</td></tr>
            </table>
        </div>

    </div>

    <!-- MEDIA SECTION -->
    @if($recharge->vehicle)
        @php
            $vehicle = $recharge->vehicle;
            $media = [
                "Vehicle Photo" => $vehicle->vehicle_photos,
                "ID Proof" => $vehicle->id_proofs,
                "Product Image" => $vehicle->product_images,
                "Insurance" => $vehicle->insurance->first(),
                "Pollution" => $vehicle->pollution->first(),
                "RC" => $vehicle->registration_certificate->first(),
            ];
        @endphp

        <div class="invoice-box">
            <h3 class="font-semibold text-blue-800 mb-3">Attached Documents</h3>

            <div class="grid grid-cols-3 gap-3">
                @foreach($media as $label => $file)
                    <div class="border p-2 bg-gray-50 rounded-lg text-center">
                        <img src="{{ $file ? $file->getUrl() : asset('images/add-image.png') }}"
                             class="rounded-md h-28 w-full object-cover mx-auto shadow">
                        <p class="text-[10px] mt-1 font-semibold">{{ $label }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    @endif


    <!-- FOOTER -->
    <div class="text-center mt-4 text-xs text-gray-600">
        <p class="italic">This is a computer-generated invoice. No signature required.</p>
        <p class="mt-1">
            <b>Generated On:</b> {{ $recharge->created_at->format('d-m-Y h:i A') }}
            by <b>{{ $recharge->created_by->name ?? 'System' }}</b>
        </p>
    </div>

</div>

<!-- PRINT BUTTON -->
<button onclick="window.print()" 
    class="no-print fixed bottom-4 right-4 bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded-full shadow-lg text-xs font-semibold">
    Print Invoice
</button>

</body>
</html>

@endsection

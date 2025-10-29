@extends('layouts.admin')
@section('content')

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>KYC Recharge Details - {{ $recharge->id }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Poppins', sans-serif; }
        @media print {
            .no-print { display: none !important; }
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
</head>
<body class="bg-blue-50 min-h-screen py-4 px-2 text-xs">

<div class="max-w-4xl mx-auto">

    <!-- Company Header -->
    <div class="text-center mb-4 p-4 bg-blue-50 rounded-lg shadow-md">
        <h1 class="text-2xl font-bold text-blue-800 mb-1">EEMOTRACK</h1>
        <p class="text-blue-600 text-sm mb-2">Authorized Dealer & Service Center</p>
        <p class="text-blue-700 text-xs mb-1">
            1st Floor, Kamla Bhattacharya Road, Patna (Bihar) - 800 001
        </p>
        <p class="text-blue-700 text-xs mb-1">
            Phone: <a href="tel:7857868055" class="underline">78578 68055</a> | Email: <a href="mailto:info@eemotrack.com" class="underline">info@eemotrack.com</a>
        </p>
        <p class="text-blue-700 text-xs">
            GSTIN: 10AQFPK9218D1ZA | State: Bihar
        </p>
    </div>

    <!-- Invoice Container -->
    <div class="bg-gradient-to-r from-blue-700 to-indigo-700 text-white p-4 flex justify-between items-center">
    
    <!-- 🔹 Left Section -->
    <div class="flex flex-col items-start">
        <h1 class="text-lg font-bold">KYC RECHARGE</h1>
        <p class="text-blue-200 mt-1 text-xs">ORIGINAL FOR RECORD</p>
    </div>

    <!-- 🔹 Center Section -->
    <div class="text-center">
        <p class="text-sm">
            
            <h1 class="px-3 py-1 {{ $recharge->payment_status == 'success' ? 'text-green-600' : 'text-yellow-500' }} ">
                {{ ucfirst($recharge->payment_status) }}
    </h1>
        </p>
    </div>

    <!-- 🔹 Right Section -->
    <div class="text-right bg-white p-2 rounded-lg text-xs text-blue-900 shadow">
        <p class="font-semibold">Recharge ID: {{ $recharge->id }}</p>
    </div>

</div>


        <!-- User & Vehicle Info -->
        <div class="p-4 flex flex-col md:flex-row gap-4">

            <!-- User Details -->
            <div class="w-full md:w-1/2 bg-blue-50 p-4 rounded-lg">
                <h4 class="font-semibold text-blue-700 mb-2 text-sm">User Details</h4>
                <table class="w-full text-xs">
                    <tbody>
                        <tr class="border-b">
                            <th class="p-2 text-left font-medium">Name</th>
                            <td class="p-2">{{ $recharge->user->name ?? 'N/A' }}</td>
                        </tr>
                        <tr class="border-b">
                            <th class="p-2 text-left font-medium">Email</th>
                            <td class="p-2">{{ $recharge->user->email ?? 'N/A' }}</td>
                        </tr>
                        <tr class="border-b">
                            <th class="p-2 text-left font-medium">Phone</th>
                            <td class="p-2">{{ $recharge->user->number ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th class="p-2 text-left font-medium">Address</th>
                            <td class="p-2">{{ $recharge->user->full_address ?? 'N/A' }}</td>
                        </tr>
                    </tbody>
                </table>

               
            </div>

            <!-- Vehicle & Payment Details -->
            <div class="w-full md:w-1/2 bg-blue-50 p-4 rounded-lg">
                <h4 class="font-semibold text-blue-700 mb-2 text-sm">Vehicle & Payment Details</h4>
                <table class="w-full text-xs">
                    <tbody>
                        <tr class="border-b">
                            <th class="p-2 text-left font-medium">Vehicle Number</th>
                            <td class="p-2">{{ $recharge->vehicle_number ?? 'N/A' }}</td>
                        </tr>
                        <tr class="border-b">
                            <th class="p-2 text-left font-medium">Title</th>
                            <td class="p-2">{{ $recharge->title ?? 'N/A' }}</td>
                        </tr>
                        <tr class="border-b">
                            <th class="p-2 text-left font-medium">Description</th>
                            <td class="p-2">{{ $recharge->description ?? 'N/A' }}</td>
                        </tr>
                        <tr class="border-b">
                            <th class="p-2 text-left font-medium">Transaction ID</th>
                            <td class="p-2">{{ $recharge->razorpay_order_id ?? 'N/A' }}</td>
                        </tr>
                        <tr class="border-b">
                            <th class="p-2 text-left font-medium">Payment Method</th>
                            <td class="p-2">{{ $recharge->payment_method ?? 'N/A' }}</td>
                        </tr>
                        <tr class="border-b">
                            <th class="p-2 text-left font-medium">Payment Amount</th>
                            <td class="p-2">₹{{ number_format($recharge->payment_amount,2) }}</td>
                        </tr>
                        <tr>
                            <th class="p-2 text-left font-medium">Payment Date</th>
                            <td class="p-2">{{ $recharge->payment_date ? \Carbon\Carbon::parse($recharge->payment_date)->format('d M Y') : 'N/A' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>

        <!-- Vehicle Media -->
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
                $chunks = collect($mediaTypes)->chunk(3);
            @endphp

            <div class="p-4">
                <h3 class="font-semibold text-blue-700 mb-2">Vehicle Media</h3>
                <div class="grid grid-cols-3 gap-2">
                    @foreach($chunks as $chunk)
                        @foreach($chunk as $label => $file)
                            <div class="text-center">
                                @if($file)
                                    <img src="{{ $file->getUrl() }}" alt="{{ $label }}" class="rounded-lg mx-auto max-w-full">
                                @else
                                    <img src="{{ asset('images/add-image.png') }}" alt="Add {{ $label }}" class="rounded-lg mx-auto max-w-full">
                                @endif
                                <div class="mt-1 text-[9px] font-medium">{{ $label }}</div>
                            </div>
                        @endforeach
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Footer -->
        <div class="mt-4 p-3 border-t border-gray-300 text-xs text-gray-600 space-y-1 text-center">
            <p class="italic text-gray-500">
                This is a computer-generated KYC recharge detail and does not require a physical signature.
            </p>
            <p>
                <span class="font-semibold text-gray-700">Generated On:</span>
                {{ $recharge->created_at->format('d-m-Y \a\t h:i A') }}
                by <span class="font-medium">{{ $recharge->created_by->name ?? 'System' }}</span>
            </p>
        </div>

    </div>

    <!-- Print Button -->
    <div class="fixed bottom-4 right-4 no-print">
        <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-full shadow-lg flex items-center text-xs">
            <i class="fas fa-print mr-1"></i> Print KYC Details
        </button>
    </div>

</div>

</body>
</html>
@endsection

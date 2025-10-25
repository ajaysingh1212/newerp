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
        .invoice-container { box-shadow: 0 10px 30px rgba(0,0,0,0.1); border-radius: 0.75rem; }
        .header-gradient { background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); color: #fff; }
        .table-header { background: linear-gradient(to right, #4facfe 0%, #00f2fe 100%); color: #fff; }

        @media print {
            body { 
                -webkit-print-color-adjust: exact; /* Chrome/Safari */
                print-color-adjust: exact;         /* Firefox */
                font-size: 10px !important;
                line-height: 1.2;
                background: #fff !important;
            }
            .invoice-container { box-shadow: none !important; border: none !important; padding: 6px !important; }
            h1,h2,h3,h4,p,span,td,th { font-size: 10px !important; }
            .no-print { display: none !important; }

            /* Force Tailwind background colors */
            .bg-blue-50 { background-color: #eff6ff !important; }
            .bg-white { background-color: #ffffff !important; }
            .header-gradient { background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%) !important; color: #fff !important; }
            .bg-white/10 { background-color: #ffffff !important; } /* replace transparency with solid */
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen py-4 px-2 text-xs">

<div class="max-w-4xl mx-auto">

    <!-- Company Header -->
   <div class="text-center mb-4 p-4 bg-blue-50 rounded-lg shadow-md">
    <h1 class="text-2xl font-bold text-blue-800 mb-1">EEMOTRACK</h1>
    <p class="text-blue-600 text-sm mb-2">Authorized Dealer & Service Center</p>
    <p class="text-blue-700 text-xs mb-1">
        1st Floor, Kamla Bhattacharya Road, Patna (Bihar) - 800 001
    </p>
    <p class="text-blue-700 text-xs mb-1">
        Phone: <a href="tel:9263906099" class="underline">9263906099</a> | Email: <a href="mailto:marutisuzukiventures@gmail.com" class="underline">marutisuzukiventures@gmail.com</a>
    </p>
    <p class="text-blue-700 text-xs">
        GSTIN: 10ABZFM8479K1ZC | State: Bihar
    </p>
</div>


    <!-- Invoice Container -->
    <div class="invoice-container bg-white rounded-xl overflow-hidden text-xs">

        <!-- Header -->
        <div class="header-gradient text-white p-4 text-xs">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-lg font-bold">KYC RECHARGE</h1>
                    <p class="text-blue-200 mt-1 text-xs">ORIGINAL FOR RECORD</p>
                </div>
                <div class="text-right bg-white p-2 rounded-lg text-xs">
                    <p class="font-semibold text-blue-900">Recharge ID: {{ $recharge->id }}</p>
                    <p class="text-blue-900">Status: 
                        <span class="px-2 py-1 rounded-full {{ $recharge->payment_status == 'paid' ? 'bg-green-600' : 'bg-yellow-500' }}">
                            {{ ucfirst($recharge->payment_status) }}
                        </span>
                    </p>
                </div>
            </div>
        </div>

        <!-- User & Vehicle Info -->
        <div class="bg-white shadow-lg rounded-xl p-4 mb-4 overflow-x-auto">
            <h3 class="font-semibold text-blue-700 mb-2 text-sm">KYC Recharge Details</h3>
            <table class="w-full text-xs border border-gray-200">
                <tbody>
                    <tr>
                       <!-- Left Column: User Details -->
<td class="w-1/2 align-top bg-blue-50 p-4 rounded-lg border-r border-gray-200">
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

    <!-- KYC Image (Moved OUT of table) -->
    <div class="p-4 border-t border-gray-200 mt-4">
        
        <div class="flex items-center justify-center">
            @if($recharge->getFirstMediaUrl('kyc_recharge_images'))
                <img src="{{ $recharge->getFirstMediaUrl('kyc_recharge_images') }}" 
                     alt="KYC Image" class="rounded-lg" style="max-width: 250px;">
            @else
                <img src="{{ asset('images/add-image.png') }}" 
                     alt="Add KYC Image" class="rounded-lg" style="max-width: 250px;">
            @endif
        </div>
    </div>
</td>

                        <!-- Right Column: Vehicle & Payment Details -->
                        <td class="w-1/2 align-top bg-blue-50 p-4 rounded-lg">
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
                        </td>
                    </tr>
                </tbody>
            </table>
      

       

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
                <table class="w-full">
                    <tbody>
                        @foreach($chunks as $chunk)
                            <tr>
                                @foreach($chunk as $label => $file)
                                    <td class="text-center p-1">
                                        @if($file)
                                            <img src="{{ $file->getUrl() }}" 
                                                 alt="{{ $label }}" class="rounded-lg mx-auto" style="max-width: 120px;">
                                        @else
                                            <img src="{{ asset('images/add-image.png') }}" 
                                                 alt="Add {{ $label }}" class="rounded-lg mx-auto" style="max-width: 120px;">
                                        @endif
                                        <div class="mt-1 text-[9px] font-medium">{{ $label }}</div>
                                    </td>
                                @endforeach
                                @for($i = $chunk->count(); $i < 3; $i++)
                                    <td></td>
                                @endfor
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <!-- Footer / Print Info -->
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

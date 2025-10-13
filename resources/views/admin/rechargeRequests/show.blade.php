@extends('layouts.admin')
@section('content')
    <style>
        @page {
            margin: 10mm;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            padding: 0;
            margin: 0;
            color: #222;
        }

        .invoice-box {
            width: 100%;
            max-width: 900px;
            margin: 0 auto;
            background: #ffffff;
            padding: 20px;
            border-top: 5px solid #ffcc00;
        }

        .header {
            background: #ffcc00;
            padding: 10px;
            text-align: center;
            margin-bottom: 20px;
        }

        .header h2 {
            margin: 0;
        }

        .company-info {
            background: #fff8e1;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 10px;
        }

        .company-info p {
            margin: 3px 0;
            font-size: 13px;
        }

        table.items {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            margin-top: 10px;
        }

        .items th {
            background: #ff6600;
            color: white;
            padding: 8px;
            border: 1px solid #e06700;
            text-align: left;
        }

        .items td {
            padding: 8px;
            border: 1px solid #ddd;
        }

        .items tr:nth-child(even) {
            background: #fffaf0;
        }
    </style>
</head>
<body>

<div class="invoice-box">

    <!-- Header -->
    <div class="header">
        <h2>Recharge Request Invoice</h2>
    </div>

    <!-- Company Info -->
    <div class="company-info">
        <div class="">
        <p><strong>Maruti Suzuki Ventures</strong></p>
        <p><strong>GSTIN:</strong> 10ABZFM8479K1ZC</p>
        <p><strong>Phone:</strong> 9263906099</p>
        <p><strong>Email:</strong> marutisuzukiventures@gmail.com</p>
        <p><strong>Address:</strong> 1st Floor Kamla Market, RK Bhattacharya Road, Patna, Bihar-800001</p>
    </div>
    <div class="">
         @include('watermark')
    </div>
    </div>

    <!-- Details Table -->
    <table class="items">
        <tbody>
            <tr>
                <th>User Details</th>
                <th>Recharge By</th>
                <th>Vehicle Details</th>
            </tr>
            <tr>
                <td style="text-transform: capitalize;">
                    <p ><strong>Name :-</strong>{{ $rechargeRequest->user->name }}</p>
                    <p><strong>Email :-</strong>{{ $rechargeRequest->user->email ?? '-' }}</p>
                    <p><strong>Mobile Number :-</strong>{{ $rechargeRequest->user->mobile_number ?? '-' }}</p>
                    <p><strong>Address :-</strong>{!! $rechargeRequest->user->full_address ?? '-' !!}</p>

                </td>
                <td style="text-transform: capitalize;">
                    <p><strong>Recharge By :-</strong>{{ $rechargeRequest->created_by->name ?? '-' }}</p>
                    <p><strong>Email :-</strong>{{ $rechargeRequest->created_by->email ?? '-' }}</p>
                    <p><strong>Mobile Number :-</strong>{{ $rechargeRequest->created_by->mobile_number ?? '-' }}</p>
                    <p><strong>Address :-</strong>{!! $rechargeRequest->created_by->full_address ?? '-' !!}</p>

                </td>
                <td style="text-transform: capitalize;">
                    <p><strong>Vehicle Number :-</strong>{{ $rechargeRequest->vehicle_number ?? '-' }}</p>
                    <p><strong>Owner Name :-</strong>{{ $rechargeRequest->vehicle->owners_name ?? '-' }}</p>
                    <p><strong>Chassis No :-</strong>{{ $rechargeRequest->vehicle->chassis_number ?? '-' }}</p>
                    <p><strong>Engine No :-</strong>{{ $rechargeRequest->vehicle->engine_number ?? '-' }}</p>
                
            </tr>
            <tr>
                <th colspan="3" style="text-align: center; background: #ffcc00; color: white;">Recharge Request Details</th>
            </tr>
           <tr>
    <td colspan="3" style="padding: 15px; background: #fffaf0; font-size: 13px;">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">

            <div><strong>Recharge Plan:</strong>{{ $rechargeRequest->select_recharge->plan_name ?? '-' }}</div>

            <div><strong>Recharge Price:</strong>
                {{ $rechargeRequest->select_recharge->price ? '₹' . number_format($rechargeRequest->select_recharge->price, 2) : '-' }}
            </div>

            <div><strong>AMC Duration:</strong>
                @if($rechargeRequest->select_recharge->amc_duration)
                    {{ $rechargeRequest->select_recharge->amc_duration * 30 }} Days
                @else
                    N/A
                @endif
            </div>

            <div><strong>Subscription Duration:</strong>
                @if($rechargeRequest->select_recharge->subscription_duration)
                    {{ $rechargeRequest->select_recharge->subscription_duration * 30 }} Days
                @else
                    N/A
                @endif
            </div>

            <div><strong>Device Warranty:</strong>
                @if($rechargeRequest->select_recharge->warranty_duration)
                    {{ $rechargeRequest->select_recharge->warranty_duration * 30 }} Days
                @else
                    N/A
                @endif
            </div>

            <div><strong>Payment Method:</strong>{{ $rechargeRequest->payment_method ?? '-' }}</div>

            <div><strong>Payment ID:</strong>{{ $rechargeRequest->payment_id ?? '-' }}</div>

            <div><strong>Razorpay Payment ID:</strong>{{ $rechargeRequest->razorpay_payment_id ?? '-' }}</div>

            <div><strong>Payment Amount:</strong>₹{{ number_format($rechargeRequest->payment_amount, 2) }}</div>

            <div><strong>Redeem Amount:</strong>₹{{ number_format($rechargeRequest->redeem_amount, 2) }}</div>

            <div><strong>Payment Date:</strong>{{ $rechargeRequest->payment_date ?? '-' }}</div>
        </div>
    </td>
</tr>

               
        </tbody>
    </table>
      <!-- Action Buttons -->
<div class="action-buttons" style="margin-top: 30px; text-align: center;">
    <button onclick="window.print()" style="
        background-color: #ff6600;
        color: white;
        border: none;
        padding: 10px 20px;
        margin: 5px;
        font-size: 14px;
        border-radius: 5px;
        cursor: pointer;">
        🖨️ Print Invoice
    </button>

    <a href="{{ route('admin.rechargerequests.downloadPdf', $rechargeRequest->id) }}" style="
        background-color: #28a745;
        color: white;
        text-decoration: none;
        padding: 10px 20px;
        margin: 5px;
        font-size: 14px;
        border-radius: 5px;
        display: inline-block;">
        ⬇️ Download PDF
    </a>
</div>

<style>
    @media print {
        .action-buttons {
            display: none !important;
        }
    }
</style>


</div>
@endsection

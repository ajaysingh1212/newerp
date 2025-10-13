<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Recharge Request Invoice</title>
    <style>
        @page {
            size: A4;
            margin: 10mm;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            margin: 0;
            padding: 0;
            color: #222;
        }

        .invoice-box {
            width: 100%;
            padding: 15px;
            margin: 0 auto;
            background: #fff;
            box-sizing: border-box;
        }

        .header {
            background: #ffcc00;
            padding: 8px;
            text-align: center;
            margin-bottom: 12px;
        }

        .company-info {
            background: #fff8e1;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        .company-info p {
            margin: 3px 0;
        }

        table.items {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }

        .items th {
            background: #ff6600;
            color: white;
            padding: 6px;
            border: 1px solid #e06700;
            text-align: left;
        }

        .items td {
            padding: 6px;
            border: 1px solid #ddd;
            vertical-align: top;
        }

        .items tr:nth-child(even) {
            background: #fffaf0;
        }

        .footer {
            font-size: 10px;
            color: #666;
            margin-top: 25px;
            border-top: 1px dashed #ccc;
            padding-top: 10px;
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
        <p><strong>Maruti Suzuki Ventures</strong></p>
        <p><strong>GSTIN:</strong> 10ABZFM8479K1ZC</p>
        <p><strong>Phone:</strong> 9263906099</p>
        <p><strong>Email:</strong> marutisuzukiventures@gmail.com</p>
        <p><strong>Address:</strong> 1st Floor Kamla Market, RK Bhattacharya Road, Patna, Bihar-800001</p>
        @include('watermark')
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
            <td>
                <p><strong>Name:</strong> {{ $rechargeRequest->user->name }}</p>
                <p><strong>Email:</strong> {{ $rechargeRequest->user->email ?? '-' }}</p>
                <p><strong>Mobile:</strong> {{ $rechargeRequest->user->mobile_number ?? '-' }}</p>
                <p><strong>Address:</strong> {!! $rechargeRequest->user->full_address ?? '-' !!}</p>
            </td>
            <td>
                <p><strong>Recharge By:</strong> {{ $rechargeRequest->created_by->name ?? '-' }}</p>
                <p><strong>Email:</strong> {{ $rechargeRequest->created_by->email ?? '-' }}</p>
                <p><strong>Mobile:</strong> {{ $rechargeRequest->created_by->mobile_number ?? '-' }}</p>
                <p><strong>Address:</strong> {!! $rechargeRequest->created_by->full_address ?? '-' !!}</p>
            </td>
            <td>
                <p><strong>Vehicle No:</strong> {{ $rechargeRequest->vehicle_number ?? '-' }}</p>
                <p><strong>Owner:</strong> {{ $rechargeRequest->vehicle->owners_name ?? '-' }}</p>
                <p><strong>Chassis No:</strong> {{ $rechargeRequest->vehicle->chassis_number ?? '-' }}</p>
                <p><strong>Engine No:</strong> {{ $rechargeRequest->vehicle->engine_number ?? '-' }}</p>
            </td>
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

    <!-- Footer -->
    <div class="footer">
        <p style="text-align: center; font-style: italic;">
            Thank you for trusting <strong>Maruti Suzuki Ventures</strong>. We value your business.
        </p>

        <table width="100%" style="margin-top: 10px;">
            <tr>
                <td style="text-align: left;">
                    <p><strong>Support:</strong></p>
                    <p>Email: marutisuzukiventures@gmail.com</p>
                    <p>Phone: 9263906099</p>
                </td>
                <td style="text-align: right;">
                    <p>Authorized Signature</p>
                    <div style="height: 30px; border-bottom: 1px solid #000; width: 150px; margin-left: auto;"></div>
                    <p style="margin-top: 4px;">(Company Seal)</p>
                </td>
            </tr>
        </table>

        <p style="text-align: center; margin-top: 10px;">
            This is a system-generated invoice. No signature required.
        </p>
    </div>

</div>

</body>
</html>

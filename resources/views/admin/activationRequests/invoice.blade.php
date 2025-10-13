<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activation Request Invoice</title>
    <style>
        @page {
            margin: 10mm;
        }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 0;
            color: #222;
        }
        .invoice-box {
            max-width: 900px;
            margin: 0 auto;
            background: #fff;
            padding: 15px;
            border-top: 5px solid #007bff;
            page-break-inside: avoid;
        }
        .header {
            background: #007bff;
            padding: 8px;
            text-align: center;
            color: white;
            margin-bottom: 15px;
            page-break-inside: avoid;
        }
        .company-info {
            background: #e9f5ff;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 15px;
            page-break-inside: avoid;
        }
        .company-info p {
            margin: 3px 0;
            font-size: 13px;
        }
        table.details {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
            margin-top: 5px;
            page-break-inside: avoid;
        }
        .details th {
            background: #343a40;
            color: white;
            padding: 6px;
            border: 1px solid #444;
            text-align: left;
        }
        .details td {
            padding: 6px;
            border: 1px solid #ddd;
            vertical-align: top;
        }
        .details tr {
            page-break-inside: avoid;
        }
        .details tr:nth-child(even) {
            background: #f8f9fa;
        }
        .footer {
            margin-top: 15px;
            page-break-inside: avoid;
        }
    </style>
</head>
<body>

<div class="invoice-box">

    <!-- Header -->
    <div class="header">
        <h2>Activation Request Invoice</h2>
    </div>

    <!-- Company Information -->
    <div class="company-info">
        <p><strong>EEMO TRACK INDIA</strong></p>
        <p><strong>GSTIN:</strong> 10ABZFM8479K1ZC</p>
        <p><strong>Phone:</strong> 78578 68055</p>
        <p><strong>Email:</strong> info@eemotrack.com</p>
        <p><strong>Address:</strong> Kamala Market, RK Bhattacharya Road, Pirmuhani, Salimpur Ahra, Golambar, Patna, Bihar-800001</p>
    </div>

    <!-- Customer and Vehicle Info -->
    <table class="details">
        <tbody>
            <tr><th colspan="2">Customer Details</th></tr>
            <tr>
                <td><strong>Name:</strong> {{ $activationRequest->customer_name }}</td>
                <td><strong>Mobile:</strong> {{ $activationRequest->mobile_number }}</td>
            </tr>
            <tr>
                <td><strong>WhatsApp:</strong> {{ $activationRequest->whatsapp_number }}</td>
                <td><strong>Email:</strong> {{ $activationRequest->email }}</td>
            </tr>
            <tr>
                <td><strong>Address:</strong> {!! $activationRequest->address !!}</td>
                <td>
                    <strong>State:</strong> {{ $activationRequest->state->name ?? '-' }}<br>
                    <strong>District:</strong> {{ $activationRequest->district->name ?? '-' }}
                </td>
            </tr>

            <tr><th colspan="2">Vehicle & Product Details</th></tr>
            <tr>
                <td><strong>Vehicle Reg No:</strong> {{ $activationRequest->vehicle_reg_no }}</td>
                <td><strong>Vehicle Model:</strong> {{ $activationRequest->vehicle_model }}</td>
            </tr>
            <tr>
                <td><strong>Chassis No:</strong> {{ $activationRequest->chassis_number }}</td>
                <td><strong>Engine No:</strong> {{ $activationRequest->engine_number }}</td>
            </tr>
            <tr>
                <td><strong>Color:</strong> {{ $activationRequest->vehicle_color }}</td>
                <td><strong>Vehicle Type:</strong> {{ $activationRequest->vehicle_type->name ?? '-' }}</td>
            </tr>
            <tr>
                <td>
                    <p><strong>Product SKU:</strong> {{ $activationRequest->product_master->sku ?? '-' }}</p>
                    <p><strong>Product Name:</strong> {{ $activationRequest->product_master->product_model->product_model ?? '-' }}</p>
                    <p><strong>VTS Number:</strong> {{ $activationRequest->product_master->vts->vts_number ?? '-' }}</p>
                    <p><strong>SIM Number:</strong> {{ $activationRequest->product_master->vts->sim_number ?? '-' }}</p>
                    <p><strong>SIM Operator:</strong> {{ $activationRequest->product_master->vts->operator ?? '-' }}</p>
                </td>
                <td>
                    <p><strong>Party Type:</strong> {{ $activationRequest->party_type->title ?? '-' }}</p>
                    <p><strong>Party Name:</strong> {{ $activationRequest->select_party->name ?? '-' }}</p>
                    <p><strong>Party Mobile:</strong> {{ $activationRequest->select_party->mobile_number ?? '-' }}</p>
                </td>
            </tr>
            <tr>
                <td><strong>Request Date:</strong> {{ \Carbon\Carbon::parse($activationRequest->request_date)->format('d-m-Y') }}</td>
                <td><strong>Activated By:</strong> {{ $activationRequest->created_by->name ?? '-' }}</td>
            </tr>

            <tr><th colspan="2">Activation Status</th></tr>
            <tr>
                <td>
                    <p><strong>Status:</strong> {{ $activationRequest->status }}</p>
                    <p><strong>Activation Date:</strong> {{ \Carbon\Carbon::parse($activationRequest->request_date)->format('d-m-Y') }}</p>
                </td>
                <td>
                    <p><strong>Warranty Expires:</strong> {{ $activationRequest->warranty ? \Carbon\Carbon::parse($activationRequest->warranty)->format('d-m-Y') : '-' }}</p>
                    <p><strong>AMC Expires:</strong> {{ $activationRequest->amc ? \Carbon\Carbon::parse($activationRequest->amc)->format('d-m-Y') : '-' }}</p>
                    <p><strong>Subscription Expires:</strong> {{ $activationRequest->subscription ? \Carbon\Carbon::parse($activationRequest->subscription)->format('d-m-Y') : '-' }}</p>
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

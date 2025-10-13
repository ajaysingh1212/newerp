<!DOCTYPE html>
<html>
<head>
    <title>Print Invoice - {{ $activationRequest->activation_request_id }}</title>
    <style>
        body { font-family: Arial, sans-serif; }
        @media print {
            .no-print { display: none; }
            body { padding: 20px; }
        }
        .header { text-align: center; margin-bottom: 20px; }
        .section { margin-bottom: 30px; }
        .section-title { background-color: #f8f9fa; padding: 8px; font-weight: bold; }
        .row { display: flex; flex-wrap: wrap; margin-right: -15px; margin-left: -15px; }
        .col-6 { flex: 0 0 50%; max-width: 50%; padding: 0 15px; }
        table { width: 100%; border-collapse: collapse; }
        table, th, td { border: 1px solid #dee2e6; }
        th, td { padding: 8px; text-align: left; }
        .footer { margin-top: 50px; text-align: right; }
        .print-actions { text-align: center; margin: 20px 0; }
    </style>
</head>
<body>
    @include('watermark')
    <div class="print-actions no-print">
        <button onclick="window.print()" class="btn btn-primary">Print Now</button>
        <button onclick="window.close()" class="btn btn-secondary">Close</button>
    </div>

    <div class="header">
        <h2>Invoice</h2>
        <p>Invoice #: {{ $activationRequest->activation_request_id }}</p>
        <p>Date: {{ now()->format('d-m-Y') }}</p>
    </div>

    <div class="row">
        <div class="col-6">
            <div class="section">
                <div class="section-title">Customer Details</div>
                <p><strong>Name:</strong> {{ $activationRequest->user->name }}</p>
                <p><strong>Mobile:</strong> {{ $activationRequest->user->mobile_number }}</p>
                <p><strong>Address:</strong> {!! $activationRequest->user->full_address !!}</p>
                <p><strong>District:</strong> {{ $activationRequest->user->district->districts ?? 'N/A' }}</p>
                <p><strong>State:</strong> {{ $activationRequest->user->state->state_name ?? 'N/A' }}</p>
                <p><strong>PIN Code:</strong> {{ $activationRequest->user->pin_code ?? 'N/A' }}</p>
            </div>
        </div>
        <div class="col-6">
            <div class="section">
                <div class="section-title">Vehicle Details</div>
                <p><strong>Model:</strong> {{ $activationRequest->vehicle->vehicle_model }}</p>
                <p><strong>Reg. No:</strong> {{ $activationRequest->vehicle->vehicle_number }}</p>
                <p><strong>Chassis No:</strong> {{ $activationRequest->vehicle->chassis_number }}</p>
                <p><strong>Engine No:</strong> {{ $activationRequest->vehicle->engine_number }}</p>
                <p><strong>Vehicle Color:</strong> {{ $activationRequest->vehicle->vehicle_color }}</p>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Product Details</div>
        <table>
            <thead>
                <tr>
                    <th>Product Model</th>
                    <th>IMEI</th>
                    <th>VTS No</th>
                    <th>SIM No</th>
                    <th>Operator</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $activationRequest->product->productModel->product_model ?? 'N/A' }}</td>
                    <td>{{ $activationRequest->product->imei->imei_number ?? 'N/A' }}</td>
                    <td>{{ $activationRequest->product->vts->vts_number ?? 'N/A' }}</td>
                    <td>{{ $activationRequest->product->vts->sim_number ?? 'N/A' }}</td>
                    <td>{{ $activationRequest->product->vts->operator ?? 'N/A' }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Service Details</div>
        <table>
            <thead>
                <tr>
                    <th>Warranty (Months)</th>
                    <th>Subscription (Months)</th>
                    <th>AMC (Months)</th>
                    <th>Activation Date</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $activationRequest->product->warranty ?? 'N/A' }}</td>
                    <td>{{ $activationRequest->product->subscription ?? 'N/A' }}</td>
                    <td>{{ $activationRequest->product->amc ?? 'N/A' }}</td>
                    <td>{{ $activationRequest->request_date ?? 'N/A' }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Employee Details</div>
        <p><strong>Name:</strong> {{ $activationRequest->createdBy->name ?? 'N/A' }}</p>
        <p><strong>Mobile No:</strong> {{ $activationRequest->createdBy->mobile_number ?? 'N/A' }}</p>
        <p><strong>Email:</strong> {{ $activationRequest->createdBy->email ?? 'N/A' }}</p>
    </div>

    <div class="footer">
        <p>Authorized Signature</p>
        <p>_________________________</p>
    </div>
</body>
</html>

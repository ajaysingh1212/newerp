<!DOCTYPE html>
<html>
<head>
    <title>Invoice - {{ $activationRequest->activation_request_id }}</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .header { text-align: center; margin-bottom: 20px; }
        .section { margin-bottom: 30px; }
        .section-title { background-color: #f8f9fa; padding: 8px; font-weight: bold; }
        .row { display: flex; flex-wrap: wrap; margin-right: -15px; margin-left: -15px; }
        .col-6 { flex: 0 0 50%; max-width: 50%; padding: 0 15px; }
        table { width: 100%; border-collapse: collapse; }
        table, th, td { border: 1px solid #dee2e6; }
        th, td { padding: 8px; text-align: left; }
        .footer { margin-top: 50px; text-align: right; }
    </style>
</head>
<body>
    @include('watermark')
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
                <p><strong>Address:</strong> {{ $activationRequest->user->full_address }}</p>
            </div>
        </div>
        <div class="col-6">
            <div class="section">
                <div class="section-title">Vehicle Details</div>
                <p><strong>Model:</strong> {{ $activationRequest->vehicle->vehicle_model }}</p>
                <p><strong>Reg. No:</strong> {{ $activationRequest->vehicle->vehicle_number }}</p>
                <p><strong>Chassis No:</strong> {{ $activationRequest->vehicle->chassis_number }}</p>
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
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $activationRequest->product->productModel->product_model ?? 'N/A' }}</td>
                    <td>{{ $activationRequest->product->imei->imei_number ?? 'N/A' }}</td>
                    <td>{{ $activationRequest->product->vts->vts_number ?? 'N/A' }}</td>
                    <td>{{ $activationRequest->product->vts->sim_number ?? 'N/A' }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Service Details</div>
        <table>
            <thead>
                <tr>
                    <th>Warranty</th>
                    <th>Subscription</th>
                    <th>AMC</th>
                    <th>Activation Date</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $activationRequest->product->warranty ?? 'N/A' }} months</td>
                    <td>{{ $activationRequest->product->subscription ?? 'N/A' }} months</td>
                    <td>{{ $activationRequest->product->amc ?? 'N/A' }} months</td>
                    <td>{{ $activationRequest->history->request_date ?? 'N/A' }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="footer">
        <p>Authorized Signature</p>
        <p>_________________________</p>
    </div>
</body>
</html>
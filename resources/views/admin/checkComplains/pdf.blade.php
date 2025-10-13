<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Check Complain Invoice #{{ $checkComplain->id }}</title>
    <style>
        @page { margin: 10mm; }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            padding: 0; margin: 0; color: #222;
        }
        .invoice-box {
            width: 100%; max-width: 900px;
            margin: 0 auto; background: #fff;
            padding: 20px; border-top: 5px solid #007bff;
        }
        .header {
            background: #007bff;
            padding: 10px; text-align: center;
            color: white; margin-bottom: 20px;
        }
        .company-info {
            background: #e9f5ff;
            padding: 15px; border-radius: 10px;
            margin-bottom: 20px;
        }
        .company-info p {
            margin: 3px 0; font-size: 13px;
        }
        table.details {
            width: 100%; border-collapse: collapse; font-size: 13px; margin-top: 10px;
        }
        .details th {
            background: #343a40; color: white; padding: 8px;
            border: 1px solid #444;
            text-align: left;
        }
        .details td {
            padding: 8px; border: 1px solid #ddd;
            vertical-align: top;
        }
        .details tr:nth-child(even) { background: #f8f9fa; }

        .label {
            background: #17a2b8;
            color: white;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 12px;
            display: inline-block;
            margin-bottom: 4px;
        }

        .footer {
            margin-top: 30px;
            font-size: 11px;
            color: #555;
        }
        .footer p {
            margin: 4px 0;
        }
    </style>
</head>
<body>

<div class="invoice-box">
   
    <!-- Company Info -->
    <div class="company-info">
        <p><strong>EEMO TRACK INDIA</strong></p>
        <p><strong>GSTIN:</strong> 10ABZFM8479K1ZC</p>
        <p><strong>Phone:</strong> 78578 68055</p>
        <p><strong>Email:</strong> info@eemotrack.com</p>
        <p><strong>Address:</strong> Kamala Market, RK Bhattacharya Road, Pirmuhani, Salimpur Ahra, Golambar, Patna, Bihar-800001</p>
    </div>

    <!-- Check Complain Details Table -->
    <table class="details">
        <tbody>
            <!-- Complain Info -->
            <tr>
                <th colspan="2">Complain Information</th>
            </tr>
            <tr>
                <td><strong>ID:</strong> {{ $checkComplain->id }}</td>
                <td><strong>Ticket Number:</strong> {{ $checkComplain->ticket_number }}</td>
            </tr>
            <tr>
                <td><strong>Status:</strong> {{ \App\Models\CheckComplain::STATUS_SELECT[$checkComplain->status] ?? '-' }}</td>
                <td><strong>Created By:</strong> {{ $checkComplain->created_by->name ?? 'N/A' }}</td>
            </tr>

            <!-- Customer Info -->
            <tr>
                <th colspan="2">Customer Details</th>
            </tr>
            <tr>
                <td><strong>Name:</strong> {{ $checkComplain->created_by->name ?? 'N/A' }}</td>
                <td><strong>Phone Number:</strong> {{ $checkComplain->created_by->mobile_number ?? 'N/A' }}</td>
            </tr>

            <!-- Vehicle Info -->
            <tr>
                <th colspan="2">Vehicle & Product Details</th>
            </tr>
            <tr>
                <td>
                    <p><strong>SKU:</strong> {{ $checkComplain->vehicle->product_master->sku ?? 'N/A' }}</p>
                    <p><strong>Vts Number:</strong> {{ $checkComplain->vehicle->product_master->vts->vts_number ?? 'N/A' }}</p>
                    <p><strong>Sim Number:</strong> {{ $checkComplain->vehicle->product_master->vts->sim_number ?? 'N/A' }}</p>
                    <p><strong>Operator:</strong> {{ $checkComplain->vehicle->product_master->vts->operator ?? 'N/A' }}</p>
                    <p><strong>Product Model:</strong> {{ $checkComplain->vehicle->product_master->product_model->product_model ?? 'N/A' }}</p>
                    <p><strong>VTS IMEI:</strong> {{ $checkComplain->vehicle->product_master->imei->imei_number ?? 'N/A' }}</p>
                </td>
                <td>
                    <p><strong>Vehicle Reg No:</strong> {{ $checkComplain->vehicle_no ?? 'N/A' }}</p>
                    <p><strong>Subscription:</strong> {{ $checkComplain->vehicle->subscription ?? 'N/A' }}</p>
                    <p><strong>AMC:</strong> {{ $checkComplain->vehicle->amc ?? 'N/A' }}</p>
                    <p><strong>Warranty:</strong> {{ $checkComplain->vehicle->warranty ?? 'N/A' }}</p>
                    <p><strong>Vehicle Type:</strong> {{ $checkComplain->vehicle->select_vehicle_type_id ?? 'N/A' }}</p>
                    <p><strong>Model:</strong> {{ $checkComplain->vehicle->vehicle_model ?? 'N/A' }}</p>
                </td>
            </tr>

            {{-- Additional Linked Vehicles --}}
            @if($checkComplain->select_vehicles->isNotEmpty())
                @foreach($checkComplain->select_vehicles as $key => $vehicle)
                    <tr>
                        <td colspan="2" style="padding-top: 10px;">
                            <h4 style="margin-bottom: 5px; color: #007bff;">Additional Linked Vehicle {{ $key + 1 }}</h4>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p><strong>Vehicle Reg No:</strong> {{ $vehicle->vehicle_number ?? 'N/A' }}</p>
                            <p><strong>Engine Number:</strong> {{ $vehicle->engine_number ?? 'N/A' }}</p>
                            <p><strong>Subscription:</strong> {{ $vehicle->subscription ?? 'N/A' }}</p>
                            <p><strong>Warranty:</strong> {{ $vehicle->warranty ?? 'N/A' }}</p>
                            <p><strong>Vehicle Type:</strong> {{ $vehicle->select_vehicle_type_id ?? 'N/A' }}</p>
                            <p><strong>Model:</strong> {{ $vehicle->vehicle_model ?? 'N/A' }}</p>
                            <p><strong>Color:</strong> {{ $vehicle->vehicle_color ?? 'N/A' }}</p>
                            <p><strong>AMC:</strong> {{ $vehicle->amc ?? 'N/A' }}</p>
                        </td>
                    </tr>
                @endforeach
            @endif

            <!-- Complaints -->
            <tr>
                <th colspan="2">Complain Details</th>
            </tr>
            <tr>
                <td colspan="2">
                    <strong>Selected Complains:</strong><br>
                    @forelse($checkComplain->select_complains as $complain)
                        <span class="label">{{ $complain->title }}</span><br>
                    @empty
                        <span>No complaints selected.</span>
                    @endforelse
                </td>
            </tr>
            <tr>
                <td colspan="2"><strong>Reason:</strong><br>{!! $checkComplain->reason !!}</td>
            </tr>
            <tr>
                <td colspan="2"><strong>Notes:</strong><br>{!! $checkComplain->notes !!}</td>
            </tr>

            <!-- Admin Message -->
            <tr>
                <th colspan="2">Admin Message</th>
            </tr>
            <tr>
                <td colspan="2">{!! $checkComplain->admin_message !!}</td>
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

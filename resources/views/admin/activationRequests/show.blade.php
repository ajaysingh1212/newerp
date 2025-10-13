@extends('layouts.admin')
@section('content')
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

    @media print {
        .action-buttons { display: none !important; }
    }
</style>

<div class="invoice-box">
    <!-- Header -->
    <div class="header">
        <h2>Activation Request Invoice</h2>
    </div>

    <!-- Company Info -->
    <div class="company-info">
        <p><strong>EEMO TRACK INDIA</strong></p>
        <p><strong>GSTIN:</strong> 10ABZFM8479K1ZC</p>
        <p><strong>Phone:</strong> 78578 68055</p>
        <p><strong>Email:</strong>info@eemotrack.com</p>
        <p><strong>Address:</strong> Kamala Market, RK Bhattacharya Road, Pirmuhani, Salimpur Ahra, Golambar, Patna, Bihar-800001</p>
    </div>

    <!-- Watermark -->
    <div>
        @include('watermark')
    </div>

    <!-- Customer and Vehicle Info -->
    <table class="details">
        <tbody>
            <tr>
                <th colspan="2">Customer Details</th>
            </tr>
            <tr>
                <td><strong>Name:</strong> {{ $activationRequest->customer_name }}</td>
                <td><strong>Mobile:</strong> {{ $activationRequest->mobile_number }}</td>
            </tr>
            <tr>
                <td><strong>WhatsApp:</strong> {{ $activationRequest->whatsapp_number }}</td>
                <td><strong>Email:</strong> {{ $activationRequest->email }}</td>
            </tr>
            <tr>
                <td><strong>Address:</strong> {!! $activationRequest->ddress !!}</td>
                <td>
                    <strong>State:</strong> {{ $activationRequest->state_id ?? '-' }}<br>
                    <strong>District:</strong> {{ $activationRequest->disrict_id ?? '-' }}
                </td>
            </tr>

            <tr>
                <th colspan="2">Vehicle & Product Details</th>
            </tr>
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
                <td><strong>Vehicle Type:</strong> {{ $activationRequest->vehicle_type_id ?? '-' }}</td>
            </tr>
            <tr>
                <td>
                    <p><strong>Product SKU:</strong> {{ $activationRequest->product_master->sku ?? '-' }}</p> 
                 <p>  <strong>Product Name:</strong> {{ $activationRequest->product_master->product_model->product_model ?? '-' }}</p>
               <p><strong>Product VTS:</strong> {{ $activationRequest->product_master->vts->vts_number ?? '-' }} </p>
                   <p> <strong>Product IMEI:</strong> {{ $activationRequest->product_master->vts->sim_number ?? '-' }} </p>     
                   <p> <strong>Product IMEI:</strong> {{ $activationRequest->product_master->vts->operator ?? '-' }} </p>      
</td>
                <td>
                   <p> <strong>Party Type:</strong> {{ $activationRequest->party_type->title ?? '-' }}</p>
                    <p><strong>Party Name:</strong> {{ $activationRequest->select_party->name ?? '-' }} </p>
                   <p> <strong>Party Mobile:</strong> {{ $activationRequest->select_party->mobile_number ?? '-' }}</P>
                </td>
            </tr>
            <tr>
                <td><strong>Request Date:</strong> {{ \Carbon\Carbon::parse($activationRequest->request_date)->format('d-m-Y') }}</td>
                <td><strong>Activated By:</strong> {{ $activationRequest->createdBy->name ?? '-' }}</td>
            </tr>

            <tr>
                <th colspan="2">Activation Status</th>
            </tr>
            <tr>
                <td>
                    <p><strong>Activation Status:</strong> {{ $activationRequest->status }} </p>
                      <p><strong>Activation Date:</strong> {{ \Carbon\Carbon::parse($activationRequest->request_date)->format('d-m-Y') }}</p>
                    </td>
                 <td>
                    <p><strong>Device warranty Expire: </strong>{{ $activationRequest->warranty ? \Carbon\Carbon::parse($activationRequest->warranty)->format('d-m-Y') : '-' }} </p>

                    <p><strong>AMC Expire: </strong>{{ $activationRequest->amc ? \Carbon\Carbon::parse($activationRequest->amc)->format('d-m-Y') : '-' }} </p>

                    <p><strong>Subscription Expire: </strong>{{ $activationRequest->subscription ? \Carbon\Carbon::parse($activationRequest->subscription)->format('d-m-Y') : '-' }} </p>
</td>

            </tr>
            <tr>
               
                    

            
            </td>
                
            </tr>
        </tbody>
    </table>

    <!-- Action Buttons -->
    <div class="action-buttons" style="margin-top: 30px; text-align: center;">
        <button onclick="window.print()" style="background-color: #007bff; color: white; border: none; padding: 10px 20px; margin: 5px; font-size: 14px; border-radius: 5px; cursor: pointer;">
            🖨️ Print Invoice
        </button>

       <a href="{{ route('admin.activationrequests.invoice', $activationRequest->id) }}">
    ⬇️ Download PDF
</a>

    </div>
</div>
@endsection

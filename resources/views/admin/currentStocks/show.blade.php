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
        <h2>Current Stock Details</h2>
    </div>

    <!-- Company Info -->
    <div class="company-info">
        <p><strong>EEMO TRACK INDIA</strong></p>
        <p><strong>GSTIN:</strong> 10ABZFM8479K1ZC</p>
        <p><strong>Phone:</strong> 78578 68055</p>
        <p><strong>Email:</strong> info@eemotrack.com</p>
        <p><strong>Address:</strong> Kamala Market, RK Bhattacharya Road, Pirmuhani, Salimpur Ahra, Golambar, Patna, Bihar-800001</p>
    </div>

    <!-- Watermark (Optional) -->
    <div>
        @include('watermark')
    </div>

    <!-- Stock Detail Table -->
    <table class="details">
        <tbody>
            <tr>
                <th colspan="2">Stock Information</th>
            </tr>
            <tr>
                <td><strong>ID:</strong> {{ $currentStock->id }}</td>
                <td><strong>SKU:</strong> {{ $currentStock->sku }}</td>
            </tr>
            <tr>
                <td><strong>Product Model:</strong> {{ $productMaster->product_model->product_model ?? '-' }}</td>
                <td><strong>IMEI:</strong> {{ $productMaster->imei->imei_number ?? '-' }}</td>
            </tr>
            <tr>
                <td><strong>VTS Number:</strong> {{ $productMaster->vts->vts_number ?? '-' }}</td>
                <td><strong>Operator:</strong> {{ $productMaster->vts->operator ?? '-' }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Action Buttons -->
    <div class="action-buttons" style="margin-top: 30px; text-align: center;">
        <button onclick="window.print()" style="background-color: #007bff; color: white; border: none; padding: 10px 20px; margin: 5px; font-size: 14px; border-radius: 5px; cursor: pointer;">
            🖨️ Print Details
        </button>

        <a href="{{ route('admin.current-stocks.invoice', $currentStock->id) }}" style="margin-left: 15px; font-size: 16px; color: #007bff; text-decoration: none;">
            ⬇️ Download PDF
        </a>
    </div>
</div>
@endsection

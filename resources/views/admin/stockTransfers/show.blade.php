@extends('layouts.admin')
@section('content')

<style>
    @page { margin: 10mm; }
    body {
        font-family: DejaVu Sans, sans-serif;
        font-size: 12px;
        color: #222;
    }
    .invoice-box {
        width: 100%;
        max-width: 950px;
        margin: 0 auto;
        background: #fff;
        padding: 20px;
        border-top: 5px solid #007bff;
    }
    .header {
        background: #007bff;
        color: white;
        padding: 15px;
        text-align: center;
        margin-bottom: 20px;
    }
    .company-info {
        background: #e9f5ff;
        padding: 15px;
        border-radius: 10px;
        margin-bottom: 20px;
    }
    .company-info p {
        margin: 3px 0;
        font-size: 13px;
    }
    table.details {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
        margin-top: 10px;
    }
    .details th {
        background: #343a40;
        color: white;
        padding: 8px;
        border: 1px solid #444;
        text-align: left;
    }
    .details td {
        padding: 8px;
        border: 1px solid #ddd;
        vertical-align: top;
    }
    .details tr:nth-child(even) { background: #f8f9fa; }

    .action-buttons {
        text-align: center;
        margin-top: 30px;
    }
    .action-buttons button,
    .action-buttons a {
        background-color: #007bff;
        color: white;
        border: none;
        padding: 10px 20px;
        margin: 5px;
        font-size: 14px;
        border-radius: 5px;
        text-decoration: none;
    }
    .action-buttons a {
        display: inline-block;
    }
    @media print {
        .action-buttons { display: none !important; }
    }
</style>

<div class="invoice-box">
    <div class="header">
        <h2>Stock Transfer Details</h2>
    </div>

    <div class="company-info">
        <p><strong>EEMO TRACK INDIA</strong></p>
        <p><strong>GSTIN:</strong> 10ABZFM8479K1ZC</p>
        <p><strong>Phone:</strong> 78578 68055</p>
        <p><strong>Email:</strong> info@eemotrack.com</p>
        <p><strong>Address:</strong> Kamala Market, RK Bhattacharya Road, Pirmuhani, Salimpur Ahra, Golambar, Patna, Bihar-800001</p>
    </div>

    <!-- Watermark -->
    @include('watermark')

    <table class="details">
    <tr>
        <th>Transfer To ({{ $stockTransfer->transfer_date }})</th>
        <th>Transfer From ({{ $stockTransfer->transfer_date }})</th>
    </tr>
    <tr>
        <td>
            @if($stockTransfer->select_user)
                Name: {{ $stockTransfer->select_user->name }}<br>
                Email: {{ $stockTransfer->select_user->email }}<br>
                Phone: {{ $stockTransfer->select_user->mobile_number ?? '-' }}<br>
            @else
                <em>-</em><br>
            @endif
        </td>
        <td>
            @if($stockTransfer->transferUser)
                Name: {{ $stockTransfer->transferUser->name }}<br>
                Email: {{ $stockTransfer->transferUser->email }}<br>
                Phone: {{ $stockTransfer->transferUser->mobile_number ?? '-' }}<br>
            @else
                <em>No Transfer User Found</em>
            @endif
        </td>
    </tr>
</table>


    <table class="details" style="margin-top: 25px;">
        <thead>
            <tr>
                <th>Product</th>
                <th>Device Warranty</th>
                <th>AMC</th>
                <th>MRP</th>
                <th>GST</th>
                <th>Quantity</th>
                <th>Discount Value</th>
                <th>Final Price</th>
            </tr>
        </thead>
   @php
    $groupedProducts = [];

    foreach ($stockTransfer->select_products as $product) {
        $productModel = $product->product?->product_model?->product_model ?? 'N/A';

        if (!isset($groupedProducts[$productModel])) {
            $groupedProducts[$productModel] = [
                'product_model' => $productModel,
                'qty' => 1,
                'total_price' => $product->pivot->final_price ?? 0,
                'warranty' => $product->pivot->warranty ?? '-',
                'amc' => $product->pivot->amc ?? '-',
                'mrp' => $product->pivot->mrp,
                'role_price' => $product->pivot->role_price,
                'discount_type' => $product->pivot->discount_type,
                'discount_value' => $product->pivot->discount_value,
            ];
        } else {
            $groupedProducts[$productModel]['qty'] += 1;
            $groupedProducts[$productModel]['total_price'] += $product->pivot->final_price ?? 0;
        }
    }

    $grandTotalQty = 0;
    $grandTotalPrice = 0;
@endphp

<tbody>
    @foreach($groupedProducts as $item)
        @php
            $grandTotalQty += $item['qty'];
            $grandTotalPrice += $item['total_price'];
        @endphp

        <tr>
            <td><strong>{{ $item['product_model'] }}</strong></td>
            <td>{{ $item['warranty'] }}</td>
            <td>{{ $item['amc'] }}</td>
            <td>₹{{ number_format($item['mrp'], 2) }}</td>
            <td>₹{{ number_format($item['role_price'], 2) }}</td>
            <td>{{ $item['qty'] }}</td>
            <td>
                @if($item['discount_type'] === 'percentage')
                    {{ $item['discount_value'] }}%
                @else
                    ₹{{ number_format($item['discount_value'], 2) }}
                @endif
            </td>
            <td>₹{{ number_format($item['total_price'], 2) }}</td>
        </tr>
    @endforeach
</tbody>

<tfoot>
    <tr>
        <th colspan="5">Total</th>
        <th>{{ $grandTotalQty }}</th>
        <th></th>
        <th>₹{{ number_format($grandTotalPrice, 2) }}</th>
    </tr>
</tfoot>



   

    </table>

    <div class="action-buttons">
        <button onclick="window.print()">🖨️ Print</button>
        <a href="{{ route('admin.stock-transfers.index') }}">🔙 Back to List</a>
    </div>
</div>
@endsection

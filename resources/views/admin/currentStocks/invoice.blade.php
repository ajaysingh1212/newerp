<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Current Stock Details</title>
    <style>
        @page { margin: 10mm; }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 0;
            color: #222;
            background: #f4f6f9;
        }
        .invoice-box {
            width: 100%;
            max-width: 900px;
            margin: 20px auto;
            background: #fff;
            padding: 25px 30px;
            border-top: 5px solid #007bff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.08);
        }
        .header {
            background: #007bff;
            color: white;
            padding: 15px;
            text-align: center;
            border-radius: 5px 5px 0 0;
            margin-bottom: 25px;
        }
        .company-info {
            background: #e9f5ff;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 25px;
            line-height: 1.6;
        }
        .company-info p {
            margin: 2px 0;
            font-size: 13px;
        }
        table.details {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 13px;
        }
        .details th {
            background-color: #343a40;
            color: white;
            padding: 10px;
            border: 1px solid #444;
            text-align: left;
        }
        .details td {
            padding: 10px;
            border: 1px solid #ddd;
        }
        .details tr:nth-child(even) {
            background: #f8f9fa;
        }
        .footer {
            margin-top: 40px;
            font-size: 12px;
        }
        .footer p {
            margin: 4px 0;
        }
        .action-buttons {
            text-align: center;
            margin-top: 30px;
        }
        .action-buttons button,
        .action-buttons a {
            display: inline-block;
            padding: 10px 20px;
            margin: 5px;
            font-size: 14px;
            text-decoration: none;
            border-radius: 5px;
        }
        .action-buttons button {
            background-color: #007bff;
            color: #fff;
            border: none;
            cursor: pointer;
        }
        .action-buttons a {
            color: #007bff;
            background: #f0f8ff;
            border: 1px solid #007bff;
        }
        @media print {
            .action-buttons {
                display: none !important;
            }
        }
    </style>
</head>
<body>

<div class="invoice-box">
    
    <!-- Header -->
    <div class="header">
        <h2>Current Stock Details</h2>
    </div>

    <!-- Company Info -->
    <div class="company-info">
        <p><strong>EEMOTRACK INDIA</strong></p>
        <p><strong>GSTIN:</strong> 10ABZFM8479K1ZC</p>
        <p><strong>Phone:</strong> 78578 68055</p>
        <p><strong>Email:</strong> info@eemotrack.com</p>
        <p><strong>Address:</strong> Kamala Market, RK Bhattacharya Road, Pirmuhani, Salimpur Ahra, Golambar, Patna, Bihar-800001</p>
    </div>

    <!-- Watermark -->
    @include('watermark')

    <!-- Stock Details -->
    <table class="details">
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
    </table>

    <!-- Footer -->
    <div class="footer">
        <p style="text-align: center; font-style: italic;">
            Thank you for trusting <strong>EEMOTRACK INDIA</strong>. We value your business.
        </p>

        <table width="100%" style="margin-top: 15px;">
            <tr>
                <td style="text-align: left;">
                    <p><strong>Support:</strong></p>
                    <p>Email: marutisuzukiventures@gmail.com</p>
                    <p>Phone: 9263906099</p>
                </td>
                <td style="text-align: right;">
                    <p><strong>Authorized Signature</strong></p>
                    <div style="height: 30px; border-bottom: 1px solid #000; width: 160px; margin-left: auto;"></div>
                    <p style="margin-top: 4px;">(Company Seal)</p>
                </td>
            </tr>
        </table>

        <p style="text-align: center; margin-top: 12px;">
            This is a system-generated invoice. No signature required.
        </p>
    </div>

   

</div>

</body>
</html>

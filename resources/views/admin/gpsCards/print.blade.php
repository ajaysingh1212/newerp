<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print GPS Smart Card</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Orbitron:wght@500;700;800;900&family=Rajdhani:wght@500;600;700&family=Share+Tech+Mono&display=swap');

        :root {
            --print-bg: #07111d;
        }

        * {
            box-sizing: border-box;
        }

        @page {
            size: 86mm 54mm;
            margin: 0;
        }

        body {
            margin: 0;
            min-height: 100vh;
            padding: 28px 16px 42px;
            display: flex;
            flex-direction: column;
            align-items: center;
            background:
                radial-gradient(circle at top left, rgba(61, 189, 181, 0.16), transparent 30%),
                radial-gradient(circle at bottom right, rgba(240, 124, 38, 0.14), transparent 26%),
                var(--print-bg);
            color: #fff;
            font-family: 'Rajdhani', sans-serif;
        }

        .print-toolbar {
            width: min(100%, 1180px);
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            justify-content: flex-start;
            margin-bottom: 24px;
        }

        .print-toolbar a,
        .print-toolbar button {
            border: 0;
            border-radius: 12px;
            padding: 12px 18px;
            background: #153251;
            color: #fff;
            text-decoration: none;
            font-size: 14px;
            font-weight: 700;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            cursor: pointer;
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.22);
        }

        .print-toolbar button.primary {
            background: linear-gradient(135deg, #3dbdb5, #2f8fcb);
        }

        @include('gps_card_lookup.partials.smart-card-styles')

        @media print {
            body {
                padding: 0;
                background: #fff;
                display: block;
            }

            .print-toolbar {
                display: none !important;
            }
        }
    </style>
</head>
<body>
@php
    $activation = $gpsCard->assignedActivationRequest;
    $vehicleNumber = $activation?->vehicle?->vehicle_number ?? $activation?->vehicle_reg_no ?? 'N/A';
    $productModelName = $gpsCard->productModel?->product_model ?? $gpsCard->productModel?->name ?? 'UNASSIGNED';
@endphp

<div class="print-toolbar">
    <button type="button" class="primary" onclick="window.print()">Print Card</button>
    <a href="{{ route('admin.gps-cards.show', $gpsCard->id) }}">Back to Card</a>
</div>

@include('gps_card_lookup.partials.smart-card', [
    'gpsCard' => $gpsCard,
    'activation' => $activation,
    'vehicleNumber' => $vehicleNumber,
    'productModelName' => $productModelName,
    'smartCardScope' => 'print-' . $gpsCard->card_number,
    'showCardSectionShell' => false,
    'showCardDownloadButton' => true,
    'showCardTitle' => false,
])

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
    @include('gps_card_lookup.partials.smart-card-scripts')
</script>
</body>
</html>

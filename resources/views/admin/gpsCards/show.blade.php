@extends('layouts.admin')
@section('content')

@php
    $seed = str_repeat(md5($gpsCard->card_number . $gpsCard->batch_code), 12);
@endphp

<style>
    .gps-show {
        --gps-deep: #091a2b;
        --gps-mid: #112e4c;
        --gps-cyan: #5bf2ff;
        --gps-orange: #ff9f45;
        --gps-text: rgba(255,255,255,0.92);
    }

    .gps-show__toolbar {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 18px;
    }

    .gps-device-card {
        position: relative;
        min-height: 355px;
        border-radius: 30px;
        overflow: hidden;
        padding: 28px 32px;
        color: var(--gps-text);
        background:
            radial-gradient(circle at 10% 85%, rgba(255, 159, 69, 0.12), transparent 22%),
            radial-gradient(circle at 82% 18%, rgba(91, 242, 255, 0.18), transparent 18%),
            linear-gradient(145deg, #091a2b 0%, #102946 48%, #07111b 100%);
        box-shadow: 0 34px 62px rgba(9, 18, 31, 0.3);
        border: 1px solid rgba(91, 242, 255, 0.16);
        margin-bottom: 26px;
    }

    .gps-device-card::before,
    .gps-device-card::after {
        content: "";
        position: absolute;
        inset: 0;
        pointer-events: none;
    }

    .gps-device-card::before {
        background:
            linear-gradient(rgba(91, 242, 255, 0.06) 1px, transparent 1px),
            linear-gradient(90deg, rgba(91, 242, 255, 0.06) 1px, transparent 1px);
        background-size: 38px 38px;
    }

    .gps-device-card::after {
        background:
            linear-gradient(115deg, transparent 0 40%, rgba(91,242,255,0.05) 40% 42%, transparent 42% 100%);
    }

    .gps-device-card__trim {
        position: absolute;
        inset: auto 0 0 0;
        height: 6px;
        background: linear-gradient(90deg, #ffb347, #ff6958 52%, #30d4ff);
    }

    .gps-device-card__header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 24px;
        margin-bottom: 26px;
    }

    .gps-device-card__logo {
        width: 86px;
        height: 86px;
        object-fit: contain;
        filter: drop-shadow(0 10px 20px rgba(0,0,0,0.25));
    }

    .gps-device-card__brand {
        text-align: right;
        font-family: Bahnschrift, "Trebuchet MS", sans-serif;
        text-transform: uppercase;
        letter-spacing: 0.2rem;
    }

    .gps-device-card__brand small {
        display: block;
        color: var(--gps-cyan);
        letter-spacing: 0.32rem;
        margin-top: 10px;
        font-size: 0.68rem;
    }

    .gps-device-card__chip {
        width: 72px;
        height: 54px;
        border-radius: 14px;
        background: linear-gradient(135deg, #f7db8a, #be8a2d 52%, #f3d16d 100%);
        box-shadow: inset 0 0 0 1px rgba(0,0,0,0.08), 0 16px 24px rgba(0,0,0,0.18);
        margin-bottom: 32px;
        position: relative;
    }

    .gps-device-card__chip::before,
    .gps-device-card__chip::after {
        content: "";
        position: absolute;
        border-radius: 12px;
        border: 1px solid rgba(135, 92, 15, 0.28);
    }

    .gps-device-card__chip::before {
        inset: 9px;
    }

    .gps-device-card__chip::after {
        inset: 18px 8px;
    }

    .gps-device-card__meta {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        gap: 22px;
        margin-bottom: 78px;
    }

    .gps-device-card__eyebrow {
        color: var(--gps-cyan);
        text-transform: uppercase;
        letter-spacing: 0.32rem;
        font-size: 0.72rem;
    }

    .gps-device-card__model {
        font-family: Bahnschrift, "Trebuchet MS", sans-serif;
        font-size: 2.7rem;
        line-height: 1;
        letter-spacing: 0.1rem;
        margin-top: 10px;
    }

    .gps-device-card__dates {
        display: grid;
        grid-template-columns: repeat(2, minmax(96px, 1fr));
        gap: 18px;
        text-align: center;
    }

    .gps-device-card__date-label {
        color: rgba(255,255,255,0.56);
        font-size: 0.72rem;
        text-transform: uppercase;
        letter-spacing: 0.22rem;
    }

    .gps-device-card__date-value {
        font-family: Bahnschrift, "Trebuchet MS", sans-serif;
        font-size: 1.2rem;
        margin-top: 10px;
        letter-spacing: 0.1rem;
    }

    .gps-device-card__number {
        font-family: "Lucida Console", Consolas, monospace;
        font-size: 2.3rem;
        letter-spacing: 0.52rem;
        margin-bottom: 18px;
    }

    .gps-device-card__footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        color: rgba(255,255,255,0.62);
        font-size: 0.78rem;
        text-transform: uppercase;
        letter-spacing: 0.26rem;
    }

    .gps-device-card__status {
        padding: 10px 16px;
        border-radius: 999px;
        background: rgba(255,255,255,0.08);
        color: #fff;
    }

    .gps-device-card--back {
        min-height: 422px;
    }

    .gps-device-card__magnetic {
        height: 82px;
        border-radius: 16px;
        margin: 8px -6px 22px;
        background:
            linear-gradient(90deg, rgba(0,0,0,0.88), rgba(21,31,41,0.95), rgba(0,0,0,0.88)),
            repeating-linear-gradient(90deg, rgba(255,255,255,0.04), rgba(255,255,255,0.04) 2px, transparent 2px, transparent 8px);
        box-shadow: inset 0 0 24px rgba(255,255,255,0.04);
    }

    .gps-device-card__back-grid {
        display: grid;
        grid-template-columns: 210px 1fr;
        gap: 28px;
        align-items: start;
        margin-bottom: 28px;
    }

    .gps-qr {
        width: 196px;
        height: 196px;
        position: relative;
        border-radius: 18px;
        background: #fff;
        padding: 18px;
        box-shadow: 0 24px 40px rgba(0,0,0,0.24);
    }

    .gps-qr__grid {
        display: grid;
        grid-template-columns: repeat(15, 1fr);
        gap: 2px;
        width: 100%;
        height: 100%;
    }

    .gps-qr__grid span {
        border-radius: 1px;
        background: transparent;
    }

    .gps-qr__grid span.is-on {
        background: #111;
    }

    .gps-qr__finder {
        position: absolute;
        width: 44px;
        height: 44px;
        border: 6px solid #111;
        border-radius: 8px;
        background: #fff;
    }

    .gps-qr__finder::after {
        content: "";
        position: absolute;
        inset: 8px;
        background: #111;
        border-radius: 4px;
    }

    .gps-qr__finder--tl { top: 14px; left: 14px; }
    .gps-qr__finder--tr { top: 14px; right: 14px; }
    .gps-qr__finder--bl { bottom: 14px; left: 14px; }

    .gps-device-card__back-title {
        color: var(--gps-cyan);
        text-transform: uppercase;
        letter-spacing: 0.32rem;
        font-size: 0.72rem;
        margin-bottom: 10px;
    }

    .gps-device-card__back-value {
        font-family: Bahnschrift, "Trebuchet MS", sans-serif;
        font-size: 1.15rem;
        color: #fff;
        margin-bottom: 18px;
        word-break: break-word;
    }

    .gps-device-card__notes {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 28px;
        color: rgba(255,255,255,0.64);
        font-size: 0.97rem;
        line-height: 1.75;
    }

    .gps-device-card__notes b {
        color: var(--gps-cyan);
    }

    .gps-side-panel {
        border: 0;
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 24px 42px rgba(12, 25, 43, 0.11);
        margin-bottom: 24px;
    }

    .gps-side-panel .card-header {
        background: linear-gradient(90deg, #153451, #123763 52%, #1d5e77);
        color: #fff;
        border-bottom: 0;
        padding: 18px 22px;
    }

    .gps-side-panel .card-body {
        background: linear-gradient(180deg, #fbfdff, #eef4fb);
        padding: 22px;
    }

    .gps-side-panel .table td,
    .gps-side-panel .table th {
        vertical-align: middle;
    }

    @media (max-width: 991px) {
        .gps-device-card {
            padding: 24px;
        }

        .gps-device-card__meta,
        .gps-device-card__header,
        .gps-device-card__footer,
        .gps-device-card__back-grid,
        .gps-device-card__notes {
            display: block;
        }

        .gps-device-card__brand,
        .gps-device-card__dates {
            margin-top: 18px;
            text-align: left;
        }

        .gps-device-card__dates {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .gps-device-card__number {
            font-size: 1.7rem;
            letter-spacing: 0.22rem;
        }

        .gps-qr {
            margin-bottom: 22px;
        }
    }
</style>

<div class="gps-show">
    <div class="gps-show__toolbar">
        <a href="{{ route('admin.gps-cards.index') }}" class="btn btn-default">{{ trans('global.back_to_list') }}</a>
        <a href="{{ route('admin.gps-cards.print', $gpsCard->id) }}" target="_blank" class="btn btn-primary">
            <i class="fas fa-print mr-1"></i> Print Card
        </a>
        <a href="{{ route('admin.gps-cards.delete', $gpsCard->id) }}" class="btn btn-warning">
            <i class="fas fa-trash-alt mr-1"></i> Delete Card
        </a>
    </div>

    <div class="gps-device-card">
        <div class="gps-device-card__header">
            <img src="{{ asset('img/eemot_logo.png') }}" alt="EEMOT" class="gps-device-card__logo">
            <div class="gps-device-card__brand">
                <div>EEMOT GPS SYSTEMS</div>
                <small>Track . Navigate . Protect</small>
            </div>
        </div>

        <div class="gps-device-card__chip"></div>

        <div class="gps-device-card__meta">
            <div>
                <div class="gps-device-card__eyebrow">GPS Tracking Device</div>
                <div class="gps-device-card__model">{{ $gpsCard->productModel->product_model ?? 'UNASSIGNED MODEL' }}</div>
            </div>

            <div class="gps-device-card__dates">
                <div>
                    <div class="gps-device-card__date-label">Valid From</div>
                    <div class="gps-device-card__date-value">{{ $gpsCard->formatted_valid_from }}</div>
                </div>
                <div>
                    <div class="gps-device-card__date-label">Valid To</div>
                    <div class="gps-device-card__date-value">{{ $gpsCard->formatted_valid_to }}</div>
                </div>
            </div>
        </div>

        <div class="gps-device-card__number">{{ $gpsCard->formatted_card_number }}</div>

        <div class="mt-3" style="font-size: 0.95rem; letter-spacing: 0.12rem; color: rgba(255,255,255,0.78); text-transform: uppercase;">
            Card Holder: {{ strtoupper(\App\Models\User::where('id', $gpsCard->used_by_id)->value('name') ?? 'Not Assigned') }}
        </div>

        <div class="gps-device-card__footer">
            <span>{{ $gpsCard->usage_status }} . {{ $gpsCard->print_status }}</span>
            <span class="gps-device-card__status">{{ $gpsCard->display_status }}</span>
        </div>

        <div class="gps-device-card__trim"></div>
    </div>

    <div class="gps-device-card gps-device-card--back">
        <div class="gps-device-card__header" style="margin-bottom: 12px;">
            <img src="{{ asset('img/eemot_logo.png') }}" alt="EEMOT" class="gps-device-card__logo" style="width: 58px; height: 58px;">
            <div class="gps-device-card__brand">
                <div>Device Card</div>
                <small>Batch {{ $gpsCard->batch_code }}</small>
            </div>
        </div>

        <div class="gps-device-card__magnetic"></div>

        <div class="gps-device-card__back-grid">
            <div>
                <div class="gps-qr">
                    <div class="gps-qr__finder gps-qr__finder--tl"></div>
                    <div class="gps-qr__finder gps-qr__finder--tr"></div>
                    <div class="gps-qr__finder gps-qr__finder--bl"></div>
                    <div class="gps-qr__grid">
                        @for($row = 0; $row < 15; $row++)
                            @for($col = 0; $col < 15; $col++)
                                @php
                                    $index = (($row * 15) + $col) % strlen($seed);
                                    $char = $seed[$index];
                                    $isFinderZone = ($row < 4 && $col < 4) || ($row < 4 && $col > 10) || ($row > 10 && $col < 4);
                                    $isOn = !$isFinderZone && hexdec($char) % 2 === 0;
                                @endphp
                                <span class="{{ $isOn ? 'is-on' : '' }}"></span>
                            @endfor
                        @endfor
                    </div>
                </div>
                <div class="text-center mt-3" style="color: rgba(255,255,255,0.58); letter-spacing: 0.24rem; text-transform: uppercase; font-size: 0.76rem;">
                    Scan to verify device
                </div>
            </div>

            <div>
                <div class="gps-device-card__back-title">16-Digit Device ID</div>
                <div class="gps-device-card__back-value">{{ $gpsCard->formatted_card_number }}</div>

                <div class="gps-device-card__back-title">Assigned Product Model</div>
                <div class="gps-device-card__back-value">{{ $gpsCard->productModel->product_model ?? 'N/A' }}</div>

                <div class="gps-device-card__back-title">Card Holder Name</div>
                <div class="gps-device-card__back-value">{{ strtoupper(\App\Models\User::where('id', $gpsCard->used_by_id)->value('name') ?? 'Not Assigned') }}</div>

                <div class="gps-device-card__back-title">Generated By</div>
                <div class="gps-device-card__back-value">{{ $gpsCard->createdBy->name ?? 'System User' }}</div>

                <div class="gps-device-card__back-title">Validity Window</div>
                <div class="gps-device-card__back-value">{{ $gpsCard->formatted_valid_from }}  -  {{ $gpsCard->formatted_valid_to }}</div>
            </div>
        </div>

        <div class="gps-device-card__notes">
            <div>
                <div><b>1.</b> Ye device card har GPS unit ke saath issue kiya gaya unique identity asset hai.</div>
                <div><b>2.</b> Batch code <strong>{{ $gpsCard->batch_code }}</strong> dispatch tracking aur stock reconciliation ke liye maintain kiya gaya hai.</div>
            </div>
            <div>
                <div><b>3.</b> Card number ko unauthorized share na karein. Service mapping aur device traceability isi ID se hoti hai.</div>
                <div><b>4.</b> Warranty, service aur replacement process mein is card ko reference ke roop mein use kiya ja sakta hai.</div>
            </div>
        </div>

        <div class="gps-device-card__trim"></div>
    </div>

    <div class="row">
        <div class="col-lg-5">
            <div class="card gps-side-panel">
                <div class="card-header">
                    <strong>Card Information</strong>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped mb-0">
                        <tbody>
                            <tr>
                                <th width="38%">Card ID</th>
                                <td>{{ $gpsCard->id }}</td>
                            </tr>
                            <tr>
                                <th>Batch Code</th>
                                <td>{{ $gpsCard->batch_code }}</td>
                            </tr>
                            <tr>
                                <th>Product Model</th>
                                <td>{{ $gpsCard->productModel->product_model ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Card Number</th>
                                <td>{{ $gpsCard->formatted_card_number }}</td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td>{{ $gpsCard->display_status }}</td>
                            </tr>
                            <tr>
                                <th>Usage</th>
                                <td>{{ $gpsCard->usage_status }}</td>
                            </tr>
                            <tr>
                                <th>Card Holder</th>
                                <td>{{ strtoupper(\App\Models\User::where('id', $gpsCard->used_by_id)->value('name') ?? 'Not Assigned') }}</td>
                            </tr>
                            <tr>
                                <th>Used For Activation</th>
                                <td>
                                    @if($gpsCard->assignedActivationRequest)
                                        <a href="{{ route('admin.activation-requests.show', $gpsCard->assignedActivationRequest->id) }}" target="_blank">
                                            #{{ $gpsCard->assignedActivationRequest->id }}
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Print Status</th>
                                <td>{{ $gpsCard->print_status }}</td>
                            </tr>
                            <tr>
                                <th>Printed At</th>
                                <td>{{ optional($gpsCard->printed_at)->format('d M Y, h:i A') ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Generated By</th>
                                <td>{{ $gpsCard->createdBy->name ?? 'System User' }}</td>
                            </tr>
                            <tr>
                                <th>Created At</th>
                                <td>{{ optional($gpsCard->created_at)->format('d M Y, h:i A') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card gps-side-panel">
                <div class="card-header">
                    <strong>Batch Cards</strong>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-hover mb-0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Card Number</th>
                                <th>Holder</th>
                                <th>Print</th>
                                <th>Status</th>
                                <th>Open</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($batchCards as $batchCard)
                                <tr>
                                    <td>{{ $batchCard->id }}</td>
                                    <td>{{ $batchCard->formatted_card_number }}</td>
                                    <td>{{ $batchCard->card_holder_name ?: '-' }}</td>
                                    <td>{{ $batchCard->print_status }}</td>
                                    <td>{{ $batchCard->display_status }}</td>
                                    <td>
                                        <a href="{{ route('admin.gps-cards.show', $batchCard->id) }}" class="btn btn-xs btn-primary">
                                            View
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

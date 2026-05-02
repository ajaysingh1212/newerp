<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Device Details — GPS Tracker</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Cabinet+Grotesk:wght@400;500;700;800;900&family=Instrument+Mono:ital,wght@0,400;1,400&family=Instrument+Sans:wght@400;500;600&display=swap" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

:root {
    --bg:      #fafaf8;
    --card:    #ffffff;
    --ink:     #111118;
    --accent:  #2563eb;
    --success: #16a34a;
    --danger:  #dc2626;
    --warning: #d97706;
    --border:  #e5e5e0;
    --muted:   #71717a;
    --pale:    #f4f4f1;
    --mono:    'Instrument Mono', monospace;
}

html, body { background: var(--bg); color: var(--ink); font-family: 'Instrument Sans', sans-serif; }

/* ── Nav ── */
.top-nav {
    background: var(--card);
    border-bottom: 1px solid var(--border);
    padding: 0.875rem 1.5rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    position: sticky;
    top: 0;
    z-index: 50;
    flex-wrap: wrap;
}

.nav-brand {
    display: flex;
    align-items: center;
    gap: 10px;
    font-family: 'Cabinet Grotesk', sans-serif;
    font-weight: 800;
    font-size: 1rem;
    color: var(--ink);
    text-decoration: none;
}

.nav-logo {
    width: 32px;
    height: 32px;
    background: var(--ink);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.card-chip {
    font-family: var(--mono);
    font-size: 0.8rem;
    letter-spacing: 0.1em;
    color: var(--muted);
    background: var(--pale);
    padding: 4px 10px;
    border-radius: 100px;
    border: 1px solid var(--border);
}

.nav-actions { display: flex; gap: 8px; align-items: center; }

.nav-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 7px 14px;
    border-radius: 8px;
    font-size: 0.8rem;
    font-weight: 600;
    border: 1px solid var(--border);
    background: var(--pale);
    color: var(--ink);
    cursor: pointer;
    text-decoration: none;
    transition: all 0.15s;
}

.nav-btn:hover { background: var(--ink); color: #fff; border-color: var(--ink); }

.nav-btn-red {
    background: var(--danger);
    color: #fff;
    border-color: var(--danger);
}
.nav-btn-red:hover { background: #b91c1c; border-color: #b91c1c; color: #fff; }

/* ── Content ── */
#reportContent { max-width: 900px; margin: 0 auto; padding: 2rem 1.25rem 5rem; }

/* ── Hero ── */
.device-hero {
    background: var(--ink);
    border-radius: 20px;
    padding: 2rem;
    color: #fff;
    margin-bottom: 1.5rem;
    display: flex;
    flex-wrap: wrap;
    gap: 1.5rem;
    align-items: center;
    position: relative;
    overflow: hidden;
    animation: slideIn 0.5s ease both;
}

@keyframes slideIn {
    from { opacity:0; transform: translateY(12px); }
    to   { opacity:1; transform: translateY(0); }
}

.hero-bg-orb {
    position: absolute;
    right: -40px; bottom: -60px;
    width: 220px; height: 220px;
    background: radial-gradient(circle, rgba(37,99,235,0.35), transparent 65%);
    pointer-events: none;
}

.hero-text { flex: 1; min-width: 200px; position: relative; z-index: 1; }

.hero-vehicle-num {
    font-family: 'Cabinet Grotesk', sans-serif;
    font-size: 2.2rem;
    font-weight: 900;
    letter-spacing: -0.03em;
    line-height: 1;
    margin-bottom: 4px;
}

.hero-owner {
    font-size: 0.95rem;
    color: rgba(255,255,255,0.6);
    margin-bottom: 0.875rem;
}

.hero-tags { display: flex; flex-wrap: wrap; gap: 6px; }

.hero-tag {
    font-size: 0.72rem;
    font-weight: 500;
    color: rgba(255,255,255,0.7);
    background: rgba(255,255,255,0.08);
    border: 1px solid rgba(255,255,255,0.12);
    padding: 3px 9px;
    border-radius: 100px;
}

.hero-status-panel {
    display: flex;
    gap: 20px;
    position: relative;
    z-index: 1;
}

.hstat { text-align: center; }

.hstat-val {
    font-family: 'Cabinet Grotesk', sans-serif;
    font-size: 1.5rem;
    font-weight: 900;
    color: #fff;
}

.hstat-label {
    font-size: 0.68rem;
    color: rgba(255,255,255,0.45);
    letter-spacing: 0.07em;
    text-transform: uppercase;
}

/* ── Section ── */
.section {
    margin-bottom: 1.25rem;
    animation: fadeUp 0.4s ease both;
}

@keyframes fadeUp {
    from { opacity:0; transform: translateY(10px); }
    to   { opacity:1; transform: translateY(0); }
}

.section:nth-child(2) { animation-delay: 0.07s; }
.section:nth-child(3) { animation-delay: 0.14s; }
.section:nth-child(4) { animation-delay: 0.21s; }
.section:nth-child(5) { animation-delay: 0.28s; }
.section:nth-child(6) { animation-delay: 0.35s; }
.section:nth-child(7) { animation-delay: 0.42s; }

.section-head {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 0.75rem;
}

.s-icon {
    width: 30px;
    height: 30px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.ic-blue   { background: #dbeafe; color: #1d4ed8; }
.ic-green  { background: #dcfce7; color: #15803d; }
.ic-amber  { background: #fef3c7; color: #b45309; }
.ic-slate  { background: #f1f5f9; color: #475569; }
.ic-rose   { background: #ffe4e6; color: #be123c; }
.ic-teal   { background: #ccfbf1; color: #0f766e; }
.ic-violet { background: #ede9fe; color: #6d28d9; }

.s-title {
    font-family: 'Cabinet Grotesk', sans-serif;
    font-size: 0.95rem;
    font-weight: 800;
    color: var(--ink);
    letter-spacing: -0.01em;
}

.s-badge-wrap { margin-left: auto; }

/* ── Card ── */
.info-card {
    background: var(--card);
    border: 1px solid var(--border);
    border-radius: 14px;
    overflow: hidden;
}

/* ── Grid ── */
.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
}

.cell {
    padding: 0.875rem 1rem;
    border-right: 1px solid var(--border);
    border-bottom: 1px solid var(--border);
}

.cell:nth-child(even) { background: #fafaf8; }

.cell-lbl {
    font-size: 0.68rem;
    font-weight: 600;
    letter-spacing: 0.07em;
    text-transform: uppercase;
    color: var(--muted);
    margin-bottom: 3px;
}

.cell-val {
    font-size: 0.88rem;
    font-weight: 500;
    color: var(--ink);
}

.cell-val.mono { font-family: var(--mono); font-size: 0.82rem; letter-spacing: 0.05em; }
.cell-val.big  { font-family: 'Cabinet Grotesk', sans-serif; font-size: 1.05rem; font-weight: 800; }

/* ── Badge ── */
.badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 3px 9px;
    border-radius: 100px;
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.02em;
}

.b-green  { background: #dcfce7; color: #166534; }
.b-amber  { background: #fef3c7; color: #92400e; }
.b-red    { background: #fee2e2; color: #991b1b; }
.b-blue   { background: #dbeafe; color: #1e40af; }
.b-slate  { background: #f1f5f9; color: #475569; }
.b-violet { background: #ede9fe; color: #5b21b6; }

/* ── Expiry Bars ── */
.expiry-row {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
    gap: 1px;
    background: var(--border);
}

.expiry-cell {
    background: var(--card);
    padding: 1rem 1.1rem;
}

.ex-label { font-size: 0.7rem; font-weight: 600; letter-spacing: 0.07em; text-transform: uppercase; color: var(--muted); margin-bottom: 4px; }
.ex-date  { font-family: 'Cabinet Grotesk', sans-serif; font-size: 1rem; font-weight: 800; color: var(--ink); margin-bottom: 6px; }

.ex-bar {
    height: 5px;
    background: var(--pale);
    border-radius: 3px;
    overflow: hidden;
    margin-bottom: 5px;
}

.ex-fill { height: 100%; border-radius: 3px; }
.f-green { background: var(--success); }
.f-amber { background: var(--warning); }
.f-red   { background: var(--danger); }

.ex-info { font-size: 0.75rem; font-weight: 500; }
.t-green { color: var(--success); }
.t-amber { color: var(--warning); }
.t-red   { color: var(--danger); }

/* ── Recharge Banner ── */
.recharge-banner {
    display: flex;
    align-items: center;
    gap: 1.25rem;
    padding: 1.25rem;
    flex-wrap: wrap;
}

.rb-num {
    font-family: 'Cabinet Grotesk', sans-serif;
    font-size: 2.8rem;
    font-weight: 900;
    line-height: 1;
}

.rb-unit { font-size: 0.85rem; color: var(--muted); margin-top: 2px; }
.rb-info h4 { font-weight: 700; font-size: 0.9rem; margin-bottom: 3px; }
.rb-info p  { font-size: 0.8rem; color: var(--muted); }

/* ── Timeline ── */
.tl-wrap { padding: 1rem 1.1rem; }

.tl-item {
    display: flex;
    gap: 10px;
    padding-bottom: 1rem;
    position: relative;
}

.tl-item:not(:last-child)::before {
    content: '';
    position: absolute;
    left: 11px; top: 22px; bottom: 0;
    width: 2px;
    background: var(--border);
}

.tl-dot {
    width: 22px; height: 22px;
    border-radius: 50%;
    border: 2px solid var(--border);
    background: var(--card);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    z-index: 1;
}

.tl-dot.ok   { border-color: var(--success); background: #dcfce7; }
.tl-dot.warn { border-color: var(--warning); background: #fef3c7; }

.tl-body { flex: 1; min-width: 0; }

.tl-head {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 8px;
    margin-bottom: 2px;
}

.tl-name { font-size: 0.85rem; font-weight: 600; }
.tl-date { font-family: var(--mono); font-size: 0.72rem; color: var(--muted); white-space: nowrap; }
.tl-meta { font-size: 0.77rem; color: var(--muted); }

/* ── KYC Success ── */
.kyc-done {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 1rem 1.1rem;
    background: #f0fdf4;
    border-bottom: 1px solid var(--border);
}

.kyc-icon {
    width: 36px; height: 36px;
    background: #dcfce7;
    border-radius: 9px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--success);
    flex-shrink: 0;
}

.kyc-text h4 { font-weight: 700; font-size: 0.875rem; color: var(--success); margin-bottom: 2px; }
.kyc-text p  { font-size: 0.78rem; color: #4b7c5e; }

/* ── No Data ── */
.no-data {
    padding: 1.5rem;
    text-align: center;
    color: var(--muted);
    font-size: 0.85rem;
}

/* ── Delete Alert ── */
.del-alert {
    background: #fff5f5;
    border: 1.5px solid #fca5a5;
    border-radius: 14px;
    padding: 1rem 1.1rem;
    display: flex;
    gap: 10px;
    margin-bottom: 1.25rem;
}

.del-icon { color: var(--danger); flex-shrink: 0; margin-top: 2px; }
.del-title { font-weight: 700; color: var(--danger); margin-bottom: 3px; font-size: 0.9rem; }
.del-body  { font-size: 0.8rem; color: #7f1d1d; }

/* ── Print ── */
@media print {
    .top-nav, .nav-actions { display: none !important; }
    .section { animation: none !important; }
    .device-hero { background: #111118 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
}

@media (max-width: 480px) {
    .hero-status-panel { flex-wrap: wrap; gap: 12px; }
    .info-grid { grid-template-columns: 1fr 1fr; }
}
</style>
</head>
<body>

{{-- ── NAV ── --}}
<nav class="top-nav">
    <a href="{{ route('user.gps-card-lookup.index') }}" class="nav-brand">
        <div class="nav-logo">
            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="1.5"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/><circle cx="12" cy="9" r="2" fill="white" stroke="none"/></svg>
        </div>
        GPS Tracker
    </a>
    <span class="card-chip">{{ chunk_split($cardNumber, 4, '  ') }}</span>
    <div class="nav-actions">
        <button class="nav-btn" onclick="window.print()">
            <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
            Print
        </button>
        <button class="nav-btn nav-btn-red" id="pdfBtn">
            <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            Save PDF
        </button>
    </div>
</nav>

<div id="reportContent">

{{-- ── DELETE ALERT ── --}}
@if($deleteRecord)
<div class="del-alert">
    <div class="del-icon">
        <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
    </div>
    <div>
        <div class="del-title">Notice: A deletion record exists for this device</div>
        <div class="del-body">
            Deleted on {{ $deleteRecord->delete_date?->format('d M Y') ?? 'N/A' }}
            @if($deleteRecord->reason_for_deletion) — {{ $deleteRecord->reason_for_deletion }} @endif
        </div>
    </div>
</div>
@endif

{{-- ── HERO ── --}}
<div class="device-hero">
    <div class="hero-bg-orb"></div>
    <div class="hero-text">
        <div class="hero-vehicle-num">{{ $vehicle?->vehicle_number ?? $activation?->vehicle_reg_no ?? 'N/A' }}</div>
        <div class="hero-owner">{{ $vehicle?->owners_name ?? $activation?->customer_name ?? 'Owner' }}</div>
        <div class="hero-tags">
            @if($vehicle?->vehicle_model ?? $activation?->vehicle_model)
                <span class="hero-tag">{{ $vehicle?->vehicle_model ?? $activation?->vehicle_model }}</span>
            @endif
            @if($vehicle?->vehicle_color ?? $activation?->vehicle_color)
                <span class="hero-tag">{{ $vehicle?->vehicle_color ?? $activation?->vehicle_color }}</span>
            @endif
            @if($activation)
                <span class="hero-tag" style="color:{{ $activation->status === 'activated' ? '#86efac' : '#fca5a5' }}">
                    ● {{ ucfirst($activation->status) }}
                </span>
            @endif
            <span class="hero-tag">{{ $gpsCard->formatted_card_number }}</span>
        </div>
    </div>
    <div class="hero-status-panel">
        <div class="hstat">
            <div class="hstat-val">{{ $rechargeHistory->count() }}</div>
            <div class="hstat-label">Recharges</div>
        </div>
        <div class="hstat">
            <div class="hstat-val">{{ $kycRecharge ? '✓' : '✗' }}</div>
            <div class="hstat-label">KYC</div>
        </div>
        @if($nextRechargeInfo)
        <div class="hstat">
            <div class="hstat-val" style="color: {{ $nextRechargeInfo['is_overdue'] ? '#f87171' : '#86efac' }};">
                {{ $nextRechargeInfo['days_left'] }}d
            </div>
            <div class="hstat-label">{{ $nextRechargeInfo['is_overdue'] ? 'Overdue' : 'Till Renewal' }}</div>
        </div>
        @endif
    </div>
</div>

{{-- ── VEHICLE DETAILS ── --}}
@if($vehicle || $activation)
<div class="section">
    <div class="section-head">
        <div class="s-icon ic-teal">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
        </div>
        <span class="s-title">Vehicle Information</span>
    </div>
    <div class="info-card">
        <div class="info-grid">
            <div class="cell">
                <div class="cell-lbl">Vehicle Number</div>
                <div class="cell-val big mono">{{ $vehicle?->vehicle_number ?? $activation?->vehicle_reg_no ?? '—' }}</div>
            </div>
            <div class="cell">
                <div class="cell-lbl">Owner Name</div>
                <div class="cell-val">{{ $vehicle?->owners_name ?? $activation?->customer_name ?? '—' }}</div>
            </div>
            <div class="cell">
                <div class="cell-lbl">Vehicle Model</div>
                <div class="cell-val">{{ $vehicle?->vehicle_model ?? $activation?->vehicle_model ?? '—' }}</div>
            </div>
            <div class="cell">
                <div class="cell-lbl">Vehicle Type</div>
                <div class="cell-val">{{ $vehicle?->select_vehicle_type?->name ?? $activation?->vehicle_type?->name ?? '—' }}</div>
            </div>
            <div class="cell">
                <div class="cell-lbl">Color</div>
                <div class="cell-val">{{ $vehicle?->vehicle_color ?? $activation?->vehicle_color ?? '—' }}</div>
            </div>
            <div class="cell">
                <div class="cell-lbl">Chassis No.</div>
                <div class="cell-val mono">{{ $vehicle?->chassis_number ?? $activation?->chassis_number ?? '—' }}</div>
            </div>
            <div class="cell">
                <div class="cell-lbl">Engine No.</div>
                <div class="cell-val mono">{{ $vehicle?->engine_number ?? $activation?->engine_number ?? '—' }}</div>
            </div>
            @if($vehicle?->insurance_expiry_date)
            <div class="cell">
                <div class="cell-lbl">Insurance Expiry</div>
                <div class="cell-val">{{ \Carbon\Carbon::parse($vehicle->insurance_expiry_date)->format('d M Y') }}</div>
            </div>
            @endif
            @if($vehicle?->app_url ?? $activation?->app_url)
            <div class="cell">
                <div class="cell-lbl">Tracking App URL</div>
                <div class="cell-val">{{ $vehicle?->app_url ?? $activation?->app_url ?? '—' }}</div>
            </div>
            @endif
        </div>
    </div>
</div>
@endif

{{-- ── DEVICE DETAILS ── --}}
@if($productMaster)
<div class="section">
    <div class="section-head">
        <div class="s-icon ic-amber">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
        </div>
        <span class="s-title">Installed GPS Device</span>
    </div>
    <div class="info-card">
        <div class="info-grid">
            <div class="cell">
                <div class="cell-lbl">Device Model</div>
                <div class="cell-val">{{ $productMaster->product_model?->name ?? $productMaster->productModel?->name ?? '—' }}</div>
            </div>
            <div class="cell">
                <div class="cell-lbl">IMEI Number</div>
                <div class="cell-val mono">{{ $productMaster->imei?->imei_number ?? '—' }}</div>
            </div>
            <div class="cell">
                <div class="cell-lbl">VTS / SIM No.</div>
                <div class="cell-val mono">{{ $productMaster->vts?->vts_number ?? $productMaster->vts?->sim_number ?? '—' }}</div>
            </div>
            <div class="cell">
                <div class="cell-lbl">SKU</div>
                <div class="cell-val mono">{{ $productMaster->sku ?? '—' }}</div>
            </div>
            <div class="cell">
                <div class="cell-lbl">GPS Card No.</div>
                <div class="cell-val mono">{{ $gpsCard->formatted_card_number }}</div>
            </div>
            <div class="cell">
                <div class="cell-lbl">Card Valid Thru</div>
                <div class="cell-val">{{ $gpsCard->formatted_valid_to }}</div>
            </div>
        </div>
    </div>
</div>
@endif

{{-- ── SERVICE EXPIRY ── --}}
@if(!empty($expiryData))
<div class="section">
    <div class="section-head">
        <div class="s-icon ic-violet">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        </div>
        <span class="s-title">Service Validity</span>
    </div>
    <div class="info-card">
        <div class="expiry-row">
            @foreach(['amc' => 'AMC', 'warranty' => 'Warranty', 'subscription' => 'Subscription'] as $key => $label)
                @if(isset($expiryData[$key]))
                    @php
                        $ex  = $expiryData[$key];
                        $pct = $ex['expired'] ? 0 : min(100, max(5, $ex['days_left'] / 365 * 100));
                        $fc  = $ex['expired'] ? 'f-red' : ($ex['soon'] ? 'f-amber' : 'f-green');
                        $tc  = $ex['expired'] ? 't-red' : ($ex['soon'] ? 't-amber' : 't-green');
                    @endphp
                    <div class="expiry-cell">
                        <div class="ex-label">{{ $label }}</div>
                        <div class="ex-date">{{ $ex['date']->format('d M Y') }}</div>
                        <div class="ex-bar"><div class="ex-fill {{ $fc }}" style="width:{{ $pct }}%"></div></div>
                        <div class="ex-info {{ $tc }}">
                            @if($ex['expired']) ✗ Expired {{ $ex['days_left'] }} days ago
                            @elseif($ex['soon']) ⚡ {{ $ex['days_left'] }} days left — renew soon
                            @else ✓ {{ $ex['days_left'] }} days remaining
                            @endif
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
</div>
@endif

{{-- ── KYC ── --}}
<div class="section">
    <div class="section-head">
        <div class="s-icon ic-green">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
        </div>
        <span class="s-title">KYC Verification</span>
        <div class="s-badge-wrap">
            @if($kycRecharge)
                <span class="badge b-green">✓ Verified</span>
            @else
                <span class="badge b-amber">Pending</span>
            @endif
        </div>
    </div>
    @if($kycRecharge)
    <div class="info-card">
        <div class="kyc-done">
            <div class="kyc-icon">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
            </div>
            <div class="kyc-text">
                <h4>KYC Successfully Completed</h4>
                <p>Verified on {{ $kycRecharge->payment_date?->format('d M Y') ?? 'N/A' }} · Amount: ₹{{ number_format($kycRecharge->payment_amount ?? 0, 2) }} · {{ ucfirst($kycRecharge->payment_method ?? '—') }}</p>
            </div>
        </div>
        <div class="info-grid">
            <div class="cell">
                <div class="cell-lbl">Payment Status</div>
                <div class="cell-val">
                    <span class="badge {{ $kycRecharge->payment_status === 'paid' ? 'b-green' : 'b-amber' }}">{{ ucfirst($kycRecharge->payment_status ?? '—') }}</span>
                </div>
            </div>
            <div class="cell">
                <div class="cell-lbl">Razorpay Order</div>
                <div class="cell-val mono">{{ $kycRecharge->razorpay_order_id ?? '—' }}</div>
            </div>
            <div class="cell">
                <div class="cell-lbl">Location</div>
                <div class="cell-val">{{ $kycRecharge->location ?? '—' }}</div>
            </div>
        </div>
    </div>
    @else
    <div class="info-card"><div class="no-data">KYC not yet completed for this vehicle.</div></div>
    @endif
</div>

{{-- ── NEXT RECHARGE ── --}}
@if($nextRechargeInfo)
<div class="section">
    <div class="section-head">
        <div class="s-icon ic-blue">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M21.5 2v6h-6M2.5 22v-6h6M2 11.5a10 10 0 0 1 18.8-4.3M22 12.5a10 10 0 0 1-18.8 4.2"/></svg>
        </div>
        <span class="s-title">Renewal Status</span>
    </div>
    <div class="info-card">
        <div class="recharge-banner">
            <div>
                <div class="rb-num" style="color: {{ $nextRechargeInfo['is_overdue'] ? 'var(--danger)' : ($nextRechargeInfo['days_left'] <= 30 ? 'var(--warning)' : 'var(--success)') }}">
                    {{ $nextRechargeInfo['days_left'] }}
                </div>
                <div class="rb-unit">days {{ $nextRechargeInfo['is_overdue'] ? 'overdue' : 'until renewal' }}</div>
            </div>
            <div class="rb-info">
                <h4>Next Renewal: {{ $nextRechargeInfo['next_date']->format('d M Y') }}</h4>
                <p>{{ $nextRechargeInfo['plan_name'] }} · ₹{{ number_format($nextRechargeInfo['plan_price'], 2) }}</p>
            </div>
            @if($nextRechargeInfo['is_overdue'])
                <span class="badge b-red" style="margin-left:auto;">⚠ Overdue</span>
            @elseif($nextRechargeInfo['days_left'] <= 30)
                <span class="badge b-amber" style="margin-left:auto;">Renew Soon</span>
            @else
                <span class="badge b-green" style="margin-left:auto;">Active</span>
            @endif
        </div>
    </div>
</div>
@endif

{{-- ── RECHARGE HISTORY ── --}}
<div class="section">
    <div class="section-head">
        <div class="s-icon ic-slate">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
        </div>
        <span class="s-title">Recharge History</span>
        <div class="s-badge-wrap"><span class="badge b-slate">{{ $rechargeHistory->count() }} records</span></div>
    </div>
    @if($rechargeHistory->count() > 0)
    <div class="info-card">
        <div class="tl-wrap">
            @foreach($rechargeHistory as $rch)
            <div class="tl-item">
                <div class="tl-dot {{ $rch->payment_status === 'paid' ? 'ok' : 'warn' }}">
                    @if($rch->payment_status === 'paid')
                        <svg width="9" height="9" fill="none" viewBox="0 0 24 24" stroke="var(--success)" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                    @else
                        <svg width="9" height="9" viewBox="0 0 4 4"><circle cx="2" cy="2" r="2" fill="var(--warning)"/></svg>
                    @endif
                </div>
                <div class="tl-body">
                    <div class="tl-head">
                        <span class="tl-name">
                            {{ $rch->select_recharge?->plan_name ?? 'Recharge' }}
                            <span class="badge {{ $rch->payment_status === 'paid' ? 'b-green' : 'b-amber' }}" style="font-size:0.65rem;margin-left:5px;">{{ ucfirst($rch->payment_status ?? '—') }}</span>
                        </span>
                        <span class="tl-date">{{ $rch->created_at?->format('d M Y') }}</span>
                    </div>
                    <div class="tl-meta">
                        ₹{{ number_format($rch->payment_amount ?? 0, 2) }}
                        @if($rch->payment_method) · {{ $rch->payment_method }} @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @else
    <div class="info-card"><div class="no-data">No recharge history found.</div></div>
    @endif
</div>

{{-- Footer ── --}}
<div style="text-align:center; padding: 2rem 0 1rem; color: var(--muted); font-size: 0.78rem;">
    Generated {{ now()->format('d M Y, h:i A') }} · GPS Tracker Device Intelligence
</div>

</div>{{-- end reportContent --}}

<script>
document.getElementById('pdfBtn').addEventListener('click', function () {
    const btn = this;
    btn.disabled = true;
    btn.textContent = 'Generating…';

    html2pdf().set({
        margin:      [8, 8, 8, 8],
        filename:    'GPS_Device_{{ $cardNumber }}.pdf',
        image:       { type: 'jpeg', quality: 0.95 },
        html2canvas: { scale: 2, useCORS: true, scrollY: 0 },
        jsPDF:       { unit: 'mm', format: 'a4', orientation: 'portrait' },
        pagebreak:   { mode: ['avoid-all', 'css', 'legacy'] }
    })
    .from(document.getElementById('reportContent'))
    .save()
    .then(() => {
        btn.disabled = false;
        btn.innerHTML = `<svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg> Save PDF`;
    });
});
</script>
</body>
</html>

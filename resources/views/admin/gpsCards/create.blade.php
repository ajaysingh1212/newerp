@extends('layouts.admin')
@section('content')

<style>
@import url('https://fonts.googleapis.com/css2?family=Orbitron:wght@400;600;700;900&family=Rajdhani:wght@400;500;600;700&family=Share+Tech+Mono&display=swap');

.gps-create {
    --gps-deep: #091a2c;
    --gps-mid: #12385d;
    --gps-neon: #59f4ff;
    --gps-amber: #ffb347;
    --gps-white: rgba(255,255,255,0.92);
}

.gps-create__shell {
    border-radius: 28px;
    overflow: hidden;
    border: 1px solid rgba(86, 225, 255, 0.14);
    background:
        radial-gradient(circle at top right, rgba(95, 243, 255, 0.16), transparent 24%),
        radial-gradient(circle at left bottom, rgba(255, 179, 71, 0.14), transparent 22%),
        linear-gradient(135deg, #0a1525, #123252 50%, #0f1d2d 100%);
    box-shadow: 0 28px 60px rgba(9, 22, 37, 0.26);
    position: relative;
}

.gps-create__shell::before {
    content: "";
    position: absolute;
    inset: 0;
    background:
        linear-gradient(rgba(89, 244, 255, 0.07) 1px, transparent 1px),
        linear-gradient(90deg, rgba(89, 244, 255, 0.07) 1px, transparent 1px);
    background-size: 40px 40px;
    pointer-events: none;
}

.gps-create__left,
.gps-create__right {
    position: relative;
    z-index: 1;
    padding: 32px;
}

.gps-create__left {
    color: var(--gps-white);
    border-right: 1px solid rgba(255,255,255,0.08);
}

.gps-create__eyebrow {
    letter-spacing: 0.42rem;
    text-transform: uppercase;
    color: var(--gps-neon);
    font-size: 0.72rem;
    margin-bottom: 14px;
}

.gps-create__title {
    font-family: Bahnschrift, "Trebuchet MS", sans-serif;
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 10px;
}

.gps-create__text {
    color: rgba(255,255,255,0.72);
    margin-bottom: 24px;
    max-width: 520px;
}

.gps-create .form-control,
.gps-create .custom-select {
    min-height: 52px;
    border-radius: 16px;
    border: 1px solid rgba(18, 56, 93, 0.16);
    box-shadow: none;
}

.gps-create label {
    font-weight: 600;
    color: #18304b;
}

.gps-create__panel {
    border-radius: 24px;
    background: linear-gradient(180deg, #ffffff, #eef4fb);
    box-shadow: inset 0 1px 0 rgba(255,255,255,0.65);
}

.gps-create__badge {
    display: inline-flex;
    align-items: center;
    padding: 8px 14px;
    border-radius: 999px;
    background: rgba(89, 244, 255, 0.14);
    color: var(--gps-neon);
    font-size: 0.78rem;
    letter-spacing: 0.16rem;
    text-transform: uppercase;
}

.gps-create__tip {
    border-radius: 18px;
    background: rgba(18, 56, 93, 0.08);
    padding: 18px;
    color: #425b78;
}

/* ── Card Preview Scaler ── */
.card-preview-wrap {
    width: 100%;
    overflow: hidden;
    margin-top: 8px;
}
.card-scale-inner {
    transform-origin: top left;
    display: inline-block;
    width: 685px;
}

/* ══════════════════════════════════════
   CARD CSS — exact from design doc
   ══════════════════════════════════════ */
.card {
    width: 685px;
    height: 432px;
    border-radius: 20px;
    position: relative;
    overflow: hidden;
    box-shadow:
        0 0 0 1px rgba(255,255,255,0.06),
        0 30px 60px rgba(0,0,0,0.7),
        0 60px 120px rgba(0,0,0,0.5);
}

.front { background: #0B1929; }

.bg-grid {
    position: absolute; inset: 0;
    background-image:
        linear-gradient(rgba(61,189,181,0.055) 1px, transparent 1px),
        linear-gradient(90deg, rgba(61,189,181,0.055) 1px, transparent 1px);
    background-size: 38px 38px;
}

.road-diag {
    position: absolute; inset: 0;
    background-image: repeating-linear-gradient(
        -50deg,
        transparent 0, transparent 55px,
        rgba(255,255,255,0.012) 55px, rgba(255,255,255,0.012) 57px
    );
}

.route-deco {
    position: absolute;
    top: 0; right: 0;
    width: 420px; height: 290px;
    pointer-events: none;
}

.glow-a {
    position: absolute;
    top: -80px; right: -60px;
    width: 360px; height: 360px;
    background: radial-gradient(circle, rgba(61,189,181,0.18) 0%, transparent 65%);
}
.glow-b {
    position: absolute;
    bottom: -70px; left: -50px;
    width: 340px; height: 220px;
    background: radial-gradient(ellipse, rgba(240,124,38,0.18) 0%, transparent 65%);
}
.glow-c {
    position: absolute;
    top: 50%; left: 40%;
    transform: translate(-50%,-50%);
    width: 500px; height: 280px;
    background: radial-gradient(ellipse, rgba(46,134,171,0.06) 0%, transparent 70%);
}

.edge-top {
    position: absolute; top: 0; left: 0; right: 0; height: 3px;
    background: linear-gradient(90deg, transparent, #3DBDB5 25%, #2E86AB 75%, transparent);
}
.edge-bottom {
    position: absolute; bottom: 0; left: 0; right: 0; height: 4px;
    background: linear-gradient(90deg, transparent, #F07C26 15%, #E8473F 85%, transparent);
}

.wave-band {
    position: absolute;
    bottom: 28px; left: 0; right: 0;
    opacity: 0.1;
    pointer-events: none;
}

.front-content {
    position: absolute; inset: 0;
    padding: 28px 36px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.top-row {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
}

.eomt-logo { width: 72px; height: 72px; }

.company-block { text-align: right; padding-top: 4px; }
.company-name {
    font-family: 'Orbitron', monospace;
    font-size: 12.5px; font-weight: 700;
    color: rgba(255,255,255,0.9);
    letter-spacing: 2.5px;
    line-height: 1.35;
    text-transform: uppercase;
}
.company-tag {
    font-family: 'Rajdhani', sans-serif;
    font-size: 9.5px; font-weight: 600;
    color: #3DBDB5;
    letter-spacing: 4px;
    text-transform: uppercase;
    margin-top: 5px;
}

.middle-row {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
}

.chip-wrap { display: flex; flex-direction: column; gap: 8px; }
.chip {
    width: 54px; height: 42px;
    border-radius: 7px;
    background: linear-gradient(135deg, #BFA36A 0%, #E8C96A 35%, #A07840 55%, #D4A855 80%, #C8A060 100%);
    position: relative;
    box-shadow: 0 3px 12px rgba(0,0,0,0.5), inset 0 1px 0 rgba(255,255,255,0.3);
}
.chip::before {
    content:''; position: absolute;
    inset: 6px; border: 1px solid rgba(140,100,40,0.5); border-radius: 3px;
}
.chip::after {
    content:''; position: absolute;
    top:50%; left:0; right:0; height:1px;
    background: rgba(120,80,20,0.4); transform: translateY(-50%);
}
.chip-line2 {
    position: absolute; left:0; right:0; top: 30%;
    height:1px; background: rgba(120,80,20,0.3);
}
.chip-label {
    font-family: 'Rajdhani', sans-serif;
    font-size: 8.5px; letter-spacing: 3px;
    color: rgba(255,255,255,0.2);
    text-transform: uppercase;
}

.model-block { text-align: right; }
.device-tag {
    font-family: 'Rajdhani', sans-serif;
    font-size: 10px; font-weight: 600;
    letter-spacing: 4.5px;
    color: #3DBDB5;
    text-transform: uppercase;
    margin-bottom: 5px;
}
.model-name {
    font-family: 'Orbitron', monospace;
    font-size: 26px; font-weight: 900;
    color: #ffffff;
    letter-spacing: 2px;
    text-shadow: 0 0 30px rgba(61,189,181,0.25);
}
.mfg-row {
    display: flex; justify-content: flex-end; gap: 20px;
    margin-top: 7px;
}
.mfg-item { text-align: right; }
.mfg-key {
    font-family: 'Rajdhani', sans-serif;
    font-size: 8px; letter-spacing: 3px;
    color: rgba(255,255,255,0.3); text-transform: uppercase;
}
.mfg-val {
    font-family: 'Share Tech Mono', monospace;
    font-size: 12px; color: rgba(255,255,255,0.65);
    margin-top: 1px;
}

.bottom-row { display: flex; flex-direction: column; gap: 10px; }

.card-number {
    font-family: 'Share Tech Mono', monospace;
    font-size: 28px;
    letter-spacing: 7px;
    color: rgba(255,255,255,0.93);
    text-shadow: 0 0 25px rgba(61,189,181,0.2);
}

.card-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.card-footer-left {
    font-family: 'Rajdhani', sans-serif;
    font-size: 10.5px; font-weight: 600;
    letter-spacing: 3.5px;
    color: rgba(255,255,255,0.3);
    text-transform: uppercase;
}
.card-footer-right {
    display: flex; align-items: center; gap: 10px;
}
.valid-text {
    font-family: 'Rajdhani', sans-serif;
    font-size: 9px; letter-spacing: 2.5px;
    color: rgba(255,255,255,0.3); text-transform: uppercase;
}

@media (max-width: 991px) {
    .gps-create__left {
        border-right: 0;
        border-bottom: 1px solid rgba(255,255,255,0.08);
    }
    .gps-create__title { font-size: 1.65rem; }
}
</style>

<div class="gps-create">
    <div class="gps-create__shell mb-4">
        <div class="row no-gutters">
            <div class="col-lg-6 gps-create__left">
                <div class="gps-create__eyebrow">Device Identity Series</div>
                <div class="gps-create__title">Bulk GPS Smart Card Generator</div>
                <p class="gps-create__text">
                    Product model choose kijiye, validity months set kijiye aur quantity bhar dijiye. System automatically har card ke liye ATM-style unique 16-digit number generate karega.
                </p>

                <div class="gps-create__badge mb-4">
                    Default Status: Active
                </div>

                <!-- ══ Card Preview (Doc 1 Design) ══ -->
                <div class="card-preview-wrap">
                    <div class="card-scale-inner" id="cardScaleInner">
                        <div class="card front">
                            <div class="bg-grid"></div>
                            <div class="road-diag"></div>
                            <div class="glow-a"></div>
                            <div class="glow-b"></div>
                            <div class="glow-c"></div>
                            <div class="edge-top"></div>
                            <div class="edge-bottom"></div>

                            <!-- GPS route decoration -->
                            <svg class="route-deco" viewBox="0 0 420 290" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M420 30 Q340 30 300 80 Q260 130 180 110 Q100 90 40 140" stroke="#3DBDB5" stroke-width="1.5" stroke-dasharray="8 6" opacity="0.3" fill="none"/>
                                <path d="M420 70 Q360 70 320 120 Q280 170 200 150 Q120 130 60 180" stroke="#2E86AB" stroke-width="1" stroke-dasharray="6 8" opacity="0.2" fill="none"/>
                                <path d="M420 110 Q380 110 340 155 Q300 200 240 185 Q180 170 120 210" stroke="#3DBDB5" stroke-width="0.75" opacity="0.15" fill="none"/>
                                <circle cx="300" cy="80" r="9" fill="rgba(240,124,38,0.35)" stroke="#F07C26" stroke-width="1.5"/>
                                <circle cx="300" cy="80" r="4" fill="#F07C26" opacity="0.7"/>
                                <line x1="300" y1="89" x2="300" y2="105" stroke="#F07C26" stroke-width="1.5" opacity="0.4"/>
                                <circle cx="300" cy="80" r="20" fill="none" stroke="#F07C26" stroke-width="0.6" opacity="0.2"/>
                                <circle cx="300" cy="80" r="34" fill="none" stroke="#F07C26" stroke-width="0.4" opacity="0.1"/>
                                <circle cx="180" cy="110" r="7" fill="rgba(61,189,181,0.35)" stroke="#3DBDB5" stroke-width="1.5"/>
                                <circle cx="180" cy="110" r="3" fill="#3DBDB5" opacity="0.7"/>
                                <line x1="180" y1="117" x2="180" y2="130" stroke="#3DBDB5" stroke-width="1.5" opacity="0.4"/>
                                <circle cx="180" cy="110" r="16" fill="none" stroke="#3DBDB5" stroke-width="0.6" opacity="0.2"/>
                                <rect x="360" y="220" width="42" height="20" rx="5" fill="none" stroke="rgba(61,189,181,0.3)" stroke-width="1.2"/>
                                <circle cx="368" cy="240" r="5" fill="none" stroke="rgba(61,189,181,0.3)" stroke-width="1.2"/>
                                <circle cx="394" cy="240" r="5" fill="none" stroke="rgba(61,189,181,0.3)" stroke-width="1.2"/>
                                <path d="M360 225 L370 215 L392 215 L402 225" fill="none" stroke="rgba(61,189,181,0.2)" stroke-width="1"/>
                                <circle cx="80" cy="220" r="22" fill="none" stroke="rgba(46,134,171,0.15)" stroke-width="1"/>
                                <circle cx="80" cy="220" r="12" fill="none" stroke="rgba(46,134,171,0.2)" stroke-width="0.8"/>
                                <circle cx="80" cy="220" r="4" fill="rgba(46,134,171,0.3)"/>
                                <line x1="58" y1="220" x2="68" y2="220" stroke="rgba(46,134,171,0.3)" stroke-width="0.8"/>
                                <line x1="92" y1="220" x2="102" y2="220" stroke="rgba(46,134,171,0.3)" stroke-width="0.8"/>
                                <line x1="80" y1="198" x2="80" y2="208" stroke="rgba(46,134,171,0.3)" stroke-width="0.8"/>
                                <line x1="80" y1="232" x2="80" y2="242" stroke="rgba(46,134,171,0.3)" stroke-width="0.8"/>
                            </svg>

                            <!-- Wave band -->
                            <svg class="wave-band" viewBox="0 0 685 70" fill="none" xmlns="http://www.w3.org/2000/svg" height="70">
                                <path d="M0 35 Q85 5 170 35 Q255 65 340 35 Q425 5 510 35 Q595 65 685 35" stroke="#3DBDB5" stroke-width="2.5" fill="none"/>
                                <path d="M0 48 Q85 18 170 48 Q255 78 340 48 Q425 18 510 48 Q595 78 685 48" stroke="#2E86AB" stroke-width="1.5" fill="none"/>
                                <path d="M0 60 Q85 30 170 60 Q255 90 340 60 Q425 30 510 60 Q595 90 685 60" stroke="#F07C26" stroke-width="1" fill="none"/>
                            </svg>

                            <div class="front-content">
                                <!-- TOP ROW -->
                                <div class="top-row">
                                    <svg class="eomt-logo" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                                        <rect x="4" y="4" width="43" height="43" rx="11" fill="#3DBDB5"/>
                                        <text x="25.5" y="37" font-family="'Arial Black',Arial,sans-serif" font-weight="900" font-size="30" fill="white" text-anchor="middle" dominant-baseline="auto">E</text>
                                        <rect x="53" y="4" width="43" height="43" rx="11" fill="#2B82A8"/>
                                        <text x="74.5" y="37" font-family="'Arial Black',Arial,sans-serif" font-weight="900" font-size="30" fill="white" text-anchor="middle" dominant-baseline="auto">E</text>
                                        <rect x="4" y="53" width="43" height="43" rx="11" fill="#F07C26"/>
                                        <text x="25.5" y="86" font-family="'Arial Black',Arial,sans-serif" font-weight="900" font-size="30" fill="white" text-anchor="middle" dominant-baseline="auto">M</text>
                                        <rect x="53" y="53" width="43" height="43" rx="11" fill="#E8473F"/>
                                        <text x="74.5" y="86" font-family="'Arial Black',Arial,sans-serif" font-weight="900" font-size="30" fill="white" text-anchor="middle" dominant-baseline="auto">T</text>
                                        <circle cx="50" cy="50" r="17" fill="white"/>
                                        <text x="50" y="57" font-family="'Arial Black',Arial,sans-serif" font-weight="900" font-size="19" fill="#2B82A8" text-anchor="middle" dominant-baseline="auto">O</text>
                                    </svg>
                                    <div class="company-block">
                                        <div class="company-name">EOMT GPS<br>SYSTEMS</div>
                                        <div class="company-tag">Track &nbsp;·&nbsp; Navigate &nbsp;·&nbsp; Protect</div>
                                    </div>
                                </div>

                                <!-- MIDDLE ROW -->
                                <div class="middle-row">
                                    <div class="chip-wrap">
                                        <div class="chip"><div class="chip-line2"></div></div>
                                        <div class="chip-label">Smart Card</div>
                                    </div>
                                    <div class="model-block">
                                        <div class="device-tag">GPS Tracking Device</div>
                                        <div class="model-name" id="previewModel">Select Model</div>
                                        <div class="mfg-row">
                                            <div class="mfg-item">
                                                <div class="mfg-key">Valid From</div>
                                                <div class="mfg-val" id="previewFrom">{{ now()->format('m / Y') }}</div>
                                            </div>
                                            <div class="mfg-item">
                                                <div class="mfg-key">Valid Thru</div>
                                                <div class="mfg-val" id="previewTo">{{ now()->addYear()->format('m / Y') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- BOTTOM ROW -->
                                <div class="bottom-row">
                                    <div class="card-number" id="previewCardNumber">0000&nbsp;&nbsp;0000&nbsp;&nbsp;0000&nbsp;&nbsp;0000</div>
                                    <div class="card-footer">
                                        <div class="card-footer-left" id="previewQuantity">1 Card</div>
                                        <div class="card-footer-right">
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" style="opacity:0.45;">
                                                <circle cx="12" cy="12" r="3" fill="#3DBDB5"/>
                                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z" stroke="#3DBDB5" stroke-width="1.5" fill="none"/>
                                                <path d="M12 6.5C9.52 6.5 7.5 8.52 7.5 12S9.52 17.5 12 17.5 16.5 15.48 16.5 12 14.48 6.5 12 6.5z" stroke="#3DBDB5" stroke-width="1" fill="none"/>
                                            </svg>
                                            <div class="valid-text" id="previewStatus">Active</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- ══ End Card Preview ══ -->

            </div>

            <div class="col-lg-6 gps-create__right">
                <div class="gps-create__panel p-4 p-lg-5">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h3 class="mb-1">Create Smart Card Batch</h3>
                            <p class="text-muted mb-0">Ek submit par jitni quantity doge utne cards generate ho jayenge.</p>
                        </div>
                        <a href="{{ route('admin.gps-cards.index') }}" class="btn btn-outline-secondary">
                            {{ trans('global.back_to_list') }}
                        </a>
                    </div>

                    <form method="POST" action="{{ route('admin.gps-cards.store') }}">
                        @csrf
                        <div class="form-group">
                            <label class="required" for="product_model_id">Product Model</label>
                            <select name="product_model_id" id="product_model_id" class="form-control {{ $errors->has('product_model_id') ? 'is-invalid' : '' }}" required>
                                <option value="">{{ trans('global.pleaseSelect') }}</option>
                                @foreach($productModels as $productModel)
                                    <option value="{{ $productModel->id }}" {{ old('product_model_id') == $productModel->id ? 'selected' : '' }}>
                                        {{ $productModel->product_model }}
                                    </option>
                                @endforeach
                            </select>
                            @if($errors->has('product_model_id'))
                                <span class="text-danger">{{ $errors->first('product_model_id') }}</span>
                            @endif
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label class="required" for="valid_from">Valid From</label>
                                <input type="month" name="valid_from" id="valid_from" class="form-control {{ $errors->has('valid_from') ? 'is-invalid' : '' }}" value="{{ old('valid_from', now()->format('Y-m')) }}" required>
                                @if($errors->has('valid_from'))
                                    <span class="text-danger">{{ $errors->first('valid_from') }}</span>
                                @endif
                            </div>
                            <div class="form-group col-md-6">
                                <label class="required" for="valid_to">Valid To</label>
                                <input type="month" name="valid_to" id="valid_to" class="form-control {{ $errors->has('valid_to') ? 'is-invalid' : '' }}" value="{{ old('valid_to', now()->addYear()->format('Y-m')) }}" required>
                                @if($errors->has('valid_to'))
                                    <span class="text-danger">{{ $errors->first('valid_to') }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label class="required" for="quantity">Quantity</label>
                                <input type="number" min="1" max="500" name="quantity" id="quantity" class="form-control {{ $errors->has('quantity') ? 'is-invalid' : '' }}" value="{{ old('quantity', 1) }}" required>
                                @if($errors->has('quantity'))
                                    <span class="text-danger">{{ $errors->first('quantity') }}</span>
                                @endif
                            </div>
                            <div class="form-group col-md-6">
                                <label class="required" for="status">Status</label>
                                <select name="status" id="status" class="form-control {{ $errors->has('status') ? 'is-invalid' : '' }}" required>
                                    @foreach(\App\Models\GpsCard::STATUS_SELECT as $key => $label)
                                        <option value="{{ $key }}" {{ old('status', 'active') === $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @if($errors->has('status'))
                                    <span class="text-danger">{{ $errors->first('status') }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="gps-create__tip mb-4">
                            Batch auto-create hone ke baad har card ko unique batch code milega, jisse dispatch aur tracking easy ho jayegi.
                        </div>

                        <button class="btn btn-primary btn-lg px-4" type="submit">
                            <i class="fas fa-layer-group mr-1"></i> Generate Smart Cards
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
@parent
<script>
    // Responsive scale for card preview
    (function () {
        function scaleCard() {
            var inner = document.getElementById('cardScaleInner');
            if (!inner) return;
            var wrap = inner.parentElement;
            var available = wrap.clientWidth;
            var scale = available / 685;
            if (scale < 1) {
                inner.style.transform = 'scale(' + scale + ')';
                wrap.style.height = Math.round(432 * scale) + 'px';
            } else {
                inner.style.transform = 'none';
                wrap.style.height = '432px';
            }
        }
        window.addEventListener('resize', scaleCard);
        setTimeout(scaleCard, 80);
    })();

    // Live card preview updater
    (function () {
        var modelSelect = document.getElementById('product_model_id');
        var validFrom   = document.getElementById('valid_from');
        var validTo     = document.getElementById('valid_to');
        var quantity    = document.getElementById('quantity');
        var status      = document.getElementById('status');

        var previewModel      = document.getElementById('previewModel');
        var previewFrom       = document.getElementById('previewFrom');
        var previewTo         = document.getElementById('previewTo');
        var previewQuantity   = document.getElementById('previewQuantity');
        var previewStatus     = document.getElementById('previewStatus');
        var previewCardNumber = document.getElementById('previewCardNumber');

        function formatMonth(value) {
            if (!value || value.indexOf('-') === -1) return '-- / ----';
            var parts = value.split('-');
            return parts[1] + ' / ' + parts[0];
        }

        function updatePreviewNumber() {
            var seed = String(quantity.value || '1').padStart(4, '0');
            previewCardNumber.textContent = '8246\u00a0\u00a08' + seed.substring(0,3) + '\u00a0\u00a01064\u00a0\u00a04502';
        }

        function updatePreview() {
            var selectedOption = modelSelect.options[modelSelect.selectedIndex];
            previewModel.textContent    = (selectedOption && selectedOption.value) ? selectedOption.text : 'Select Model';
            previewFrom.textContent     = formatMonth(validFrom.value);
            previewTo.textContent       = formatMonth(validTo.value);
            var qty = quantity.value || 1;
            previewQuantity.textContent = qty + ' Card' + (qty > 1 ? 's' : '');
            previewStatus.textContent   = status.options[status.selectedIndex].text;
            updatePreviewNumber();
        }

        [modelSelect, validFrom, validTo, quantity, status].forEach(function (el) {
            el.addEventListener('change', updatePreview);
            el.addEventListener('input',  updatePreview);
        });

        updatePreview();
    })();
</script>
@endsection

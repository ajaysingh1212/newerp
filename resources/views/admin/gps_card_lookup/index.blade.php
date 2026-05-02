@extends('layouts.admin')

@section('content')
<style>
/* ── Google Font ── */
@import url('https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Mono:wght@400;500&family=DM+Sans:ital,wght@0,300;0,400;0,500;1,300&display=swap');

:root {
    --ink:       #0a0a12;
    --surface:   #f5f4f0;
    --card:      #ffffff;
    --accent:    #4f46e5;
    --accent2:   #7c3aed;
    --gold:      #d97706;
    --danger:    #dc2626;
    --success:   #059669;
    --border:    #e2e1dc;
    --muted:     #6b7280;
    --glow:      rgba(79,70,229,0.15);
}

body { background: var(--surface); font-family: 'DM Sans', sans-serif; }

.lookup-hero {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 3rem 1.5rem;
    background:
        radial-gradient(ellipse 80% 50% at 20% 10%, rgba(79,70,229,0.08) 0%, transparent 60%),
        radial-gradient(ellipse 60% 40% at 80% 90%, rgba(124,58,237,0.06) 0%, transparent 60%),
        var(--surface);
    position: relative;
    overflow: hidden;
}

.lookup-hero::before {
    content: '';
    position: absolute;
    inset: 0;
    background-image:
        linear-gradient(var(--border) 1px, transparent 1px),
        linear-gradient(90deg, var(--border) 1px, transparent 1px);
    background-size: 48px 48px;
    opacity: 0.5;
    pointer-events: none;
}

.lookup-card {
    width: 100%;
    max-width: 580px;
    background: var(--card);
    border: 1px solid var(--border);
    border-radius: 20px;
    padding: 3rem;
    position: relative;
    box-shadow: 0 4px 6px -1px rgba(0,0,0,0.04), 0 20px 60px -10px rgba(0,0,0,0.08);
    animation: cardIn 0.6s cubic-bezier(0.22,1,0.36,1) both;
}

@keyframes cardIn {
    from { opacity:0; transform: translateY(24px) scale(0.98); }
    to   { opacity:1; transform: translateY(0) scale(1); }
}

.lookup-card::before {
    content: '';
    position: absolute;
    top: -1px; left: 20px; right: 20px; height: 3px;
    background: linear-gradient(90deg, var(--accent), var(--accent2));
    border-radius: 0 0 3px 3px;
}

.badge-admin {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: linear-gradient(135deg, var(--accent), var(--accent2));
    color: #fff;
    font-family: 'DM Mono', monospace;
    font-size: 10px;
    letter-spacing: 2px;
    text-transform: uppercase;
    padding: 5px 14px;
    border-radius: 100px;
    margin-bottom: 1.5rem;
}

.lookup-heading {
    font-family: 'Syne', sans-serif;
    font-size: 2.4rem;
    font-weight: 800;
    color: var(--ink);
    line-height: 1.1;
    margin-bottom: 0.75rem;
    letter-spacing: -1px;
}

.lookup-sub {
    color: var(--muted);
    font-size: 0.95rem;
    line-height: 1.6;
    margin-bottom: 2.5rem;
}

.input-group {
    margin-bottom: 1.5rem;
}

.input-label {
    display: block;
    font-size: 0.8rem;
    font-weight: 500;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: var(--muted);
    margin-bottom: 0.6rem;
}

.card-input-wrap {
    position: relative;
}

.card-number-input {
    width: 100%;
    font-family: 'DM Mono', monospace;
    font-size: 1.5rem;
    font-weight: 500;
    letter-spacing: 0.15em;
    color: var(--ink);
    background: #fafaf8;
    border: 2px solid var(--border);
    border-radius: 12px;
    padding: 1rem 3.5rem 1rem 1.25rem;
    outline: none;
    transition: all 0.2s;
    text-align: center;
}

.card-number-input:focus {
    border-color: var(--accent);
    background: #fff;
    box-shadow: 0 0 0 4px var(--glow);
}

.card-number-input::placeholder { color: #c4c3be; letter-spacing: 0.2em; }

.card-icon {
    position: absolute;
    right: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--muted);
    pointer-events: none;
}

.char-count {
    display: flex;
    justify-content: flex-end;
    font-family: 'DM Mono', monospace;
    font-size: 0.75rem;
    color: var(--muted);
    margin-top: 0.4rem;
    transition: color 0.2s;
}

.char-count.ready { color: var(--success); font-weight: 500; }

.submit-btn {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    background: var(--ink);
    color: #fff;
    border: none;
    border-radius: 12px;
    padding: 1rem 2rem;
    font-family: 'Syne', sans-serif;
    font-size: 1rem;
    font-weight: 700;
    letter-spacing: 0.02em;
    cursor: pointer;
    transition: all 0.2s;
    position: relative;
    overflow: hidden;
}

.submit-btn::after {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, var(--accent), var(--accent2));
    opacity: 0;
    transition: opacity 0.3s;
}

.submit-btn:hover { transform: translateY(-1px); box-shadow: 0 8px 24px rgba(0,0,0,0.15); }
.submit-btn:hover::after { opacity: 1; }
.submit-btn span, .submit-btn svg { position: relative; z-index: 1; }

.info-chips {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    margin-top: 2rem;
    padding-top: 1.5rem;
    border-top: 1px solid var(--border);
}

.chip {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    background: #f3f4f6;
    color: var(--muted);
    font-size: 0.75rem;
    padding: 4px 10px;
    border-radius: 100px;
}

.error-box {
    background: #fef2f2;
    border: 1px solid #fecaca;
    border-radius: 10px;
    padding: 0.875rem 1rem;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 8px;
    color: var(--danger);
    font-size: 0.875rem;
    font-weight: 500;
    animation: shake 0.4s ease;
}

@keyframes shake {
    0%,100% { transform: translateX(0); }
    25%      { transform: translateX(-6px); }
    75%      { transform: translateX(6px); }
}
</style>

<div class="lookup-hero">
    <div class="lookup-card">
        <div class="badge-admin">
            <svg width="8" height="8" viewBox="0 0 8 8" fill="currentColor"><circle cx="4" cy="4" r="4"/></svg>
            Admin Panel
        </div>

        <h1 class="lookup-heading">GPS Device<br>Intelligence</h1>
        <p class="lookup-sub">Enter the 16-digit GPS card number to retrieve complete device, vehicle, customer & service history.</p>

        @if($errors->any())
            <div class="error-box">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('admin.gps-card-lookup.search') }}" method="POST" id="lookupForm">
            @csrf
            <div class="input-group">
                <label class="input-label">GPS Card Number</label>
                <div class="card-input-wrap">
                    <input
                        type="text"
                        name="card_number"
                        id="cardInput"
                        class="card-number-input"
                        placeholder="0000  0000  0000  0000"
                        maxlength="19"
                        autocomplete="off"
                        value="{{ old('card_number') }}"
                        inputmode="numeric"
                    >
                    <div class="card-icon">
                        <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <rect x="2" y="5" width="20" height="14" rx="2" ry="2"/>
                            <line x1="2" y1="10" x2="22" y2="10"/>
                        </svg>
                    </div>
                </div>
                <div class="char-count" id="charCount">0 / 16 digits</div>
            </div>

            <button type="submit" class="submit-btn" id="submitBtn">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                </svg>
                <span>Lookup Device</span>
            </button>
        </form>

        <div class="info-chips">
            <span class="chip">
                <svg width="10" height="10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                Secure Admin Access
            </span>
            <span class="chip">
                <svg width="10" height="10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                Real-time Data
            </span>
            <span class="chip">
                <svg width="10" height="10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/></svg>
                PDF Export
            </span>
        </div>
    </div>
</div>

<script>
const input  = document.getElementById('cardInput');
const count  = document.getElementById('charCount');
const form   = document.getElementById('lookupForm');

input.addEventListener('input', function () {
    // Strip non-digits
    let raw = this.value.replace(/\D/g, '').slice(0, 16);
    // Format in groups of 4
    let formatted = raw.match(/.{1,4}/g)?.join('  ') ?? '';
    this.value = formatted;

    const digits = raw.length;
    count.textContent = digits + ' / 16 digits';
    count.classList.toggle('ready', digits === 16);
});

form.addEventListener('submit', function (e) {
    // Send raw digits only
    let raw = input.value.replace(/\D/g, '');
    input.value = raw;
});
</script>
@endsection

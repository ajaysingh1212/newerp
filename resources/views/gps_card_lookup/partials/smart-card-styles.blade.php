.smart-card-section {
    margin: 24px 0;
    padding: 24px;
    border-radius: 26px;
    background: linear-gradient(180deg, rgba(18, 25, 41, 0.98), rgba(11, 20, 34, 0.98));
    border: 1px solid rgba(255, 255, 255, 0.08);
    box-shadow: 0 24px 54px rgba(8, 12, 20, 0.2);
}

.smart-card-section--plain {
    width: 100%;
    max-width: 1180px;
    margin: 0 auto;
    padding: 0;
    background: transparent;
    border: 0;
    box-shadow: none;
}

.smart-card-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    flex-wrap: wrap;
    margin-bottom: 18px;
}

.smart-card-head-copy h3 {
    margin: 0 0 6px;
    font-size: 24px;
    color: #fff;
    letter-spacing: -0.03em;
}

.smart-card-head-copy p {
    margin: 0;
    color: rgba(255, 255, 255, 0.72);
    font-size: 14px;
    line-height: 1.6;
}

.smart-card-download-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    min-width: 200px;
    padding: 12px 18px;
    border: 0;
    border-radius: 999px;
    background: linear-gradient(135deg, #f07c26, #ea503c);
    color: #fff;
    font-size: 13px;
    font-weight: 700;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    cursor: pointer;
    box-shadow: 0 14px 28px rgba(240, 124, 38, 0.22);
}

.smart-card-download-btn:disabled {
    cursor: wait;
    opacity: 0.7;
}

.smart-card-sheet {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 18px;
    justify-items: center;
}

.smart-card-footer-actions {
    margin-top: 18px;
    display: flex;
    justify-content: center;
}

.smart-card-page {
    width: 86mm;
}

.smart-card {
    width: 86mm;
    height: 54mm;
    border-radius: 4.2mm;
    position: relative;
    overflow: hidden;
    background:
        linear-gradient(150deg, rgba(61, 189, 181, 0.18), transparent 26%),
        linear-gradient(320deg, rgba(240, 124, 38, 0.18), transparent 28%),
        linear-gradient(135deg, #08131f, #10233a 50%, #0b1a2b);
    box-shadow:
        0 0 0 1px rgba(255, 255, 255, 0.05),
        0 24px 50px rgba(0, 0, 0, 0.45);
    isolation: isolate;
    font-family: 'Rajdhani', sans-serif;
}

.smart-card::before,
.smart-card::after {
    content: "";
    position: absolute;
    inset: 0;
    pointer-events: none;
}

.smart-card::before {
    background:
        linear-gradient(rgba(61, 189, 181, 0.07) 0.22mm, transparent 0.22mm),
        linear-gradient(90deg, rgba(61, 189, 181, 0.07) 0.22mm, transparent 0.22mm);
    background-size: 3.4mm 3.4mm;
    opacity: 0.55;
}

.smart-card::after {
    background:
        radial-gradient(circle at 82% 14%, rgba(61, 189, 181, 0.24), transparent 19mm),
        radial-gradient(circle at 14% 100%, rgba(240, 124, 38, 0.22), transparent 16mm),
        radial-gradient(circle at 55% 60%, rgba(36, 92, 141, 0.18), transparent 25mm);
}

.smart-card-sheen {
    position: absolute;
    inset: 0;
    background:
        linear-gradient(118deg, transparent 0 48%, rgba(255, 255, 255, 0.06) 55%, transparent 62%),
        linear-gradient(0deg, rgba(255, 255, 255, 0.04), transparent 30%);
    pointer-events: none;
}

.smart-card-top-edge,
.smart-card-bottom-edge {
    position: absolute;
    left: 0;
    right: 0;
    z-index: 1;
}

.smart-card-top-edge {
    top: 0;
    height: 0.9mm;
    background: linear-gradient(90deg, transparent, #3dbdb5 22%, #2f8fcb 78%, transparent);
}

.smart-card-bottom-edge {
    bottom: 0;
    height: 1.1mm;
    background: linear-gradient(90deg, transparent, #f07c26 22%, #ea503c 78%, transparent);
}

.smart-card--back .smart-card-top-edge {
    background: linear-gradient(90deg, transparent, #f07c26 20%, #ea503c 78%, transparent);
}

.smart-card--back .smart-card-bottom-edge {
    background: linear-gradient(90deg, transparent, #3dbdb5 22%, #2f8fcb 78%, transparent);
}

.smart-card-route-wave {
    position: absolute;
    right: -5mm;
    bottom: 8mm;
    width: 35mm;
    height: 14mm;
    opacity: 0.28;
    pointer-events: none;
}

.smart-front-content,
.smart-back-content {
    position: relative;
    z-index: 2;
    width: 100%;
    height: 100%;
    padding: 4.2mm 4.3mm 3.9mm;
}

.smart-front-content {
    display: grid;
    grid-template-rows: auto 1fr auto;
    gap: 3.3mm;
}

.smart-front-header,
.smart-front-middle,
.smart-front-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 3mm;
}

.smart-logo-wrap {
    display: flex;
    align-items: center;
    gap: 2.8mm;
    min-width: 0;
}

.smart-eemot-logo {
    width: 12.6mm;
    height: 12.6mm;
    flex: 0 0 auto;
    filter: drop-shadow(0 2px 6px rgba(0, 0, 0, 0.35));
}

.smart-brand-copy {
    min-width: 0;
}

.smart-brand-name {
    margin: 0;
    font-family: 'Orbitron', monospace;
    font-size: 3.1mm;
    line-height: 1.2;
    letter-spacing: 0.32mm;
    color: #fff;
    text-transform: uppercase;
}

.smart-brand-tag {
    margin-top: 0.7mm;
    color: #84f0e4;
    font-size: 1.95mm;
    font-weight: 700;
    letter-spacing: 0.2mm;
    text-transform: uppercase;
}

.smart-holder-pill {
    max-width: 30mm;
    padding: 1.2mm 2mm;
    border: 0.25mm solid rgba(255, 255, 255, 0.12);
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.05);
    font-size: 2.15mm;
    font-weight: 700;
    color: rgba(255, 255, 255, 0.88);
    text-transform: uppercase;
    letter-spacing: 0.16mm;
    text-align: right;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.smart-front-middle {
    align-items: stretch;
}

.smart-vehicle-panel {
    width: 21mm;
    display: flex;
    flex-direction: column;
    justify-content: flex-end;
    gap: 1.7mm;
}

.smart-vehicle-label {
    font-size: 1.8mm;
    font-weight: 700;
    letter-spacing: 0.26mm;
    text-transform: uppercase;
    color: rgba(255, 255, 255, 0.42);
}

.smart-vehicle-plate {
    width:120px;
    {{-- min-height: 12.2mm; --}}
    padding: 1.5mm 1.6mm;
    border-radius: 1.8mm;
    border: 0.35mm solid rgba(255, 255, 255, 0.18);
    background: rgba(255, 255, 255, 0.08);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    font-family: 'Orbitron', monospace;
    font-size: 2.55mm;
    font-weight: 800;
    line-height: 1.18;
    letter-spacing: 0.04mm;
    word-break: break-word;
}

.smart-vehicle-status {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 1.2mm 1.6mm;
    border-radius: 999px;
    background: rgba(61, 189, 181, 0.16);
    border: 0.25mm solid rgba(61, 189, 181, 0.22);
    color: #84f0e4;
    font-size: 1.95mm;
    font-weight: 800;
    letter-spacing: 0.18mm;
    text-transform: uppercase;
}

.smart-device-copy {
    flex: 1;
    min-width: 0;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    gap: 2.8mm;
    text-align: right;
}

.smart-device-kicker {
    font-size: 2.1mm;
    font-weight: 700;
    letter-spacing: 0.4mm;
    text-transform: uppercase;
    color: #84f0e4;
}

.smart-model-name {
    margin: 0;
    font-family: 'Orbitron', monospace;
    font-size: 5.15mm;
    line-height: 1.06;
    font-weight: 900;
    color: #fff;
    letter-spacing: 0.16mm;
    text-shadow: 0 0 12px rgba(61, 189, 181, 0.2);
    word-break: break-word;
}

.smart-meta-row {
    display: flex;
    justify-content: flex-end;
    gap: 3mm;
    flex-wrap: wrap;
}

.smart-meta-box {
    min-width: 15mm;
    padding: 1.4mm 1.8mm;
    border-radius: 2mm;
    background: rgba(255, 255, 255, 0.05);
    border: 0.25mm solid rgba(255, 255, 255, 0.08);
}

.smart-meta-label {
    font-size: 1.75mm;
    letter-spacing: 0.28mm;
    text-transform: uppercase;
    color: rgba(255, 255, 255, 0.3);
    font-weight: 700;
}

.smart-meta-value {
    margin-top: 0.55mm;
    font-family: 'Share Tech Mono', monospace;
    font-size: 2.55mm;
    color: rgba(255, 255, 255, 0.88);
    font-weight: 700;
}

.smart-front-number {
    font-family: 'Share Tech Mono', monospace;
    font-size: 4.35mm;
    line-height: 1;
    letter-spacing: 0.28mm;
    color: #fff;
    text-shadow: 0 0 12px rgba(61, 189, 181, 0.2);
}

.smart-front-footer {
    align-items: flex-end;
}

.smart-front-footer-left {
    display: flex;
    flex-direction: column;
    gap: 0.8mm;
    min-width: 0;
}

.smart-footer-label {
    font-size: 1.8mm;
    font-weight: 700;
    letter-spacing: 0.3mm;
    text-transform: uppercase;
    color: rgba(255, 255, 255, 0.3);
}

.smart-footer-name {
    font-size: 2.6mm;
    font-weight: 700;
    color: #fff;
    text-transform: uppercase;
    letter-spacing: 0.08mm;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 43mm;
}

{{-- .smart-status-badge {
    flex: 0 0 auto;
    padding: 1.4mm 2.3mm;
    border-radius: 999px;
    background: rgba(61, 189, 181, 0.16);
    color: #84f0e4;
    border: 0.25mm solid rgba(61, 189, 181, 0.22);
    font-size: 2.15mm;
    font-weight: 700;
    letter-spacing: 0.25mm;
    text-transform: uppercase;
} --}}

.smart-back-content {
    display: grid;
    grid-template-rows: auto auto 1fr auto;
    gap: 2.1mm;
}

.smart-back-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 2.4mm;
}

.smart-back-logo {
    width: 10mm;
    height: 10mm;
    flex: 0 0 auto;
}

.smart-back-title {
    flex: 1;
    min-width: 0;
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 0.35mm;
    text-align: right;
}

.smart-back-title-main {
    font-family: 'Orbitron', monospace;
    font-size: 2.8mm;
    font-weight: 800;
    letter-spacing: 0.18mm;
    color: #fff;
    text-transform: uppercase;
}

.smart-back-title-sub {
    font-size: 1.95mm;
    font-weight: 700;
    color: rgba(255, 255, 255, 0.62);
    text-transform: uppercase;
    letter-spacing: 0.15mm;
}

.smart-mag-stripe {
    height: 8.9mm;
    margin-left: -4.3mm;
    margin-right: -4.3mm;
    background:
        linear-gradient(180deg, #191919, #070707 55%, #151515),
        repeating-linear-gradient(90deg, rgba(61, 189, 181, 0.02) 0 1.2mm, transparent 1.2mm 3.6mm);
    box-shadow: inset 0 0.6mm 1mm rgba(255, 255, 255, 0.03), inset 0 -0.6mm 1.2mm rgba(0, 0, 0, 0.7);
}

.smart-back-main {
    display: grid;
    grid-template-columns: 23.8mm 1fr;
    gap: 3.1mm;
    align-items: start;
}

.smart-qr-panel {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1.05mm;
    color: inherit;
    text-decoration: none;
}

.smart-qr-box {
    width: 22.8mm;
    height: 22.8mm;
    border-radius: 2.4mm;
    background: #fff;
    padding: 1.55mm;
    box-shadow: 0 1.8mm 3.6mm rgba(0, 0, 0, 0.28);
    display: flex;
    align-items: center;
    justify-content: center;
}

.smart-qr-mount,
.smart-qr-mount img,
.smart-qr-mount svg {
    width: 100%;
    height: 100%;
    display: block;
}

.smart-scan-copy {
    font-size: 2mm;
    font-weight: 700;
    color: rgba(255, 255, 255, 0.88);
    text-align: center;
    text-transform: uppercase;
    letter-spacing: 0.14mm;
}

.smart-scan-url {
    font-size: 1.45mm;
    color: rgba(255, 255, 255, 0.3);
    text-align: center;
    line-height: 1.15;
    word-break: break-word;
}

.smart-back-details {
    display: flex;
    flex-direction: column;
    gap: 1.65mm;
    min-width: 0;
}

.smart-detail-block {
    padding-bottom: 1.05mm;
    border-bottom: 0.25mm solid rgba(255, 255, 255, 0.08);
}

.smart-detail-block:last-child {
    border-bottom: 0;
    padding-bottom: 0;
}

.smart-detail-label {
    font-size: 1.78mm;
    font-weight: 700;
    letter-spacing: 0.22mm;
    text-transform: uppercase;
    color: #84f0e4;
}

.smart-detail-value {
    margin-top: 0.45mm;
    font-size: 2.8mm;
    line-height: 1.18;
    color: #fff;
    font-weight: 700;
    word-break: break-word;
}

.smart-detail-value--mono {
    font-family: 'Share Tech Mono', monospace;
    font-size: 2.45mm;
}

.smart-back-footer {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.45mm 2mm;
    padding-top: 1.35mm;
    border-top: 0.25mm solid rgba(61, 189, 181, 0.16);
}

.smart-back-note {
    font-size: 1.86mm;
    line-height: 1.22;
    color: rgba(255, 255, 255, 0.88);
}

.smart-back-note strong {
    color: #ffb76b;
}

.smart-back-note span {
    color: #84f0e4;
    font-weight: 700;
}

@media (max-width: 1180px) {
    .smart-card-sheet {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 700px) {
    .smart-card-section {
        padding: 18px 14px;
    }

    .smart-card-head {
        align-items: stretch;
    }

    .smart-card-download-btn {
        width: 100%;
    }
}

@media print {
    .smart-card-section {
        margin: 0;
        padding: 0;
        background: transparent;
        border: 0;
        box-shadow: none;
    }

    .smart-card-head,
    .smart-card-download-btn {
        display: none !important;
    }

    .smart-card-sheet {
        display: block;
    }

    .smart-card-page {
        width: 86mm;
        page-break-after: always;
        break-after: page;
    }

    .smart-card-page:last-child {
        page-break-after: auto;
        break-after: auto;
    }

    .smart-card {
        box-shadow: none;
    }
}

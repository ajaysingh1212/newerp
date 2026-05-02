<style>
@import url('https://fonts.googleapis.com/css2?family=Orbitron:wght@500;700;800;900&family=Rajdhani:wght@500;600;700&family=Share+Tech+Mono&display=swap');

:root {
    --page-bg: #f6f1e8;
    --panel: #ffffff;
    --panel-soft: #fbfaf7;
    --ink: #14121d;
    --muted: #6e6a78;
    --line: #e7dfd2;
    --brand: #1240ab;
    --brand-soft: #dfe8ff;
    --accent: #0f766e;
    --accent-soft: #d7f7f1;
    --warn: #b45309;
    --warn-soft: #fff0c9;
    --danger: #b42318;
    --danger-soft: #ffe1dd;
    --good: #127c4c;
    --good-soft: #d8f6e6;
    --shadow: 0 20px 60px rgba(20, 18, 29, 0.08);
}

* { box-sizing: border-box; }
html { scroll-behavior: smooth; }
body {
    margin: 0;
    background:
        radial-gradient(circle at top left, rgba(18, 64, 171, 0.08), transparent 28%),
        radial-gradient(circle at bottom right, rgba(15, 118, 110, 0.08), transparent 22%),
        var(--page-bg);
    color: var(--ink);
    font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
}

.gps-shell {
    min-height: 100vh;
    padding: 24px;
}

.gps-topbar {
    position: sticky;
    top: 0;
    z-index: 30;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    flex-wrap: wrap;
    margin: 0 auto 20px;
    max-width: 1380px;
    padding: 16px 18px;
    border: 1px solid rgba(255, 255, 255, 0.22);
    border-radius: 20px;
    backdrop-filter: blur(18px);
    background: rgba(20, 18, 29, 0.86);
    box-shadow: var(--shadow);
}

.gps-topbar-group {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
}

.gps-pill,
.gps-action {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    border-radius: 999px;
    text-decoration: none;
    font-size: 13px;
    line-height: 1;
}

.gps-pill {
    padding: 10px 14px;
    border: 1px solid rgba(255, 255, 255, 0.14);
    color: rgba(255, 255, 255, 0.88);
    background: rgba(255, 255, 255, 0.08);
}

.gps-pill.gps-back {
    color: #fff;
}

.gps-pill.gps-chip {
    font-family: Consolas, Monaco, monospace;
    letter-spacing: 0.08em;
}

.gps-action {
    padding: 11px 16px;
    border: none;
    cursor: pointer;
    background: #fff;
    color: var(--ink);
    font-weight: 600;
}

.gps-action:hover {
    transform: translateY(-1px);
}

.gps-action.primary {
    background: linear-gradient(135deg, #2f63de, #1444b8);
    color: #fff;
}

.gps-report {
    max-width: 1380px;
    margin: 0 auto;
}

.company-strip {
    margin-bottom: 24px;
    padding: 22px 24px;
    border-radius: 28px;
    background:
        linear-gradient(135deg, rgba(255, 255, 255, 0.98), rgba(244, 246, 255, 0.96)),
        #fff;
    border: 1px solid rgba(231, 223, 210, 0.92);
    box-shadow: var(--shadow);
}

.company-grid {
    display: grid;
    grid-template-columns: minmax(220px, 320px) minmax(0, 1fr);
    gap: 22px;
    align-items: center;
}

.company-brand {
    display: flex;
    align-items: center;
    gap: 18px;
}

.company-logo-wrap {
    width: 84px;
    height: 84px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 24px;
    background: linear-gradient(135deg, #eef2ff, #f3e8ff);
    border: 1px solid #dbe2ff;
    overflow: hidden;
    flex-shrink: 0;
}

.company-logo {
    width: 72px;
    height: 72px;
    object-fit: contain;
}

.company-kicker {
    margin: 0 0 6px;
    color: var(--brand);
    text-transform: uppercase;
    font-size: 12px;
    letter-spacing: 0.12em;
    font-weight: 700;
}

.company-name {
    margin: 0;
    font-size: clamp(26px, 4vw, 38px);
    line-height: 1;
    letter-spacing: -0.05em;
}

.company-tagline {
    margin: 8px 0 0;
    color: var(--muted);
    font-size: 14px;
    line-height: 1.7;
}

.company-meta {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 12px;
}

.company-meta-card {
    padding: 16px 18px;
    border-radius: 20px;
    background: var(--panel-soft);
    border: 1px solid var(--line);
}

.company-meta-label {
    color: var(--muted);
    text-transform: uppercase;
    letter-spacing: 0.08em;
    font-size: 11px;
    margin-bottom: 6px;
}

.company-meta-value {
    font-size: 14px;
    font-weight: 600;
    line-height: 1.6;
}

.activated-banner {
    position: relative;
    overflow: hidden;
    margin-bottom: 24px;
    padding: 24px 26px;
    border-radius: 28px;
    background: linear-gradient(120deg, #0b4f3d, #13875a 60%, #26a17b 100%);
    color: #fff;
    box-shadow: var(--shadow);
}

.activated-banner::before,
.activated-banner::after {
    content: "";
    position: absolute;
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.12);
}

.activated-banner::before {
    width: 220px;
    height: 220px;
    top: -110px;
    right: -40px;
}

.activated-banner::after {
    width: 160px;
    height: 160px;
    bottom: -70px;
    left: 32%;
}

.activated-grid {
    position: relative;
    z-index: 1;
    display: grid;
    grid-template-columns: minmax(0, 1.6fr) minmax(220px, 0.9fr);
    gap: 20px;
    align-items: center;
}

.activated-title {
    margin: 0 0 8px;
    font-size: clamp(30px, 5vw, 48px);
    line-height: 1;
    letter-spacing: -0.05em;
}

.activated-copy {
    margin: 0;
    color: rgba(255, 255, 255, 0.86);
    font-size: 15px;
    line-height: 1.8;
}

.activated-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 140px;
    padding: 18px;
    border-radius: 24px;
    background: rgba(255, 255, 255, 0.12);
    border: 1px solid rgba(255, 255, 255, 0.18);
    text-align: center;
    font-size: 15px;
    font-weight: 700;
    line-height: 1.8;
    text-transform: uppercase;
    letter-spacing: 0.08em;
}

.hero-card {
    position: relative;
    overflow: hidden;
    margin-bottom: 24px;
    padding: 26px;
    border-radius: 28px;
    background: linear-gradient(135deg, #161328, #1f2957 58%, #0f766e 130%);
    color: #fff;
    box-shadow: var(--shadow);
}

.hero-card::before,
.hero-card::after {
    content: "";
    position: absolute;
    border-radius: 999px;
    filter: blur(8px);
    opacity: 0.45;
}

.hero-card::before {
    width: 320px;
    height: 320px;
    right: -80px;
    top: -120px;
    background: rgba(255, 255, 255, 0.15);
}

.hero-card::after {
    width: 220px;
    height: 220px;
    left: 35%;
    bottom: -140px;
    background: rgba(98, 188, 255, 0.22);
}

.hero-grid {
    position: relative;
    z-index: 1;
    display: grid;
    grid-template-columns: minmax(0, 1.9fr) minmax(280px, 1fr);
    gap: 24px;
}

.hero-title {
    margin: 0;
    font-size: clamp(28px, 5vw, 44px);
    line-height: 1;
    letter-spacing: -0.04em;
}

.hero-subtitle {
    margin: 10px 0 16px;
    color: rgba(255, 255, 255, 0.76);
    font-size: 15px;
}

.hero-tags,
.hero-stats,
.info-grid,
.timeline-grid,
.documents-grid,
.section-grid {
    display: grid;
    gap: 14px;
}

.hero-tags {
    grid-template-columns: repeat(auto-fit, minmax(160px, max-content));
}

.tag {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    width: fit-content;
    max-width: 100%;
    padding: 10px 14px;
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.16);
    color: rgba(255, 255, 255, 0.92);
    font-size: 13px;
}

.hero-stats {
    grid-template-columns: repeat(2, minmax(0, 1fr));
    align-content: start;
}

.stat-card {
    padding: 18px;
    border-radius: 20px;
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.14);
}

.stat-value {
    font-size: 28px;
    font-weight: 700;
    line-height: 1;
}

.stat-label {
    margin-top: 8px;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: rgba(255, 255, 255, 0.68);
}

.alert-banner {
    margin-bottom: 24px;
    padding: 18px 20px;
    border-radius: 22px;
    border: 1px solid #f5a59d;
    background: linear-gradient(135deg, #fff7f6, #ffeae7);
    color: #7f1d1d;
    box-shadow: 0 10px 24px rgba(180, 35, 24, 0.08);
}

.section {
    margin-bottom: 24px;
    padding: 22px;
    border-radius: 26px;
    background: rgba(255, 255, 255, 0.92);
    border: 1px solid rgba(231, 223, 210, 0.92);
    box-shadow: var(--shadow);
}

.section-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    flex-wrap: wrap;
    margin-bottom: 18px;
}

.section-title-wrap {
    display: flex;
    align-items: center;
    gap: 12px;
}

.section-icon {
    width: 42px;
    height: 42px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 14px;
    background: var(--brand-soft);
    color: var(--brand);
    font-size: 18px;
    font-weight: 700;
}

.section-title {
    margin: 0;
    font-size: 22px;
    letter-spacing: -0.03em;
}

.section-subtitle {
    margin: 4px 0 0;
    color: var(--muted);
    font-size: 13px;
}

.section-grid {
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
}

.metric-card,
.timeline-card,
.document-card {
    padding: 18px;
    border-radius: 20px;
    border: 1px solid var(--line);
    background: var(--panel-soft);
}

.metric-label {
    margin-bottom: 8px;
    color: var(--muted);
    text-transform: uppercase;
    letter-spacing: 0.08em;
    font-size: 12px;
}

.metric-value {
    font-size: 16px;
    font-weight: 600;
    word-break: break-word;
}

.metric-value.mono {
    font-family: Consolas, Monaco, monospace;
}

.badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 12px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 700;
}

.badge.good { background: var(--good-soft); color: var(--good); }
.badge.warn { background: var(--warn-soft); color: var(--warn); }
.badge.danger { background: var(--danger-soft); color: var(--danger); }
.badge.brand { background: var(--brand-soft); color: var(--brand); }

.service-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 16px;
}

.service-card {
    padding: 18px;
    border-radius: 20px;
    border: 1px solid var(--line);
    background: linear-gradient(180deg, #fff, #fbf9f4);
}

.service-date {
    margin: 6px 0 12px;
    font-size: 28px;
    font-weight: 700;
    letter-spacing: -0.03em;
}

.service-track {
    height: 8px;
    border-radius: 999px;
    background: #ece8df;
    overflow: hidden;
}

.service-fill {
    height: 100%;
    border-radius: 999px;
}

.service-note {
    margin-top: 12px;
    font-size: 13px;
    font-weight: 600;
}

.service-fill.good,
.service-note.good { color: var(--good); background-color: var(--good); }
.service-fill.warn,
.service-note.warn { color: var(--warn); background-color: var(--warn); }
.service-fill.danger,
.service-note.danger { color: var(--danger); background-color: var(--danger); }

.service-note.good,
.service-note.warn,
.service-note.danger {
    background: transparent;
}

.timeline-grid {
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
}

.timeline-card h4,
.document-card h4 {
    margin: 0 0 6px;
    font-size: 16px;
}

.timeline-meta,
.document-meta,
.empty-state,
.soft-text {
    color: var(--muted);
    font-size: 13px;
    line-height: 1.6;
}

.timeline-note {
    margin-top: 12px;
    padding-top: 12px;
    border-top: 1px dashed var(--line);
    font-size: 13px;
    line-height: 1.6;
}

.recharge-banner {
    display: grid;
    grid-template-columns: minmax(140px, 220px) minmax(0, 1fr);
    gap: 18px;
    align-items: center;
}

.countdown {
    padding: 18px;
    border-radius: 22px;
    background: linear-gradient(135deg, #182452, #123a9a);
    color: #fff;
    text-align: center;
}

.countdown-days {
    font-size: clamp(36px, 8vw, 56px);
    font-weight: 800;
    line-height: 1;
}

.countdown-label {
    margin-top: 8px;
    font-size: 13px;
    color: rgba(255, 255, 255, 0.72);
    text-transform: uppercase;
    letter-spacing: 0.08em;
}

.documents-grid {
    grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
}

.document-thumb {
    width: 100%;
    height: 150px;
    object-fit: cover;
    border-radius: 14px;
    background: #f2ede4;
}

.document-actions {
    margin-top: 14px;
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.mini-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 9px 12px;
    border-radius: 999px;
    border: 1px solid var(--line);
    background: #fff;
    color: var(--ink);
    text-decoration: none;
    font-size: 12px;
    font-weight: 600;
}

.empty-state {
    padding: 18px;
    border-radius: 18px;
    background: var(--panel-soft);
    border: 1px dashed var(--line);
}

.welcome-grid {
    display: grid;
    grid-template-columns: 1.15fr 0.85fr;
    gap: 20px;
}

.welcome-note {
    padding: 22px;
    border-radius: 24px;
    background: linear-gradient(180deg, #fff, #fbf9f4);
    border: 1px solid var(--line);
}

.welcome-note h3,
.timeline-card h4,
.document-card h4 {
    margin: 0 0 10px;
}

.welcome-points {
    margin: 0;
    padding-left: 18px;
    color: var(--ink);
}

.welcome-points li {
    margin-bottom: 10px;
    line-height: 1.7;
    color: var(--ink);
}

.print-meta-panel {
    padding: 22px;
    border-radius: 24px;
    background: linear-gradient(135deg, #f3f6ff, #fbfbff);
    border: 1px solid #dfe3f5;
}

.print-meta-row {
    padding: 12px 0;
    border-bottom: 1px dashed #d7ddee;
}

.print-meta-row:last-child {
    border-bottom: none;
}

.print-meta-label {
    color: var(--muted);
    text-transform: uppercase;
    letter-spacing: 0.08em;
    font-size: 11px;
    margin-bottom: 6px;
}

.print-meta-value {
    font-size: 15px;
    font-weight: 600;
}

.certificate-page {
    position: relative;
    overflow: hidden;
    margin: 28px 0 24px;
    padding: 34px;
    min-height: 1080px;
    border-radius: 34px;
    background:
        radial-gradient(circle at top left, rgba(160, 174, 255, 0.24), transparent 22%),
        radial-gradient(circle at top right, rgba(214, 193, 255, 0.32), transparent 20%),
        radial-gradient(circle at bottom left, rgba(157, 182, 255, 0.24), transparent 23%),
        radial-gradient(circle at bottom right, rgba(121, 97, 209, 0.28), transparent 22%),
        linear-gradient(180deg, rgba(255, 255, 255, 0.99), rgba(252, 249, 255, 0.98));
    box-shadow: var(--shadow);
    page-break-before: always;
}

.certificate-page::before {
    content: "";
    position: absolute;
    inset: 18px;
    border-radius: 28px;
    border: 3px solid rgba(68, 68, 82, 0.72);
}

.certificate-page::after {
    content: "";
    position: absolute;
    inset: 30px;
    border-radius: 22px;
    border: 1px solid rgba(122, 123, 137, 0.22);
}

.certificate-confetti {
    position: absolute;
    color: rgba(106, 99, 214, 0.35);
    font-size: clamp(30px, 6vw, 76px);
    line-height: 1;
    user-select: none;
}

.confetti-a { top: 46px; left: 58px; transform: rotate(-12deg); }
.confetti-b { top: 38px; right: 72px; transform: rotate(12deg); }
.confetti-c { top: 220px; left: 90px; transform: rotate(8deg); }
.confetti-d { top: 250px; right: 106px; transform: rotate(-7deg); }
.confetti-e { bottom: 110px; left: 52px; transform: rotate(-16deg); }
.confetti-f { bottom: 96px; right: 46px; transform: rotate(14deg); }

.certificate-inner {
    position: relative;
    z-index: 2;
    min-height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
}

.certificate-logo {
    width: 96px;
    height: 96px;
    object-fit: contain;
    margin-bottom: 18px;
}

.certificate-org {
    margin: 0 0 10px;
    color: #615bb6;
    font-size: 14px;
    text-transform: uppercase;
    letter-spacing: 0.18em;
    font-weight: 700;
}

.certificate-title {
    margin: 0;
    font-size: clamp(54px, 7vw, 100px);
    line-height: 0.94;
    letter-spacing: -0.05em;
    font-weight: 800;
}

.certificate-subtitle {
    margin: 12px 0 0;
    font-size: clamp(22px, 3vw, 34px);
    text-transform: uppercase;
    letter-spacing: 0.28em;
}

.certificate-divider {
    width: min(360px, 100%);
    margin: 28px auto 34px;
    display: flex;
    align-items: center;
    gap: 14px;
}

.certificate-divider::before,
.certificate-divider::after {
    content: "";
    flex: 1;
    height: 3px;
    border-radius: 999px;
    background: #282634;
}

.certificate-gem {
    width: 20px;
    height: 20px;
    transform: rotate(45deg);
    border-radius: 4px;
    background: #7768db;
    border: 2px solid #22202e;
}

.certificate-intro,
.certificate-copy,
.certificate-company-line,
.certificate-meta {
    max-width: 880px;
}

.certificate-intro {
    margin: 0 0 10px;
    font-size: 23px;
    letter-spacing: 0.08em;
}

.certificate-recipient {
    margin: 10px 0 12px;
    font-size: clamp(48px, 7vw, 92px);
    line-height: 1.02;
    font-weight: 700;
    color: #6a58cc;
    letter-spacing: -0.05em;
}

.certificate-recipient-line {
    width: min(560px, 90%);
    height: 3px;
    border-radius: 999px;
    background: #22202e;
    margin-bottom: 22px;
}

.certificate-copy {
    font-size: clamp(22px, 3vw, 34px);
    line-height: 1.65;
    margin: 0 auto 22px;
}

.certificate-company-line {
    margin: 0 auto 12px;
    font-size: 24px;
    font-weight: 800;
}

.certificate-meta {
    margin: 0 auto 40px;
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 16px;
}

.certificate-meta-card {
    padding: 16px 14px;
    border-radius: 20px;
    background: rgba(255, 255, 255, 0.74);
    border: 1px solid rgba(150, 150, 170, 0.22);
}

.certificate-meta-label {
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.12em;
    color: var(--muted);
    margin-bottom: 8px;
}

.certificate-meta-value {
    font-size: 18px;
    font-weight: 700;
    line-height: 1.5;
}

.certificate-footer {
    width: 100%;
    margin-top: auto;
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 20px;
    align-items: end;
}

.certificate-sign {
    padding-top: 16px;
}

.certificate-sign-line {
    width: min(220px, 100%);
    height: 3px;
    margin: 0 auto 12px;
    border-radius: 999px;
    background: #22202e;
}

.certificate-sign-name {
    color: #615bb6;
    font-size: 26px;
    font-weight: 800;
}

.certificate-sign-role {
    color: #2d2a39;
    font-size: 15px;
    text-transform: uppercase;
    letter-spacing: 0.14em;
}

.certificate-contact {
    padding: 14px 16px;
    border-radius: 22px;
    background: rgba(255, 255, 255, 0.78);
    border: 1px solid rgba(150, 150, 170, 0.22);
    font-size: 13px;
    line-height: 1.8;
}

.certificate-contact strong {
    display: block;
    margin-bottom: 6px;
    font-size: 14px;
    letter-spacing: 0.08em;
    text-transform: uppercase;
}

@media (max-width: 960px) {
    .gps-shell { padding: 14px; }
    .company-grid,
    .activated-grid,
    .welcome-grid,
    .hero-grid,
    .recharge-banner,
    .certificate-meta,
    .certificate-footer {
        grid-template-columns: 1fr;
    }

    .company-brand {
        align-items: flex-start;
    }

    .certificate-page {
        min-height: auto;
        padding: 28px 20px;
    }

    .certificate-recipient {
        word-break: break-word;
    }
}

@media print {
    body {
        background: #fff;
    }

    .gps-topbar,
    .no-print {
        display: none !important;
    }

    .gps-shell {
        padding: 0;
    }

    .section,
    .hero-card,
    .alert-banner,
    .company-strip,
    .activated-banner,
    .certificate-page {
        box-shadow: none !important;
        break-inside: avoid;
        page-break-inside: avoid;
    }

    .hero-card {
        background: #1b2444 !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    .activated-banner,
    .certificate-page {
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
}

@include('gps_card_lookup.partials.smart-card-styles')
</style>

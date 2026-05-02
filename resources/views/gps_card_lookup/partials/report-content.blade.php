@php
    $gpsCard = $lookup['gpsCard'];
    $activation = $lookup['activation'];
    $vehicle = $lookup['vehicle'];
    $user = $lookup['user'];
    $productMaster = $lookup['productMaster'];
    $latestKyc = $lookup['latestKyc'];
    $complaints = $lookup['complaints'];
    $rechargeHistory = $lookup['rechargeHistory'];
    $deleteRecord = $lookup['deleteRecord'];
    $documents = $lookup['documents'];
    $serviceValidity = $lookup['serviceValidity'];
    $nextRecharge = $lookup['nextRecharge'];
    $serviceDeadline = $lookup['serviceDeadline'];
    $stats = $lookup['stats'];
    $meta = $lookup['meta'];

    $productModelName = $productMaster?->product_model?->product_model
        ?? $lookup['productModel']?->product_model
        ?? $lookup['productModel']?->name
        ?? $gpsCard?->productModel?->product_model
        ?? $gpsCard?->productModel?->name
        ?? '-';

    $vehicleTypeName = $vehicle?->select_vehicle_type?->vehicle_type
        ?? $activation?->vehicle_type?->vehicle_type
        ?? '-';

    $stateName = function ($state) {
        return $state?->state_name ?? $state?->name ?? '-';
    };

    $districtName = function ($district) {
        return $district?->districts ?? $district?->name ?? '-';
    };

    $roleNames = $user ? $user->roles->pluck('title')->implode(', ') : '-';
    $ownerName = $user?->name ?? $vehicle?->owners_name ?? $activation?->customer_name ?? 'Unknown';
    $vehicleNumber = $vehicle?->vehicle_number ?? $activation?->vehicle?->vehicle_number ?? $activation?->vehicle_reg_no ?? 'N/A';
    $deviceImei = $productMaster?->imei?->imei_number ?? '-';
    $deviceVts = $productMaster?->vts?->vts_number ?? '-';
    $deviceSim = $productMaster?->vts?->sim_number ?? '-';
    $activationStatus = strtolower((string) ($activation?->status ?? 'unknown'));
    $cardStatus = strtolower((string) $gpsCard->display_status);
    $statusClass = str_contains($activationStatus, 'activ') ? 'good' : (str_contains($activationStatus, 'pending') || str_contains($activationStatus, 'process') ? 'warn' : 'danger');
    $cardClass = $cardStatus === 'active' ? 'good' : ($cardStatus === 'expired' ? 'danger' : 'warn');
    $company = [
        'name' => 'EEMOTRACK INDIA',
        'phone' => '+91 78578 68055',
        'email' => 'info@eemotrack.com',
        'address' => 'Kamala Market, RK Bhattacharya Road, Pirmuhani, Salimpur Ahra, Golambar, Patna, Bihar-800001',
        'logo' => asset('img/logo.webp'),
    ];
    $isFullyActivated = $activationStatus === 'activated' && $cardStatus === 'active';
    $certificateDate = optional($serviceDeadline['date'] ?? null)?->format('d M Y') ?? now()->format('d M Y');
    $reportDate = now()->format('d M Y, h:i A');
@endphp

<div class="company-strip">
    <div class="company-grid">
        <div class="company-brand">
            <div class="company-logo-wrap">
                <img src="{{ $company['logo'] }}" alt="{{ $company['name'] }} Logo" class="company-logo">
            </div>
            <div>
                <p class="company-kicker">{{ $isAdminView ? 'Admin Verified Report' : 'Customer Device Certificate' }}</p>
                <h1 class="company-name">{{ $company['name'] }}</h1>
                <p class="company-tagline">
                    Trusted GPS activation, live device verification, service validity tracking and installation certification for every registered vehicle.
                </p>
            </div>
        </div>

        <div class="company-meta">
            <div class="company-meta-card">
                <div class="company-meta-label">Support Phone</div>
                <div class="company-meta-value">{{ $company['phone'] }}</div>
            </div>
            <div class="company-meta-card">
                <div class="company-meta-label">Official Email</div>
                <div class="company-meta-value">{{ $company['email'] }}</div>
            </div>
            <div class="company-meta-card" style="grid-column: 1 / -1;">
                <div class="company-meta-label">Office Address</div>
                <div class="company-meta-value">{{ $company['address'] }}</div>
            </div>
        </div>
    </div>
</div>

@if($isFullyActivated)
    <div class="activated-banner">
        <div class="activated-grid">
            <div>
                <h2 class="activated-title">ACTIVATED</h2>
                <p class="activated-copy">
                    This vehicle is currently marked as activated and the linked GPS smart card is active.
                    The installation status has been verified against activation, product and service records.
                </p>
            </div>
            <div class="activated-badge">
                Certified GPS Installation<br>
                Live Vehicle Identity<br>
                Active Smart Card
            </div>
        </div>
    </div>
@endif

<div class="hero-card">
    <div class="hero-grid">
        <div>
            <span class="badge {{ $deleteRecord ? 'danger' : 'brand' }}">
                {{ $isAdminView ? 'Admin Device Intelligence' : 'Device Service Passport' }}
            </span>
            <h1 class="hero-title">{{ $vehicleNumber }}</h1>
            <p class="hero-subtitle">
                {{ $ownerName }} - {{ $productModelName }} - GPS card {{ $gpsCard->formatted_card_number }}
            </p>
            <div class="hero-tags">
                <span class="tag">Activation: {{ ucfirst($activation?->status ?? 'not found') }}</span>
                <span class="tag">Card: {{ $gpsCard->display_status }}</span>
                <span class="tag">Model: {{ $productModelName }}</span>
                <span class="tag">IMEI: {{ $deviceImei }}</span>
                @if($serviceDeadline)
                    <span class="tag">{{ $serviceDeadline['label'] }} deadline: {{ $serviceDeadline['date']->format('d M Y') }}</span>
                @endif
            </div>
        </div>

        <div class="hero-stats">
            <div class="stat-card">
                <div class="stat-value">{{ $stats['recharge_count'] }}</div>
                <div class="stat-label">Recharge Records</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">{{ $stats['complaint_count'] }}</div>
                <div class="stat-label">Complaints</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">{{ $stats['kyc_done'] ? 'Yes' : 'No' }}</div>
                <div class="stat-label">KYC Completed</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">{{ $stats['document_count'] }}</div>
                <div class="stat-label">Documents</div>
            </div>
        </div>
    </div>
</div>

@include('gps_card_lookup.partials.smart-card', [
    'gpsCard' => $gpsCard,
    'vehicle' => $vehicle,
    'activation' => $activation,
    'vehicleNumber' => $vehicleNumber,
    'ownerName' => $ownerName,
    'productModelName' => $productModelName,
    'smartCardScope' => ($isAdminView ? 'admin' : 'public') . '-' . $gpsCard->card_number,
    'showCardSectionShell' => true,
    'showCardDownloadButton' => true,
    'showCardTitle' => true,
])

<section class="section">
    <div class="section-head">
        <div class="section-title-wrap">
            <div class="section-icon">00</div>
            <div>
                <h2 class="section-title">Welcome Note</h2>
                <p class="section-subtitle">A short professional declaration that explains what this GPS report and certificate confirm.</p>
            </div>
        </div>
    </div>

    <div class="welcome-grid">
        <div class="welcome-note">
            <h3>Professional Confirmation</h3>
            <ul class="welcome-points">
                <li>This certificate confirms that a GPS tracking device is installed in the vehicle listed in this report.</li>
                <li>The vehicle identity, linked smart card, IMEI, VTS and product model have been matched through recorded activation data.</li>
                <li>The document also reflects the latest visible AMC, warranty, subscription, KYC and recharge information available in the system.</li>
                <li>{{ $company['name'] }} maintains structured installation and service records to support transparency and professional customer service.</li>
                <li>The report is designed to help owners, fleet teams and administrators quickly verify operational GPS presence and service continuity.</li>
                <li>Our team remains available on {{ $company['phone'] }} and {{ $company['email'] }} for support, renewal guidance and record verification.</li>
            </ul>
        </div>

        <div class="print-meta-panel">
            <div class="print-meta-row">
                <div class="print-meta-label">Report Opened On</div>
                <div class="print-meta-value" id="reportOpenedAt">{{ $reportDate }}</div>
            </div>
            <div class="print-meta-row">
                <div class="print-meta-label">Print Date</div>
                <div class="print-meta-value" id="printDateValue">Will auto update when printed</div>
            </div>
            <div class="print-meta-row">
                <div class="print-meta-label">PDF Download Date</div>
                <div class="print-meta-value" id="pdfDateValue">Will auto update when saved as PDF</div>
            </div>
            <div class="print-meta-row">
                <div class="print-meta-label">Certificate Eligibility</div>
                <div class="print-meta-value">{{ $isFullyActivated ? 'Eligible - activation and card are active' : 'Pending - certificate becomes stronger after activation and active card status' }}</div>
            </div>
        </div>
    </div>
</section>

@if($deleteRecord)
    <div class="alert-banner">
        <strong>Deletion record found.</strong>
        Deleted on {{ $deleteRecord->delete_date?->format('d M Y, h:i A') ?? '-' }}
        @if($deleteRecord->reason_for_deletion)
            - Reason: {{ $deleteRecord->reason_for_deletion }}
        @endif
        @if($isAdminView && $deleteRecord->counter_name)
            - Processed by: {{ $deleteRecord->counter_name }}
        @endif
    </div>
@endif

<section class="certificate-page">
    <div class="certificate-confetti confetti-a">~</div>
    <div class="certificate-confetti confetti-b">~</div>
    <div class="certificate-confetti confetti-c">*</div>
    <div class="certificate-confetti confetti-d">*</div>
    <div class="certificate-confetti confetti-e>*</div>
    <div class="certificate-confetti confetti-f>*</div>

    <div class="certificate-inner">
        <img src="{{ $company['logo'] }}" alt="{{ $company['name'] }} Logo" class="certificate-logo">
        <p class="certificate-org">{{ $company['name'] }}</p>
        <h2 class="certificate-title">Certificate</h2>
        <p class="certificate-subtitle">of GPS Installation</p>

        <div class="certificate-divider">
            <span class="certificate-gem"></span>
            <span class="certificate-gem"></span>
            <span class="certificate-gem"></span>
        </div>

        <p class="certificate-intro">This is to certify that</p>
        <div class="certificate-recipient">{{ $ownerName }}</div>
        <div class="certificate-recipient-line"></div>

        <p class="certificate-copy">
            the vehicle <strong>{{ $vehicleNumber }}</strong> has a GPS tracking device installed and recorded under our service network with
            product model <strong>{{ $productModelName }}</strong>, IMEI <strong>{{ $deviceImei }}</strong> and VTS <strong>{{ $deviceVts }}</strong>.
            This certificate supports professional confirmation of GPS device presence, traceable installation identity and linked service coverage.
        </p>

        <p class="certificate-company-line">
            Presented by {{ $company['name'] }} on {{ $certificateDate }}
        </p>

        <div class="certificate-meta">
            <div class="certificate-meta-card">
                <div class="certificate-meta-label">Activation Status</div>
                <div class="certificate-meta-value">{{ ucfirst($activation?->status ?? 'Not Found') }}</div>
            </div>
            <div class="certificate-meta-card">
                <div class="certificate-meta-label">Card Status</div>
                <div class="certificate-meta-value">{{ $gpsCard->display_status }}</div>
            </div>
            <div class="certificate-meta-card">
                <div class="certificate-meta-label">Service Snapshot</div>
                <div class="certificate-meta-value">{{ $serviceDeadline ? $serviceDeadline['label'] . ' till ' . $serviceDeadline['date']->format('d M Y') : 'Service date not available' }}</div>
            </div>
        </div>

        <div class="certificate-footer">
            <div class="certificate-contact">
                <strong>Company Contact</strong>
                Phone: {{ $company['phone'] }}<br>
                Email: {{ $company['email'] }}<br>
                Address: {{ $company['address'] }}
            </div>

            <div class="certificate-sign">
                <div class="certificate-sign-line"></div>
                <div class="certificate-sign-name">EEMOTRACK INDIA</div>
                <div class="certificate-sign-role">Authorized GPS Service Desk</div>
            </div>

            <div class="certificate-contact">
                <strong>Document Log</strong>
                Print Date: <span id="certificatePrintDate">Will auto update when printed</span><br>
                PDF Date: <span id="certificatePdfDate">Will auto update when saved</span><br>
                Report Ref: {{ $gpsCard->formatted_card_number }}
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="section-head">
        <div class="section-title-wrap">
            <div class="section-icon">01</div>
            <div>
                <h2 class="section-title">Core Snapshot</h2>
                <p class="section-subtitle">Card, activation, user, vehicle and installed device in one place.</p>
            </div>
        </div>
    </div>

    <div class="section-grid">
        <div class="metric-card">
            <div class="metric-label">GPS Card Number</div>
            <div class="metric-value mono">{{ $gpsCard->formatted_card_number }}</div>
        </div>
        <div class="metric-card">
            <div class="metric-label">Card Batch</div>
            <div class="metric-value mono">{{ $gpsCard->batch_code ?? '-' }}</div>
        </div>
        <div class="metric-card">
            <div class="metric-label">Card Status</div>
            <div class="metric-value"><span class="badge {{ $cardClass }}">{{ $gpsCard->display_status }}</span></div>
        </div>
        <div class="metric-card">
            <div class="metric-label">Activation Status</div>
            <div class="metric-value"><span class="badge {{ $statusClass }}">{{ ucfirst($activation?->status ?? 'not found') }}</span></div>
        </div>
        <div class="metric-card">
            <div class="metric-label">Vehicle Number</div>
            <div class="metric-value mono">{{ $vehicleNumber }}</div>
        </div>
        <div class="metric-card">
            <div class="metric-label">Vehicle Type</div>
            <div class="metric-value">{{ $vehicleTypeName }}</div>
        </div>
        <div class="metric-card">
            <div class="metric-label">Product Model</div>
            <div class="metric-value">{{ $productModelName }}</div>
        </div>
        <div class="metric-card">
            <div class="metric-label">SKU</div>
            <div class="metric-value mono">{{ $productMaster?->sku ?? '-' }}</div>
        </div>
        <div class="metric-card">
            <div class="metric-label">IMEI</div>
            <div class="metric-value mono">{{ $deviceImei }}</div>
        </div>
        <div class="metric-card">
            <div class="metric-label">VTS Number</div>
            <div class="metric-value mono">{{ $deviceVts }}</div>
        </div>
        <div class="metric-card">
            <div class="metric-label">SIM Number</div>
            <div class="metric-value mono">{{ $deviceSim }}</div>
        </div>
        <div class="metric-card">
            <div class="metric-label">Activation Date</div>
            <div class="metric-value">{{ $activation?->request_date ?? $vehicle?->request_date ?? '-' }}</div>
        </div>
    </div>
</section>

<section class="section">
    <div class="section-head">
        <div class="section-title-wrap">
            <div class="section-icon">02</div>
            <div>
                <h2 class="section-title">Customer and Vehicle</h2>
                <p class="section-subtitle">Identity, contact, location and installed vehicle details.</p>
            </div>
        </div>
    </div>

    <div class="section-grid">
        <div class="metric-card">
            <div class="metric-label">Customer Name</div>
            <div class="metric-value">{{ $ownerName }}</div>
        </div>
        <div class="metric-card">
            <div class="metric-label">Company Name</div>
            <div class="metric-value">{{ $user?->company_name ?? '-' }}</div>
        </div>
        <div class="metric-card">
            <div class="metric-label">Role</div>
            <div class="metric-value">{{ $roleNames ?: '-' }}</div>
        </div>
        <div class="metric-card">
            <div class="metric-label">Mobile Number</div>
            <div class="metric-value mono">{{ $user?->mobile_number ?? $activation?->mobile_number ?? '-' }}</div>
        </div>
        <div class="metric-card">
            <div class="metric-label">WhatsApp Number</div>
            <div class="metric-value mono">{{ $user?->whatsapp_number ?? $activation?->whatsapp_number ?? '-' }}</div>
        </div>
        <div class="metric-card">
            <div class="metric-label">Email</div>
            <div class="metric-value">{{ $user?->email ?? $activation?->email ?? '-' }}</div>
        </div>
        <div class="metric-card">
            <div class="metric-label">State</div>
            <div class="metric-value">{{ $stateName($user?->state ?? $activation?->state) }}</div>
        </div>
        <div class="metric-card">
            <div class="metric-label">District</div>
            <div class="metric-value">{{ $districtName($user?->district ?? $activation?->disrict) }}</div>
        </div>
        <div class="metric-card">
            <div class="metric-label">Address</div>
            <div class="metric-value">{{ $user?->full_address ?? $activation?->address ?? '-' }}</div>
        </div>
        <div class="metric-card">
            <div class="metric-label">Vehicle Model</div>
            <div class="metric-value">{{ $vehicle?->vehicle_model ?? $activation?->vehicle_model ?? '-' }}</div>
        </div>
        <div class="metric-card">
            <div class="metric-label">Vehicle Color</div>
            <div class="metric-value">{{ $vehicle?->vehicle_color ?? $activation?->vehicle_color ?? '-' }}</div>
        </div>
        <div class="metric-card">
            <div class="metric-label">Chassis Number</div>
            <div class="metric-value mono">{{ $vehicle?->chassis_number ?? $activation?->chassis_number ?? '-' }}</div>
        </div>
        <div class="metric-card">
            <div class="metric-label">Engine Number</div>
            <div class="metric-value mono">{{ $vehicle?->engine_number ?? $activation?->engine_number ?? '-' }}</div>
        </div>
        <div class="metric-card">
            <div class="metric-label">Insurance Expiry</div>
            <div class="metric-value">{{ $vehicle?->insurance_expiry_date?->format('d M Y') ?? '-' }}</div>
        </div>
        <div class="metric-card">
            <div class="metric-label">Vehicle App Link</div>
            <div class="metric-value">{{ $vehicle?->appLink?->link ?? $activation?->app_link?->link ?? '-' }}</div>
        </div>
        <div class="metric-card">
            <div class="metric-label">Fitter Details</div>
            <div class="metric-value">
                {{ $activation?->fitter_name ?? '-' }}
                @if($activation?->fitter_number)
                    - {{ $activation->fitter_number }}
                @endif
            </div>
        </div>
        @if($isAdminView)
            <div class="metric-card">
                <div class="metric-label">App User ID</div>
                <div class="metric-value mono">{{ $vehicle?->user_id ?? $activation?->user_id ?? '-' }}</div>
            </div>
            <div class="metric-card">
                <div class="metric-label">App Password</div>
                <div class="metric-value mono">{{ $vehicle?->password ?? $activation?->password ?? '-' }}</div>
            </div>
        @endif
    </div>
</section>

<section class="section">
    <div class="section-head">
        <div class="section-title-wrap">
            <div class="section-icon">03</div>
            <div>
                <h2 class="section-title">Activation and Validity</h2>
                <p class="section-subtitle">Expiry dates use activation date plus product model duration, then recharge extensions when available.</p>
            </div>
        </div>
    </div>

    @if(!empty($serviceValidity))
        <div class="service-grid">
            @foreach($serviceValidity as $validity)
                @php
                    $fillClass = $validity['expired'] ? 'danger' : ($validity['soon'] ? 'warn' : 'good');
                    $progress = $validity['expired'] ? 100 : min(100, max(8, (int) (($validity['absolute_days'] > 365 ? 365 : $validity['absolute_days']) / 365 * 100)));
                @endphp
                <div class="service-card">
                    <div class="metric-label">{{ $validity['label'] }}</div>
                    <div class="service-date">{{ $validity['date']->format('d M Y') }}</div>
                    <div class="service-track">
                        <div class="service-fill {{ $fillClass }}" style="width: {{ $progress }}%;"></div>
                    </div>
                    <div class="service-note {{ $fillClass }}">
                        @if($validity['expired'])
                            Expired {{ $validity['absolute_days'] }} days ago
                        @else
                            {{ $validity['absolute_days'] }} days remaining
                        @endif
                    </div>
                    <div class="soft-text">Source logic: {{ str_replace('_', ' ', $validity['source']) }}</div>
                </div>
            @endforeach
        </div>
    @else
        <div class="empty-state">Validity dates are not available for this card yet.</div>
    @endif
</section>

<section class="section">
    <div class="section-head">
        <div class="section-title-wrap">
            <div class="section-icon">04</div>
            <div>
                <h2 class="section-title">KYC and Recharge</h2>
                <p class="section-subtitle">Latest KYC, first recharge plan, current renewal due date and full recharge history.</p>
            </div>
        </div>
    </div>

    <div class="section-grid" style="margin-bottom: 18px;">
        <div class="metric-card">
            <div class="metric-label">KYC Status</div>
            <div class="metric-value">
                <span class="badge {{ $latestKyc ? 'good' : 'warn' }}">{{ $latestKyc ? 'Completed' : 'Pending' }}</span>
            </div>
        </div>
        <div class="metric-card">
            <div class="metric-label">KYC Date</div>
            <div class="metric-value">{{ $latestKyc?->payment_date?->format('d M Y, h:i A') ?? '-' }}</div>
        </div>
        <div class="metric-card">
            <div class="metric-label">KYC Amount</div>
            <div class="metric-value">{{ $latestKyc ? 'Rs ' . number_format($latestKyc->payment_amount ?? 0, 2) : '-' }}</div>
        </div>
        <div class="metric-card">
            <div class="metric-label">KYC Method</div>
            <div class="metric-value">{{ $latestKyc?->payment_method ? ucfirst($latestKyc->payment_method) : '-' }}</div>
        </div>
    </div>

    @if($nextRecharge)
        <div class="metric-card" style="margin-bottom: 18px;">
            <div class="recharge-banner">
                <div class="countdown">
                    <div class="countdown-days">{{ $nextRecharge['expired'] ? $nextRecharge['days_left'] * -1 : $nextRecharge['days_left'] }}</div>
                    <div class="countdown-label">{{ $nextRecharge['expired'] ? 'days overdue' : 'days to next due' }}</div>
                </div>
                <div>
                    <h3 style="margin: 0 0 8px; font-size: 24px;">{{ $nextRecharge['label'] }} due on {{ $nextRecharge['due_date']->format('d M Y') }}</h3>
                    <div class="soft-text">
                        First recharge plan:
                        <strong>{{ $nextRecharge['first_plan_name'] ?? 'Not recharged yet' }}</strong>
                        @if($nextRecharge['first_recharge_date'])
                            - {{ $nextRecharge['first_recharge_date']->format('d M Y') }}
                        @endif
                    </div>
                    <div class="soft-text">
                        Latest recharge plan:
                        <strong>{{ $nextRecharge['latest_plan_name'] ?? 'No recharge history' }}</strong>
                        @if($nextRecharge['latest_recharge_date'])
                            - {{ $nextRecharge['latest_recharge_date']->format('d M Y') }}
                        @endif
                        @if($nextRecharge['latest_plan_amount'])
                            - Rs {{ number_format($nextRecharge['latest_plan_amount'], 2) }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if($rechargeHistory->isNotEmpty())
        <div class="timeline-grid">
            @foreach($rechargeHistory as $recharge)
                <div class="timeline-card">
                    <h4>{{ $recharge->select_recharge?->plan_name ?? 'Recharge Plan' }}</h4>
                    <div class="timeline-meta">
                        {{ $recharge->select_recharge?->type ? ucfirst($recharge->select_recharge->type) : 'Plan type not set' }}
                        - {{ \Carbon\Carbon::parse($recharge->payment_date ?? $recharge->created_at)->format('d M Y, h:i A') }}
                    </div>
                    <div class="timeline-note">
                        Amount: Rs {{ number_format($recharge->payment_amount ?? 0, 2) }}<br>
                        Payment status: {{ ucfirst($recharge->payment_status ?? 'unknown') }}<br>
                        Payment method: {{ $recharge->payment_method ?? '-' }}<br>
                        AMC / Warranty / Subscription extension:
                        {{ $recharge->select_recharge?->amc_duration ?? 0 }} /
                        {{ $recharge->select_recharge?->warranty_duration ?? 0 }} /
                        {{ $recharge->select_recharge?->subscription_duration ?? 0 }} months
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="empty-state">No recharge history found for this vehicle yet.</div>
    @endif
</section>

<section class="section">
    <div class="section-head">
        <div class="section-title-wrap">
            <div class="section-icon">05</div>
            <div>
                <h2 class="section-title">Complaints and Resolution</h2>
                <p class="section-subtitle">Complaint status, reason, resolution note and resolution timing.</p>
            </div>
        </div>
    </div>

    @if($complaints->isNotEmpty())
        <div class="timeline-grid">
            @foreach($complaints as $complaint)
                @php
                    $complaintBadge = strtolower((string) $complaint->status) === 'solved'
                        ? 'good'
                        : (strtolower((string) $complaint->status) === 'processing' ? 'warn' : 'danger');
                @endphp
                <div class="timeline-card">
                    <div style="display:flex;justify-content:space-between;gap:12px;align-items:flex-start;">
                        <div>
                            <h4>{{ $complaint->ticket_number ?? 'Complaint' }}</h4>
                            <div class="timeline-meta">
                                Raised {{ $complaint->created_at?->format('d M Y, h:i A') ?? '-' }}
                                - By {{ $complaint->created_by?->name ?? 'Unknown' }}
                            </div>
                        </div>
                        <span class="badge {{ $complaintBadge }}">{{ ucfirst($complaint->status ?? 'unknown') }}</span>
                    </div>
                    <div class="timeline-note">
                        Complaint type:
                        {{ $complaint->select_complains->pluck('title')->implode(', ') ?: '-' }}<br>
                        Reason:
                        {{ trim(strip_tags((string) $complaint->reason)) ?: '-' }}<br>
                        Resolution note:
                        {{ trim(strip_tags((string) $complaint->admin_message)) ?: 'Not added yet' }}<br>
                        @if(strtolower((string) $complaint->status) === 'solved')
                            Solved on {{ $complaint->updated_at?->format('d M Y, h:i A') ?? '-' }}.
                            @if($meta['handled_by_available'])
                                Handled by field available.
                            @else
                                Solver name is not stored in the current database structure.
                            @endif
                        @else
                            Last updated on {{ $complaint->updated_at?->format('d M Y, h:i A') ?? '-' }}.
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="empty-state">No complaint record is linked to this vehicle.</div>
    @endif
</section>

<section class="section">
    <div class="section-head">
        <div class="section-title-wrap">
            <div class="section-icon">06</div>
            <div>
                <h2 class="section-title">Documents and Proofs</h2>
                <p class="section-subtitle">ID proofs, RC, insurance, pollution, vehicle photos and installation photos from activation and vehicle records.</p>
            </div>
        </div>
    </div>

    @if($documents->isNotEmpty())
        <div class="documents-grid">
            @foreach($documents as $document)
                <div class="document-card">
                    @if($document['is_image'])
                        <img src="{{ $document['thumbnail'] }}" alt="{{ $document['label'] }}" class="document-thumb">
                    @else
                        <div class="document-thumb" style="display:flex;align-items:center;justify-content:center;font-size:40px;">DOC</div>
                    @endif
                    <h4>{{ $document['label'] }}</h4>
                    <div class="document-meta">
                        {{ $document['source'] }}<br>
                        {{ $document['file_name'] }}
                    </div>
                    <div class="document-actions">
                        <a href="{{ $document['url'] }}" target="_blank" class="mini-btn">Open</a>
                        <a href="{{ $document['url'] }}" download class="mini-btn">Download</a>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="empty-state">No uploaded documents were found for this vehicle.</div>
    @endif
</section>

<section class="section">
    <div class="section-head">
        <div class="section-title-wrap">
            <div class="section-icon">07</div>
            <div>
                <h2 class="section-title">Deletion Audit</h2>
                <p class="section-subtitle">If the user data or vehicle record was deleted, the most recent audit entry appears here.</p>
            </div>
        </div>
    </div>

    @if($deleteRecord)
        <div class="section-grid">
            <div class="metric-card">
                <div class="metric-label">Owner Name</div>
                <div class="metric-value">{{ $deleteRecord->owner_name ?? $deleteRecord->user_name ?? '-' }}</div>
            </div>
            <div class="metric-card">
                <div class="metric-label">Owner Phone</div>
                <div class="metric-value mono">{{ $deleteRecord->owner_phone ?? $deleteRecord->number ?? '-' }}</div>
            </div>
            <div class="metric-card">
                <div class="metric-label">Delete Date</div>
                <div class="metric-value">{{ $deleteRecord->delete_date?->format('d M Y, h:i A') ?? '-' }}</div>
            </div>
            <div class="metric-card">
                <div class="metric-label">Reason for Deletion</div>
                <div class="metric-value">{{ $deleteRecord->reason_for_deletion ?? '-' }}</div>
            </div>
            <div class="metric-card">
                <div class="metric-label">Date of Fitting</div>
                <div class="metric-value">{{ $deleteRecord->date_of_fitting?->format('d M Y') ?? '-' }}</div>
            </div>
            <div class="metric-card">
                <div class="metric-label">Previous Expiry Date</div>
                <div class="metric-value">{{ $deleteRecord->expiry_date?->format('d M Y') ?? '-' }}</div>
            </div>
            <div class="metric-card">
                <div class="metric-label">IMEI / VTS / SIM</div>
                <div class="metric-value mono">
                    {{ $deleteRecord->imei_no ?? '-' }} - {{ $deleteRecord->vts_no ?? '-' }} - {{ $deleteRecord->sim_number ?? '-' }}
                </div>
            </div>
            @if($isAdminView)
                <div class="metric-card">
                    <div class="metric-label">Deleted By / Counter</div>
                    <div class="metric-value">{{ $deleteRecord->counter_name ?? '-' }}</div>
                </div>
            @endif
        </div>
    @else
        <div class="empty-state">No delete-data audit was found for this card, vehicle, IMEI or VTS.</div>
    @endif
</section>

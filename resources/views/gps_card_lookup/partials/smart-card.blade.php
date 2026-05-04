@php
    $smartCardVehicle = $vehicle ?? null;
    $smartCardActivation = $activation ?? null;
    $smartCardLookup = $lookup ?? null;
    $smartCardOwnerName = $ownerName ?? null;
    $smartCardScope = $smartCardScope ?? preg_replace('/[^A-Za-z0-9_-]/', '-', (string) ($gpsCard->card_number ?? 'gps-card'));
    $smartCardVehicleNumber = trim((string) (
        $vehicleNumber
        ?? $smartCardVehicle?->vehicle_number
        ?? $smartCardActivation?->vehicle?->vehicle_number
        ?? $smartCardActivation?->vehicle_reg_no
        ?? $gpsCard->assignedActivationRequest?->vehicle?->vehicle_number
        ?? $gpsCard->assignedActivationRequest?->vehicle_reg_no
        ?? 'N/A'
    ));
    $smartCardVehicleNumber = $smartCardVehicleNumber !== '' ? $smartCardVehicleNumber : 'N/A';
    $smartCardProductModel = $productModelName
        ?? $smartCardLookup['productModel']?->product_model
        ?? $smartCardLookup['productModel']?->name
        ?? $gpsCard->productModel?->product_model
        ?? $gpsCard->productModel?->name
        ?? 'UNASSIGNED';
    $smartCardHolderName = strtoupper($smartCardOwnerName ?: ($gpsCard->card_holder_name ?: ($gpsCard->usedBy?->name ?? 'NOT ASSIGNED')));
    $smartCardStatus = strtoupper($gpsCard->display_status ?: 'ACTIVE');
    $smartCardLookupUrl = $smartCardLookupUrl ?? 'https://erp.eemotrack.com/device/lookup';
    $showCardSectionShell = $showCardSectionShell ?? true;
    $showCardDownloadButton = $showCardDownloadButton ?? true;
    $showCardTitle = $showCardTitle ?? true;
@endphp

<section class="smart-card-section{{ $showCardSectionShell ? '' : ' smart-card-section--plain' }} js-smart-card-scope" data-card-scope="{{ $smartCardScope }}">
    @if($showCardTitle)
        <div class="smart-card-head no-print">
            <div class="smart-card-head-copy">
                <h3>GPS Smart Card Preview</h3>
                <p>Front and back card design in exact 54 x 86 mm size. JPG download will save both sides separately.</p>
            </div>
        </div>
    @endif

    <div class="smart-card-sheet">
        <div class="smart-card-page">
            <article
                class="smart-card smart-card--front"
                id="smartCardFront-{{ $smartCardScope }}"
                data-filename="gps-card-{{ $gpsCard->card_number }}-front.jpg"
            >
                <div class="smart-card-sheen"></div>
                <div class="smart-card-top-edge"></div>
                <div class="smart-card-bottom-edge"></div>

                <svg class="smart-card-route-wave" viewBox="0 0 140 56" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path d="M0 18C18 8 30 7 48 18C66 29 77 29 94 18C111 7 121 8 140 18" stroke="#3dbdb5" stroke-width="2.2"/>
                    <path d="M0 31C18 21 30 20 48 31C66 42 77 42 94 31C111 20 121 21 140 31" stroke="#2f8fcb" stroke-width="1.5"/>
                    <path d="M0 44C18 34 30 33 48 44C66 55 77 55 94 44C111 33 121 34 140 44" stroke="#f07c26" stroke-width="1.2"/>
                </svg>

                <div class="smart-front-content">
                    <div class="smart-front-header">
                        <div class="smart-logo-wrap">
                            <svg class="smart-eemot-logo" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <rect x="4" y="4" width="43" height="43" rx="11" fill="#3DBDB5"/>
                                <text x="25.5" y="37" font-family="'Arial Black', Arial, sans-serif" font-weight="900" font-size="30" fill="white" text-anchor="middle">E</text>
                                <rect x="53" y="4" width="43" height="43" rx="11" fill="#2B82A8"/>
                                <text x="74.5" y="37" font-family="'Arial Black', Arial, sans-serif" font-weight="900" font-size="30" fill="white" text-anchor="middle">E</text>
                                <rect x="4" y="53" width="43" height="43" rx="11" fill="#F07C26"/>
                                <text x="25.5" y="86" font-family="'Arial Black', Arial, sans-serif" font-weight="900" font-size="30" fill="white" text-anchor="middle">M</text>
                                <rect x="53" y="53" width="43" height="43" rx="11" fill="#E8473F"/>
                                <text x="74.5" y="86" font-family="'Arial Black', Arial, sans-serif" font-weight="900" font-size="30" fill="white" text-anchor="middle">T</text>
                                <circle cx="50" cy="50" r="17" fill="white"/>
                                <text x="50" y="57" font-family="'Arial Black', Arial, sans-serif" font-weight="900" font-size="19" fill="#2B82A8" text-anchor="middle">O</text>
                            </svg>

                            <div class="smart-brand-copy">
                                <p class="smart-brand-name">EEMOT GPS Systems</p>
                                <div class="smart-brand-tag">Track . Navigate . Protect</div>
                                 <div class="tagline" style="font-size: 10px;color: white;">EEMOT – Reliable GPS Solutions Across India.</div>

                            </div>
                        </div>
                            <div class="smart-vehicle-status">{{ $smartCardStatus }}</div>
                        {{-- <div class="smart-holder-pill">{{ $gpsCard->usage_status }} / {{ $gpsCard->print_status }}</div> --}}
                    </div>

                    <div class="smart-front-middle">
                        <div class="smart-vehicle-panel">
                                <div class="smart-front-footer">
                                    <div class="smart-front-footer-left">
                                        <div class="smart-footer-label">Card Holder</div>
                                        @php
                                            dd($smartCardHolderName)
                                        @endphp
                                        <div class="smart-footer-name">{{ strtoupper(\App\Models\User::where('id', $smartCardHolderName)->value('name') ?? 'Not Assigned') }}</div>
                                        {{-- {{ strtoupper(\App\Models\User::where('id', $smartCardHolderName)->value('name') ?? 'Not Assigned') }} --}}
                                    </div>

                                    {{-- <div class="smart-status-badge">{{ $smartCardStatus }}</div> --}}
                                </div>
                                <div class="d-flex">
                                    <div class="smart-vehicle-plate">{{ $smartCardVehicleNumber }}</div>

                                </div>
                        </div>

                        <div class="smart-device-copy">
                            <div>
                                <div class="smart-device-kicker">GPS Tracking Device</div>
                                <p class="smart-model-name">{{ $smartCardProductModel }}</p>
                            </div>

                            <div class="smart-meta-row">
                                <div class="smart-meta-box">
                                    <div class="smart-meta-label">Valid From</div>
                                    <div class="smart-meta-value">{{ $gpsCard->formatted_valid_from }}</div>
                                </div>

                                <div class="smart-meta-box">
                                    <div class="smart-meta-label">Valid Thru</div>
                                    <div class="smart-meta-value">{{ $gpsCard->formatted_valid_to }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <div class="smart-front-number">{{ $gpsCard->formatted_card_number }}</div>

                        {{-- <div class="smart-front-footer">
                            <div class="smart-front-footer-left">
                                <div class="smart-footer-label">Card Holder</div>
                                <div class="smart-footer-name">{{ $smartCardHolderName }}</div>
                            </div>

                            <div class="smart-status-badge">{{ $smartCardStatus }}</div>
                        </div> --}}
                    </div>
                </div>
            </article>
        </div>

        <div class="smart-card-page">
            <article
                class="smart-card smart-card--back"
                id="smartCardBack-{{ $smartCardScope }}"
                data-filename="gps-card-{{ $gpsCard->card_number }}-back.jpg"
            >
                <div class="smart-card-sheen"></div>
                <div class="smart-card-top-edge"></div>
                <div class="smart-card-bottom-edge"></div>

                <svg class="smart-card-route-wave" viewBox="0 0 140 56" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path d="M0 18C18 8 30 7 48 18C66 29 77 29 94 18C111 7 121 8 140 18" stroke="#f07c26" stroke-width="2.2"/>
                    <path d="M0 31C18 21 30 20 48 31C66 42 77 42 94 31C111 20 121 21 140 31" stroke="#ea503c" stroke-width="1.5"/>
                    <path d="M0 44C18 34 30 33 48 44C66 55 77 55 94 44C111 33 121 34 140 44" stroke="#3dbdb5" stroke-width="1.2"/>
                </svg>

                <div class="smart-back-content">
                    <div class="smart-back-header">
                        <svg class="smart-back-logo" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <rect x="4" y="4" width="43" height="43" rx="11" fill="#3DBDB5"/>
                            <text x="25.5" y="37" font-family="'Arial Black', Arial, sans-serif" font-weight="900" font-size="30" fill="white" text-anchor="middle">E</text>
                            <rect x="53" y="4" width="43" height="43" rx="11" fill="#2B82A8"/>
                            <text x="74.5" y="37" font-family="'Arial Black', Arial, sans-serif" font-weight="900" font-size="30" fill="white" text-anchor="middle">E</text>
                            <rect x="4" y="53" width="43" height="43" rx="11" fill="#F07C26"/>
                            <text x="25.5" y="86" font-family="'Arial Black', Arial, sans-serif" font-weight="900" font-size="30" fill="white" text-anchor="middle">M</text>
                            <rect x="53" y="53" width="43" height="43" rx="11" fill="#E8473F"/>
                            <text x="74.5" y="86" font-family="'Arial Black', Arial, sans-serif" font-weight="900" font-size="30" fill="white" text-anchor="middle">T</text>
                            <circle cx="50" cy="50" r="17" fill="white"/>
                            <text x="50" y="57" font-family="'Arial Black', Arial, sans-serif" font-weight="900" font-size="19" fill="#2B82A8" text-anchor="middle">O</text>
                        </svg>

                        <div class="smart-back-title">
                            <div class="smart-back-title-main">EEMOT Device Verification Card</div>
                            <div class="smart-back-title-sub">Batch {{ $gpsCard->batch_code ?? 'N/A' }}</div>
                        </div>
                    </div>

                    <div class="smart-mag-stripe"></div>

                    <div class="smart-back-main">
                        <a class="smart-qr-panel" href="{{ $smartCardLookupUrl }}" target="_blank" rel="noopener">
                            <div class="smart-qr-box">
                                <div class="smart-qr-mount js-smart-card-qr" data-lookup-url="{{ $smartCardLookupUrl }}" aria-label="QR code"></div>
                            </div>
                            {{-- <div class="smart-scan-copy">Scan for lookup</div>
                            <div class="smart-scan-url">erp.eemotrack.com/device/lookup</div> --}}
                        </a>

                        <div class="smart-back-details">
                            <div class="smart-detail-block">
                                <div class="smart-detail-label">16-Digit Device ID</div>
                                <div class="smart-detail-value smart-detail-value--mono">{{ $gpsCard->formatted_card_number }}</div>
                            </div>

                            <div class="smart-detail-block">
                                <div class="smart-detail-label">Vehicle Number</div>
                                <div class="smart-detail-value">{{ $smartCardVehicleNumber }}</div>
                            </div>

                            <div class="smart-detail-block">
                                <div class="smart-detail-label">Card Holder</div>
                                <div class="smart-detail-value">{{ strtoupper(\App\Models\User::where('id', $smartCardHolderName)->value('name') ?? 'Not Assigned') }}</div>
                            </div>

                            <div class="smart-detail-block">
                                <div class="smart-detail-label">Product Model</div>
                                <div class="smart-detail-value">{{ $smartCardProductModel }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="smart-back-footer">
                        <div class="smart-back-note"><span>1.</span> Linked vehicle: <strong>{{ $smartCardVehicleNumber }}</strong> for service verification.</div>
                        <div class="smart-back-note"><span>2.</span> Scan QR to open the official EEMOT device lookup page directly.</div>
                        <div class="smart-back-note"><span>3.</span> Card status: <strong>{{ $smartCardStatus }}</strong> and usage: <strong>{{ $gpsCard->usage_status }}</strong>.</div>
                        <div class="smart-back-note"><span>4.</span> Keep this card safe for print, download and support reference.</div>
                    </div>
                </div>
            </article>
        </div>
    </div>

    @if($showCardDownloadButton)
        <div class="smart-card-footer-actions no-print">
            <button
                type="button"
                class="smart-card-download-btn js-smart-card-download"
                data-front-id="smartCardFront-{{ $smartCardScope }}"
                data-back-id="smartCardBack-{{ $smartCardScope }}"
                disabled
            >
                Preparing JPG...
            </button>
        </div>
    @endif
</section>

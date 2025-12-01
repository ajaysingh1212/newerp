@extends('layouts.admin')
@section('content')
<style>
   /* ---------- existing styles ---------- */
   .main-div {
    position: relative;
   }

   /* LEFT full border */
   .main-div::before {
    content: "";
    position: absolute;
    left: 0;
    top: 0;
    width: 8px;           /* border thickness */
    height: 100%;
    background: #0080ff;  /* border color */
   }

   /* TOP half border */
   .main-div::after {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    height: 8px;
    width: 40%;          /* top border length */
    background: #0080ff;
   }

   /* BOTTOM half border */
   .main-div .bottom-line {
    content: "";
    position: absolute;
    bottom: 0;
    left: 0;
    height: 8px;
    width: 40%;          /* bottom border length */
    background: #0080ff;
   }

    #top-popup {
      position: fixed;
      top: 40px !important;
      left: 50%;
      transform: translateX(-50%);
      z-index: 99999;
      width: auto;
      max-width: 90%;
      opacity: 0;
      transition: opacity 0.4s ease, transform 0.3s ease;
    }

    #top-popup.show {
      opacity: 1;
      transform: translateX(-50%) translateY(10px);
    }

   /* ---------- Glassmorphism Premium Investor Card ---------- */
   #investor-card {
     background: rgba(255,255,255,0.55);
     border-radius: 18px;
     border: 1px solid rgba(255,255,255,0.35);
     backdrop-filter: blur(10px) saturate(120%);
     -webkit-backdrop-filter: blur(10px) saturate(120%);
     box-shadow: 0 12px 30px rgba(12, 40, 80, 0.12);
     overflow: hidden;
     transition: transform .25s ease, box-shadow .25s ease;
   }
   #investor-card:hover {
     transform: translateY(-6px);
     box-shadow: 0 20px 40px rgba(12, 40, 80, 0.18);
   }

   .investor-header {
     background: linear-gradient(90deg, rgba(79,70,229,0.95), rgba(124,58,237,0.95));
     color: white;
     padding: 18px 22px;
     display: flex;
     align-items: center;
     gap: 18px;
   }

   .investor-header img {
     width: 88px;
     height: 88px;
     border-radius: 999px;
     border: 4px solid rgba(255,255,255,0.85);
     box-shadow: 0 8px 20px rgba(14, 35, 70, 0.18);
     object-fit: cover;
   }

   .investor-body {
     padding: 18px 22px;
     color: #0f172a;
   }

   .status-pill {
     display:inline-flex;
     align-items:center;
     gap:8px;
     padding:6px 10px;
     border-radius:999px;
     font-weight:600;
     font-size:12px;
     color:white;
     margin-left:6px;
   }

   .status-green { background:#16a34a; }
   .status-red   { background:#dc2626; }
   .status-yellow{ background:#f59e0b; color:#111827; }

   .plan-card {
        background: linear-gradient(180deg, #ffffff 0%, #f0fcff 100%);
        border-left: 6px solid #07a0b6;
        border-radius:12px;
        overflow:hidden;
   }
   .plan-card h4 { color: #0f6b74; }
   .plan-card p { color: #3b3b3b; }

   /* small responsive tweaks */
   @media (max-width: 768px) {
     .investor-header { padding: 14px; }
     .investor-body { padding: 14px; }
     .investor-header img { width:72px; height:72px; border-width:3px; }
   }

   /* top popup style tuning */
   #top-popup-body { min-width: 260px; max-width: 90vw; text-align:center; }

</style>

<div class="max-w-6xl mx-auto py-8">
    <div class="bg-white shadow-xl rounded-2xl p-8 border border-gray-200">
        <div class="pb-4 border-b mb-6">
            <h2 class="text-2xl font-bold text-indigo-600">{{ trans('global.create') }} {{ trans('cruds.investment.title_singular') }}</h2>
        </div>

    <form id="investment-form" method="POST" action="{{ route('admin.investments.store') }}" enctype="multipart/form-data">
        @csrf
        <div id="top-popup" class="fixed left-1/2 transform -translate-x-1/2 top-6 z-50 hidden">
            <div id="top-popup-body" class="rounded-lg px-5 py-3 text-sm font-medium shadow-lg"></div>
        </div>

        <div class="grid grid-cols-1 gap-6">

            <div class="flex flex-col md:flex-row gap-6">
                <div class="w-full md:w-1/2 bg-gradient-to-br from-gray-50 to-white border rounded-2xl p-5 shadow-sm">
                    <label class="block text-sm font-semibold text-gray-700 mb-3 required">{{ trans('cruds.investment.fields.select_investor') }}</label>
                    @php
                        $user = auth()->user();
                        $userRole = $user->roles->first()->title ?? null;
                    @endphp

                    @if($userRole === 'Admin')
                        <select name="select_investor_id" id="select_investor_id" class="w-full rounded-lg border-gray-300 shadow-sm px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">{{ trans('global.pleaseSelect') }}</option>
                            @foreach($select_investors as $id => $entry)
                                <option value="{{ $id }}" {{ old('select_investor_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                            @endforeach
                        </select>
                    @else
                        @if(isset($selected_investor) && $selected_investor)
                            <input type="hidden" name="select_investor_id" id="select_investor_id" value="{{ $selected_investor->id }}">
                            <p class="text-sm text-gray-700">Investor: <strong>{{ $selected_investor->reg }}</strong></p>
                        @else
                            <p class="text-sm text-red-600">आपके खाते से जुड़ा कोई Investor रिकॉर्ड नहीं मिला। कृपया Admin से संपर्क करें।</p>
                        @endif
                    @endif

                    {{-- Premium Glassmorphism Investor Card --}}
                    <div id="investor-card" class="mt-4 hidden rounded-2xl overflow-hidden shadow-lg border">
                        <div class="investor-header">
                            <img id="investor-profile-img" src="/mnt/data/5db75b29-5ec8-440f-b172-2f44fd5d1bfb.png" alt="profile">
                            <div class="flex-1">
                                <h3 id="investor-reg" class="text-xl font-semibold">Investor</h3>
                                <p id="investor-role" class="text-sm opacity-90 mt-1">Investor Role</p>
                                <div id="investor-status-flags" class="mt-3 flex items-center"></div>
                            </div>
                            <div class="text-right ml-4">
                                <div class="text-xs text-white opacity-80">Account Snapshot</div>
                                <div id="investor-quick-amount" class="text-lg font-semibold text-white mt-1"></div>
                            </div>
                        </div>

                        <div class="investor-body grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                            <div class="space-y-1">
                                <p><span class="font-semibold">ID:</span> <span id="investor-id-val"></span></p>
                                <p><span class="font-semibold">Reg:</span> <span id="investor-reg-val"></span></p>
                                <p><span class="font-semibold">Referral:</span> <span id="investor-referral-val"></span></p>
                                <p><span class="font-semibold">Aadhaar:</span> <span id="investor-aadhaar-val"></span></p>
                                <p><span class="font-semibold">PAN:</span> <span id="investor-pan-val"></span></p>
                                <p><span class="font-semibold">DOB:</span> <span id="investor-dob-val"></span></p>
                                <p><span class="font-semibold">Gender:</span> <span id="investor-gender-val"></span></p>
                                <p><span class="font-semibold">Father:</span> <span id="investor-father-val"></span></p>
                            </div>
                            <div class="space-y-1">
                                <p><span class="font-semibold">Address 1:</span> <span id="investor-addr1-val"></span></p>
                                <p><span class="font-semibold">Address 2:</span> <span id="investor-addr2-val"></span></p>
                                <p><span class="font-semibold">Pincode:</span> <span id="investor-pincode-val"></span></p>
                                <p><span class="font-semibold">City:</span> <span id="investor-city-val"></span></p>
                                <p><span class="font-semibold">State:</span> <span id="investor-state-val"></span></p>
                                <p><span class="font-semibold">Country:</span> <span id="investor-country-val"></span></p>
                                <p><span class="font-semibold">A/C Holder:</span> <span id="investor-bank-holder-val"></span></p>
                                <p><span class="font-semibold">A/C No.:</span> <span id="investor-bank-ac-val"></span></p>
                                <p><span class="font-semibold">IFSC:</span> <span id="investor-ifsc-val"></span></p>
                                <p><span class="font-semibold">Bank/Branch:</span> <span id="investor-bank-name-val"></span></p>
                                <p><span class="font-semibold">Income:</span> <span id="investor-income-val"></span></p>
                                <p><span class="font-semibold">Occupation:</span> <span id="investor-occ-val"></span></p>
                                <p><span class="font-semibold">Risk:</span> <span id="investor-risk-val"></span></p>
                                <p><span class="font-semibold">Experience:</span> <span id="investor-exp-val"></span></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="w-full md:w-1/2">
                    <label class="block text-sm font-semibold text-gray-700 mb-3">{{ trans('cruds.investment.fields.select_plan') }} (choose one)</label>
                    <div class="plan-area bg-white border rounded-2xl p-4 shadow-sm">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($plans as $plan)
                               {{-- note: data-plan uses single quotes so @json output is valid --}}
                               <div class="main-div plan-card relative w-full bg-white shadow-md border p-0 overflow-hidden" data-plan='@json($plan)'>
                                    <!-- Top Number Box -->
                                    <div class="absolute top-0 left-0 text-black px-4 py-1 rounded-br-xl">
                                        <span class="font-bold text-lg">ID: {{ $plan->id }}</span>
                                    </div>

                                    <!-- Blue Header -->
                                    <div class="bg-blue-500 text-white text-center py-1 mt-9">
                                        <h4 class="text-xl text-white font-semibold ">
                                            {{ $plan->plan_name }}
                                        </h4>
                                    </div>

                                    <!-- Content Body -->
                                    <div class="p-4 space-y-2 text-sm text-gray-700">
                                        <p><span class="font-semibold">Secure Interest %:</span> {{ $plan->secure_interest_percent }}</p>
                                        <p><span class="font-semibold">Market Interest %:</span> {{ $plan->market_interest_percent }}</p>
                                        <p><span class="font-semibold">Total Interest %:</span> {{ $plan->total_interest_percent }}</p>
                                        <p><span class="font-semibold">Payout Frequency:</span> {{ $plan->payout_frequency }}</p>
                                        <p><span class="font-semibold">Min Invest:</span> <span class="plan-min">{{ $plan->min_invest_amount }}</span></p>
                                        <p><span class="font-semibold">Max Invest:</span> {{ $plan->max_invest_amount }}</p>
                                        <p><span class="font-semibold">Lockin Days:</span> {{ $plan->lockin_days }}</p>
                                        <p><span class="font-semibold">Withdraw Processing Hours:</span> {{ $plan->withdraw_processing_hours }}</p>
                                        <p><span class="font-semibold">Status:</span> {{ $plan->status }}</p>

                                        <!-- Checkbox -->
                                        <div class="flex justify-end pt-4">
                                            <input type="checkbox" value="{{ $plan->id }}"
                                                class="plan-checkbox h-5 w-5 accent-blue-600">
                                        </div>

                                        <div class="mt-3 flex flex-col gap-2">
                                            <button type="button" class="text-left text-sm underline">View T&C</button>
                                            <button type="button" class="text-left text-sm underline">View Policy</button>
                                            <button type="button" class="text-left text-sm underline">View Details</button>
                                        </div>

                                        <div class="bottom-line" style="height:8px; background:#0080ff; margin-top:10px;"></div>
                                    </div>
                               </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-green-50 p-4 rounded-lg">
                    <label class="block text-sm font-semibold text-gray-700 mb-1 required">{{ trans('cruds.investment.fields.principal_amount') }}</label>
                    <input type="number" step="0.01" name="principal_amount" id="principal_amount" value="{{ old('principal_amount') }}" class="w-full rounded-lg border-gray-300 shadow-sm px-3 py-2 text-sm" required>
                </div>

                <div class="bg-yellow-50 p-4 rounded-lg">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">{{ trans('cruds.investment.fields.secure_interest_percent') }}</label>
                    <input type="text" name="secure_interest_percent" id="secure_interest_percent" value="{{ old('secure_interest_percent') }}" class="w-full rounded-lg border-gray-300 shadow-sm px-3 py-2 text-sm" readonly>
                </div>

                <div class="bg-yellow-50 p-4 rounded-lg">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">{{ trans('cruds.investment.fields.market_interest_percent') }}</label>
                    <input type="text" name="market_interest_percent" id="market_interest_percent" value="{{ old('market_interest_percent') }}" class="w-full rounded-lg border-gray-300 shadow-sm px-3 py-2 text-sm" readonly>
                </div>

                <div class="bg-yellow-50 p-4 rounded-lg">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">{{ trans('cruds.investment.fields.total_interest_percent') }}</label>
                    <input type="text" name="total_interest_percent" id="total_interest_percent" value="{{ old('total_interest_percent') }}" class="w-full rounded-lg border-gray-300 shadow-sm px-3 py-2 text-sm" readonly>
                </div>

                <div class="bg-purple-50 p-4 rounded-lg">
                    <label class="block text-sm font-semibold text-gray-700 mb-1 required">{{ trans('cruds.investment.fields.start_date') }}</label>
                    <input type="text" name="start_date" id="start_date" value="{{ date('Y-m-d') }}" class="w-full rounded-lg border-gray-300 shadow-sm px-3 py-2 text-sm" readonly>
                </div>
            </div>
            <div class="form-group">
                <label for="select_agent_id">Select Agent</label>
                <select class="form-control select2 {{ $errors->has('select_agent') ? 'is-invalid' : '' }}" name="select_agent_id" id="select_agent_id">
                    @foreach($select_agents as $id => $entry)
                        <option value="{{ $id }}" {{ old('select_agent_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('select_agent'))
                    <span class="text-danger">{{ $errors->first('select_agent') }}</span>
                @endif
                
            </div>
        </div>

        <div class="mt-8 text-right">
            <button id="submit-btn" type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg shadow hover:bg-indigo-700 transition">{{ trans('global.save') }}</button>
        </div>
    </form>
</div>
</div>

<script>
    const REGISTRATIONS = @json($registrations ?? collect());
    document.addEventListener('DOMContentLoaded', function () {

        /* Robust popup */
        function showTopPopup(message, type = 'info') {
            const popup = document.getElementById('top-popup');
            const body = document.getElementById('top-popup-body');
            if (!popup || !body) return;

            body.className = 'rounded-lg px-5 py-3 text-sm font-medium shadow-lg';
            if (type === 'error') {
                body.classList.add('bg-red-600', 'text-white');
            } else if (type === 'success') {
                body.classList.add('bg-green-600', 'text-white');
            } else {
                body.classList.add('bg-indigo-600', 'text-white');
            }
            body.textContent = message;

            popup.classList.remove('hidden');
            // reflow to allow animation class
            void popup.offsetWidth;
            popup.classList.add('show');

            clearTimeout(window.__topPopupTimeout);
            window.__topPopupTimeout = setTimeout(() => {
                popup.classList.remove('show');
                setTimeout(() => popup.classList.add('hidden'), 400);
            }, 3500);
        }

        function makeStatusPill(text, ok) {
            const span = document.createElement('span');
            span.className = 'status-pill ' + (ok ? 'status-green' : 'status-red');
            span.textContent = text;
            return span;
        }

        function showInvestorCard(regObj) {
            const card = document.getElementById('investor-card');
            if (!card) return;

            if (!regObj) {
                card.classList.add('hidden');
                return;
            }

            card.classList.remove('hidden');

            // fill fields safely
            const safe = v => (typeof v === 'undefined' || v === null) ? '' : v;

            document.getElementById('investor-reg').textContent = safe(regObj.reg) || 'Investor';
            document.getElementById('investor-role').textContent = safe(regObj.role_title) || '';
            document.getElementById('investor-reg-val').textContent = safe(regObj.reg);
            document.getElementById('investor-id-val').textContent = safe(regObj.id);
            document.getElementById('investor-referral-val').textContent = safe(regObj.referral_code);
            document.getElementById('investor-aadhaar-val').textContent = safe(regObj.aadhaar_number);
            document.getElementById('investor-pan-val').textContent = safe(regObj.pan_number);
            document.getElementById('investor-dob-val').textContent = safe(regObj.dob);
            document.getElementById('investor-gender-val').textContent = safe(regObj.gender);
            document.getElementById('investor-father-val').textContent = safe(regObj.father_name);
            document.getElementById('investor-addr1-val').textContent = safe(regObj.address_line_1);
            document.getElementById('investor-addr2-val').textContent = safe(regObj.address_line_2);
            document.getElementById('investor-pincode-val').textContent = safe(regObj.pincode);
            document.getElementById('investor-city-val').textContent = safe(regObj.city);
            document.getElementById('investor-state-val').textContent = safe(regObj.state);
            document.getElementById('investor-country-val').textContent = safe(regObj.country);
            document.getElementById('investor-bank-holder-val').textContent = safe(regObj.bank_account_holder_name);
            document.getElementById('investor-bank-ac-val').textContent = safe(regObj.bank_account_number);
            document.getElementById('investor-ifsc-val').textContent = safe(regObj.ifsc_code);
            document.getElementById('investor-bank-name-val').textContent = (safe(regObj.bank_name) + ' / ' + safe(regObj.bank_branch)).replace(/^ \/ /,'');
            document.getElementById('investor-income-val').textContent = safe(regObj.income_range);
            document.getElementById('investor-occ-val').textContent = safe(regObj.occupation);
            document.getElementById('investor-risk-val').textContent = safe(regObj.risk_profile);
            document.getElementById('investor-exp-val').textContent = safe(regObj.investment_experience);

            // quick amount placeholder (if you have balance or similar you can fill)
            const quick = document.getElementById('investor-quick-amount');
            if (quick) quick.textContent = regObj.wallet_balance ? ('₹' + regObj.wallet_balance) : '';

            // flags
            const flagsDiv = document.getElementById('investor-status-flags');
            flagsDiv.innerHTML = '';
            const kyc = safe(regObj.kyc_status);
            const account = safe(regObj.account_status);
            const email = safe(regObj.is_email_verified);
            const phone = safe(regObj.is_phone_verified);

            function makeFlag(text, ok) {
                const s = document.createElement('span');
                s.textContent = text;
                s.style.marginRight = '6px';
                s.style.padding = '6px 10px';
                s.style.borderRadius = '999px';
                s.style.fontSize = '12px';
                s.style.fontWeight = '600';
                s.style.color = ok ? '#fff' : '#fff';
                s.style.background = ok ? '#16a34a' : '#dc2626';
                return s;
            }

            flagsDiv.appendChild(makeFlag('KYC: ' + (kyc || 'N/A'), String(kyc).toLowerCase() === 'verified'));
            flagsDiv.appendChild(makeFlag('Account: ' + (account || 'N/A'), String(account).toLowerCase() === 'active'));
            flagsDiv.appendChild(makeFlag('Email: ' + (email || 'N/A'), String(email).toLowerCase() === 'yes' || String(email).toLowerCase() === 'true'));
            flagsDiv.appendChild(makeFlag('Phone: ' + (phone || 'N/A'), String(phone).toLowerCase() === 'yes' || String(phone).toLowerCase() === 'true'));

            // profile image
            const img = document.getElementById('investor-profile-img');
            try {
                const profileUrl = (regObj.profile_image && regObj.profile_image.url) ? regObj.profile_image.url : '/mnt/data/5db75b29-5ec8-440f-b172-2f44fd5d1bfb.png';
                if (img) img.src = profileUrl;
            } catch (err) { /* ignore image errors */ }

            // store verification data on form
            const form = document.getElementById('investment-form');
            if (form) {
                form.dataset.investorKyc = kyc || '';
                form.dataset.investorAccount = account || '';
                form.dataset.investorEmail = email || '';
                form.dataset.investorPhone = phone || '';
            }
        }

        /* wiring select investor */
        const selectInvestorEl = document.getElementById('select_investor_id');
        if (selectInvestorEl) {
            selectInvestorEl.addEventListener('change', function (e) {
                const id = e.target.value;
                if (!id) { showInvestorCard(null); return; }
                const regObj = REGISTRATIONS.find(r => String(r.id) === String(id));
                showInvestorCard(regObj || null);
            });
            // initial show if value present
            if (selectInvestorEl.value) {
                const regObj = REGISTRATIONS.find(r => String(r.id) === String(selectInvestorEl.value));
                showInvestorCard(regObj || null);
            }
        } else {
            // if select isn't present (non-admin) but a selected_investor exists server-side
            @if(isset($selected_investor) && $selected_investor)
                showInvestorCard(@json($selected_investor));
            @endif
        }

        /* Plan checkbox logic (only one allowed, sets values) */
        const planCheckboxes = Array.from(document.querySelectorAll('.plan-checkbox'));
        planCheckboxes.forEach(cb => {
            cb.addEventListener('change', function () {
                try {
                    if (this.checked) {
                        // uncheck others
                        planCheckboxes.forEach(other => { if (other !== this) other.checked = false; });

                        // set this checkbox name so it posts
                        planCheckboxes.forEach(other => other.removeAttribute('name'));
                        this.setAttribute('name', 'select_plan_id');

                        // read associated plan data from ancestor .plan-card
                        const card = this.closest('.plan-card');
                        let plan = null;
                        if (card && card.dataset && card.dataset.plan) {
                            try { plan = JSON.parse(card.dataset.plan); } catch (err) { plan = null; }
                        }
                        if (plan) {
                            document.getElementById('secure_interest_percent').value = plan.secure_interest_percent ?? '';
                            document.getElementById('market_interest_percent').value = plan.market_interest_percent ?? '';
                            document.getElementById('total_interest_percent').value = plan.total_interest_percent ?? '';
                            // store min for validation
                            const principal = document.getElementById('principal_amount');
                            if (principal) principal.dataset.selectedPlanMin = plan.min_invest_amount ?? '';
                        } else {
                            // fallback - clear if no plan data
                            document.getElementById('secure_interest_percent').value = '';
                            document.getElementById('market_interest_percent').value = '';
                            document.getElementById('total_interest_percent').value = '';
                            const principal = document.getElementById('principal_amount');
                            if (principal) principal.removeAttribute('data-selected-plan-min');
                        }
                    } else {
                        // unchecked -> clear name and fields
                        this.removeAttribute('name');
                        document.getElementById('secure_interest_percent').value = '';
                        document.getElementById('market_interest_percent').value = '';
                        document.getElementById('total_interest_percent').value = '';
                        const principal = document.getElementById('principal_amount');
                        if (principal) principal.removeAttribute('data-selected-plan-min');
                    }
                } catch (err) {
                    console.error('Plan checkbox handler error', err);
                }
            });
        });

        /* Form submit validation */
        const investForm = document.getElementById('investment-form');
        if (investForm) {
            investForm.addEventListener('submit', function (e) {
                const form = e.target;
                const kyc = (form.dataset.investorKyc ?? '').toString().toLowerCase();
                const account = (form.dataset.investorAccount ?? '').toString().toLowerCase();
                const email = (form.dataset.investorEmail ?? '').toString().toLowerCase();
                const phone = (form.dataset.investorPhone ?? '').toString().toLowerCase();

                const kycOk = kyc === 'verified';
                const accountOk = account === 'active';
                const emailOk = email === 'yes' || email === 'true';
                const phoneOk = phone === 'yes' || phone === 'true';

                if (!kycOk || !accountOk || !emailOk || !phoneOk) {
                    e.preventDefault();
                    showTopPopup('Investment request cannot be processed because investor account does not meet required verifications. Required: KYC = Verified, Account = active, Email Verified = Yes, Phone Verified = Yes.', 'error');
                    return false;
                }

                const anyPlan = Array.from(document.querySelectorAll('.plan-checkbox')).some(cb => cb.checked);
                if (!anyPlan) {
                    e.preventDefault();
                    showTopPopup('कृपया एक Plan चुनें।', 'error');
                    return false;
                }

                const principalEl = document.getElementById('principal_amount');
                const principalVal = parseFloat(principalEl.value || 0);
                const minVal = parseFloat(principalEl.dataset.selectedPlanMin || 0);
                if (minVal && principalVal < minVal) {
                    e.preventDefault();
                    showTopPopup('Entered amount is less than the selected plan minimum amount (' + minVal + ').', 'error');
                    return false;
                }

                // allow submit
                return true;
            });
        }

        /* set start_date to today if empty (YYYY-MM-DD) */
        const today = new Date();
        const yyyy = today.getFullYear();
        const mm = String(today.getMonth() + 1).padStart(2, '0');
        const dd = String(today.getDate()).padStart(2, '0');
        const todayStr = yyyy + '-' + mm + '-' + dd;
        const startDateEl = document.getElementById('start_date');
        if (startDateEl && !startDateEl.value) startDateEl.value = todayStr;

    });
</script>

@endsection

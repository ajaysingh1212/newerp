@extends('layouts.admin')
@section('content')
<style>
<<<<<<< HEAD
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
=======
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

>>>>>>> cf226d133b58e81c5a92a53bff0ccbf670b3c6a2
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

                    <div id="investor-card" class="mt-4 hidden rounded-2xl overflow-hidden shadow-lg border">
                        <div class="bg-gradient-to-r from-white to-gray-50 p-5 flex gap-4 items-center">
                            <div class="flex-shrink-0">
                                <img id="investor-profile-img" src="/mnt/data/5db75b29-5ec8-440f-b172-2f44fd5d1bfb.png" class="w-28 h-28 rounded-full object-cover ring-4 ring-white shadow-md" alt="profile">
                            </div>
                            <div class="flex-1">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 id="investor-reg" class="text-xl font-semibold text-gray-800">Investor</h3>
                                        <p id="investor-role" class="text-sm text-gray-500 mt-1"></p>
                                    </div>
                                    <div id="investor-status-flags" class="space-x-2 text-sm"></div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm text-gray-700 mt-4">
                                    <div class="space-y-1">
                                        <p><span class="font-semibold">ID:</span> <span id="investor-id-val"></span></p>
                                        <p><span class="font-semibold">Reg:</span> <span id="investor-reg-val"></span></p>
                                        <p><span class="font-semibold">Referral Code:</span> <span id="investor-referral-val"></span></p>
                                        <p><span class="font-semibold">Aadhaar:</span> <span id="investor-aadhaar-val"></span></p>
                                        <p><span class="font-semibold">PAN:</span> <span id="investor-pan-val"></span></p>
                                        <p><span class="font-semibold">DOB:</span> <span id="investor-dob-val"></span></p>
                                        <p><span class="font-semibold">Gender:</span> <span id="investor-gender-val"></span></p>
                                        <p><span class="font-semibold">Father Name:</span> <span id="investor-father-val"></span></p>
                                    </div>
                                    <div class="space-y-1">
                                        <p><span class="font-semibold">Address 1:</span> <span id="investor-addr1-val"></span></p>
                                        <p><span class="font-semibold">Address 2:</span> <span id="investor-addr2-val"></span></p>
                                        <p><span class="font-semibold">Pincode:</span> <span id="investor-pincode-val"></span></p>
                                        <p><span class="font-semibold">City:</span> <span id="investor-city-val"></span></p>
                                        <p><span class="font-semibold">State:</span> <span id="investor-state-val"></span></p>
                                        <p><span class="font-semibold">Country:</span> <span id="investor-country-val"></span></p>
                                        <p><span class="font-semibold">Bank A/C Holder:</span> <span id="investor-bank-holder-val"></span></p>
                                        <p><span class="font-semibold">Bank A/C Number:</span> <span id="investor-bank-ac-val"></span></p>
                                        <p><span class="font-semibold">IFSC:</span> <span id="investor-ifsc-val"></span></p>
                                        <p><span class="font-semibold">Bank Name / Branch:</span> <span id="investor-bank-name-val"></span></p>
                                        <p><span class="font-semibold">Income Range:</span> <span id="investor-income-val"></span></p>
                                        <p><span class="font-semibold">Occupation:</span> <span id="investor-occ-val"></span></p>
                                        <p><span class="font-semibold">Risk Profile:</span> <span id="investor-risk-val"></span></p>
                                        <p><span class="font-semibold">Investment Experience:</span> <span id="investor-exp-val"></span></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="w-full md:w-1/2">
                    <label class="block text-sm font-semibold text-gray-700 mb-3">{{ trans('cruds.investment.fields.select_plan') }} (choose one)</label>
                    <div class="plan-area bg-white border rounded-2xl p-4 shadow-sm">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($plans as $plan)
                            
                               <div class=" main-div plan-card relative w-full bg-white shadow-md border p-0 overflow-hidden">

    <!-- Top Number Box (01 Style) -->
    <div class="absolute top-0 left-0  text-black px-4 py-1 rounded-br-xl">
        <span class="font-bold text-lg">ID: {{ $plan->id }}</span>
    </div>

    <!-- Blue Header -->
    <div class="bg-blue-500 text-white text-center py-1 mt-9">
        <h4 class="text-xl  text-white font-semibold ">
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
            <input type="checkbox" name="select_plan_id" value="{{ $plan->id }}"
                class="plan-checkbox h-5 w-5 accent-blue-600">
        </div>

        <button>View T&C</button> <br>
        <button>View Policy</button> <br>
        <button>View Details</button>
        <div class="bottom-line"></div>

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
                    <input type="text" name="secure_interest_percent" id="secure_interest_percent" value="{{ old('secure_interest_percent') }}" class="w-full rounded-lg border-gray-300 shadow-sm px-3 py-2 text-sm">
                </div>

                <div class="bg-yellow-50 p-4 rounded-lg">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">{{ trans('cruds.investment.fields.market_interest_percent') }}</label>
                    <input type="text" name="market_interest_percent" id="market_interest_percent" value="{{ old('market_interest_percent') }}" class="w-full rounded-lg border-gray-300 shadow-sm px-3 py-2 text-sm">
                </div>

                <div class="bg-yellow-50 p-4 rounded-lg">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">{{ trans('cruds.investment.fields.total_interest_percent') }}</label>
                    <input type="text" name="total_interest_percent" id="total_interest_percent" value="{{ old('total_interest_percent') }}" class="w-full rounded-lg border-gray-300 shadow-sm px-3 py-2 text-sm">
                </div>

                <div class="bg-purple-50 p-4 rounded-lg">
                    <label class="block text-sm font-semibold text-gray-700 mb-1 required">{{ trans('cruds.investment.fields.start_date') }}</label>
                    <input type="text" name="start_date" id="start_date" value="{{ date('d-m-Y') }}" class="w-full rounded-lg border-gray-300 shadow-sm px-3 py-2 text-sm" readonly>
                </div>
            </div>

        </div>

        <div class="mt-8 text-right">
            <button id="submit-btn" type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg shadow hover:bg-indigo-700 transition">{{ trans('global.save') }}</button>
        </div>
    </form>
</div>
```

</div>

<script>
    const REGISTRATIONS = @json($registrations ?? collect());
    document.addEventListener('DOMContentLoaded', function () {
        function showTopPopup(message, type = 'info') {
            const popup = document.getElementById('top-popup');
            const body = document.getElementById('top-popup-body');

            if (type === 'error') {
                body.className = 'rounded-lg px-5 py-3 text-sm font-medium shadow-lg bg-red-600 text-white';
            } else if (type === 'success') {
                body.className = 'rounded-lg px-5 py-3 text-sm font-medium shadow-lg bg-green-600 text-white';
            } else {
                body.className = 'rounded-lg px-5 py-3 text-sm font-medium shadow-lg bg-indigo-600 text-white';
            }

            body.textContent = message;

            popup.classList.add("show");
            popup.classList.remove("hidden");

            setTimeout(() => {
                popup.classList.remove("show");
                setTimeout(() => popup.classList.add("hidden"), 400);
            }, 3500);
        }

<<<<<<< HEAD
=======

        function flagElement(text, ok) {
            const el = document.createElement('span');
            el.textContent = text;
            el.className = 'px-3 py-1 rounded text-white text-xs ' + (ok ? 'bg-green-600' : 'bg-red-600');
            return el;
        }

>>>>>>> cf226d133b58e81c5a92a53bff0ccbf670b3c6a2
        function showInvestorCard(regObj) {
            if (!regObj) { document.getElementById('investor-card').classList.add('hidden'); return; }
            document.getElementById('investor-card').classList.remove('hidden');
            document.getElementById('investor-reg').textContent = regObj.reg || 'Investor';
            document.getElementById('investor-reg-val').textContent = regObj.reg ?? '';
            document.getElementById('investor-id-val').textContent = regObj.id ?? '';
            document.getElementById('investor-referral-val').textContent = regObj.referral_code ?? '';
            document.getElementById('investor-aadhaar-val').textContent = regObj.aadhaar_number ?? '';
            document.getElementById('investor-pan-val').textContent = regObj.pan_number ?? '';
            document.getElementById('investor-dob-val').textContent = regObj.dob ?? '';
            document.getElementById('investor-gender-val').textContent = regObj.gender ?? '';
            document.getElementById('investor-father-val').textContent = regObj.father_name ?? '';
            document.getElementById('investor-addr1-val').textContent = regObj.address_line_1 ?? '';
            document.getElementById('investor-addr2-val').textContent = regObj.address_line_2 ?? '';
            document.getElementById('investor-pincode-val').textContent = regObj.pincode ?? '';
            document.getElementById('investor-city-val').textContent = regObj.city ?? '';
            document.getElementById('investor-state-val').textContent = regObj.state ?? '';
            document.getElementById('investor-country-val').textContent = regObj.country ?? '';
            document.getElementById('investor-bank-holder-val').textContent = regObj.bank_account_holder_name ?? '';
            document.getElementById('investor-bank-ac-val').textContent = regObj.bank_account_number ?? '';
            document.getElementById('investor-ifsc-val').textContent = regObj.ifsc_code ?? '';
            document.getElementById('investor-bank-name-val').textContent = (regObj.bank_name ?? '') + ' / ' + (regObj.bank_branch ?? '');
            document.getElementById('investor-income-val').textContent = regObj.income_range ?? '';
            document.getElementById('investor-occ-val').textContent = regObj.occupation ?? '';
            document.getElementById('investor-risk-val').textContent = regObj.risk_profile ?? '';
            document.getElementById('investor-exp-val').textContent = regObj.investment_experience ?? '';

            const flagsDiv = document.getElementById('investor-status-flags');
            flagsDiv.innerHTML = '';
            const kyc = regObj.kyc_status ?? '';
            const account = regObj.account_status ?? '';
            const email = regObj.is_email_verified ?? '';
            const phone = regObj.is_phone_verified ?? '';
            function flag(text, ok) {
                const span = document.createElement('span');
                span.className = 'px-3 py-1 rounded text-white text-xs ' + (ok ? 'bg-green-600' : 'bg-red-600');
                span.textContent = text;
                return span;
            }
            flagsDiv.appendChild(flag('KYC: ' + kyc, String(kyc).toLowerCase() === 'verified'));
            flagsDiv.appendChild(flag('Account: ' + account, String(account).toLowerCase() === 'active'));
            flagsDiv.appendChild(flag('Email: ' + email, String(email).toLowerCase() === 'yes'));
            flagsDiv.appendChild(flag('Phone: ' + phone, String(phone).toLowerCase() === 'yes'));

            const img = document.getElementById('investor-profile-img');
            const profileUrl = regObj.profile_image && regObj.profile_image.url ? regObj.profile_image.url : '/mnt/data/5db75b29-5ec8-440f-b172-2f44fd5d1bfb.png';
            img.src = profileUrl;

            const form = document.getElementById('investment-form');
            form.dataset.investorKyc = kyc;
            form.dataset.investorAccount = account;
            form.dataset.investorEmail = email;
            form.dataset.investorPhone = phone;
        }

        const selectInvestorEl = document.getElementById('select_investor_id');
        if (selectInvestorEl) {
            selectInvestorEl.addEventListener('change', function (e) {
                const id = e.target.value;
                if (!id) { showInvestorCard(null); return; }
                const regObj = REGISTRATIONS.find(r => String(r.id) === String(id));
                showInvestorCard(regObj || null);
            });
            if (selectInvestorEl.value) {
                const regObj = REGISTRATIONS.find(r => String(r.id) === String(selectInvestorEl.value));
                showInvestorCard(regObj || null);
            }
        } else {
            @if(isset($selected_investor) && $selected_investor)
                showInvestorCard(@json($selected_investor));
            @endif
        }

        const planCheckboxes = document.querySelectorAll('.plan-checkbox');
        planCheckboxes.forEach(cb => {
            cb.addEventListener('change', function (e) {
                if (this.checked) {
                    planCheckboxes.forEach(other => { if (other !== this) other.checked = false; });
                    const card = this.closest('.plan-card');
                    const plan = JSON.parse(card.getAttribute('data-plan'));
                    document.getElementById('secure_interest_percent').value = plan.secure_interest_percent ?? '';
                    document.getElementById('market_interest_percent').value = plan.market_interest_percent ?? '';
                    document.getElementById('total_interest_percent').value = plan.total_interest_percent ?? '';
                    planCheckboxes.forEach(other => other.removeAttribute('name'));
                    this.setAttribute('name', 'select_plan_id');
                    document.getElementById('principal_amount').dataset.selectedPlanMin = plan.min_invest_amount ?? '';
                } else {
                    document.getElementById('secure_interest_percent').value = '';
                    document.getElementById('market_interest_percent').value = '';
                    document.getElementById('total_interest_percent').value = '';
                    this.removeAttribute('name');
                    document.getElementById('principal_amount').removeAttribute('data-selected-plan-min');
                }
            });
        });

        document.getElementById('investment-form').addEventListener('submit', function (e) {
            const form = e.target;
            const kyc = (form.dataset.investorKyc ?? '').toLowerCase();
            const account = (form.dataset.investorAccount ?? '').toLowerCase();
            const email = (form.dataset.investorEmail ?? '').toLowerCase();
            const phone = (form.dataset.investorPhone ?? '').toLowerCase();
            const kycOk = kyc === 'verified';
            const accountOk = account === 'active';
            const emailOk = email === 'yes';
            const phoneOk = phone === 'yes';
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

            return true;
        });

        const today = new Date();
        const yyyy = today.getFullYear();
        const mm = String(today.getMonth() + 1).padStart(2, '0');
        const dd = String(today.getDate()).padStart(2, '0');
        const todayStr = yyyy + '-' + mm + '-' + dd;
        const startDateEl = document.getElementById('start_date');
        if (startDateEl) startDateEl.value = startDateEl.value || todayStr;
    });
</script>

<style>
    .plan-card {
        background: linear-gradient(180deg, #ffffff 0%, #f0fcff 100%);
        border-left: 6px solid #07a0b6;
    }
    .plan-card h4 { color: #0f6b74; }
    .plan-card p { color: #3b3b3b; }
    #investor-card { transition: box-shadow .25s ease; }
    #investor-card:hover { box-shadow: 0 12px 30px rgba(10, 50, 70, 0.12); }
    #top-popup { left: 50%; transform: translateX(-50%); }
</style>

@endsection

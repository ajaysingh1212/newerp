@extends('layouts.admin')
@section('content')

@php
    $user = Auth::user();
    $userRole = $user->roles->first()->title ?? null;
    // $investments, $isAdmin, $message are provided by controller
@endphp

<style>
/* (same styles as before — kept intact) */
.badge-status{
    padding:4px 8px;
    border-radius:6px;
    font-size:12px;
    font-weight:700;
    color:white;
}
.badge-approved{ background:#059669; }
.badge-pending{ background:#eab308; color:#000; }
.badge-rejected{ background:#dc2626; }

.badge{padding:4px 8px;border-radius:8px;font-size:12px;font-weight:600}
.badge-pending{background:#fbbf24;color:#000}
.badge-active{background:#4ade80;color:#064e3a}
.badge-completed{background:#60a5fa;color:#1e3a8a}
.badge-withdrawn{background:#f87171;color:#7f1d1d}
.badge-withdraw_requested{background:#facc15;color:#78350f}
.cardBox{background:white;border-radius:20px;padding:24px;box-shadow:0 4px 20px rgba(0,0,0,0.07)}
.cardTitle{font-size:18px;font-weight:700;margin-bottom:10px;color:#1f2937}
.valueBig{font-size:30px;font-weight:800;color:#111}
.table-fixed{table-layout:fixed;word-wrap:break-word}
.modalOverlay{position:fixed;inset:0;background:rgba(0,0,0,0.45);display:none;align-items:center;justify-content:center;z-index:9999}
.modalPanel{background:#fff;border-radius:12px;padding:18px;max-width:900px;width:95%;max-height:85vh;overflow:auto}
.smallMuted{font-size:13px;color:#6b7280}
.input-note{font-size:13px;color:#374151;margin-top:6px}
.error-text{color:#b91c1c;font-size:13px;margin-top:6px}
.gradient-card{background:linear-gradient(180deg, rgba(7,162,182,0.06), #fff);border-left:6px solid #07a0b6}
.total-card{background:linear-gradient(180deg,#fff7ed,#fffbf0);border-left:6px solid #f59e0b}
.approved-card{background:linear-gradient(180deg,#f0fdf4,#ffffff);border-left:6px solid #10b981}
@media (max-width:640px){
 .cardBox { padding: 16px }
 .valueBig { font-size: 22px }
}
.table td, .table th { padding: 8px; border-bottom: 1px solid #e5e7eb; }
.action-btn { padding:6px 10px; border-radius:6px; font-weight:600; cursor:pointer; border: none; }
.action-approve { background:#059669; color:#fff; }
.action-view { background:#0ea5e9; color:#fff; }
.action-download { background:#ef4444; color:#fff; }
.small-link { color:#0ea5e9; text-decoration:underline; cursor:pointer}

/* Professional message card */
.no-data-card {
  max-width: 800px;
  margin: 40px auto;
  background: #fff9ed;
  border-left: 6px solid #f59e0b;
  padding: 28px;
  border-radius: 12px;
  text-align: center;
  box-shadow: 0 6px 30px rgba(15, 23, 42, 0.06);
  color: #92400e;
  font-weight: 700;
  font-size: 18px;
}
</style>

<div class="max-w-7xl mx-auto py-8">

  {{-- Header / Select (Admin only) --}}
  <div class="cardBox mb-6" id="select-card">
    <h2 class="text-2xl font-bold text-indigo-600 mb-4"  >Investment Details</h2>

    {{-- If admin: show a single dropdown to select investment.
        If non-admin: this block will be hidden by JS (select not available) --}}
    @if($isAdmin)
      <label class="text-sm font-semibold mb-2 block">Select Investment</label>
      <select id="investment_id" class="w-full rounded-lg border-gray-300 shadow-sm px-3 py-2">
        <option value="">Select Investment</option>
        @foreach($investments as $inv)
          <option value="{{ $inv->id }}">#{{ $inv->id }} — {{ optional($inv->select_investor)->reg }} — ₹{{ number_format($inv->principal_amount,2) }}</option>
        @endforeach
      </select>
    @else
      {{-- For non-admin we still render a hidden select so JS can reuse code (but hide it) --}}
      <select id="investment_id" class="hidden">
        @foreach($investments as $inv)
          <option value="{{ $inv->id }}">#{{ $inv->id }}</option>
        @endforeach
      </select>
    @endif
  </div>

  {{-- If controller passed a message (no registration / no investments), show a professional message card --}}
  @if(!empty($message))
    <div class="no-data-card">
      {{ $message }}
    </div>
  @endif

  {{-- Result area: default hidden; shown when investment is loaded --}}
  <div id="result-area" class="{{ empty($message) ? 'hidden' : 'hidden' }}">
    {{-- (All the UI blocks remain the same as your existing Blade) --}}
    <div class="cardBox mb-6">
      <h3 class="cardTitle">Daily Interest Chart</h3>
      <canvas id="dailyInterestChart" height="160"></canvas>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
      <div class="cardBox gradient-card">
        <h3 class="cardTitle">Principal Details</h3>
        <div class="valueBig" id="principal_amount">₹0.00</div>
        <p class="text-sm text-gray-600 mt-2">Start Date: <span id="start_date">—</span></p>
        <p class="text-sm text-gray-600">Status: <span id="status">—</span></p>
      </div>

      <div class="cardBox">
        <h3 class="cardTitle">Interest Summary</h3>
        <p class="text-sm mb-1">Daily Interest: <b id="daily_interest">0.00</b></p>
        <p class="text-sm mb-1">Days Passed: <b id="days_passed">0</b></p>
        <p class="text-xl font-bold mt-2">Total Earned: ₹<span id="total_interest">0.00</span></p>
        <div class="mt-4 flex justify-between items-center">
          <button id="moreDetailsBtn" type="button" class="px-4 py-2 bg-gray-100 text-gray-800 rounded-lg shadow hover:bg-gray-200">
            <i class="fas fa-info-circle mr-2"></i>
          </button>
          <button id="withdrawBtn" type="button" class="px-4 py-2 bg-green-600 text-white rounded-lg shadow hover:bg-green-700">Withdraw</button>
        </div>
      </div>

      <div class="cardBox total-card">
        <h3 class="cardTitle">Principal + Interest</h3>
        <div class="valueBig" id="principal_plus_interest">₹0.00</div>
        <p class="input-note">Total (principal + earned interest)</p>
      </div>

      <div class="cardBox approved-card">
        <h3 class="cardTitle">Approved Withdrawals Summary</h3>
        <p class="text-sm">Total Approved: <b id="approved_total_amount">₹0.00</b></p>
        <p class="text-sm">Approved Interest: <b id="approved_interest_amount">₹0.00</b></p>
        <p class="text-sm">Approved Principal: <b id="approved_principal_amount">₹0.00</b></p>
        <p class="text-sm">Approved Total-type: <b id="approved_totaltype_amount">₹0.00</b></p>
        <hr class="my-2">
        <p class="text-sm">Final Interest Balance: <b id="final_interest_balance">₹0.00</b></p>
        <p class="text-sm">Final Principal Balance: <b id="final_principal_balance">₹0.00</b></p>
      </div>
    </div>

    <div class="cardBox mt-6">
      <h3 class="cardTitle">Investor Information</h3>
      <div class="flex gap-4 items-center">
        <img id="investor_img" class="w-20 h-20 rounded-full ring-2 ring-gray-300"
             src="/mnt/data/default.png" alt="profile">
        <div>
          <p><b>Reg:</b> <span id="investor_reg">—</span></p>
          <p><b>Name:</b> <span id="investor_name">—</span></p>
          <p><b>PAN:</b> <span id="investor_pan">—</span></p>
          <p><b>Aadhaar:</b> <span id="investor_aadhaar">—</span></p>
        </div>
      </div>
    </div>

    <div class="cardBox mt-6 bg-blue-50">
      <h3 class="cardTitle">Plan Details</h3>
      <p><b>Name:</b> <span id="plan_name">—</span></p>
      <p><b>Secure %:</b> <span id="plan_secure">—</span></p>
      <p><b>Market %:</b> <span id="plan_market">—</span></p>
      <p><b>Total %:</b> <span id="plan_total">—</span></p>
      <p><b>Payout Freq:</b> <span id="plan_payout">—</span></p>
      <p><b>Lock-in Days:</b> <span id="plan_lockin">—</span></p>
      <p><b>Lock-in End Date:</b> <span id="lockin_end">—</span></p>
    </div>

    <div class="cardBox mt-6">
      <h3 class="cardTitle">Monthly Payout Records</h3>
      <table class="table table-bordered w-full table-fixed">
        <thead>
          <tr><th>Month</th><th>Secure</th><th>Market</th><th>Total</th><th>Status</th></tr>
        </thead>
        <tbody id="payout_table"></tbody>
      </table>
    </div>

    <div class="cardBox mt-6">
      <div style="display: flex;">
        <div>
          <h3 class="cardTitle">Withdrawal Requests</h3>
        </div>
        <div >
          <a href="{{ route('admin.pending.report') }}" class="btn btn-outline-success">📑 Complete Pending / Active Report</a></a>
        </div>
      </div>
      <table class="table table-striped w-full table-fixed">
        <thead>
          <tr><th>Amount</th><th>Type</th><th>Status</th><th>Requested At</th><th>Action</th></tr>
        </thead>
        <tbody id="withdraw_table"></tbody>
      </table>
    </div>

    <div class="cardBox mt-6">
      <h3 class="cardTitle">Approved Withdrawals (Only Approved)</h3>
      <table class="table table-fixed w-full">
        <thead>
          <tr>
            <th>Amount</th>
            <th>Type</th>
            <th>Requested At</th>
            <th>Approved At</th>
            <th>Notes</th>
            <th>Remarks</th>
            <th>Attachment</th>
          </tr>
        </thead>

        <tbody id="approved_withdrawals_table"></tbody>
      </table>
    </div>

    <div class="flex justify-end mt-6">
      <a id="pdf_btn" target="_blank" class="px-4 py-2 bg-red-600 text-white rounded-lg shadow hover:bg-red-700">
        Download PDF
      </a>
    </div>

  </div> {{-- end result-area --}}
</div> {{-- end container --}}

{{-- DETAILS MODAL --}}
<div id="detailsModal" class="modalOverlay" aria-hidden="true">
  <div class="modalPanel">
    <div class="flex justify-between items-center mb-3">
      <h3 class="text-lg font-semibold">Daily Interest Details</h3>
      <button id="closeDetails" type="button" class="px-3 py-1 bg-gray-100 rounded">Close</button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
      <div class="p-3 border rounded">
        <div class="smallMuted">Days Passed</div>
        <div class="valueBig" id="detail_days">0</div>
      </div>
      <div class="p-3 border rounded">
        <div class="smallMuted">Total Earned</div>
        <div class="valueBig">₹<span id="detail_total_earned">0.00</span></div>
      </div>
      <div class="p-3 border rounded">
        <div class="smallMuted">Total Withdrawn</div>
        <div class="valueBig">₹<span id="detail_total_withdrawn">0.00</span></div>
      </div>
    </div>

    <div class="mb-4">
      <h4 class="font-semibold mb-2">Daily Interest (by date)</h4>
      <table class="table table-bordered w-full">
        <thead><tr><th>Date</th><th>Daily Interest</th></tr></thead>
        <tbody id="detail_daily_table"></tbody>
      </table>
    </div>

    <div class="mb-4">
      <h4 class="font-semibold mb-2">Withdrawal History</h4>
      <table class="table table-bordered w-full">
        <thead><tr><th>Date</th><th>Amount</th><th>Type</th><th>Status</th></tr></thead>
        <tbody id="detail_withdraw_table"></tbody>
      </table>
    </div>

  </div>
</div>

{{-- CREATE WITHDRAWAL MODAL --}}
<div id="withdrawModal" class="modalOverlay" aria-hidden="true">
  <div class="modalPanel" style="max-width:420px;">
    <h2 class="text-xl font-semibold mb-4">Create Withdrawal Request</h2>

    <p id="withdraw_disclaimer" class="text-sm text-gray-600 italic mb-4"></p>

    <form id="withdrawForm">
      <input type="hidden" id="w_investment_id">

      <label class="text-sm font-medium">Withdrawal Type</label>
      <select id="w_type" class="w-full rounded border-gray-300 mb-2">
        <option value="interest">Interest Only</option>
        <option value="principal">Principal Only</option>
        <option value="total">Total</option>
      </select>

      <label class="text-sm font-medium">Amount</label>
      <input id="w_amount" type="number" class="w-full rounded border mb-1" step="0.01" min="0">

      <div id="amount_note" class="input-note"></div>
      <div id="amount_error" class="error-text" style="display:none"></div>

      <label class="text-sm font-medium mt-3">Notes</label>
      <textarea id="w_notes" class="w-full rounded border mb-3"></textarea>

      <div class="flex items-center gap-2 mb-3">
        <input type="checkbox" id="confirm_checkbox" />
        <label for="confirm_checkbox" class="text-sm">
          I confirm the above request and accept processing time and terms.
        </label>
      </div>

      <div class="flex justify-end gap-3">
        <button type="button" id="closeModal" class="px-4 py-2 bg-gray-200 rounded">Cancel</button>
        <button id="submitWithdraw" type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded">
          Submit
        </button>
      </div>
    </form>

  </div>
</div>

{{-- ADMIN APPROVAL MODAL (kept same) --}}
<div id="approveModal" class="modalOverlay" aria-hidden="true">
  <div class="modalPanel" style="max-width:720px;">
    <div class="flex justify-between items-center mb-4 border-b pb-2">
      <h3 class="text-2xl font-bold text-indigo-700 flex items-center gap-2">
        <i class="fas fa-check-circle text-indigo-600"></i> Approve / Update Withdrawal
      </h3>
      <button id="closeApprove" type="button" class="px-3 py-1 bg-gray-200 hover:bg-gray-300 rounded shadow">
        Close
      </button>
    </div>

    <div id="approve_errors" style="display:none"
         class="error-text mb-2 bg-red-50 border border-red-300 p-3 rounded-lg"></div>

    <form id="approveForm">
      <input type="hidden" id="ap_withdrawal_id" name="withdrawal_id">

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

        <div class="gradient-card p-3 rounded-xl shadow-sm border">
          <label class="text-sm font-semibold text-gray-700">Status</label>
          <select id="ap_status" name="status"
                  class="w-full mt-1 rounded-lg p-2 bg-white border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-300">
            <option value="approved">Approved</option>
            <option value="rejected">Rejected</option>
            <option value="pending">Pending</option>
          </select>
        </div>

        <div class="gradient-card p-3 rounded-xl shadow-sm border">
          <label class="text-sm font-semibold text-gray-700">Approved At</label>
          <input id="ap_approved_at" name="approved_at" type="datetime-local"
                 class="w-full mt-1 rounded-lg p-2 bg-white border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-300"/>
        </div>

        <div class="gradient-card md:col-span-2 p-3 rounded-xl shadow-sm border">
          <label class="text-sm font-semibold">Notes</label>
          <textarea id="ap_notes" name="notes"
                    class="w-full mt-1 rounded-lg p-2 border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-300"
                    rows="2"></textarea>
        </div>

        <div class="gradient-card md:col-span-2 p-3 rounded-xl shadow-sm border">
          <label class="text-sm font-semibold">Remarks</label>
          <textarea id="ap_remarks" name="remarks"
                    class="w-full mt-1 rounded-lg p-2 border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-300"
                    rows="2"></textarea>
        </div>

        <div class="md:col-span-2">
          <label class="text-sm font-semibold text-gray-700">Attachment (PDF / JPG / PNG)</label>

          <div class="mt-2 p-4 border-2 border-dashed border-indigo-400 rounded-xl bg-indigo-50 cursor-pointer hover:bg-indigo-100 transition">
            <div class="flex items-center gap-3">
              <div class="bg-indigo-200 p-3 rounded-full">
                <i class="fas fa-upload text-indigo-700 text-xl"></i>
              </div>
              <div>
                <p class="font-semibold text-indigo-700">Upload File</p>
                <p class="text-sm text-gray-600">Choose attachment to upload for approval record.</p>
              </div>
            </div>

            <input id="ap_attachment" name="attachment" type="file"
                   class="mt-3 w-full text-sm text-gray-700 file:bg-indigo-600 file:text-white 
                          file:px-4 file:py-2 file:rounded-lg file:cursor-pointer"/>
          </div>

          <div id="ap_existing_attachment" class="mt-2 smallMuted text-gray-700"></div>
        </div>

      </div>

      <hr class="my-4">

      <div class="gradient-card p-4 rounded-2xl shadow border">
        <h4 class="text-lg font-bold text-indigo-700 mb-3 flex items-center gap-2">
          <i class="fas fa-user-shield text-indigo-600"></i> Investor / User Details
        </h4>

        <div id="ap_investor_info" class="space-y-1 text-gray-800 text-sm"></div>
      </div>

      <div class="flex justify-end gap-3 mt-5">
        <button type="button" id="ap_cancel"
                class="px-4 py-2 bg-gray-300 hover:bg-gray-400 rounded-lg shadow">
          Cancel
        </button>

        <button type="submit" id="ap_submit"
                class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg shadow-lg">
          Save Approval
        </button>
      </div>

    </form>

  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
/* Globals and endpoints */
const CSRF = '{{ csrf_token() }}';
const IS_ADMIN = {!! json_encode($isAdmin) !!};
const STORE_AJAX_URL = '/admin/withdrawal-requests/store-ajax';
const FETCH_DETAILS_URL = id => '/admin/investment-details/' + id;
const FETCH_DAILY_URL = id => '/admin/investment-details/daily-interest/' + id;
const PDF_URL = id => '/admin/investment-details/pdf/' + id;
const WITHDRAWAL_DETAILS_URL = id => '/admin/withdrawal-requests/' + id + '/details';
const WITHDRAWAL_APPROVE_URL = '/admin/withdrawal-requests/approve';

let chartInstance = null;
let lastLoadedInvestment = null;

const el = id => document.getElementById(id);

/* initial hide modals */
if (el('detailsModal')) el('detailsModal').style.display = 'none';
if (el('withdrawModal')) el('withdrawModal').style.display = 'none';
if (el('approveModal')) el('approveModal').style.display = 'none';

/* modal close handlers */
if (el('closeDetails')) el('closeDetails').addEventListener('click', () => { el('detailsModal').style.display = 'none'; });
if (el('closeModal')) el('closeModal').addEventListener('click', () => { el('withdrawModal').style.display = 'none'; });
if (el('closeApprove')) el('closeApprove').addEventListener('click', () => { el('approveModal').style.display = 'none'; });
if (el('ap_cancel')) el('ap_cancel').addEventListener('click', () => { el('approveModal').style.display = 'none'; });

/* investments array for JS (useful for auto-selection on non-admin) */
const INVESTMENTS = {!! json_encode($investments->map(function($i){
    return ['id'=>$i->id,'reg'=>optional($i->select_investor)->reg,'principal_amount'=>$i->principal_amount];
})) !!};

/* show/hide select for admin vs user and auto-load if non-admin */
document.addEventListener('DOMContentLoaded', function() {

  // If message present (no registration / no investments), hide result area and select
  const serverMessage = {!! json_encode($message ?? null) !!};
  if (serverMessage) {
    if (el('select-card')) el('select-card').style.display = 'none';
    if (el('result-area')) el('result-area').style.display = 'none';
    // message card already rendered by blade
    return;
  }

  // If admin: show select (already rendered). Attach change handler.
  if (IS_ADMIN) {
    if (el('investment_id')) {
      el('investment_id').addEventListener('change', function(){
        const id = this.value;
        if (!id) return;
        loadInvestment(id);
      });
    }
  } else {
    // Non-admin: hide select card visually (select exists but hidden in blade).
    if (el('select-card')) el('select-card').style.display = 'none';

    // Auto select first investment (if exists)
    if (INVESTMENTS && INVESTMENTS.length > 0) {
      const firstId = INVESTMENTS[0].id;
      // Add a tiny delay so UI components are ready
      setTimeout(() => {
        loadInvestment(firstId);
      }, 100);
    } else {
      // No investments (should normally be handled by $message), but safe-guard
      if (el('result-area')) el('result-area').style.display = 'none';
      const noDataHtml = `<div class="no-data-card">You have not made any investments yet.</div>`;
      document.querySelector('.max-w-7xl')?.insertAdjacentHTML('afterbegin', noDataHtml);
    }
  }
});

/* loadInvestment + render + loadDailyGraph (same logic as your previous code) */
function loadInvestment(id) {
  fetch(FETCH_DETAILS_URL(id))
    .then(checkFetch)
    .then(r => r.json())
    .then(data => {
      lastLoadedInvestment = data;
      renderData(data);
      loadDailyInterestData(id);
      loadDailyGraph(id);
    })
    .catch(e => {
      console.error('loadInvestment error', e);
      alert('Unable to load investment details.');
    });
}

/* renderData: update DOM with returned payload */
function renderData(data) {
  const inv = data.investment;

  if (el('result-area')) el('result-area').classList.remove('hidden');

  el('principal_amount').innerText = '₹' + Number(inv.principal_amount).toFixed(2);
  el('start_date').innerText = data.safeStartDate || inv.start_date || '—';
  el('status').innerHTML = `<span class="badge badge-${inv.status}">${inv.status || '—'}</span>`;
  el('daily_interest').innerText = Number(data.dailyInterest).toFixed(2);
  el('days_passed').innerText = data.daysPassed;
  el('total_interest').innerText = Number(data.totalEarnedInterest).toFixed(2);
  el('principal_plus_interest').innerText =
        '₹' + (Number(inv.principal_amount || 0) + Number(data.totalEarnedInterest || 0)).toFixed(2);

  el('investor_reg').innerText = inv.select_investor?.reg || '—';
  el('investor_name').innerText = inv.select_investor?.father_name || '—';
  el('investor_pan').innerText = inv.select_investor?.pan_number || '—';
  el('investor_aadhaar').innerText = inv.select_investor?.aadhaar_number || '—';
  el('investor_img').src = inv.select_investor?.profile_image?.url || '/mnt/data/default.png';

  const p = inv.select_plan;
  el('plan_name').innerText = p?.plan_name || '—';
  el('plan_secure').innerText = p?.secure_interest_percent || '—';
  el('plan_market').innerText = p?.market_interest_percent || '—';
  el('plan_total').innerText = p?.total_interest_percent || '—';
  el('plan_payout').innerText = p?.payout_frequency || '—';
  el('plan_lockin').innerText = p?.lockin_days || '—';
  el('lockin_end').innerText = inv.lockin_end_date || '—';

  // payout records
  let payoutHTML = '';
  (inv.investment_monthly_payout_records || []).forEach(x => {
    payoutHTML += `<tr>
      <td>${x.month_for}</td>
      <td>₹${Number(x.secure_interest_amount).toFixed(2)}</td>
      <td>₹${Number(x.market_interest_amount).toFixed(2)}</td>
      <td>₹${Number(x.total_payout_amount).toFixed(2)}</td>
      <td>${x.status}</td>
    </tr>`;
  });
  el('payout_table').innerHTML = payoutHTML;

  // withdrawals table
  let withdrawHTML = '';
  (inv.investment_withdrawal_requests || []).forEach(w => {

    let badgeClass =
      w.status === 'approved' ? 'badge-approved' :
      (w.status === 'pending' ? 'badge-pending' : 'badge-rejected');

    withdrawHTML += `<tr>
      <td>₹${Number(w.amount).toFixed(2)}</td>
      <td>${w.type}</td>
      <td><span class="badge-status ${badgeClass}">${w.status}</span></td>
      <td>${w.requested_at||''}</td>
      <td>
        <button class="action-btn action-view"
                onclick="openAdminView(${w.id})">
          View
        </button>

        ${ (IS_ADMIN && w.status !== 'approved')
            ? `<button class="action-btn action-approve"
                       onclick="openAdminApprove(${w.id})">
                  Approve
               </button>`
            : ''
         }
      </td>
    </tr>`;
  });
  el('withdraw_table').innerHTML = withdrawHTML;

  el('pdf_btn').href = PDF_URL(inv.id);

  if (el('moreDetailsBtn')) el('moreDetailsBtn').onclick = () => openDetailsModal(inv);
  if (el('withdrawBtn')) el('withdrawBtn').onclick = () => openWithdrawModal(inv, data);

  const s = data.approvedSummary || {
    total_approved:0, approved_interest:0, approved_principal:0, approved_totaltype:0, final_interest:0, final_principal:0, approved_withdrawals:[]
  };
  el('approved_total_amount').innerText = '₹' + Number(s.total_approved).toFixed(2);
  el('approved_interest_amount').innerText = '₹' + Number(s.approved_interest).toFixed(2);
  el('approved_principal_amount').innerText = '₹' + Number(s.approved_principal).toFixed(2);
  el('approved_totaltype_amount').innerText = '₹' + Number(s.approved_totaltype).toFixed(2);
  el('final_interest_balance').innerText = '₹' + Number(s.final_interest).toFixed(2);
  el('final_principal_balance').innerText = '₹' + Number(s.final_principal).toFixed(2);

  let apHTML = '';
  (s.approved_withdrawals || []).forEach(w => {

    let attachmentHTML = '—';

    if (w.media && w.media.length > 0) {
        attachmentHTML = w.media
          .map(m => `<a href="${m.url}" target="_blank">${m.file_name}</a>`)
          .join('<br>');
    }

    apHTML += `<tr>
      <td>₹${Number(w.amount).toFixed(2)}</td>
      <td>${w.type}</td>
      <td>${w.requested_at || ''}</td>
      <td>${w.approved_at || ''}</td>
      <td>${w.notes || ''}</td>
      <td>${w.remarks || ''}</td>
      <td>${attachmentHTML}</td>
    </tr>`;
  });

  el('approved_withdrawals_table').innerHTML = apHTML;
}

/* load daily interest rows for totals & counts */
function loadDailyInterestData(id) {
  fetch(FETCH_DAILY_URL(id))
    .then(checkFetch)
    .then(r => r.json())
    .then(rows => {
      let total = 0;
      rows.forEach(r => total += Number(r.daily_interest_amount || 0));
      if (el('total_interest')) el('total_interest').innerText = total.toFixed(2);
      if (el('days_passed')) el('days_passed').innerText = rows.length;

      const inv = lastLoadedInvestment.investment;
      if (el('principal_plus_interest')) el('principal_plus_interest').innerText =
        '₹' + (Number(inv.principal_amount) + Number(total)).toFixed(2);
    })
    .catch(e => {
      console.error('dailyInterest error', e);
    });
}

/* graph */
function loadDailyGraph(id) {
  fetch(FETCH_DAILY_URL(id))
    .then(checkFetch)
    .then(r => r.json())
    .then(graph => {
      const labels = graph.map(x => x.interest_date);
      const values = graph.map(x => Number(x.daily_interest_amount || 0));
      drawGraph(labels, values);
    })
    .catch(e => {
      console.error('graph error', e);
    });
}

function drawGraph(labels, values) {
  if (chartInstance) chartInstance.destroy();
  const ctx = document.getElementById('dailyInterestChart').getContext('2d');
  chartInstance = new Chart(ctx, {
    type: 'line',
    data: {
      labels: labels,
      datasets: [{
        label: "Daily Interest (₹)",
        data: values,
        borderWidth: 3,
        tension: .3,
        fill: true,
        backgroundColor: () => {
          const g = ctx.createLinearGradient(0,0,0,160);
          g.addColorStop(0,'rgba(14,165,233,0.25)');
          g.addColorStop(1,'rgba(14,165,233,0.02)');
          return g;
        },
        borderColor: '#0ea5e9'
      }]
    },
    options: {responsive:true,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true}}}
  });
}

/* DETAILS modal open */
function openDetailsModal(inv) {
  const id = inv.id;
  fetch(FETCH_DAILY_URL(id))
    .then(checkFetch)
    .then(r => r.json())
    .then(rows => {
      let dailyHTML = '';
      let total = 0;
      rows.forEach(row => {
        dailyHTML += `<tr><td>${row.interest_date}</td><td>₹${Number(row.daily_interest_amount || 0).toFixed(2)}</td></tr>`;
        total += Number(row.daily_interest_amount || 0);
      });
      el('detail_daily_table').innerHTML = dailyHTML;
      el('detail_total_earned').innerText = total.toFixed(2);
      el('detail_days').innerText = rows.length;

      let withdrawHTML = '';
      let withdrawn = 0;
      inv.investment_withdrawal_requests.forEach(w => {
        withdrawHTML += `<tr>
          <td>${w.requested_at||''}</td>
          <td>₹${Number(w.amount).toFixed(2)}</td>
          <td>${w.type}</td>
          <td>${w.status}</td>
        </tr>`;
        if (w.status==='approved') withdrawn += Number(w.amount);
      });
      el('detail_withdraw_table').innerHTML = withdrawHTML;
      el('detail_total_withdrawn').innerText = withdrawn.toFixed(2);

      el('detailsModal').style.display='flex';
    })
    .catch(e => {
      console.error('openDetailsModal error', e);
      alert('Unable to load details.');
    });
}

/* OPEN CREATE withdraw (existing logic) */
function openWithdrawModal(inv, data) {
  const today = new Date();
  const startDate = inv.start_date ? new Date(inv.start_date) : null;
  const planLockDays = Number(inv.select_plan?.lockin_days || 0);
  const lockinEnd = inv.lockin_end_date ? new Date(inv.lockin_end_date) : null;

  if (lockinEnd) {
    const diff = Math.ceil((lockinEnd - today) / 86400000);
    if (diff > 0) {
      alert("You can withdraw after " + diff + " days");
      return;
    }
  }

  if (startDate && planLockDays > 0) {
    const daysPassed = Math.floor((today - startDate) / 86400000);
    const remain = planLockDays - daysPassed;
    if (remain > 0) {
      alert("You can withdraw after " + remain + " more days (plan lock)");
      return;
    }
  }

  const id = inv.id;

  fetch(FETCH_DAILY_URL(id))
    .then(checkFetch)
    .then(r => r.json())
    .then(rows => {

      let earned = 0;
      rows.forEach(r => earned += Number(r.daily_interest_amount || 0));

      let approved = 0;
      let pending = 0;

      (inv.investment_withdrawal_requests || []).forEach(w => {
        if (w.status === 'approved') approved += Number(w.amount || 0);
        if (w.status === 'pending') pending += Number(w.amount || 0);
      });

      const maxInterest = Math.max(0, earned - approved - pending);
      const principal = Number(inv.principal_amount || 0);
      const maxPrincipal = principal;
      const maxTotal = principal + maxInterest;

      el('withdrawModal').style.display = 'flex';

      el('w_investment_id').value = id;
      el('w_amount').value = '';
      el('w_notes').value = '';
      el('confirm_checkbox').checked = false;

      const typeEl = el('w_type');
      const amountEl = el('w_amount');
      const amountNote = el('amount_note');
      const note = el('withdraw_disclaimer');
      const err = el('amount_error');

      function upd() {
        err.style.display = 'none';
        if (typeEl.value === 'interest') {
          amountEl.max = maxInterest;
          amountEl.value = maxInterest.toFixed(2);
          amountNote.innerText = "Max interest: ₹" + maxInterest.toFixed(2);
          note.innerText = "Interest-only withdrawals are processed within 24-48 hours.";
        }
        if (typeEl.value === 'principal') {
          amountEl.max = maxPrincipal;
          amountEl.value = maxPrincipal.toFixed(2);
          amountNote.innerText = "Max principal: ₹" + maxPrincipal.toFixed(2);
          note.innerText = "Principal withdrawals may take up to 14 days.";
        }
        if (typeEl.value === 'total') {
          amountEl.max = maxTotal;
          amountEl.value = maxTotal.toFixed(2);
          amountNote.innerText = "Max total: ₹" + maxTotal.toFixed(2);
          note.innerText = "Total withdrawals follow principal rules.";
        }
      }

      typeEl.onchange = upd;

      amountEl.oninput = () => {
        if (Number(amountEl.value) > Number(amountEl.max)) {
          err.innerText = "Amount exceeds limit";
          err.style.display='block';
        } else err.style.display='none';
      };

      upd();

      el('withdrawForm').onsubmit = e => {
        e.preventDefault();

        if (!el('confirm_checkbox').checked) {
          alert("Please confirm the request");
          return;
        }

        if (Number(amountEl.value) > Number(amountEl.max)) {
          alert("Amount exceeds limit");
          return;
        }

        const payload = {
          investment_id: id,
          amount: Number(amountEl.value),
          type: typeEl.value,
          notes: el('w_notes').value
        };

        fetch(STORE_AJAX_URL, {
          method:'POST',
          headers:{
            'Content-Type':'application/json',
            'Accept':'application/json',
            'X-CSRF-TOKEN':CSRF
          },
          body: JSON.stringify(payload)
        })
        .then(checkFetch)
        .then(r => r.json())
        .then(res => {
          alert(res.message || "Request submitted");
          el('withdrawModal').style.display='none';
          loadInvestment(id);
        })
        .catch(err => {
          console.error('withdraw submit error', err);
          alert('Unable to submit withdrawal request.');
        });
      };

    });
}

/* Admin: view basic withdraw details popup */
function openAdminView(withdrawId) {
  fetch(WITHDRAWAL_DETAILS_URL(withdrawId))
    .then(checkFetch)
    .then(r => r.json())
    .then(payload => {
      const w = payload.withdrawal || {};
      const reg = payload.registration || {};
      const user = payload.user || {};
      let info = `Amount: ₹${Number(w.amount || 0).toFixed(2)}\nType: ${w.type||''}\nStatus: ${w.status||''}\nRequested At: ${w.requested_at||''}\n\n`;
      info += `Investor Reg: ${reg.reg||''}\nAadhaar: ${reg.aadhaar_number||''}\nPAN: ${reg.pan_number||''}\nBank Holder: ${reg.bank_account_holder_name||''}\nBank A/C: ${reg.bank_account_number||''}\nIFSC: ${reg.ifsc_code||''}\n\nUser Name: ${user.name||''}`;
      alert(info);
    })
    .catch(e => {
      console.error('openAdminView error', e);
      alert('Unable to load withdrawal details.');
    });
}

/* Admin: open approval modal */
function openAdminApprove(withdrawId) {
  fetch(WITHDRAWAL_DETAILS_URL(withdrawId))
    .then(checkFetch)
    .then(r => r.json())
    .then(payload => {
      const w = payload.withdrawal || {};
      const reg = payload.registration || {};
      const user = payload.user || {};

      el('ap_withdrawal_id').value = w.id || '';
      el('ap_status').value = w.status || 'pending';

      if (w.approved_at) {
        const dt = new Date(w.approved_at);
        const tzOffset = dt.getTimezoneOffset() * 60000;
        const localISO = new Date(dt - tzOffset).toISOString().slice(0,16);
        el('ap_approved_at').value = localISO;
      } else {
        el('ap_approved_at').value = '';
      }

      el('ap_notes').value = w.notes || '';
      el('ap_remarks').value = w.remarks || '';
      el('ap_existing_attachment').innerHTML = '';

      if (w.media && w.media.length) {
        let links = '';
        w.media.forEach(m => {
          links += `<div><a href="${m.url}" target="_blank">${m.file_name}</a></div>`;
        });
        el('ap_existing_attachment').innerHTML = links;
      } else if (w.attachment_path) {
        el('ap_existing_attachment').innerHTML = `<div><a href="/storage/${w.attachment_path}" target="_blank">Existing attachment</a></div>`;
      }

      let infoHtml = '';
      infoHtml += `<div><b>Reg:</b> ${reg.reg||'—'}</div>`;
      infoHtml += `<div><b>Aadhaar:</b> ${reg.aadhaar_number||'—'}</div>`;
      infoHtml += `<div><b>PAN:</b> ${reg.pan_number||'—'}</div>`;
      infoHtml += `<div><b>Bank Holder:</b> ${reg.bank_account_holder_name||'—'}</div>`;
      infoHtml += `<div><b>Bank Account:</b> ${reg.bank_account_number||'—'}</div>`;
      infoHtml += `<div><b>IFSC:</b> ${reg.ifsc_code||'—'}</div>`;
      infoHtml += `<div><b>Bank Name:</b> ${reg.bank_name||'—'}</div>`;
      infoHtml += `<div><b>Bank Branch:</b> ${reg.bank_branch||'—'}</div>`;
      infoHtml += `<div><b>KYC:</b> ${reg.kyc_status||'—'}</div>`;
      infoHtml += `<div><b>Account Status:</b> ${reg.account_status||'—'}</div>`;
      infoHtml += `<div><b>Email Verified:</b> ${reg.is_email_verified||'—'}</div>`;
      infoHtml += `<div><b>Phone Verified:</b> ${reg.is_phone_verified||'—'}</div>`;
      infoHtml += `<div><b>User Name:</b> ${user.name||'—'}</div>`;

      el('ap_investor_info').innerHTML = infoHtml;

      el('approve_errors').style.display = 'none';
      el('approveModal').style.display = 'flex';
    })
    .catch(e => {
      console.error('openAdminApprove error', e);
      alert('Unable to load approval data.');
    });
}

/* handle approve form with attachment (FormData) */
if (el('approveForm')) {
  el('approveForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const form = new FormData();
    form.append('withdrawal_id', el('ap_withdrawal_id').value);
    form.append('status', el('ap_status').value);
    if (el('ap_approved_at').value) {
      form.append('approved_at', el('ap_approved_at').value);
    }
    form.append('notes', el('ap_notes').value);
    form.append('remarks', el('ap_remarks').value);

    const fileEl = el('ap_attachment');
    if (fileEl && fileEl.files && fileEl.files[0]) {
      form.append('attachment', fileEl.files[0]);
    }

    fetch(WITHDRAWAL_APPROVE_URL, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': CSRF,
        'Accept': 'application/json'
      },
      body: form
    })
    .then(res => {
      if (res.status === 422) return res.json().then(data => Promise.reject({validation: data}));
      if (!res.ok) return Promise.reject({status: res.status});
      return res.json();
    })
    .then(data => {
      alert(data.message || 'Withdrawal updated');
      el('approveModal').style.display = 'none';
      if (lastLoadedInvestment && lastLoadedInvestment.investment) {
        loadInvestment(lastLoadedInvestment.investment.id);
      } else {
        location.reload();
      }
    })
    .catch(err => {
      console.error('approve submit error', err);
      if (err.validation && err.validation.errors) {
        let messages = Object.values(err.validation.errors).flat().join('\n');
        el('approve_errors').innerText = messages;
        el('approve_errors').style.display = 'block';
      } else {
        el('approve_errors').innerText = 'Unable to update withdrawal. Try again.';
        el('approve_errors').style.display = 'block';
      }
    });
  });
}

/* basic fetch error handling helper */
function checkFetch(res) {
  if (res.status === 422) {
    return res.json().then(data => {
      alert(data.message || 'Invalid request.');
      throw 'stop';
    });
  }
  if (!res.ok) throw new Error('Server error ' + res.status);
  return res;
}
</script>

@endsection

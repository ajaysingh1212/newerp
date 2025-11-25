@extends('layouts.admin')
@section('content')

<style>
/* -- visual styles -- */
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
/* mobile tweaks */
@media (max-width:640px){
 .cardBox { padding: 16px }
 .valueBig { font-size: 22px }
}
</style>

<div class="max-w-7xl mx-auto py-8">

  <div class="cardBox mb-6">
    <h2 class="text-2xl font-bold text-indigo-600 mb-4">Investment Details</h2>

    <label class="text-sm font-semibold mb-2 block">Select Investment</label>
    <select id="investment_id" class="w-full rounded-lg border-gray-300 shadow-sm px-3 py-2">
      <option value="">Select Investment</option>
      @foreach($investments as $inv)
        <option value="{{ $inv->id }}">#{{ $inv->id }} — {{ $inv->select_investor->reg }} — ₹{{ $inv->principal_amount }}</option>
      @endforeach
    </select>
  </div>

  <div class="cardBox mb-6">
    <h3 class="cardTitle">Daily Interest Chart</h3>
    <canvas id="dailyInterestChart" height="160"></canvas>
  </div>

  <div id="result-area" class="hidden">

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

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
          <button id="moreDetailsBtn" type="button" class="px-4 py-2 bg-gray-100 text-gray-800 rounded-lg shadow hover:bg-gray-200">More details</button>
          <button id="withdrawBtn" type="button" class="px-4 py-2 bg-green-600 text-white rounded-lg shadow hover:bg-green-700">Withdraw</button>
        </div>
      </div>

      <div class="cardBox total-card">
        <h3 class="cardTitle">Principal + Interest</h3>
        <div class="valueBig" id="principal_plus_interest">₹0.00</div>
        <p class="input-note">Total (principal + earned interest)</p>
      </div>

    </div>

    <div class="cardBox mt-6">
      <h3 class="cardTitle">Investor Information</h3>
      <div class="flex gap-4 items-center">
        <img id="investor_img" class="w-20 h-20 rounded-full ring-2 ring-gray-300" src="/mnt/data/ad02fcc7-d5fe-4a68-a53e-ca612d124e4c.png" alt="profile">
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
      <h3 class="cardTitle">Withdrawal Requests</h3>
      <table class="table table-striped w-full table-fixed">
        <thead>
          <tr><th>Amount</th><th>Type</th><th>Status</th><th>Requested At</th></tr>
        </thead>
        <tbody id="withdraw_table"></tbody>
      </table>
    </div>

    <div class="flex justify-end mt-6">
      <a id="pdf_btn" target="_blank" class="px-4 py-2 bg-red-600 text-white rounded-lg shadow hover:bg-red-700">Download PDF</a>
    </div>

  </div>
</div>

<!-- DETAILS MODAL -->
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

<!-- WITHDRAW MODAL (centered) -->
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
      <div id="amount_error" class="error-text" style="display:none">Amount exceeds limit.</div>

      <label class="text-sm font-medium mt-3">Notes</label>
      <textarea id="w_notes" class="w-full rounded border mb-3"></textarea>

      <div class="flex items-center gap-2 mb-3">
        <input type="checkbox" id="confirm_checkbox" />
        <label for="confirm_checkbox" class="text-sm">I confirm the above request and accept processing time and terms.</label>
      </div>

      <div class="flex justify-end gap-3">
        <button type="button" id="closeModal" class="px-4 py-2 bg-gray-200 rounded">Cancel</button>
        <button id="submitWithdraw" type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded">Submit</button>
      </div>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
/*
  Updated JS:
   - Robust fetch error handling
   - Compute Total Earned from daily-interest rows (fixes "Total Earned = 0")
   - Debug logs for withdrawal payload and response
   - Centered modals and correct show/hide toggles
   - Validation + disclaimer + confirmation checkbox before submit
*/

const CSRF = '{{ csrf_token() }}';
const STORE_AJAX_URL = '/admin/withdrawal-requests/store-ajax'; // update if your route differs
const FETCH_DETAILS_URL = id => '/admin/investment-details/' + id;
const FETCH_DAILY_URL = id => '/admin/investment-details/daily-interest/' + id;
const PDF_URL = id => '/admin/investment-details/pdf/' + id;

let chartInstance = null;
let lastLoadedInvestment = null;

// safe-get helpers
const el = id => document.getElementById(id);

// initialize hidden
if (el('detailsModal')) el('detailsModal').style.display = 'none';
if (el('withdrawModal')) el('withdrawModal').style.display = 'none';

// close handlers
if (el('closeDetails')) el('closeDetails').addEventListener('click', () => { el('detailsModal').style.display = 'none'; });
if (el('closeModal')) el('closeModal').addEventListener('click', () => { el('withdrawModal').style.display = 'none'; });

// investment select
el('investment_id').addEventListener('change', function() {
  const id = this.value;
  if (!id) return;
  loadInvestment(id);
});

function loadInvestment(id) {
  // fetch main details
  fetch(FETCH_DETAILS_URL(id))
    .then(checkFetch)
    .then(r => r.json())
    .then(data => {
      console.debug('loadInvestment response:', data);
      lastLoadedInvestment = data;
      renderData(data);
      loadDailyInterestData(id);
      loadDailyGraph(id);
    })
    .catch(err => {
      console.error('loadInvestment error', err);
      alert('Unable to load investment details. See console for details.');
    });
}

function renderData(data) {
  if (!data || !data.investment) { console.warn('renderData: invalid data', data); return; }
  el('result-area').classList.remove('hidden');

  const inv = data.investment;
  el('principal_amount').innerText = '₹' + Number(inv.principal_amount || 0).toFixed(2);
  el('start_date').innerText = inv.start_date || '—';
  const status = inv.status || 'pending';
  el('status').innerHTML = `<span class="badge badge-${status}">${status}</span>`;

  // dailyInterest & daysPassed from server (fallbacks)
  el('daily_interest').innerText = Number(data.dailyInterest || 0).toFixed(2);
  el('days_passed').innerText = data.daysPassed || 0;

  // totalInterest will be computed in loadDailyInterestData (from daily rows)
  // set immediate total if available
  el('total_interest').innerText = Number(data.totalEarnedInterest || 0).toFixed(2);

  // principal + interest (updates again once daily rows fetched)
  const totalPreview = Number(inv.principal_amount || 0) + Number(data.totalEarnedInterest || 0);
  el('principal_plus_interest').innerText = '₹' + Number(totalPreview).toFixed(2);

  // investor
  el('investor_reg').innerText = inv.select_investor?.reg ?? '—';
  el('investor_name').innerText = inv.select_investor?.father_name ?? '—';
  el('investor_pan').innerText = inv.select_investor?.pan_number ?? '—';
  el('investor_aadhaar').innerText = inv.select_investor?.aadhaar_number ?? '—';
  el('investor_img').src = inv.select_investor?.profile_image?.url ?? '/mnt/data/ad02fcc7-d5fe-4a68-a53e-ca612d124e4c.png';

  // plan
  const p = inv.select_plan || {};
  el('plan_name').innerText = p.plan_name ?? '—';
  el('plan_secure').innerText = p.secure_interest_percent ?? '—';
  el('plan_market').innerText = p.market_interest_percent ?? '—';
  el('plan_total').innerText = p.total_interest_percent ?? '—';
  el('plan_payout').innerText = p.payout_frequency ?? '—';

  // monthly payouts
  let payoutHTML = '';
  (inv.investment_monthly_payout_records || []).forEach(x => {
    payoutHTML += `<tr>
      <td>${x.month_for}</td>
      <td>₹${Number(x.secure_interest_amount || 0).toFixed(2)}</td>
      <td>₹${Number(x.market_interest_amount || 0).toFixed(2)}</td>
      <td>₹${Number(x.total_payout_amount || 0).toFixed(2)}</td>
      <td>${x.status}</td>
    </tr>`;
  });
  el('payout_table').innerHTML = payoutHTML;

  // withdrawals table
  let withdrawHTML = '';
  (inv.investment_withdrawal_requests || []).forEach(w => {
    withdrawHTML += `<tr>
      <td>₹${Number(w.amount || 0).toFixed(2)}</td>
      <td>${w.type}</td>
      <td>${w.status}</td>
      <td>${w.requested_at ?? ''}</td>
    </tr>`;
  });
  el('withdraw_table').innerHTML = withdrawHTML;

  // PDF link
  el('pdf_btn').href = PDF_URL(inv.id);

  // wire buttons
  el('moreDetailsBtn').onclick = () => openDetailsModal(inv);
  el('withdrawBtn').onclick = () => openWithdrawModal(inv, data);
}

// fetch daily rows, compute totals and days properly
function loadDailyInterestData(id) {
  fetch(FETCH_DAILY_URL(id))
    .then(checkFetch)
    .then(r => r.json())
    .then(rows => {
      console.debug('dailyInterest rows', rows);
      let total = 0;
      rows.forEach(r => total += Number(r.daily_interest_amount || 0));
      el('total_interest').innerText = Number(total).toFixed(2);
      el('days_passed').innerText = rows.length;
      // update principal+interest
      const inv = lastLoadedInvestment?.investment;
      if (inv) {
        const totalAll = Number(inv.principal_amount || 0) + Number(total);
        el('principal_plus_interest').innerText = '₹' + totalAll.toFixed(2);
      }
    })
    .catch(e => {
      console.error('dailyInterest fetch error', e);
      // keep previous totals if any
    });
}

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
      console.error('graph fetch error', e);
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
        tension: 0.3,
        fill: true,
        backgroundColor: function(context) {
          const g = ctx.createLinearGradient(0,0,0,160);
          g.addColorStop(0, 'rgba(14,165,233,0.25)');
          g.addColorStop(1, 'rgba(14,165,233,0.02)');
          return g;
        },
        borderColor: '#0ea5e9',
        pointRadius: 3,
      }]
    },
    options: {
      responsive: true,
      plugins: { legend: { display: false } },
      scales: { y: { beginAtZero: true, ticks: { callback: v => '₹' + v } } }
    }
  });
}

function openDetailsModal(inv) {
  const id = inv.id;
  fetch(FETCH_DAILY_URL(id))
    .then(checkFetch)
    .then(r => r.json())
    .then(rows => {
      let dailyHTML = '', total = 0;
      rows.forEach(row => {
        dailyHTML += `<tr><td>${row.interest_date}</td><td>₹${Number(row.daily_interest_amount || 0).toFixed(2)}</td></tr>`;
        total += Number(row.daily_interest_amount || 0);
      });
      el('detail_daily_table').innerHTML = dailyHTML;
      el('detail_total_earned').innerText = total.toFixed(2);
      el('detail_days').innerText = rows.length;

      // withdrawals
      let withdrawHTML = '', totalWithdrawn = 0;
      (inv.investment_withdrawal_requests || []).forEach(w => {
        withdrawHTML += `<tr>
          <td>${w.requested_at ?? ''}</td>
          <td>₹${Number(w.amount || 0).toFixed(2)}</td>
          <td>${w.type}</td>
          <td>${w.status}</td>
        </tr>`;
        if (w.status === 'approved') totalWithdrawn += Number(w.amount || 0);
      });
      el('detail_withdraw_table').innerHTML = withdrawHTML;
      el('detail_total_withdrawn').innerText = totalWithdrawn.toFixed(2);

      el('detailsModal').style.display = 'flex';
    })
    .catch(e => { console.error('openDetailsModal error', e); alert('Unable to load daily interest details'); });
}

function openWithdrawModal(inv, data) {
  const id = inv.id;
  fetch(FETCH_DAILY_URL(id))
    .then(checkFetch)
    .then(r => r.json())
    .then(rows => {
      // compute earned/pending/withdrawn
      let earned = 0;
      rows.forEach(r => earned += Number(r.daily_interest_amount || 0));
      let withdrawn = 0;
      (inv.investment_withdrawal_requests || []).forEach(w => { if (w.status === 'approved') withdrawn += Number(w.amount || 0); });
      let pending = 0;
      (inv.investment_withdrawal_requests || []).forEach(w => { if (w.status === 'pending') pending += Number(w.amount || 0); });
      const maxEligibleInterest = Math.max(0, earned - withdrawn - pending);
      const principal = Number(inv.principal_amount || 0);
      const maxPrincipal = principal;
      const maxTotal = Math.max(0, principal + earned - withdrawn - pending);

      // reset fields
      el('w_investment_id').value = id;
      el('w_amount').value = '';
      el('w_notes').value = '';
      el('amount_error').style.display = 'none';
      el('confirm_checkbox').checked = false;

      // disclaimers
      const disclaimerInterest = 'Interest-only withdrawals are processed within 24-48 hours.';
      const disclaimerPrincipal = 'Principal withdrawals may take up to 14 days for settlement.';
      const disclaimerTotal = 'Total (principal + interest) withdrawals follow principal rules and may take up to 14 days.';

      const typeEl = el('w_type');
      const amountNote = el('amount_note');
      const amountEl = el('w_amount');
      const amountError = el('amount_error');

      function updateForType() {
        const t = typeEl.value;
        amountError.style.display = 'none';
        if (t === 'interest') {
          el('withdraw_disclaimer').innerText = disclaimerInterest;
          amountNote.innerText = 'Max interest available: ₹' + maxEligibleInterest.toFixed(2);
          amountEl.value = maxEligibleInterest.toFixed(2);
          amountEl.max = maxEligibleInterest;
          amountEl.removeAttribute('readonly');
        } else if (t === 'principal') {
          el('withdraw_disclaimer').innerText = disclaimerPrincipal;
          amountNote.innerText = 'Max principal available: ₹' + maxPrincipal.toFixed(2);
          amountEl.value = maxPrincipal.toFixed(2);
          amountEl.max = maxPrincipal;
          amountEl.removeAttribute('readonly');
        } else {
          el('withdraw_disclaimer').innerText = disclaimerTotal;
          amountNote.innerText = 'Max total available: ₹' + maxTotal.toFixed(2);
          amountEl.value = maxTotal.toFixed(2);
          amountEl.max = maxTotal;
          amountEl.removeAttribute('readonly');
        }
      }

      typeEl.onchange = updateForType;

      amountEl.oninput = function() {
        const max = Number(amountEl.max || 0);
        const val = Number(amountEl.value || 0);
        if (val > max) {
          amountError.innerText = 'Amount exceeds allowed maximum ₹' + max.toFixed(2);
          amountError.style.display = 'block';
        } else {
          amountError.style.display = 'none';
        }
      };

      updateForType();
      el('withdrawModal').style.display = 'flex';

      // submit handler (re-bind to ensure up-to-date values)
      el('withdrawForm').onsubmit = function(e) {
        e.preventDefault();
        const amount = Number(amountEl.value || 0);
        const t = typeEl.value;
        const max = Number(amountEl.max || 0);

        if (!el('confirm_checkbox').checked) {
          alert('Please confirm the request before submitting.');
          return;
        }
        if (amount <= 0 || amount > max) {
          alert('Invalid amount. Please enter an amount up to ₹' + max.toFixed(2));
          return;
        }
        if (!confirm('Are you sure you want to submit this withdrawal request?')) return;

        const payload = {
          investment_id: id,
          amount: amount,
          type: t,
          notes: el('w_notes').value || ''
        };

        console.info('Submitting withdrawal payload:', payload);

        fetch(STORE_AJAX_URL, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': CSRF,
            'Accept': 'application/json'
          },
          body: JSON.stringify(payload)
        })
        .then(async r => {
          // helpful debug: if not ok, try to read response text/json
          if (!r.ok) {
            const txt = await r.text();
            throw new Error('Server returned ' + r.status + ': ' + txt);
          }
          return r.json();
        })
        .then(resp => {
          console.debug('withdraw response', resp);
          alert(resp.message || 'Request submitted');
          el('withdrawModal').style.display = 'none';
          // reload to show latest tables / totals
          loadInvestment(id);
        })
        .catch(err => {
          console.error('withdraw submit error', err);
          // show helpful error to user
          alert('Unable to submit withdrawal request. See console for details.');
        });
      };
    })
    .catch(e => { console.error('openWithdrawModal error', e); alert('Unable to prepare withdrawal modal.'); });
}

/* helper: check fetch response and throw readable error */
function checkFetch(res) {
  if (!res.ok) {
    // try to include server body for debugging
    throw new Error('Network response not ok (' + res.status + ') — ' + res.url);
  }
  return res;
}
</script>

@endsection

@extends('layouts.admin')
@section('content')

<style>
.badge{padding:4px 8px;border-radius:8px;font-size:12px;font-weight:600}
.badge-pending{background:#fbbf24;color:#000}
.badge-active{background:#4ade80;color:#064e3b}
.badge-completed{background:#60a5fa;color:#1e3a8a}
.badge-withdrawn{background:#f87171;color:#7f1d1d}
.badge-withdraw_requested{background:#facc15;color:#78350f}
.cardBox{background:white;border-radius:20px;padding:24px;box-shadow:0 4px 20px rgba(0,0,0,0.07)}
.cardTitle{font-size:18px;font-weight:700;margin-bottom:10px;color:#1f2937}
.valueBig{font-size:30px;font-weight:800;color:#111}
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
        <canvas id="dailyInterestChart" height="120"></canvas>
    </div>

    <div id="result-area" class="hidden">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <div class="cardBox">
                <h3 class="cardTitle">Principal Details</h3>
                <div class="valueBig" id="principal_amount"></div>
                <p class="text-sm text-gray-600 mt-2">Start Date: <span id="start_date"></span></p>
                <p class="text-sm text-gray-600">Status: <span id="status"></span></p>
            </div>

            <div class="cardBox">
                <h3 class="cardTitle">Interest Summary</h3>
                <p class="text-sm mb-1">Daily Interest: <b id="daily_interest"></b></p>
                <p class="text-sm mb-1">Days Passed: <b id="days_passed"></b></p>
                <p class="text-xl font-bold mt-2">Total Earned: ₹<span id="total_interest"></span></p>

                <div class="mt-4 flex justify-end">
                    <button id="withdrawBtn" class="px-4 py-2 bg-green-600 text-white rounded-lg shadow hover:bg-green-700">
                        Withdraw
                    </button>
                </div>
            </div>

        </div>

        <div class="cardBox mt-6">
            <h3 class="cardTitle">Investor Information</h3>
            <div class="flex gap-4">
                <img id="investor_img" class="w-20 h-20 rounded-full ring-2 ring-gray-300">
                <div>
                    <p><b>Reg:</b> <span id="investor_reg"></span></p>
                    <p><b>Name:</b> <span id="investor_name"></span></p>
                    <p><b>PAN:</b> <span id="investor_pan"></span></p>
                    <p><b>Aadhaar:</b> <span id="investor_aadhaar"></span></p>
                </div>
            </div>
        </div>

        <div class="cardBox mt-6 bg-blue-50">
            <h3 class="cardTitle">Plan Details</h3>
            <p><b>Name:</b> <span id="plan_name"></span></p>
            <p><b>Secure %:</b> <span id="plan_secure"></span></p>
            <p><b>Market %:</b> <span id="plan_market"></span></p>
            <p><b>Total %:</b> <span id="plan_total"></span></p>
            <p><b>Payout Freq:</b> <span id="plan_payout"></span></p>
        </div>

        <div class="cardBox mt-6">
            <h3 class="cardTitle">Monthly Payout Records</h3>
            <table class="table table-bordered w-full">
                <thead>
                    <tr>
                        <th>Month</th>
                        <th>Secure</th>
                        <th>Market</th>
                        <th>Total</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody id="payout_table"></tbody>
            </table>
        </div>

        <div class="cardBox mt-6">
            <h3 class="cardTitle">Withdrawal Requests</h3>
            <table class="table table-striped w-full">
                <thead>
                    <tr>
                        <th>Amount</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Requested At</th>
                    </tr>
                </thead>
                <tbody id="withdraw_table"></tbody>
            </table>
        </div>

        <div class="flex justify-end mt-6">
            <a id="pdf_btn" target="_blank"
            class="px-4 py-2 bg-red-600 text-white rounded-lg shadow hover:bg-red-700">
            Download PDF
            </a>
        </div>

    </div>
</div>


<div id="withdrawModal" class="fixed inset-0 bg-black bg-opacity-40 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl p-6 w-full max-w-md shadow-xl">
        <h2 class="text-xl font-semibold mb-4">Create Withdrawal Request</h2>

        <form id="withdrawForm">
            <input type="hidden" id="w_investment_id">

            <label>Type</label>
            <select id="w_type" class="w-full rounded border-gray-300 mb-3">
                <option value="interest">Interest Only</option>
                <option value="principal">Principal Only</option>
                <option value="total">Total</option>
            </select>

            <label>Amount</label>
            <input id="w_amount" type="number" class="w-full rounded border mb-3">

            <p class="text-xs text-gray-500 mb-3">Max Allowed: <span id="w_max" class="font-semibold text-indigo-600"></span></p>

            <label>Notes</label>
            <textarea id="w_notes" class="w-full rounded border mb-4"></textarea>

            <div class="flex justify-end gap-3">
                <button type="button" id="closeModal" class="px-4 py-2 bg-gray-200 rounded">Cancel</button>
                <button class="px-4 py-2 bg-indigo-600 text-white rounded">Submit</button>
            </div>
        </form>

    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
let chartInstance=null;

document.getElementById('investment_id').addEventListener('change',function(){
    let id=this.value;
    if(!id)return;

    fetch("/admin/investment-details/"+id)
    .then(res=>res.json())
    .then(data=>{
        document.getElementById("result-area").classList.remove("hidden");

        let inv=data.investment;

        document.getElementById("principal_amount").innerHTML="₹"+inv.principal_amount;
        document.getElementById("start_date").innerHTML=inv.start_date;

        let cls="badge-"+inv.status;
        document.getElementById("status").innerHTML='<span class="badge '+cls+'">'+inv.status+'</span>';

        document.getElementById("daily_interest").innerHTML=data.dailyInterest.toFixed(2);
        document.getElementById("days_passed").innerHTML=data.daysPassed;
        document.getElementById("total_interest").innerHTML=data.totalEarnedInterest.toFixed(2);

        document.getElementById("investor_reg").innerHTML=inv.select_investor.reg;
        document.getElementById("investor_name").innerHTML=inv.select_investor.father_name;
        document.getElementById("investor_pan").innerHTML=inv.select_investor.pan_number;
        document.getElementById("investor_aadhaar").innerHTML=inv.select_investor.aadhaar_number;

        let img=inv.select_investor.profile_image?.url ?? "/default.jpg";
        document.getElementById("investor_img").src=img;

        let plan=inv.select_plan;
        document.getElementById("plan_name").innerHTML=plan.plan_name;
        document.getElementById("plan_secure").innerHTML=plan.secure_interest_percent;
        document.getElementById("plan_market").innerHTML=plan.market_interest_percent;
        document.getElementById("plan_total").innerHTML=plan.total_interest_percent;
        document.getElementById("plan_payout").innerHTML=plan.payout_frequency;

        let pHTML="";
        inv.investment_monthly_payout_records.forEach(p=>{
            pHTML+=`<tr>
                <td>${p.month_for}</td>
                <td>${p.secure_interest_amount}</td>
                <td>${p.market_interest_amount}</td>
                <td>${p.total_payout_amount}</td>
                <td>${p.status}</td>
            </tr>`;
        });
        document.getElementById("payout_table").innerHTML=pHTML;

        let wHTML="";
        inv.investment_withdrawal_requests.forEach(w=>{
            wHTML+=`<tr>
                <td>${w.amount}</td>
                <td>${w.type}</td>
                <td>${w.status}</td>
                <td>${w.requested_at}</td>
            </tr>`;
        });
        document.getElementById("withdraw_table").innerHTML=wHTML;

        document.getElementById("pdf_btn").href="/investment-details/pdf/"+id;

        calcWithdraw(inv,data);

        fetch("/investment/daily-interest/"+id)
        .then(r=>r.json())
        .then(graph=>{
            let labels=graph.map(x=>x.interest_date);
            let values=graph.map(x=>x.daily_interest_amount);
            drawGraph(labels,values);
        });
    });
});

function drawGraph(labels,values){
    if(chartInstance){chartInstance.destroy();}
    const ctx=document.getElementById('dailyInterestChart').getContext('2d');
    chartInstance=new Chart(ctx,{
        type:'line',
        data:{
            labels:labels,
            datasets:[{
                label:"Daily Interest (₹)",
                data:values,
                borderWidth:3,
                tension:0.3
            }]
        }
    });
}

function calcWithdraw(inv,data){
    let earned=data.totalEarnedInterest;
    let withdrawn=0;
    inv.investment_withdrawal_requests.forEach(w=>{if(w.status==="approved"){withdrawn+=parseFloat(w.amount);}});
    let pending=0;
    inv.investment_withdrawal_requests.forEach(w=>{if(w.status==="pending"){pending+=parseFloat(w.amount);}});
    let max=earned-withdrawn-pending;
    if(max<0)max=0;

    document.getElementById("withdrawBtn").onclick=function(){
        document.getElementById("withdrawModal").classList.remove("hidden");
        document.getElementById("w_investment_id").value=inv.id;
        document.getElementById("w_max").innerHTML=max.toFixed(2);
    };

    document.getElementById("closeModal").onclick=function(){
        document.getElementById("withdrawModal").classList.add("hidden");
    };

    document.getElementById("withdrawForm").onsubmit=function(e){
        e.preventDefault();
        fetch("/withdrawal/create",{
            method:"POST",
            headers:{"Content-Type":"application/json","X-CSRF-TOKEN":"{{ csrf_token() }}"},
            body:JSON.stringify({
                investment_id:inv.id,
                amount:document.getElementById("w_amount").value,
                type:document.getElementById("w_type").value,
                notes:document.getElementById("w_notes").value
            })
        })
        .then(r=>r.json())
        .then(r=>{
            alert(r.message);
            document.getElementById("withdrawModal").classList.add("hidden");
            document.getElementById("investment_id").dispatchEvent(new Event('change'));
        });
    };
}
</script>

@endsection

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <title>Investment Report</title>

    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        .header { text-align: center; padding-bottom: 10px; border-bottom: 2px solid #444; }
        .title { font-size: 18px; font-weight: bold; margin-top: 10px; }
        .section { margin-top: 20px; }
        .section-title { background: #f0f0f0; padding: 6px; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #555; padding: 6px; }
        th { background: #e4e4e4; }
        .badge { padding: 3px 8px; border-radius: 5px; color: white; font-size: 11px; }
        .pending { background: orange; }
        .active { background: green; }
        .completed { background: dodgerblue; }
        .withdrawn { background: red; }
        .withdraw_requested { background: brown; }
    </style>
</head>
<body>

    <div class="header">
        <h2>📄 Investment Report</h2>
        <div class="title">Investment ID: {{ $investment->id }}</div>
    </div>

    <!-- Investment Summary -->
    <div class="section">
        <div class="section-title">Investment Summary</div>
        <table>
            <tr>
                <th>Principal Amount</th>
                <td>₹{{ $investment->principal_amount }}</td>
            </tr>
            <tr>
                <th>Status</th>
                <td>
                    <span class="badge {{ $investment->status }}">{{ ucfirst($investment->status) }}</span>
                </td>
            </tr>
            <tr>
                <th>Start Date</th>
                <td>{{ $investment->start_date }}</td>
            </tr>
        </table>
    </div>

    <!-- Investor -->
    <div class="section">
        <div class="section-title">Investor Details</div>
        <table>
            <tr><th>Reg</th><td>{{ $investment->select_investor->reg }}</td></tr>
            <tr><th>PAN</th><td>{{ $investment->select_investor->pan_number }}</td></tr>
            <tr><th>Aadhaar</th><td>{{ $investment->select_investor->aadhaar_number }}</td></tr>
            <tr><th>Father Name</th><td>{{ $investment->select_investor->father_name }}</td></tr>
        </table>
    </div>

    <!-- Plan -->
    <div class="section">
        <div class="section-title">Plan Details</div>
        <table>
            <tr><th>Plan Name</th><td>{{ $investment->select_plan->plan_name }}</td></tr>
            <tr><th>Secure %</th><td>{{ $investment->select_plan->secure_interest_percent }}</td></tr>
            <tr><th>Market %</th><td>{{ $investment->select_plan->market_interest_percent }}</td></tr>
            <tr><th>Total %</th><td>{{ $investment->select_plan->total_interest_percent }}</td></tr>
            <tr><th>Payout</th><td>{{ $investment->select_plan->payout_frequency }}</td></tr>
        </table>
    </div>

    <!-- Daily Interest -->
    <div class="section">
        <div class="section-title">Daily Interest Records</div>

        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Daily Interest</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dailyInterest as $d)
                <tr>
                    <td>{{ $d->interest_date }}</td>
                    <td>₹{{ number_format($d->daily_interest_amount,2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Monthly Payouts -->
    <div class="section">
        <div class="section-title">Monthly Payout Records</div>

        <table>
            <thead>
                <tr>
                    <th>Month</th>
                    <th>Secure</th>
                    <th>Market</th>
                    <th>Total</th>
                    <th>Status</th>
                </tr>
            </thead>

            <tbody>
            @foreach($investment->investmentMonthlyPayoutRecords as $p)
                <tr>
                    <td>{{ $p->month_for }}</td>
                    <td>{{ $p->secure_interest_amount }}</td>
                    <td>{{ $p->market_interest_amount }}</td>
                    <td>{{ $p->total_payout_amount }}</td>
                    <td>{{ ucfirst($p->status) }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

</body>
</html>

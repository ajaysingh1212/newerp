@php
    $role = $userRole ?? null;
@endphp
{{-- wwddsvvdc --}}

@extends('layouts.admin')
@section('content')
<!-- ================= New Investor Dashboard (PUT THIS AT TOP) ================= -->
<div class="row mb-4" id="investor-dashboard-card">
    <div class="col-12">
        <div class="card p-3 shadow-sm">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <h5 class="mb-1">Investor Summary</h5>
                    <small class="text-muted">Overview of investors, KYC & investments</small>
                </div>

                <div class="d-flex gap-2 align-items-center">
                    <select id="dash-filter" class="form-control form-select">
                        <option value="all">All Time</option>
                        <option value="today">Today</option>
                        <option value="yesterday">Yesterday</option>
                        <option value="this_week">This Week</option>
                        <option value="this_month">This Month</option>
                        <option value="last_3_month">Last 3 Months</option>
                        <option value="last_6_month">Last 6 Months</option>
                        <option value="last_9_month">Last 9 Months</option>
                        <option value="this_year">This Year</option>
                        <option value="custom">Custom Range</option>
                    </select>

                    <input type="date" id="dash-from" class="form-control" style="display:none;">
                    <input type="date" id="dash-to" class="form-control" style="display:none;">

                    <button id="dash-apply" class="btn btn-primary">Apply</button>
                    <button id="dash-reset" class="btn btn-outline-secondary">Reset</button>
                </div>
            </div>

            <div id="dash-cards" class="row gy-3">
                <!-- Cards will be injected/updated by JS -->
                <div class="col-md-3">
                    <div class="card text-white bg-success shadow-sm">
                        <div class="card-body text-center">
                            <div class="small">Verified Investors</div>
                            <h4 id="card-verified">0</h4>
                            <div class="small">KYC Verified</div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card text-white bg-warning shadow-sm">
                        <div class="card-body text-center">
                            <div class="small">Not Verified</div>
                            <h4 id="card-not-verified">0</h4>
                            <div class="small">Pending / Submitted</div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card text-white bg-primary shadow-sm">
                        <div class="card-body text-center">
                            <div class="small">Total Investment</div>
                            <h4 id="card-total-investment">₹0</h4>
                            <div class="small">Principal Sum</div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card text-white bg-info shadow-sm">
                        <div class="card-body text-center">
                            <div class="small">Withdrawals (Requested)</div>
                            <h4 id="card-withdraw-requested">₹0</h4>
                            <div class="small">Requested Amount</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4 align-items-center">
                <div class="col-md-6">
                    <canvas id="withdrawPieChart" height="220"></canvas>
                </div>

                <div class="col-md-6">
                    <div class="row">
                        <div class="col-6">
                            <div class="card border-secondary mb-2">
                                <div class="card-body text-center">
                                    <small>Approved</small>
                                    <h5 id="withdraw-approved">₹0</h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card border-secondary mb-2">
                                <div class="card-body text-center">
                                    <small>Pending</small>
                                    <h5 id="withdraw-pending">₹0</h5>
                                </div>
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="card border-secondary mb-2">
                                <div class="card-body text-center">
                                    <small>Rejected</small>
                                    <h5 id="withdraw-rejected">₹0</h5>
                                </div>
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="card border-secondary mb-2">
                                <div class="card-body text-center">
                                    <small>Total KYC</small>
                                    <h5 id="kyc-total">0</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- col-12 -->
</div>


<!-- ================= end of Investor Dashboard ================= -->

<div class="container">
    @if($role !== 'Customer')
    <div class="row mb-4">
        @foreach(['CNF', 'Dealer', 'Distributer', 'Customer'] as $type)
        <div class="col-md-3">
            <div class="card bg-{{ ['primary','success','warning','danger'][$loop->index] }} text-white">
                <div class="card-body">
                    <h5>Total {{ $type }}</h5>
                    <h3>{{ $totals[$type] ?? 0 }}</h3>
                </div>
            </div>
        </div>
        @endforeach
        </div>
        @if(auth()->id() == 1)
        <div class="row mt-4">
  
    @foreach(['Pending', 'Failed', 'Completed'] as $index => $status)
        <div class="col-md-3">
            <a href="{{ route('admin.kyc-recharges.index', ['status' => $status]) }}" style="text-decoration: none;">
                <div class="card bg-{{ ['info','danger','success'][$index] }} text-white text-center">
                    <div class="card-body">
                        <h5>Total {{ $status }} KYCs</h5>
                        <h3>{{ $totalsStatus[$status] ?? 0 }}</h3>
                    </div>
                </div>
            </a>
        </div>
    @endforeach

    {{-- Total KYC card --}}
    <div class="col-md-3">
        <a href="{{ route('admin.kyc-recharges.index', ['status' => 'Total']) }}" style="text-decoration: none;">
            <div class="card bg-primary text-white text-center">
                <div class="card-body">
                    <h5>Total KYCs</h5>
                    <h3>{{ array_sum($totalsStatus) }}</h3>
                </div>
            </div>
        </a>
    </div>

</div>

<div class="row mt-4">

    @foreach(['Pending', 'Failed', 'Success'] as $index => $status)
        <div class="col-md-3">
            <a href="{{ route('admin.recharge-requests.index', ['status' => $status]) }}" style="text-decoration: none;">
                <div class="card bg-{{ ['info','danger','success'][$index] }} text-white text-center">
                    <div class="card-body">
                        <h5>Total {{ $status }} Recharges</h5>
                        <h3>{{ $totalsStatusRecharge[$status] ?? 0 }}</h3>
                    </div>
                </div>
            </a>
        </div>
    @endforeach

    {{-- Total Recharges card --}}
    <div class="col-md-3">
        <a href="{{ route('admin.recharge-requests.index', ['status' => 'Total']) }}" style="text-decoration: none;">
            <div class="card bg-primary text-white text-center">
                <div class="card-body">
                    <h5>Total Recharges</h5>
                    <h3>{{ array_sum($totalsStatusRecharge) }}</h3>
                </div>
            </div>
        </a>
    </div>

</div>

<div class="row mt-4 d-flex justify-content-between flex-wrap">
    @php
        $cards = [
            ['status' => 'Pending', 'color' => 'warning'],
            ['status' => 'Processing', 'color' => 'info'],
            ['status' => 'Reject', 'color' => 'danger'],
            ['status' => 'Solved', 'color' => 'success'],
            ['status' => 'Total', 'color' => 'primary'],
        ];
    @endphp

    @foreach($cards as $card)
        <div class="col-md-2 col-sm-4 col-6 mb-3">
            <a href="{{ route('admin.check-complains.index', ['status' => $card['status']]) }}" style="text-decoration: none;">
                <div class="card bg-{{ $card['color'] }} text-white text-center shadow-sm">
                    <div class="card-body p-3">
                        <h6 class="mb-1">
                            Total {{ $card['status'] }} Complaints
                        </h6>
                        <h3 class="m-0">
                            {{ $card['status'] === 'Total' 
                                ? array_sum($totalsStatusComplain) 
                                : ($totalsStatusComplain[$card['status']] ?? 0) }}
                        </h3>
                    </div>
                </div>
            </a>
        </div>
    @endforeach


</div>
@endif



    <div class="row">
   <div class="col-lg-12">
    <div class="card mb-4 shadow-sm border-0 p-4">
        <div class="row align-items-center">
            {{-- Left: Doughnut Chart --}}
            <div class="col-md-8 text-center mb-4 mb-md-0">
                <h5 class="mb-3">Stock Count by Product Model</h5>
                <canvas id="productModelDoughnut" width="400" height="400"></canvas>
            </div>

            {{-- Right: Product List --}}
            <div class="col-md-4">
                <h5 class="mb-3 text-center">Product Stock List</h5>
                <div class="overflow-auto" style="max-height: 400px;">
                    <ul class="custom-list-group">
    @php
        $colorPalette = [
            '#FF6384', '#36A2EB', '#FFCE56',
            '#4BC0C0', '#9966FF', '#FF9F40',
            '#00C49F', '#FF8042', '#8884D8',
            '#A0D911', '#722ED1', '#FA8C16'
        ];
    @endphp

    @foreach($chartData as $index => $item)
        <li class="custom-list-item">
            <span class="custom-label">{{ $item['label'] }}</span>
            <span class="custom-badge"
                  style="background-color: {{ $colorPalette[$index % count($colorPalette)] }}">
                {{ $item['value'] }} pcs
            </span>
        </li>
    @endforeach
</ul>
<style>
    .custom-list-group {
    list-style: none;
    padding: 0;
    margin: 0;
    background: #f5f7fa;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06);
}

.custom-list-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 14px 20px;
    border-bottom: 1px solid #e0e0e0;
    background: #ffffff;
    transition: background-color 0.3s;
}

.custom-list-item:last-child {
    border-bottom: none;
}

.custom-list-item:hover {
    background-color: #f0f4ff;
}

.custom-label {
    font-weight: 500;
    text-transform: capitalize;
    color: #333;
    font-size: 16px;
}

.custom-badge {
    color: #fff;
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 14px;
    font-weight: bold;
    min-width: 70px;
    text-align: center;
}

</style>
                </div>
            </div>
        </div>

        {{-- Chart.js + DataLabels Plugin --}}
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>

        <script>
            const doughnutLabels = @json(collect($chartData)->pluck('label'));
            const doughnutDataValues = @json(collect($chartData)->pluck('value'));
            const totalStock = {{ $totalStock }};

            const doughnutCtx = document.getElementById('productModelDoughnut').getContext('2d');

            const doughnutConfig = {
                type: 'doughnut',
                data: {
                    labels: doughnutLabels,
                    datasets: [{
                        label: 'Stock Count',
                        data: doughnutDataValues,
                        backgroundColor: [
                            '#FF6384', '#36A2EB', '#FFCE56',
                            '#4BC0C0', '#9966FF', '#FF9F40',
                            '#00C49F', '#FF8042', '#8884D8',
                            '#A0D911', '#722ED1', '#FA8C16'
                        ],
                        borderColor: '#fff',
                        borderWidth: 2,
                        radius: '75%'
                    }]
                },
                options: {
                    cutout: '35%',
                    responsive: true,
                    layout: {
                        padding: 20
                    },
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                font: { size: 14 },
                                color: '#333',
                            },
                        },
                        datalabels: {
                            color: '#fff',
                            font: {
                                weight: 'bold',
                                size: 12,
                            },
                            formatter: (value, context) => {
                                const label = context.chart.data.labels[context.dataIndex];
                                return `${label}\n${value}`;
                            }
                        }
                    }
                },
                plugins: [
                    ChartDataLabels,
                    {
                        id: 'centerText',
                        beforeDraw(chart) {
                            const { width, height } = chart;
                            const ctx = chart.ctx;
                            ctx.restore();

                            const fontSize = Math.min(width, height) / 25;
                            ctx.font = `bold ${fontSize}px sans-serif`;
                            ctx.textBaseline = 'middle';
                            ctx.textAlign = 'center';
                            ctx.fillStyle = '#000';

                            const text = `Total\n${totalStock}`;
                            const lines = text.split('\n');
                            const lineHeight = fontSize * 1.2;
                            const centerX = width / 2;
                            const centerY = height / 2 - (lines.length - 1) * lineHeight / 2;

                            lines.forEach((line, i) => {
                                ctx.fillText(line, centerX, centerY + i * lineHeight);
                            });

                            ctx.save();
                        }
                    }
                ]
            };

            new Chart(doughnutCtx, doughnutConfig);
        </script>
    </div>
</div>



<div class="col-lg-12">
    <div class="card mb-4 shadow-sm border-0 p-4">
        <div class="row align-items-center">
            
            {{-- Left: Doughnut Chart --}}
            <div class="col-md-8 text-center mb-4 mb-md-0">
                <h5 class="mb-3">Activation Count by Model & Status</h5>
                <canvas id="combinedDoughnut" width="400" height="400"></canvas>
            </div>

            {{-- Right: Data List --}}
            <div class="col-md-4">
                <h5 class="mb-3 text-center">Activation Details</h5>
                <div class="overflow-auto" style="max-height: 400px;">
                    <ul class="custom-list-group">
                        @php
                            $colors = [
                                '#FF6384', '#FF9F40', '#FFCD56', '#4BC0C0',
                                '#36A2EB', '#9966FF', '#C9CBCF', '#00C49F',
                                '#B46DE6', '#F77090', '#FFD84A', '#70A1FF'
                            ];
                        @endphp

                        @foreach($combinedChartData as $index => $item)
                            <li class="custom-list-item">
                                <div>
                                    <strong>{{ $item['model'] }}</strong> - 
                                    <span class="text-muted">{{ $item['status'] }}</span>
                                    @if(!empty($item['creator_name']))
                                        <br><small>By: {{ $item['creator_name'] }}</small>
                                    @endif
                                </div>
                                <span class="custom-badge" 
                                      style="background-color: {{ $colors[$index % count($colors)] }}">
                                    {{ $item['count'] }}
                                </span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

        </div>

        {{-- Chart.js --}}
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>

        <script>
            const totalActivations = {{ $totalActivations }};
            const combinedData = @json($combinedChartData);

            const labels = combinedData.map(item => {
                let label = `${item.model} - ${item.status}`;
                if (item.creator_name) {
                    label += ` (By: ${item.creator_name})`;
                }
                return label;
            });

            const data = combinedData.map(item => item.count);

            const colors = [
                '#FF6384', '#FF9F40', '#FFCD56', '#4BC0C0',
                '#36A2EB', '#9966FF', '#C9CBCF', '#00C49F',
                '#B46DE6', '#F77090', '#FFD84A', '#70A1FF'
            ];

            const config = {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: data,
                        backgroundColor: colors,
                        borderWidth: 2,
                        borderColor: '#fff',
                        radius: '80%'
                    }]
                },
                options: {
                    responsive: true,
                    cutout: '35%',
                    layout: { padding: 20 },
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { font: { size: 14 } }
                        },
                        datalabels: {
                            color: '#fff',
                            font: { weight: 'bold', size: 10 },
                            formatter: (value, ctx) => {
                                const item = combinedData[ctx.dataIndex];
                                return `${item.model}\n${item.status}: ${item.count}`;
                            }
                        }
                    }
                },
                plugins: [
                    ChartDataLabels,
                    {
                        id: 'centerText',
                        beforeDraw(chart) {
                            const { width, height } = chart;
                            const ctx = chart.ctx;
                            ctx.restore();
                            const fontSize = Math.min(width, height) / 20;
                            ctx.font = `bold ${fontSize}px sans-serif`;
                            ctx.textBaseline = 'middle';
                            ctx.textAlign = 'center';
                            ctx.fillStyle = '#000';
                            const lines = ['Total', `${totalActivations}`];
                            const lineHeight = fontSize * 1.2;
                            const centerX = width / 2;
                            const centerY = height / 2 - (lines.length - 1) * lineHeight / 2;
                            lines.forEach((line, i) => {
                                ctx.fillText(line, centerX, centerY + i * lineHeight);
                            });
                            ctx.save();
                        }
                    }
                ]
            };

            new Chart(document.getElementById('combinedDoughnut'), config);
        </script>
    </div>
</div>
<style>
    .custom-list-group {
    list-style: none;
    padding: 0;
    margin: 0;
    background: #f5f7fa;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.06);
}

.custom-list-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 14px 20px;
    border-bottom: 1px solid #eaeaea;
    background: #ffffff;
    transition: background-color 0.3s;
}

.custom-list-item:last-child {
    border-bottom: none;
}

.custom-list-item:hover {
    background-color: #f0f4ff;
}

.custom-badge {
    color: #fff;
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 14px;
    font-weight: bold;
    min-width: 45px;
    text-align: center;
}

</style>


    <div class="row mb-4">
        {{-- Stock Chart --}}
        <div class="col-lg-6">
            <div class="card shadow">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <strong>Stock</strong>
                    <form method="GET" class="d-flex gap-2">
                        <select name="time_filter" class="form-control m-2" onchange="this.form.submit()">
                            <option value="today" {{ $timeFilter === 'today' ? 'selected' : '' }}>Today</option>
                            <option value="week" {{ $timeFilter === 'week' ? 'selected' : '' }}>This Week</option>
                            <option value="month" {{ $timeFilter === 'month' ? 'selected' : '' }}>This Month</option>
                            <option value="year" {{ $timeFilter === 'year' ? 'selected' : '' }}>This Year</option>
                        </select>

                        <select name="role_type" class="form-control m-2" onchange="this.form.submit()">
                            <option value="">-- Select Role --</option>
                            <option value="CNF" {{ $selectedRoleType === 'CNF' ? 'selected' : '' }}>CNF</option>
                            <option value="Dealer" {{ $selectedRoleType === 'Dealer' ? 'selected' : '' }}>Dealer</option>
                            <option value="Distributer" {{ $selectedRoleType === 'Distributer' ? 'selected' : '' }}>Distributer</option>
                        </select>

                        <select name="role_filter" class="form-control m-2" onchange="this.form.submit()">
                            <option value="">-- Select User --</option>
                            @foreach($users as $id => $name)
                                <option value="{{ $id }}" {{ $selectedUserId == $id ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                    </form>
                </div>
                <div class="card-body">
                    <canvas id="stockChart" height="200"></canvas>
                </div>
            </div>
        </div>

        {{-- Activation Chart --}}
        <div class="col-lg-6">
            <div class="card shadow">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <strong>Activation</strong>
                    <form method="GET" class="d-flex gap-2">
                        <select name="activation_status" class="form-control m-2" onchange="this.form.submit()">
                            <option value="">All</option>
                            <option value="Pending" {{ request('activation_status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                            <option value="Approved" {{ request('activation_status') == 'Approved' ? 'selected' : '' }}>Approved</option>
                            <option value="Rejected" {{ request('activation_status') == 'Rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>

                        <input type="date" name="activation_from" class="form-control m-2" value="{{ request('activation_from') }}" onchange="this.form.submit()">
                        <input type="date" name="activation_to" class="form-control m-2" value="{{ request('activation_to') }}" onchange="this.form.submit()">

                        <select name="activation_group" class="form-control m-2" onchange="this.form.submit()">
                            <option value="day" {{ request('activation_group') == 'day' ? 'selected' : '' }}>Day</option>
                            <option value="month" {{ request('activation_group') == 'month' ? 'selected' : '' }}>Month</option>
                            <option value="year" {{ request('activation_group') == 'year' ? 'selected' : '' }}>Year</option>
                        </select>
                    </form>
                </div>
                <div class="card-body">
                    <canvas id="activationChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

  <div class="col-lg-12">
    <div class="card mb-4 shadow-sm border-0 p-4">
        <div class="row align-items-center">
            
            {{-- LEFT: Doughnut Chart --}}
            <div class="col-md-8 text-center mb-4 mb-md-0">
                <h5 class="mb-3">Stock Transfers by User</h5>
                <canvas id="stockTransferChart" width="400" height="400"></canvas>
            </div>

            {{-- RIGHT: Data List --}}
            <div class="col-md-4">
                <h5 class="mb-3 text-center">Transfer Summary</h5>
                <div class="overflow-auto" style="max-height: 400px;">
                    <ul class="custom-list-group">
                        @php
                            $colors = [
                                '#FF6384', '#FF9F40', '#FFCD56', '#4BC0C0',
                                '#36A2EB', '#9966FF', '#C9CBCF', '#00C49F',
                                '#B46DE6', '#F77090', '#FFD84A', '#70A1FF'
                            ];
                        @endphp

                        @foreach($transferLabels as $index => $label)
                            <li class="custom-list-item">
                                <div>
                                    <strong>{{ $label }}</strong>
                                </div>
                                <span class="custom-badge" 
                                      style="background-color: {{ $colors[$index % count($colors)] }};">
                                    {{ $transferCounts[$index] }}
                                </span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

        </div>
    </div>
</div>
@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const apiUrl = "{{ route('admin.dashboard.data') }}";
    const filterEl = document.getElementById('dash-filter');
    const fromEl = document.getElementById('dash-from');
    const toEl   = document.getElementById('dash-to');
    const applyBtn = document.getElementById('dash-apply');
    const resetBtn = document.getElementById('dash-reset');

    // Show/hide custom date inputs
    filterEl.addEventListener('change', function () {
        if (this.value === 'custom') {
            fromEl.style.display = 'inline-block';
            toEl.style.display = 'inline-block';
        } else {
            fromEl.style.display = 'none';
            toEl.style.display = 'none';
            fromEl.value = '';
            toEl.value = '';
        }
    });

    // Chart instance
    let pieChart = null;
    function createOrUpdatePie(labels, data) {
        const ctx = document.getElementById('withdrawPieChart').getContext('2d');
        if (pieChart) {
            pieChart.data.labels = labels;
            pieChart.data.datasets[0].data = data;
            pieChart.update();
            return;
        }
        pieChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: ['#198754', '#ffc107', '#dc3545'],
                    borderColor: '#fff',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    }

    // Fetch and render
    async function fetchDashboard() {
        const payload = {
            filter: filterEl.value
        };
        if (filterEl.value === 'custom') {
            payload.from_date = fromEl.value;
            payload.to_date   = toEl.value;
        }

        // Build query string
        const params = new URLSearchParams(payload).toString();
        const res = await fetch(apiUrl + '?' + params, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const json = await res.json();
        if (!json || json.status !== 'success') return;

        const d = json.data;

        // Update cards
        document.getElementById('card-verified').innerText = d.totalVerified;
        document.getElementById('card-not-verified').innerText = d.totalNotVerified;
        document.getElementById('card-total-investment').innerText = '₹' + Number(d.totalInvestmentAmount).toLocaleString();
        document.getElementById('card-withdraw-requested').innerText = '₹' + Number(d.withdraw.requested).toLocaleString();

        // Withdraw breakdown
        document.getElementById('withdraw-approved').innerText = '₹' + Number(d.withdraw.approved).toLocaleString();
        document.getElementById('withdraw-pending').innerText = '₹' + Number(d.withdraw.pending).toLocaleString();
        document.getElementById('withdraw-rejected').innerText = '₹' + Number(d.withdraw.rejected).toLocaleString();

        // KYC totals
        document.getElementById('kyc-total').innerText = d.kyc.total;

        // Pie chart
        const labels = d.chartData.map(x => x.label);
        const values = d.chartData.map(x => x.value);
        createOrUpdatePie(labels, values);
    }

    // initial load
    fetchDashboard();

    applyBtn.addEventListener('click', function (e) {
        e.preventDefault();
        fetchDashboard();
    });

    resetBtn.addEventListener('click', function (e) {
        e.preventDefault();
        filterEl.value = 'all';
        fromEl.value = '';
        toEl.value = '';
        fromEl.style.display = 'none';
        toEl.style.display = 'none';
        fetchDashboard();
    });

});
</script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const transferLabels = @json($transferLabels);
    const transferCounts = @json($transferCounts);
    const chartColors = [
        '#FF6384', '#FF9F40', '#FFCD56', '#4BC0C0',
        '#36A2EB', '#9966FF', '#C9CBCF', '#00C49F',
        '#B46DE6', '#F77090', '#FFD84A', '#70A1FF'
    ];

    const ctx = document.getElementById('stockTransferChart').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: transferLabels,
            datasets: [{
                data: transferCounts,
                backgroundColor: chartColors,
                borderWidth: 2,
                borderColor: '#fff',
                radius: '80%'
            }]
        },
        options: {
            responsive: true,
            cutout: '35%',
            layout: { padding: 20 },
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { font: { size: 14 } }
                }
            }
        }
    });
</script>
@endsection


    </div>
    @else

    <div class="alert alert-info">Customers don't have dashboard access.</div>
 <div class="col-md-6 mx-auto">
        <div class="card p-3">
            <h4 class="text-center mb-4">Vehicle Status Summary</h4>
            <canvas id="vehicleStatusChart" height="300"></canvas>
        </div>
    </div>
</div>

{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>

<script>
    @if(!empty($vehicleChartData) && $vehicleChartData->count())

        const statusLabels = @json($vehicleChartData->pluck('label'));
        const statusCounts = @json($vehicleChartData->pluck('value'));
        const totalVehicles = {{ $vehicleTotalCount }};

        const ctx = document.getElementById('vehicleStatusChart').getContext('2d');

        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: statusLabels,
                datasets: [{
                    label: 'Vehicle Status',
                    data: statusCounts,
                    backgroundColor: [
                        '#36A2EB', '#FF6384', '#FFCE56',
                        '#4BC0C0', '#9966FF', '#FF9F40'
                    ],
                    borderColor: '#fff',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: '#333',
                            font: { size: 14 }
                        }
                    },
                    title: {
                        display: true,
                        text: 'Vehicle Distribution by Status',
                        font: { size: 18 },
                        color: '#111'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const value = context.raw;
                                const percent = ((value / totalVehicles) * 100).toFixed(1);
                                return `${context.label}: ${value} vehicles (${percent}%)`;
                            }
                        }
                    },
                    datalabels: {
                        formatter: (value) => {
                            const percent = ((value / totalVehicles) * 100).toFixed(1);
                            return `${percent}%`;
                        },
                        color: '#fff',
                        font: { weight: 'bold', size: 12 }
                    }
                }
            },
            plugins: [ChartDataLabels]
        });

    @else
        document.getElementById('vehicleStatusChart').parentElement.innerHTML = 
            '<p class="text-center text-muted">No data available.</p>';
    @endif
</script>
    @endif
</div>
@endsection

@section('scripts')
@parent
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Stock Chart
const stockCtx = document.getElementById('stockChart')?.getContext('2d');
if (stockCtx) {
    const stockGradient = stockCtx.createLinearGradient(0, 0, 0, 300);
    stockGradient.addColorStop(0, 'rgba(30, 144, 255, 0.4)');
    stockGradient.addColorStop(1, 'rgba(30, 144, 255, 0)');

    new Chart(stockCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($chartLabels ?? []) !!},
            datasets: [{
                label: 'Stock Count',
                data: {!! json_encode($chartValues ?? []) !!},
                fill: true,
                backgroundColor: stockGradient,
                borderColor: '#1e90ff',
                borderWidth: 2,
                pointRadius: 0,
                tension: 0.5
            }]
        },
        options: {
            responsive: true,
            animation: {
                duration: 2000,
                easing: 'easeInOutSine'
            },
            interaction: {
                mode: 'index',
                intersect: false
            },
            plugins: {
                legend: { display: true },
                tooltip: {
                    enabled: true,
                    backgroundColor: 'rgba(0,0,0,0.7)'
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { color: '#555' }
                },
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1, color: '#555' },
                    grid: { color: 'rgba(200, 200, 200, 0.3)', borderDash: [4, 4] }
                }
            }
        }
    });
}

// Activation Chart
const activationCtx = document.getElementById('activationChart')?.getContext('2d');
if (activationCtx) {
    const activationGradient = activationCtx.createLinearGradient(0, 0, 0, 300);
    activationGradient.addColorStop(0, 'rgba(255, 99, 132, 0.5)');
    activationGradient.addColorStop(1, 'rgba(255, 99, 132, 0)');

    new Chart(activationCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($activationChartLabels ?? []) !!},
            datasets: [{
                label: 'Activations',
                data: {!! json_encode($activationChartValues ?? []) !!},
                fill: true,
                backgroundColor: activationGradient,
                borderColor: '#ff6384',
                borderWidth: 2,
                pointRadius: 0,
                tension: 0.5
            }]
        },
        options: {
            responsive: true,
            animation: {
                duration: 2000,
                easing: 'easeInOutSine'
            },
            interaction: {
                mode: 'index',
                intersect: false
            },
            plugins: {
                legend: { display: true },
                tooltip: {
                    enabled: true,
                    backgroundColor: 'rgba(0,0,0,0.7)'
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { color: '#555' }
                },
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1, color: '#555' },
                    grid: { color: 'rgba(200, 200, 200, 0.3)', borderDash: [4, 4] }
                }
            }
        }
    });
}
</script>
@endsection

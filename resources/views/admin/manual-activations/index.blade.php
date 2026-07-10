@extends('layouts.admin')

@section('content')
<style>
    :root {
        --me-primary: #7C3AED;
        --me-primary-dark: #5B21B6;
        --me-secondary: #6366F1;
    }
    .me-dash-header {
        background: linear-gradient(135deg, #7C3AED 0%, #6366F1 55%, #4F46E5 100%);
        border-radius: 20px; padding: 28px 30px; color: #fff; margin-bottom: 20px;
        box-shadow: 0 16px 40px rgba(99,53,237,.25); position: relative; overflow: hidden;
    }
    .me-dash-header::after {
        content: ''; position: absolute; right: -60px; top: -60px; width: 220px; height: 220px;
        background: rgba(255,255,255,.08); border-radius: 50%;
    }
    .me-dash-header::before {
        content: ''; position: absolute; right: 40px; bottom: -80px; width: 160px; height: 160px;
        background: rgba(255,255,255,.06); border-radius: 50%;
    }
    .me-btn { background: rgba(255,255,255,.15); backdrop-filter: blur(6px); border: 1px solid rgba(255,255,255,.35); color: #fff; border-radius: 10px; padding: 9px 20px; font-weight: 600; transition: .2s; }
    .me-btn:hover { background: #fff; color: var(--me-primary-dark); }

    /* ---------- Filter Bar ---------- */
    .me-filter-bar {
        background: #fff; border-radius: 16px; padding: 16px; box-shadow: 0 8px 24px rgba(124,58,237,.08);
        margin-bottom: 20px; border: 1px solid #F1EEFE;
    }
    .me-filter-bar .form-select, .me-filter-bar .form-control {
        border-radius: 10px; border: 1px solid #E9E5FC; font-size: .85rem; font-weight: 500; color: #4B5563;
        padding: 9px 14px; background: #FBFAFF;
    }
    .me-filter-bar .form-select:focus, .me-filter-bar .form-control:focus {
        border-color: var(--me-primary); box-shadow: 0 0 0 .18rem rgba(124,58,237,.12); background: #fff;
    }
    .me-filter-label { font-size: .7rem; text-transform: uppercase; letter-spacing: .04em; color: #9CA3AF; font-weight: 700; margin-bottom: 4px; display: block; }

    /* ---------- KPI ---------- */
    .me-kpi { border: none; border-radius: 16px; padding: 20px; background: #fff; box-shadow: 0 8px 22px rgba(124,58,237,.08); transition: .25s; border: 1px solid #F5F3FF; }
    .me-kpi:hover { transform: translateY(-4px); box-shadow: 0 14px 30px rgba(124,58,237,.16); }
    .me-kpi .icon { width: 42px; height: 42px; border-radius: 12px; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #EDE9FE, #E0E7FF); color: var(--me-primary); font-size: 1.1rem; margin-bottom: 10px; }
    .me-kpi .num { font-size: 1.9rem; font-weight: 800; color: #1F2937; line-height: 1.1; }
    .me-kpi .lbl { color: #9CA3AF; font-size: .78rem; font-weight: 700; text-transform: uppercase; letter-spacing: .03em; margin-top: 2px; }

    /* ---------- Chart Card (Total + Pie/Chart Type Tabs) ---------- */
    .me-total-card {
        border: none; border-radius: 20px; padding: 26px; background: #fff; box-shadow: 0 10px 28px rgba(124,58,237,.09);
    }
    .me-total-lbl { color: #10B981; font-weight: 800; font-size: .75rem; letter-spacing: .06em; text-transform: uppercase; }
    .me-total-num { font-size: 2.4rem; font-weight: 800; color: #111827; margin-top: 2px; }
    .me-tabs { display: flex; gap: 4px; background: #F5F3FF; border-radius: 12px; padding: 4px; flex-wrap: wrap; }
    .me-tab { border: none; background: transparent; color: #6D28D9; font-weight: 700; font-size: .8rem; padding: 8px 14px; border-radius: 9px; transition: .2s; cursor: pointer; }
    .me-tab.active { background: #fff; box-shadow: 0 3px 10px rgba(124,58,237,.18); color: var(--me-primary-dark); }
    .me-group-chip { border: 1px solid #E9E5FC; color: #6D28D9; background: #fff; border-radius: 20px; padding: 6px 16px; font-size: .78rem; font-weight: 700; margin-right: 6px; margin-bottom: 6px; cursor: pointer; transition: .2s; display: inline-block; }
    .me-group-chip.active { background: linear-gradient(135deg, #7C3AED, #6366F1); color: #fff; border-color: transparent; box-shadow: 0 4px 12px rgba(124,58,237,.3); }

    .me-rank-row { display: flex; align-items: center; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #F3F4F6; }
    .me-rank-row:last-child { border-bottom: none; }
    .me-rank-dot { width: 10px; height: 10px; border-radius: 50%; display: inline-block; margin-right: 10px; }
    .me-rank-name { font-weight: 700; color: #1F2937; font-size: .92rem; }
    .me-rank-bar-track { background: #F3F4F6; border-radius: 6px; height: 6px; width: 100%; margin-top: 6px; overflow: hidden; }
    .me-rank-bar-fill { height: 100%; border-radius: 6px; }
    .me-rank-pct { font-weight: 800; color: #1F2937; font-size: 1rem; }
    .me-rank-sub { color: #9CA3AF; font-size: .74rem; font-weight: 600; }

    #chartCanvas { max-height: 320px; }

    /* ---------- Table ---------- */
    .me-chart-card { border: none; border-radius: 16px; box-shadow: 0 8px 22px rgba(124,58,237,.08); }
    .me-table thead th { background: #F5F3FF; color: #5B21B6; border: none; font-weight: 700; font-size: .76rem; text-transform: uppercase; letter-spacing: .03em; white-space: nowrap; }
    .me-table tbody td { font-size: .85rem; vertical-align: middle; }
    .me-table tbody tr:hover { background: #FAF5FF; }
    .me-date-main { font-weight: 700; color: #1F2937; }
    .me-date-sub { font-size: .72rem; color: #9CA3AF; }

    @media (max-width: 576px) { .me-dash-header { padding: 18px; } .me-total-num { font-size: 1.8rem; } }
</style>

<div class="me-dash-header animate__animated animate__fadeIn">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2" style="position:relative; z-index:1;">
        <div>
            <h4 class="mb-0"><i class="fas fa-chart-pie mr-2"></i>Activation Dashboard</h4>
            <small>Manual Entry &raquo; Activation</small>
        </div>
        @can('manual_activation_create')
            <a href="{{ route('admin.manual-activations.create') }}" class="me-btn"><i class="fas fa-plus mr-1"></i> New Activation</a>
        @endcan
    </div>
</div>

@if(session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
@endif

{{-- KPI Strip --}}
<div class="row mb-3" id="kpiStrip">
    <div class="col-md-3 col-6 mb-3">
        <div class="me-kpi"><div class="icon"><i class="fas fa-bolt"></i></div><div class="num" id="kpiTotal">0</div><div class="lbl">Total Activations</div></div>
    </div>
    <div class="col-md-3 col-6 mb-3">
        <div class="me-kpi"><div class="icon"><i class="fas fa-user-tie"></i></div><div class="num" id="kpiParties">-</div><div class="lbl">Parties Involved</div></div>
    </div>
    <div class="col-md-3 col-6 mb-3">
        <div class="me-kpi"><div class="icon"><i class="fas fa-box"></i></div><div class="num" id="kpiProducts">-</div><div class="lbl">Products Involved</div></div>
    </div>
    <div class="col-md-3 col-6 mb-3">
        <div class="me-kpi"><div class="icon"><i class="fas fa-map-marker-alt"></i></div><div class="num" id="kpiStates">-</div><div class="lbl">States Covered</div></div>
    </div>
</div>

{{-- Filters --}}
<div class="me-filter-bar">
    <form id="filterForm" class="row g-2 align-items-end">
        <div class="col-lg col-md-4 col-6 mb-2">
            <span class="me-filter-label">Party</span>
            <select name="manual_party_id" class="form-select form-select-sm">
                <option value="">All Parties</option>
                @foreach($parties as $party)
                    <option value="{{ $party->id }}">{{ $party->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-lg col-md-4 col-6 mb-2">
            <span class="me-filter-label">Fitter</span>
            <select name="manual_fitter_id" class="form-select form-select-sm">
                <option value="">All Fitters</option>
                @foreach($fitters as $fitter)
                    <option value="{{ $fitter->id }}">{{ $fitter->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-lg col-md-4 col-6 mb-2">
            <span class="me-filter-label">Product</span>
            <select name="manual_product_id" class="form-select form-select-sm">
                <option value="">All Products</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-lg col-md-4 col-6 mb-2">
            <span class="me-filter-label">State</span>
            <select name="state" id="filterState" class="form-select form-select-sm">
                <option value="">All States</option>
                @foreach($states as $state)
                    <option value="{{ $state }}">{{ $state }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-lg col-md-4 col-6 mb-2">
            <span class="me-filter-label">District</span>
            <select name="district" id="filterDistrict" class="form-select form-select-sm" disabled>
                <option value="">All Districts</option>
            </select>
        </div>
        <div class="col-lg col-md-4 col-6 mb-2">
            <span class="me-filter-label">City</span>
            <select name="city" id="filterCity" class="form-select form-select-sm" disabled>
                <option value="">All Cities</option>
            </select>
        </div>
        <div class="col-lg col-md-4 col-6 mb-2">
            <span class="me-filter-label">Date Range</span>
            <select name="range" class="form-select form-select-sm" id="rangeSelect">
                <option value="all" selected>All Time</option>
                <option value="today">Today</option>
                <option value="this_week">This Week</option>
                <option value="this_month">This Month</option>
                <option value="3_month">Last 3 Months</option>
                <option value="6_month">Last 6 Months</option>
                <option value="this_year">This Year</option>
                <option value="custom">Custom Date</option>
            </select>
        </div>
        <div class="col-lg col-md-4 col-6 mb-2 custom-range-field" style="display:none;">
            <span class="me-filter-label">From Date</span>
            <input type="date" name="from_date" class="form-control form-control-sm">
        </div>
        <div class="col-lg col-md-4 col-6 mb-2 custom-range-field" style="display:none;">
            <span class="me-filter-label">To Date</span>
            <input type="date" name="to_date" class="form-control form-control-sm">
        </div>
        <div class="col-lg-auto col-6 mb-2">
            <button type="submit" class="me-btn btn-sm w-100" style="background:linear-gradient(135deg,#7C3AED,#6366F1); border:none;"><i class="fas fa-filter mr-1"></i> Apply</button>
        </div>
    </form>
</div>

<div class="row">
    {{-- Total + Chart --}}
    <div class="col-lg-12 mb-3">
        <div class="me-total-card">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-3">
                <div>
                    <div class="me-total-lbl">Total Activations</div>
                    <div class="me-total-num" id="bigTotal">0</div>
                </div>
                <div class="me-tabs" id="chartTypeTabs">
                    <button type="button" class="me-tab active" data-chart="pie"><i class="fas fa-chart-pie mr-1"></i>Pie</button>
                    <button type="button" class="me-tab" data-chart="doughnut"><i class="fas fa-circle-notch mr-1"></i>Donut</button>
                    <button type="button" class="me-tab" data-chart="bar"><i class="fas fa-chart-bar mr-1"></i>Bar</button>
                    <button type="button" class="me-tab" data-chart="line"><i class="fas fa-wave-square mr-1"></i>Wave</button>
                    <button type="button" class="me-tab" data-chart="radar"><i class="fas fa-braille mr-1"></i>Radar</button>
                </div>
            </div>

            <div class="mb-3">
                <strong class="text-muted small d-block mb-2">GROUP BY</strong>
                <span class="me-group-chip active" data-group="product">Product</span>
                <span class="me-group-chip" data-group="party">Party</span>
                <span class="me-group-chip" data-group="fitter">Fitter</span>
                <span class="me-group-chip" data-group="state">State</span>
                <span class="me-group-chip" data-group="district">District</span>
                <span class="me-group-chip" data-group="city">City</span>
            </div>

            <div class="row">
                <div class="col-lg-5 d-flex align-items-center justify-content-center mb-3 mb-lg-0">
                    <canvas id="chartCanvas"></canvas>
                </div>
                <div class="col-lg-7">
                    <div id="rankList" style="max-height: 340px; overflow-y:auto;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
(function () {
    const ctx = document.getElementById('chartCanvas').getContext('2d');
    let chart = null;
    let currentGroup = 'product';
    let currentChartType = 'pie';

    const palette = ['#7C3AED', '#6366F1', '#F59E0B', '#10B981', '#3B82F6', '#F472B6', '#FB923C', '#34D399', '#60A5FA', '#FBBF24', '#A78BFA', '#818CF8'];

    // ---------- Cascading State -> District -> City filter (from real party data) ----------
    const locationMap = @json($locationMap);
    const stateSelect = document.getElementById('filterState');
    const districtSelect = document.getElementById('filterDistrict');
    const citySelect = document.getElementById('filterCity');

    stateSelect.addEventListener('change', function () {
        districtSelect.innerHTML = '<option value="">All Districts</option>';
        citySelect.innerHTML = '<option value="">All Cities</option>';
        citySelect.disabled = true;

        if (!this.value || !locationMap[this.value]) {
            districtSelect.disabled = true;
            return;
        }
        Object.keys(locationMap[this.value]).sort().forEach(d => {
            districtSelect.innerHTML += `<option value="${d}">${d}</option>`;
        });
        districtSelect.disabled = false;
    });

    districtSelect.addEventListener('change', function () {
        citySelect.innerHTML = '<option value="">All Cities</option>';
        const stateVal = stateSelect.value;
        if (!this.value || !locationMap[stateVal] || !locationMap[stateVal][this.value]) {
            citySelect.disabled = true;
            return;
        }
        locationMap[stateVal][this.value].slice().sort().forEach(c => {
            citySelect.innerHTML += `<option value="${c}">${c}</option>`;
        });
        citySelect.disabled = false;
    });

    // ---------- Chart type tabs ----------
    document.querySelectorAll('.me-tab').forEach(tab => {
        tab.addEventListener('click', () => {
            document.querySelectorAll('.me-tab').forEach(t => t.classList.remove('active'));
            tab.classList.add('active');
            currentChartType = tab.dataset.chart;
            loadData();
        });
    });

    // ---------- Group By chips ----------
    document.querySelectorAll('.me-group-chip').forEach(chip => {
        chip.addEventListener('click', () => {
            document.querySelectorAll('.me-group-chip').forEach(c => c.classList.remove('active'));
            chip.classList.add('active');
            currentGroup = chip.dataset.group;
            loadData();
        });
    });

    function buildChart(labels, values) {
        if (chart) chart.destroy();
        const isXY = ['bar', 'line', 'radar'].includes(currentChartType);

        chart = new Chart(ctx, {
            type: currentChartType,
            data: {
                labels: labels,
                datasets: [{
                    label: 'Activations',
                    data: values,
                    backgroundColor: isXY ? 'rgba(124,58,237,.35)' : palette,
                    borderColor: isXY ? '#7C3AED' : '#fff',
                    borderWidth: 2,
                    fill: currentChartType === 'line',
                    tension: 0.4,
                    cutout: currentChartType === 'doughnut' ? '68%' : undefined,
                }]
            },
            options: {
                responsive: true,
                animation: { duration: 800, easing: 'easeOutQuart' },
                plugins: { legend: { display: false } },
                scales: isXY ? { y: { beginAtZero: true, ticks: { precision: 0 } } } : {}
            }
        });
    }

    function buildRankList(labels, values) {
        const total = values.reduce((a, b) => a + b, 0) || 1;
        const list = document.getElementById('rankList');
        const items = labels.map((label, i) => ({ label, value: values[i], color: palette[i % palette.length] }))
            .sort((a, b) => b.value - a.value);

        list.innerHTML = items.map(item => {
            const pct = ((item.value / total) * 100).toFixed(2);
            return `
                <div class="me-rank-row">
                    <div style="flex:1;">
                        <span class="me-rank-dot" style="background:${item.color};"></span>
                        <span class="me-rank-name">${item.label}</span>
                        <div class="me-rank-bar-track"><div class="me-rank-bar-fill" style="width:${pct}%; background:${item.color};"></div></div>
                    </div>
                    <div class="text-right ml-3">
                        <div class="me-rank-pct">${pct}%</div>
                        <div class="me-rank-sub">${item.value} qty</div>
                    </div>
                </div>`;
        }).join('') || '<div class="text-center text-muted py-4">Koi data nahi mila.</div>';
    }

    function loadData() {
        const form = document.getElementById('filterForm');
        const params = new URLSearchParams(new FormData(form));
        params.set('group_by', currentGroup);

        fetch(`{{ route('admin.manual-activations.chartData') }}?${params.toString()}`)
            .then(res => res.json())
            .then(data => {
                buildChart(data.labels, data.values);
                buildRankList(data.labels, data.values);

                document.getElementById('kpiTotal').textContent = data.total;
                document.getElementById('bigTotal').textContent = data.total;
                document.getElementById('kpiParties').textContent = new Set(data.table.map(r => r.party)).size;
                document.getElementById('kpiProducts').textContent = new Set(data.table.map(r => r.product)).size;
                document.getElementById('kpiStates').textContent = new Set(data.table.map(r => r.state)).size;
            });
    }

    document.getElementById('rangeSelect').addEventListener('change', function () {
        document.querySelectorAll('.custom-range-field').forEach(f => {
            f.style.display = this.value === 'custom' ? 'block' : 'none';
        });
    });

    document.getElementById('filterForm').addEventListener('submit', function (e) {
        e.preventDefault();
        loadData();
    });

    loadData();
})();
</script>

{{-- Paginated raw list below the dashboard (for full CRUD access) --}}
<div class="card me-chart-card mt-3">
    <div class="card-body">
        <strong class="text-muted small d-block mb-2">ALL ACTIVATIONS (Paginated)</strong>
        <div class="table-responsive">
            <table class="table me-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Fitting Date</th>
                        <th>Created At</th>
                        <th>Party</th>
                        <th>Fitter</th>
                        <th>Product</th>
                        <th>Customer</th>
                        <th>Vehicle No.</th>
                        <th>Created By</th>
                        <th class="text-right">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($activations as $activation)
                        <tr>
                            <td>{{ $activation->id }}</td>
                            <td>
                                <div class="me-date-main">{{ $activation->fitting_date }}</div>
                            </td>
                            <td>
                                <div class="me-date-main">{{ $activation->created_at->format('d M Y') }}</div>
                                <div class="me-date-sub">{{ $activation->created_at->format('h:i A') }}</div>
                            </td>
                            <td>{{ optional($activation->party)->name }}</td>
                            <td>{{ optional($activation->fitter)->name ?? '-' }}</td>
                            <td>{{ optional($activation->product)->name }}</td>
                            <td>{{ $activation->customer_name ?? '-' }}</td>
                            <td>{{ $activation->vehicle_number ?? '-' }}</td>
                            <td>
                                {{ optional($activation->user)->name ?? '-' }}
                            </td>
                            <td class="text-right">
                                @can('manual_activation_show')
                                    <a href="{{ route('admin.manual-activations.show', $activation->id) }}" class="btn btn-sm btn-outline-info"><i class="fas fa-eye"></i></a>
                                @endcan
                                @can('manual_activation_edit')
                                    <a href="{{ route('admin.manual-activations.edit', $activation->id) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                                @endcan
                                @can('manual_activation_delete')
                                    <form action="{{ route('admin.manual-activations.destroy', $activation->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Pakka delete karna hai?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                                    </form>
                                @endcan
                            </td>

                        </tr>
                    @empty
                        <tr><td colspan="9" class="text-center py-4 text-muted">Koi activation nahi mila.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $activations->links() }}
    </div>
</div>
@endsection

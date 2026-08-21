@extends('layouts.admin')

@section('content')

<style>
    :root {
        --me-primary: #7C3AED;
        --me-primary-dark: #5B21B6;
        --me-secondary: #6366F1;
        --me-indigo: #4F46E5;
        --me-bg: #F8F7FF;
    }

    /* =========================================================
       HEADER
    ========================================================= */
    .me-dash-header {
        background: linear-gradient(
            135deg,
            #7C3AED 0%,
            #6366F1 55%,
            #4F46E5 100%
        );

        border-radius: 20px;
        padding: 28px 30px;
        color: #fff;
        margin-bottom: 20px;
        box-shadow: 0 16px 40px rgba(99,53,237,.25);
        position: relative;
        overflow: hidden;
    }

    .me-dash-header::after {
        content: '';
        position: absolute;
        right: -60px;
        top: -60px;
        width: 220px;
        height: 220px;
        background: rgba(255,255,255,.08);
        border-radius: 50%;
    }

    .me-dash-header::before {
        content: '';
        position: absolute;
        right: 40px;
        bottom: -80px;
        width: 160px;
        height: 160px;
        background: rgba(255,255,255,.06);
        border-radius: 50%;
    }

    .me-btn {
        background: rgba(255,255,255,.15);
        backdrop-filter: blur(6px);
        border: 1px solid rgba(255,255,255,.35);
        color: #fff;
        border-radius: 10px;
        padding: 9px 20px;
        font-weight: 600;
        transition: .2s;
    }

    .me-btn:hover {
        background: #fff;
        color: var(--me-primary-dark);
    }

    /* =========================================================
       FILTER BAR
    ========================================================= */
    .me-filter-bar {
        background: #fff;
        border-radius: 16px;
        padding: 16px;
        box-shadow: 0 8px 24px rgba(124,58,237,.08);
        margin-bottom: 20px;
        border: 1px solid #F1EEFE;
    }

    .me-filter-bar .form-select,
    .me-filter-bar .form-control {
        border-radius: 10px;
        border: 1px solid #E9E5FC;
        font-size: .85rem;
        font-weight: 500;
        color: #4B5563;
        padding: 9px 14px;
        background: #FBFAFF;
    }

    .me-filter-bar .form-select:focus,
    .me-filter-bar .form-control:focus {
        border-color: var(--me-primary);
        box-shadow: 0 0 0 .18rem rgba(124,58,237,.12);
        background: #fff;
    }

    .me-filter-label {
        font-size: .7rem;
        text-transform: uppercase;
        letter-spacing: .04em;
        color: #9CA3AF;
        font-weight: 700;
        margin-bottom: 4px;
        display: block;
    }

    /* =========================================================
       KPI
    ========================================================= */
    .me-kpi {
        border: none;
        border-radius: 16px;
        padding: 20px;
        background: #fff;
        box-shadow: 0 8px 22px rgba(124,58,237,.08);
        transition: .25s;
        border: 1px solid #F5F3FF;
    }

    .me-kpi:hover {
        transform: translateY(-4px);
        box-shadow: 0 14px 30px rgba(124,58,237,.16);
    }

    .me-kpi .icon {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg,#EDE9FE,#E0E7FF);
        color: var(--me-primary);
        font-size: 1.1rem;
        margin-bottom: 10px;
    }

    .me-kpi .num {
        font-size: 1.9rem;
        font-weight: 800;
        color: #1F2937;
        line-height: 1.1;
    }

    .me-kpi .lbl {
        color: #9CA3AF;
        font-size: .78rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .03em;
        margin-top: 2px;
    }

    /* =========================================================
       TOTAL / CHART
    ========================================================= */
    .me-total-card {
        border: none;
        border-radius: 20px;
        padding: 26px;
        background: #fff;
        box-shadow: 0 10px 28px rgba(124,58,237,.09);
    }

    .me-total-lbl {
        color: #10B981;
        font-weight: 800;
        font-size: .75rem;
        letter-spacing: .06em;
        text-transform: uppercase;
    }

    .me-total-num {
        font-size: 2.4rem;
        font-weight: 800;
        color: #111827;
        margin-top: 2px;
    }

    .me-tabs {
        display: flex;
        gap: 4px;
        background: #F5F3FF;
        border-radius: 12px;
        padding: 4px;
        flex-wrap: wrap;
    }

    .me-tab {
        border: none;
        background: transparent;
        color: #6D28D9;
        font-weight: 700;
        font-size: .8rem;
        padding: 8px 14px;
        border-radius: 9px;
        transition: .2s;
        cursor: pointer;
    }

    .me-tab.active {
        background: #fff;
        box-shadow: 0 3px 10px rgba(124,58,237,.18);
        color: var(--me-primary-dark);
    }

    .me-group-chip {
        border: 1px solid #E9E5FC;
        color: #6D28D9;
        background: #fff;
        border-radius: 20px;
        padding: 6px 16px;
        font-size: .78rem;
        font-weight: 700;
        margin-right: 6px;
        margin-bottom: 6px;
        cursor: pointer;
        transition: .2s;
        display: inline-block;
    }

    .me-group-chip.active {
        background: linear-gradient(135deg,#7C3AED,#6366F1);
        color: #fff;
        border-color: transparent;
        box-shadow: 0 4px 12px rgba(124,58,237,.3);
    }

    .me-rank-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 0;
        border-bottom: 1px solid #F3F4F6;
    }

    .me-rank-row:last-child {
        border-bottom: none;
    }

    .me-rank-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 10px;
    }

    .me-rank-name {
        font-weight: 700;
        color: #1F2937;
        font-size: .92rem;
    }

    .me-rank-bar-track {
        background: #F3F4F6;
        border-radius: 6px;
        height: 6px;
        width: 100%;
        margin-top: 6px;
        overflow: hidden;
    }

    .me-rank-bar-fill {
        height: 100%;
        border-radius: 6px;
    }

    .me-rank-pct {
        font-weight: 800;
        color: #1F2937;
        font-size: 1rem;
    }

    .me-rank-sub {
        color: #9CA3AF;
        font-size: .74rem;
        font-weight: 600;
    }

    #chartCanvas {
        max-height: 320px;
    }

    /* =========================================================
       TABLE CARD
    ========================================================= */
    .me-chart-card {
        border: none;
        border-radius: 18px;
        box-shadow: 0 8px 22px rgba(124,58,237,.08);
        overflow: hidden;
    }

    .me-table-header {
        padding: 20px 20px 15px;
        background: linear-gradient(180deg,#fff,#FCFBFF);
        border-bottom: 1px solid #F1EEFE;
    }

    .me-table-title {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .me-table-title-icon {
        width: 38px;
        height: 38px;
        border-radius: 11px;
        background: linear-gradient(135deg,#EDE9FE,#E0E7FF);
        color: #7C3AED;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .me-table-title strong {
        color: #1F2937;
        font-size: .95rem;
    }

    .me-table-title small {
        display: block;
        color: #9CA3AF;
        font-size: .7rem;
        margin-top: 2px;
    }

    .me-table thead th {
        background: #F5F3FF;
        color: #5B21B6;
        border: none;
        font-weight: 700;
        font-size: .74rem;
        text-transform: uppercase;
        letter-spacing: .03em;
        white-space: nowrap;
        padding: 13px 12px;
    }

    .me-table tbody td {
        font-size: .84rem;
        vertical-align: middle;
        padding: 12px;
        border-color: #F3F4F6;
        white-space: nowrap;
    }

    .me-table tbody tr {
        transition: .15s;
    }

    .me-table tbody tr:hover {
        background: #FAF5FF;
    }

    .me-date-main {
        font-weight: 700;
        color: #1F2937;
    }

    .me-date-sub {
        font-size: .72rem;
        color: #9CA3AF;
        margin-top: 2px;
    }

    .me-party {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        font-weight: 700;
        color: #374151;
    }

    .me-party-icon {
        width: 28px;
        height: 28px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #F5F3FF;
        color: #7C3AED;
        font-size: .7rem;
    }

    .me-location-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: #F9FAFB;
        border: 1px solid #E5E7EB;
        border-radius: 8px;
        padding: 5px 8px;
        color: #4B5563;
        font-size: .74rem;
        font-weight: 600;
    }

    .me-action-wrap {
        display: flex;
        justify-content: flex-end;
        gap: 4px;
    }

    .me-action-btn {
        width: 32px;
        height: 32px;
        border-radius: 9px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: .2s;
    }

    .me-action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 12px rgba(0,0,0,.08);
    }

    /* =========================================================
       DATATABLE
    ========================================================= */
    .activation-dt-wrapper {
        width: 100%;
    }

    .activation-dt-wrapper .dataTables_wrapper {
        width: 100%;
    }

    .activation-dt-top {
        padding: 15px 18px;
        background: #FCFBFF;
        border-bottom: 1px solid #F1EEFE;
    }

    .activation-dt-bottom {
        padding: 14px 18px;
        background: #FCFBFF;
        border-top: 1px solid #F1EEFE;
    }

    .activation-dt-wrapper .dataTables_length,
    .activation-dt-wrapper .dataTables_filter {
        margin: 0;
    }

    .activation-dt-wrapper .dataTables_length label {
        display: flex;
        align-items: center;
        gap: 7px;
        margin: 0;
        color: #6B7280;
        font-size: .76rem;
        font-weight: 600;
    }

    .activation-dt-wrapper .dataTables_length select {
        min-width: 65px;
        height: 36px;
        border: 1px solid #E9E5FC;
        border-radius: 9px;
        background: #fff;
        color: #4B5563;
        padding: 4px 25px 4px 9px;
        outline: none;
    }

    .activation-dt-wrapper .dataTables_filter {
        position: relative;
    }

    .activation-dt-wrapper .dataTables_filter label {
        margin: 0;
        width: 100%;
    }

    .activation-dt-wrapper .dataTables_filter label::before {
        content: "\f002";
        font-family: "Font Awesome 5 Free";
        font-weight: 900;
        position: absolute;
        left: 13px;
        top: 50%;
        transform: translateY(-50%);
        color: #9CA3AF;
        z-index: 2;
    }

    .activation-dt-wrapper .dataTables_filter input {
        width: 100%;
        height: 38px;
        margin-left: 0 !important;
        padding: 7px 13px 7px 37px;
        border: 1px solid #E9E5FC;
        border-radius: 10px;
        background: #fff;
        outline: none;
        font-size: .8rem;
        color: #374151;
        transition: .2s;
    }

    .activation-dt-wrapper .dataTables_filter input:focus {
        border-color: #8B5CF6;
        box-shadow: 0 0 0 3px rgba(139,92,246,.10);
    }

    .activation-dt-wrapper .dataTables_info {
        color: #9CA3AF;
        font-size: .75rem;
        font-weight: 600;
        padding-top: 0 !important;
    }

    .activation-dt-wrapper .dataTables_paginate {
        padding-top: 0 !important;
    }

    .activation-dt-wrapper .dataTables_paginate .paginate_button {
        min-width: 33px;
        height: 33px;
        line-height: 20px !important;
        padding: 6px 8px !important;
        margin: 0 2px !important;
        border: 1px solid #E9E5FC !important;
        border-radius: 9px !important;
        background: #fff !important;
        color: #6B7280 !important;
        font-size: .73rem;
        font-weight: 700;
        transition: .2s;
    }

    .activation-dt-wrapper .dataTables_paginate .paginate_button:hover {
        background: #F5F3FF !important;
        border-color: #DDD6FE !important;
        color: #6D28D9 !important;
    }

    .activation-dt-wrapper .dataTables_paginate .paginate_button.current {
        background: linear-gradient(135deg,#7C3AED,#6366F1) !important;
        border-color: transparent !important;
        color: #fff !important;
        box-shadow: 0 4px 12px rgba(124,58,237,.22);
    }

    .activation-dt-wrapper .dataTables_paginate .paginate_button.disabled {
        opacity: .4;
    }

    .activation-dt-wrapper table.dataTable {
        margin-top: 0 !important;
        margin-bottom: 0 !important;
        border-collapse: separate !important;
        border-spacing: 0;
    }

    .activation-dt-wrapper table.dataTable thead th {
        border-bottom: none !important;
    }

    .activation-dt-wrapper table.dataTable tbody td {
        border-bottom: 1px solid #F3F4F6;
    }

    /* =========================================================
       EXPORT BUTTONS
    ========================================================= */
    .dt-buttons {
        display: flex;
        gap: 5px;
        flex-wrap: wrap;
        margin-bottom: 0;
    }

    .dt-button {
        border: 1px solid #E9E5FC !important;
        background: #fff !important;
        color: #6D28D9 !important;
        border-radius: 8px !important;
        padding: 7px 11px !important;
        font-size: .72rem !important;
        font-weight: 700 !important;
        transition: .2s !important;
    }

    .dt-button:hover {
        background: #F5F3FF !important;
        border-color: #DDD6FE !important;
        color: #5B21B6 !important;
    }

    /* =========================================================
       EMPTY
    ========================================================= */
    .me-empty {
        padding: 45px 20px !important;
        text-align: center;
        color: #9CA3AF;
    }

    .me-empty i {
        display: block;
        font-size: 2rem;
        margin-bottom: 10px;
        color: #C4B5FD;
    }

    /* =========================================================
       RESPONSIVE
    ========================================================= */
    @media (max-width: 767px) {

        .me-dash-header {
            padding: 20px;
        }

        .me-total-num {
            font-size: 1.8rem;
        }

        .activation-dt-top .row > div,
        .activation-dt-bottom .row > div {
            margin-bottom: 10px;
        }

        .activation-dt-wrapper .dataTables_filter input {
            width: 100% !important;
        }

        .activation-dt-wrapper .dataTables_paginate {
            white-space: normal;
        }

        .dt-buttons {
            margin-bottom: 8px;
        }
    }
</style>


{{-- =========================================================
     HEADER
========================================================= --}}
<div class="me-dash-header animate__animated animate__fadeIn">

    <div class="d-flex justify-content-between align-items-center flex-wrap"
         style="position:relative; z-index:1;">

        <div>
            <h4 class="mb-0">
                <i class="fas fa-chart-pie mr-2"></i>
                Activation Dashboard
            </h4>

            <small>
                Manual Entry
                <i class="fas fa-angle-right mx-1"></i>
                Activation
            </small>
        </div>

        @can('manual_activation_create')

            <a href="{{ route('admin.manual-activations.create') }}"
               class="me-btn">

                <i class="fas fa-plus mr-1"></i>
                New Activation

            </a>

        @endcan

    </div>

</div>


{{-- =========================================================
     STATUS
========================================================= --}}
@if(session('status'))

    <div class="alert alert-success border-0 shadow-sm">

        <i class="fas fa-check-circle mr-1"></i>

        {{ session('status') }}

    </div>

@endif


{{-- =========================================================
     KPI
========================================================= --}}
<div class="row mb-3" id="kpiStrip">

    <div class="col-md-3 col-6 mb-3">

        <div class="me-kpi">

            <div class="icon">
                <i class="fas fa-bolt"></i>
            </div>

            <div class="num" id="kpiTotal">
                0
            </div>

            <div class="lbl">
                Total Activations
            </div>

        </div>

    </div>


    <div class="col-md-3 col-6 mb-3">

        <div class="me-kpi">

            <div class="icon">
                <i class="fas fa-user-tie"></i>
            </div>

            <div class="num" id="kpiParties">
                -
            </div>

            <div class="lbl">
                Parties Involved
            </div>

        </div>

    </div>


    <div class="col-md-3 col-6 mb-3">

        <div class="me-kpi">

            <div class="icon">
                <i class="fas fa-box"></i>
            </div>

            <div class="num" id="kpiProducts">
                -
            </div>

            <div class="lbl">
                Products Involved
            </div>

        </div>

    </div>


    <div class="col-md-3 col-6 mb-3">

        <div class="me-kpi">

            <div class="icon">
                <i class="fas fa-map-marker-alt"></i>
            </div>

            <div class="num" id="kpiStates">
                -
            </div>

            <div class="lbl">
                States Covered
            </div>

        </div>

    </div>

</div>


{{-- =========================================================
     FILTERS
========================================================= --}}
<div class="me-filter-bar">

    <form id="filterForm"
          class="row g-2 align-items-end">

        {{-- PARTY --}}
        <div class="col-lg col-md-4 col-6 mb-2">

            <span class="me-filter-label">
                Party
            </span>

            <select name="manual_party_id"
                    class="form-select form-select-sm">

                <option value="">
                    All Parties
                </option>

                @foreach($parties as $party)

                    <option value="{{ $party->id }}">
                        {{ $party->name }}
                    </option>

                @endforeach

            </select>

        </div>


        {{-- FITTER --}}
        <div class="col-lg col-md-4 col-6 mb-2">

            <span class="me-filter-label">
                Fitter
            </span>

            <select name="manual_fitter_id"
                    class="form-select form-select-sm">

                <option value="">
                    All Fitters
                </option>

                @foreach($fitters as $fitter)

                    <option value="{{ $fitter->id }}">
                        {{ $fitter->name }}
                    </option>

                @endforeach

            </select>

        </div>


        {{-- PRODUCT --}}
        <div class="col-lg col-md-4 col-6 mb-2">

            <span class="me-filter-label">
                Product
            </span>

            <select name="manual_product_id"
                    class="form-select form-select-sm">

                <option value="">
                    All Products
                </option>

                @foreach($products as $product)

                    <option value="{{ $product->id }}">
                        {{ $product->name }}
                    </option>

                @endforeach

            </select>

        </div>


        {{-- STATE --}}
        <div class="col-lg col-md-4 col-6 mb-2">

            <span class="me-filter-label">
                State
            </span>

            <select name="state"
                    id="filterState"
                    class="form-select form-select-sm">

                <option value="">
                    All States
                </option>

                @foreach($states as $state)

                    <option value="{{ $state }}">
                        {{ $state }}
                    </option>

                @endforeach

            </select>

        </div>


        {{-- DISTRICT --}}
        <div class="col-lg col-md-4 col-6 mb-2">

            <span class="me-filter-label">
                District
            </span>

            <select name="district"
                    id="filterDistrict"
                    class="form-select form-select-sm"
                    disabled>

                <option value="">
                    All Districts
                </option>

            </select>

        </div>


        {{-- CITY --}}
        <div class="col-lg col-md-4 col-6 mb-2">

            <span class="me-filter-label">
                City
            </span>

            <select name="city"
                    id="filterCity"
                    class="form-select form-select-sm"
                    disabled>

                <option value="">
                    All Cities
                </option>

            </select>

        </div>


        {{-- DATE RANGE --}}
        <div class="col-lg col-md-4 col-6 mb-2">

            <span class="me-filter-label">
                Date Range
            </span>

            <select name="range"
                    class="form-select form-select-sm"
                    id="rangeSelect">

                <option value="all" selected>
                    All Time
                </option>

                <option value="today">
                    Today
                </option>

                <option value="this_week">
                    This Week
                </option>

                <option value="this_month">
                    This Month
                </option>

                <option value="3_month">
                    Last 3 Months
                </option>

                <option value="6_month">
                    Last 6 Months
                </option>

                <option value="this_year">
                    This Year
                </option>

                <option value="custom">
                    Custom Date
                </option>

            </select>

        </div>


        {{-- FROM --}}
        <div class="col-lg col-md-4 col-6 mb-2 custom-range-field"
             style="display:none;">

            <span class="me-filter-label">
                From Date
            </span>

            <input type="date"
                   name="from_date"
                   class="form-control form-control-sm">

        </div>


        {{-- TO --}}
        <div class="col-lg col-md-4 col-6 mb-2 custom-range-field"
             style="display:none;">

            <span class="me-filter-label">
                To Date
            </span>

            <input type="date"
                   name="to_date"
                   class="form-control form-control-sm">

        </div>


        {{-- APPLY --}}
        <div class="col-lg-auto col-6 mb-2">

            <button type="submit"
                    class="me-btn btn-sm w-100"
                    style="background:linear-gradient(135deg,#7C3AED,#6366F1);border:none;">

                <i class="fas fa-filter mr-1"></i>
                Apply

            </button>

        </div>


        {{-- RESET --}}
        <div class="col-lg-auto col-6 mb-2">

            <button type="button"
                    id="resetDashboardFilters"
                    class="btn btn-light btn-sm w-100"
                    style="border-radius:10px;">

                <i class="fas fa-redo-alt mr-1"></i>
                Reset

            </button>

        </div>

    </form>

</div>


{{-- =========================================================
     CHART
========================================================= --}}
<div class="row">

    <div class="col-lg-12 mb-3">

        <div class="me-total-card">

            <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-3">

                <div>

                    <div class="me-total-lbl">
                        Total Activations
                    </div>

                    <div class="me-total-num"
                         id="bigTotal">
                        0
                    </div>

                </div>


                <div class="me-tabs"
                     id="chartTypeTabs">

                    <button type="button"
                            class="me-tab active"
                            data-chart="pie">

                        <i class="fas fa-chart-pie mr-1"></i>
                        Pie

                    </button>


                    <button type="button"
                            class="me-tab"
                            data-chart="doughnut">

                        <i class="fas fa-circle-notch mr-1"></i>
                        Donut

                    </button>


                    <button type="button"
                            class="me-tab"
                            data-chart="bar">

                        <i class="fas fa-chart-bar mr-1"></i>
                        Bar

                    </button>


                    <button type="button"
                            class="me-tab"
                            data-chart="line">

                        <i class="fas fa-wave-square mr-1"></i>
                        Wave

                    </button>


                    <button type="button"
                            class="me-tab"
                            data-chart="radar">

                        <i class="fas fa-braille mr-1"></i>
                        Radar

                    </button>

                </div>

            </div>


            <div class="mb-3">

                <strong class="text-muted small d-block mb-2">
                    GROUP BY
                </strong>

                <span class="me-group-chip active"
                      data-group="product">
                    Product
                </span>

                <span class="me-group-chip"
                      data-group="party">
                    Party
                </span>

                <span class="me-group-chip"
                      data-group="fitter">
                    Fitter
                </span>

                <span class="me-group-chip"
                      data-group="state">
                    State
                </span>

                <span class="me-group-chip"
                      data-group="district">
                    District
                </span>

                <span class="me-group-chip"
                      data-group="city">
                    City
                </span>

            </div>


            <div class="row">

                <div class="col-lg-5 d-flex align-items-center justify-content-center mb-3 mb-lg-0">

                    <canvas id="chartCanvas"></canvas>

                </div>


                <div class="col-lg-7">

                    <div id="rankList"
                         style="max-height:340px;overflow-y:auto;">

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>


{{-- =========================================================
     ALL ACTIVATIONS DATA TABLE
========================================================= --}}
<div class="card me-chart-card mt-3">

    <div class="me-table-header">

        <div class="d-flex justify-content-between align-items-center flex-wrap">

            <div class="me-table-title">

                <div class="me-table-title-icon">
                    <i class="fas fa-list-alt"></i>
                </div>

                <div>

                    <strong>
                        ALL ACTIVATIONS
                    </strong>

                    <small>
                        Search, sort, filter, export & manage all activation records
                    </small>

                </div>

            </div>


            <div class="mt-2 mt-md-0">

                <span class="badge badge-light"
                      style="border:1px solid #E9E5FC;padding:7px 11px;border-radius:10px;color:#6D28D9;">

                    <i class="fas fa-database mr-1"></i>
                    Activation Records

                </span>

            </div>

        </div>

    </div>


    <div class="activation-dt-wrapper">

        <div class="table-responsive">

            <table id="allActivationsTable"
                   class="table me-table w-100">

                <thead>

                    <tr>

                        <th>#</th>

                        <th>
                            Fitting Date
                        </th>

                        <th>
                            Created At
                        </th>

                        <th>
                            Party
                        </th>

                        <th>
                            Fitter
                        </th>

                        <th>
                            Product
                        </th>

                        <th>
                            Customer
                        </th>

                        <th>
                            Vehicle No.
                        </th>

                        <th>
                            Created By
                        </th>

                        <th class="text-right">
                            Action
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @forelse($activations as $activation)

                        <tr>

                            {{-- ID --}}
                            <td data-order="{{ $activation->id }}">

                                <span class="font-weight-bold text-muted">

                                    #{{ $activation->id }}

                                </span>

                            </td>


                            {{-- FITTING DATE --}}
                            <td data-order="{{ $activation->fitting_date }}">

                                <div class="me-date-main">

                                    {{ $activation->fitting_date }}

                                </div>

                            </td>


                            {{-- CREATED AT --}}
                            <td data-order="{{ optional($activation->created_at)->timestamp ?? 0 }}">

                                @if($activation->created_at)

                                    <div class="me-date-main">

                                        {{ $activation->created_at->format('d M Y') }}

                                    </div>

                                    <div class="me-date-sub">

                                        {{ $activation->created_at->format('h:i A') }}

                                    </div>

                                @else

                                    -

                                @endif

                            </td>


                            {{-- PARTY --}}
                            <td>

                                <div class="me-party">

                                    <span class="me-party-icon">

                                        <i class="fas fa-landmark"></i>

                                    </span>

                                    <span>

                                        {{ optional($activation->party)->name ?: '-' }}

                                    </span>

                                </div>

                            </td>


                            {{-- FITTER --}}
                            <td>

                                @if(optional($activation->fitter)->name)

                                    <span class="me-location-badge">

                                        <i class="fas fa-user-hard-hat text-primary"></i>

                                        {{ optional($activation->fitter)->name }}

                                    </span>

                                @else

                                    <span class="text-muted">
                                        -
                                    </span>

                                @endif

                            </td>


                            {{-- PRODUCT --}}
                            <td>

                                @if(optional($activation->product)->name)

                                    <span class="badge badge-light"
                                          style="padding:6px 9px;border-radius:8px;color:#5B21B6;background:#F5F3FF;">

                                        <i class="fas fa-box mr-1"></i>

                                        {{ optional($activation->product)->name }}

                                    </span>

                                @else

                                    -

                                @endif

                            </td>


                            {{-- CUSTOMER --}}
                            <td>

                                <div style="font-weight:600;color:#374151;">

                                    <i class="fas fa-user mr-1 text-muted"></i>

                                    {{ $activation->customer_name ?: '-' }}

                                </div>

                            </td>


                            {{-- VEHICLE --}}
                            <td>

                                @if($activation->vehicle_number)

                                    <span class="badge badge-dark"
                                          style="font-size:.72rem;padding:6px 9px;border-radius:7px;">

                                        <i class="fas fa-car mr-1"></i>

                                        {{ $activation->vehicle_number }}

                                    </span>

                                @else

                                    -

                                @endif

                            </td>


                            {{-- CREATED BY --}}
                            <td>

                                @if(optional($activation->user)->name)

                                    <span class="text-muted font-weight-600">

                                        <i class="fas fa-user-circle mr-1"></i>

                                        {{ optional($activation->user)->name }}

                                    </span>

                                @else

                                    -

                                @endif

                            </td>


                            {{-- ACTION --}}
                            <td>

                                <div class="me-action-wrap">

                                    @can('manual_activation_show')

                                        <a href="{{ route('admin.manual-activations.show', $activation->id) }}"
                                           class="btn btn-sm btn-outline-info me-action-btn"
                                           title="View Activation"
                                           data-toggle="tooltip">

                                            <i class="fas fa-eye"></i>

                                        </a>

                                    @endcan


                                    @can('manual_activation_edit')

                                        <a href="{{ route('admin.manual-activations.edit', $activation->id) }}"
                                           class="btn btn-sm btn-outline-primary me-action-btn"
                                           title="Edit Activation"
                                           data-toggle="tooltip">

                                            <i class="fas fa-edit"></i>

                                        </a>

                                    @endcan


                                    @can('manual_activation_delete')

                                        <form action="{{ route('admin.manual-activations.destroy', $activation->id) }}"
                                              method="POST"
                                              class="d-inline"
                                              onsubmit="return confirm('Pakka delete karna hai?')">

                                            @csrf

                                            @method('DELETE')

                                            <button type="submit"
                                                    class="btn btn-sm btn-outline-danger me-action-btn"
                                                    title="Delete Activation"
                                                    data-toggle="tooltip">

                                                <i class="fas fa-trash"></i>

                                            </button>

                                        </form>

                                    @endcan

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="10"
                                class="me-empty">

                                <i class="fas fa-inbox"></i>

                                <strong>
                                    Koi activation nahi mila.
                                </strong>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>


{{-- =========================================================
     DATATABLE CSS
========================================================= --}}
<link rel="stylesheet"
      href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">

<link rel="stylesheet"
      href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">

<link rel="stylesheet"
      href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">


{{-- =========================================================
     DATATABLE JS
========================================================= --}}
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>

<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>

<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.colVis.min.js"></script>

<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>


<script>

(function () {

    'use strict';


    /* =========================================================
       DATATABLE
    ========================================================= */

    let activationTable = null;


    function initializeActivationTable() {

        if ($.fn.DataTable.isDataTable('#allActivationsTable')) {

            $('#allActivationsTable')
                .DataTable()
                .destroy();

        }


        activationTable = $('#allActivationsTable').DataTable({

            processing: true,

            responsive: false,

            autoWidth: false,

            pageLength: 10,

            lengthMenu: [
                [10, 25, 50, 100, 250, -1],
                [10, 25, 50, 100, 250, "All"]
            ],

            order: [
                [0, 'desc']
            ],

            columnDefs: [

                {
                    targets: 0,
                    type: 'num'
                },

                {
                    targets: 1,
                    type: 'date'
                },

                {
                    targets: 2,
                    type: 'date'
                },

                {
                    targets: 9,
                    orderable: false,
                    searchable: false
                }

            ],


            dom:

                '<"activation-dt-top"' +

                    '<"row align-items-center"' +

                        '<"col-xl-3 col-lg-4 col-md-5 mb-2 mb-md-0"l>' +

                        '<"col-xl-5 col-lg-4 col-md-7 mb-2 mb-md-0"B>' +

                        '<"col-xl-4 col-lg-4 col-md-12"f>' +

                    '>' +

                '>' +

                'rt' +

                '<"activation-dt-bottom"' +

                    '<"row align-items-center"' +

                        '<"col-md-6 mb-2 mb-md-0"i>' +

                        '<"col-md-6 text-md-right"p>' +

                    '>' +

                '>',


            buttons: [

                {
                    extend: 'copyHtml5',
                    text: '<i class="fas fa-copy mr-1"></i> Copy',
                    title: 'All Activations'
                },

                {
                    extend: 'excelHtml5',
                    text: '<i class="fas fa-file-excel mr-1"></i> Excel',
                    title: 'All Activations'
                },

                {
                    extend: 'csvHtml5',
                    text: '<i class="fas fa-file-csv mr-1"></i> CSV',
                    title: 'All Activations'
                },

                {
                    extend: 'pdfHtml5',
                    text: '<i class="fas fa-file-pdf mr-1"></i> PDF',
                    title: 'All Activations',
                    orientation: 'landscape',
                    pageSize: 'A4',
                    exportOptions: {
                        columns: ':not(:last-child)'
                    }
                },

                {
                    extend: 'print',
                    text: '<i class="fas fa-print mr-1"></i> Print',
                    title: 'All Activations',
                    exportOptions: {
                        columns: ':not(:last-child)'
                    }
                },

                {
                    extend: 'colvis',
                    text: '<i class="fas fa-columns mr-1"></i> Columns',
                    columns: ':not(:last-child)'
                }

            ],


            language: {

                search: '',

                searchPlaceholder:
                    'Search activation, party, fitter, product...',

                lengthMenu:
                    '_MENU_ entries',

                info:
                    'Showing _START_ – _END_ of _TOTAL_ activations',

                infoEmpty:
                    'No activations available',

                infoFiltered:
                    '(filtered from _MAX_ total)',

                zeroRecords:
                    '🔍 No matching activation found',

                emptyTable:
                    '📭 No activation records available',

                processing:
                    'Loading activations...',

                paginate: {

                    first: '«',

                    last: '»',

                    next: '›',

                    previous: '‹'

                }

            },


            drawCallback: function () {

                if (typeof $ !== 'undefined' &&
                    typeof $.fn.tooltip === 'function') {

                    $('[data-toggle="tooltip"]').tooltip();

                }

            }

        });


        /*
         * DataTable search styling
         */

        $('.activation-dt-wrapper .dataTables_filter input')
            .attr(
                'aria-label',
                'Search activations'
            );


        /*
         * Fix DataTable responsive width
         */

        setTimeout(function () {

            if (activationTable) {

                activationTable.columns.adjust();

            }

        }, 250);

    }


    initializeActivationTable();


    /* =========================================================
       CHART
    ========================================================= */

    const canvas =
        document.getElementById('chartCanvas');

    const ctx =
        canvas.getContext('2d');


    let chart = null;

    let currentGroup = 'product';

    let currentChartType = 'pie';


    const palette = [

        '#7C3AED',
        '#6366F1',
        '#F59E0B',
        '#10B981',
        '#3B82F6',
        '#F472B6',
        '#FB923C',
        '#34D399',
        '#60A5FA',
        '#FBBF24',
        '#A78BFA',
        '#818CF8'

    ];


    /* =========================================================
       LOCATION MAP
    ========================================================= */

    const locationMap =
        @json($locationMap);


    const stateSelect =
        document.getElementById('filterState');

    const districtSelect =
        document.getElementById('filterDistrict');

    const citySelect =
        document.getElementById('filterCity');


    stateSelect.addEventListener(
        'change',
        function () {

            districtSelect.innerHTML =
                '<option value="">All Districts</option>';

            citySelect.innerHTML =
                '<option value="">All Cities</option>';

            citySelect.disabled = true;


            if (
                !this.value ||
                !locationMap[this.value]
            ) {

                districtSelect.disabled = true;

                return;

            }


            Object.keys(
                locationMap[this.value]
            )
            .sort()
            .forEach(function (district) {

                districtSelect.innerHTML +=
                    '<option value="' +
                    district +
                    '">' +
                    district +
                    '</option>';

            });


            districtSelect.disabled = false;

        }
    );


    districtSelect.addEventListener(
        'change',
        function () {

            citySelect.innerHTML =
                '<option value="">All Cities</option>';


            const stateVal =
                stateSelect.value;


            if (
                !this.value ||
                !locationMap[stateVal] ||
                !locationMap[stateVal][this.value]
            ) {

                citySelect.disabled = true;

                return;

            }


            locationMap[stateVal][this.value]
                .slice()
                .sort()
                .forEach(function (city) {

                    citySelect.innerHTML +=
                        '<option value="' +
                        city +
                        '">' +
                        city +
                        '</option>';

                });


            citySelect.disabled = false;

        }
    );


    /* =========================================================
       CHART TYPE
    ========================================================= */

    document.querySelectorAll('.me-tab')
        .forEach(function (tab) {

            tab.addEventListener(
                'click',
                function () {

                    document
                        .querySelectorAll('.me-tab')
                        .forEach(function (t) {

                            t.classList.remove(
                                'active'
                            );

                        });


                    tab.classList.add('active');


                    currentChartType =
                        tab.dataset.chart;


                    loadData();

                }

            );

        });


    /* =========================================================
       GROUP BY
    ========================================================= */

    document.querySelectorAll('.me-group-chip')
        .forEach(function (chip) {

            chip.addEventListener(
                'click',
                function () {

                    document
                        .querySelectorAll('.me-group-chip')
                        .forEach(function (c) {

                            c.classList.remove(
                                'active'
                            );

                        });


                    chip.classList.add('active');


                    currentGroup =
                        chip.dataset.group;


                    loadData();

                }

            );

        });


    /* =========================================================
       CHART BUILDER
    ========================================================= */

    function buildChart(
        labels,
        values
    ) {

        if (chart) {

            chart.destroy();

        }


        const isXY =
            ['bar','line','radar']
                .includes(currentChartType);


        chart = new Chart(
            ctx,
            {

                type: currentChartType,

                data: {

                    labels: labels,

                    datasets: [

                        {

                            label:
                                'Activations',

                            data:
                                values,

                            backgroundColor:
                                isXY
                                    ? 'rgba(124,58,237,.35)'
                                    : palette,

                            borderColor:
                                isXY
                                    ? '#7C3AED'
                                    : '#fff',

                            borderWidth: 2,

                            fill:
                                currentChartType === 'line',

                            tension: .4,

                            cutout:
                                currentChartType === 'doughnut'
                                    ? '68%'
                                    : undefined

                        }

                    ]

                },


                options: {

                    responsive: true,

                    animation: {

                        duration: 800,

                        easing: 'easeOutQuart'

                    },


                    plugins: {

                        legend: {

                            display: false

                        }

                    },


                    scales: isXY

                        ? {

                            y: {

                                beginAtZero: true,

                                ticks: {

                                    precision: 0

                                }

                            }

                        }

                        : {}

                }

            }

        );

    }


    /* =========================================================
       RANK LIST
    ========================================================= */

    function buildRankList(
        labels,
        values
    ) {

        const total =
            values.reduce(
                function (a,b) {

                    return a + b;

                },
                0
            ) || 1;


        const list =
            document.getElementById(
                'rankList'
            );


        const items =
            labels.map(
                function (label,i) {

                    return {

                        label: label,

                        value: values[i],

                        color:
                            palette[
                                i %
                                palette.length
                            ]

                    };

                }
            )
            .sort(
                function (a,b) {

                    return b.value - a.value;

                }
            );


        list.innerHTML =
            items.map(
                function (item) {

                    const pct =
                        (
                            item.value /
                            total *
                            100
                        ).toFixed(2);


                    return `

                        <div class="me-rank-row">

                            <div style="flex:1;">

                                <span
                                    class="me-rank-dot"
                                    style="background:${item.color};">
                                </span>

                                <span class="me-rank-name">
                                    ${item.label}
                                </span>

                                <div class="me-rank-bar-track">

                                    <div
                                        class="me-rank-bar-fill"
                                        style="
                                            width:${pct}%;
                                            background:${item.color};
                                        ">
                                    </div>

                                </div>

                            </div>

                            <div class="text-right ml-3">

                                <div class="me-rank-pct">
                                    ${pct}%
                                </div>

                                <div class="me-rank-sub">
                                    ${item.value} qty
                                </div>

                            </div>

                        </div>

                    `;

                }
            )
            .join('') ||

            '<div class="text-center text-muted py-4">' +
            'Koi data nahi mila.' +
            '</div>';

    }


    /* =========================================================
       LOAD CHART DATA
    ========================================================= */

    function loadData() {

        const form =
            document.getElementById(
                'filterForm'
            );


        const params =
            new URLSearchParams(
                new FormData(form)
            );


        params.set(
            'group_by',
            currentGroup
        );


        fetch(
            `{{ route('admin.manual-activations.chartData') }}?${params.toString()}`
        )

        .then(function (res) {

            if (!res.ok) {

                throw new Error(
                    'Chart data request failed'
                );

            }

            return res.json();

        })

        .then(function (data) {

            buildChart(
                data.labels || [],
                data.values || []
            );


            buildRankList(
                data.labels || [],
                data.values || []
            );


            document.getElementById(
                'kpiTotal'
            ).textContent =
                data.total || 0;


            document.getElementById(
                'bigTotal'
            ).textContent =
                data.total || 0;


            const table =
                Array.isArray(data.table)
                    ? data.table
                    : [];


            document.getElementById(
                'kpiParties'
            ).textContent =
                new Set(
                    table.map(
                        function (r) {

                            return r.party;

                        }
                    )
                ).size;


            document.getElementById(
                'kpiProducts'
            ).textContent =
                new Set(
                    table.map(
                        function (r) {

                            return r.product;

                        }
                    )
                ).size;


            document.getElementById(
                'kpiStates'
            ).textContent =
                new Set(
                    table.map(
                        function (r) {

                            return r.state;

                        }
                    )
                ).size;

        })

        .catch(function (error) {

            console.error(
                'Activation chart error:',
                error
            );

        });

    }


    /* =========================================================
       DATE RANGE
    ========================================================= */

    document
        .getElementById('rangeSelect')
        .addEventListener(
            'change',
            function () {

                document
                    .querySelectorAll(
                        '.custom-range-field'
                    )
                    .forEach(
                        function (field) {

                            field.style.display =
                                this.value === 'custom'
                                    ? 'block'
                                    : 'none';

                        }.bind(this)
                    );

            }
        );


    /* =========================================================
       APPLY FILTER
    ========================================================= */

    document
        .getElementById('filterForm')
        .addEventListener(
            'submit',
            function (e) {

                e.preventDefault();

                loadData();

            }
        );


    /* =========================================================
       RESET
    ========================================================= */

    document
        .getElementById(
            'resetDashboardFilters'
        )
        .addEventListener(
            'click',
            function () {

                document
                    .getElementById(
                        'filterForm'
                    )
                    .reset();


                districtSelect.innerHTML =
                    '<option value="">All Districts</option>';

                citySelect.innerHTML =
                    '<option value="">All Cities</option>';

                districtSelect.disabled = true;

                citySelect.disabled = true;


                document
                    .querySelectorAll(
                        '.custom-range-field'
                    )
                    .forEach(
                        function (field) {

                            field.style.display =
                                'none';

                        }
                    );


                loadData();

            }
        );


    /* =========================================================
       INITIAL LOAD
    ========================================================= */

    loadData();


})();
</script>

@endsection

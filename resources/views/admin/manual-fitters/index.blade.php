@extends('layouts.admin')

@section('content')

<style>
    /* =========================
       PAGE HEADER
    ========================== */
    .mf-page-header {
        background: linear-gradient(135deg, #7C3AED 0%, #6366F1 55%, #4F46E5 100%);
        border-radius: 20px;
        padding: 24px 28px;
        color: #fff;
        margin-bottom: 20px;
        box-shadow: 0 16px 40px rgba(99,53,237,.25);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
    }

    .mf-page-header h4 {
        font-weight: 700;
    }

    .mf-btn {
        background: rgba(255,255,255,.15);
        backdrop-filter: blur(6px);
        border: 1px solid rgba(255,255,255,.35);
        color: #fff;
        border-radius: 10px;
        padding: 9px 20px;
        font-weight: 600;
        transition: .2s;
    }

    .mf-btn:hover {
        background: #fff;
        color: #5B21B6;
    }

    /* =========================
       FILTER BAR
    ========================== */
    .mf-filter-bar {
        background: #fff;
        border-radius: 16px;
        padding: 16px;
        box-shadow: 0 8px 24px rgba(124,58,237,.08);
        margin-bottom: 20px;
        border: 1px solid #F1EEFE;
    }

    .mf-filter-bar .form-select,
    .mf-filter-bar .form-control {
        border-radius: 10px;
        border: 1px solid #E9E5FC;
        font-size: .85rem;
        font-weight: 500;
        color: #4B5563;
        padding: 9px 14px;
        background: #FBFAFF;
        transition: .2s;
    }

    .mf-filter-bar .form-select:focus,
    .mf-filter-bar .form-control:focus {
        border-color: #8B5CF6;
        box-shadow: 0 0 0 3px rgba(139,92,246,.10);
    }

    .mf-filter-label {
        font-size: .7rem;
        text-transform: uppercase;
        letter-spacing: .04em;
        color: #9CA3AF;
        font-weight: 700;
        margin-bottom: 4px;
        display: block;
    }

    /* =========================
       TABLE CARD
    ========================== */
    .mf-chart-card {
        border: none;
        border-radius: 18px;
        box-shadow: 0 8px 22px rgba(124,58,237,.08);
        overflow: hidden;
    }

    .mf-table-wrapper {
        padding: 0;
    }

    .mf-table {
        margin-bottom: 0 !important;
        border-collapse: separate;
        border-spacing: 0;
    }

    .mf-table thead th {
        background: #F5F3FF;
        color: #5B21B6;
        border: none;
        font-weight: 700;
        font-size: .74rem;
        text-transform: uppercase;
        letter-spacing: .04em;
        white-space: nowrap;
        padding: 14px 12px;
    }

    .mf-table tbody td {
        font-size: .84rem;
        vertical-align: middle;
        padding: 13px 12px;
        border-color: #F3F4F6;
        white-space: nowrap;
    }

    .mf-table tbody tr {
        transition: all .18s ease;
    }

    .mf-table tbody tr:hover {
        background: #FAF5FF;
        transform: scale(1.001);
    }

    /* =========================
       FITTER NAME
    ========================== */
    .mf-user {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .mf-avatar {
        width: 36px;
        height: 36px;
        border-radius: 11px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg,#7C3AED,#6366F1);
        color: #fff;
        font-weight: 700;
        font-size: .8rem;
        box-shadow: 0 5px 12px rgba(124,58,237,.20);
        flex-shrink: 0;
    }

    .mf-user-name {
        font-weight: 700;
        color: #374151;
    }

    .mf-user-id {
        font-size: .68rem;
        color: #9CA3AF;
    }

    /* =========================
       BADGES
    ========================== */
    .mf-badge-active,
    .mf-badge-inactive {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 5px 11px;
        border-radius: 20px;
        font-size: .7rem;
        font-weight: 700;
    }

    .mf-badge-active {
        background: #A7F3D0;
        color: #065F46;
    }

    .mf-badge-inactive {
        background: #FECACA;
        color: #991B1B;
    }

    .mf-status-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: currentColor;
    }

    /* =========================
       LOCATION
    ========================== */
    .mf-location {
        color: #4B5563;
        font-weight: 500;
    }

    .mf-location i {
        color: #8B5CF6;
        margin-right: 5px;
    }

    /* =========================
       ACTION BUTTONS
    ========================== */
    .mf-actions {
        display: flex;
        justify-content: flex-end;
        gap: 5px;
    }

    .mf-action-btn {
        width: 32px;
        height: 32px;
        border-radius: 9px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: .2s;
    }

    .mf-action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 12px rgba(0,0,0,.08);
    }

    /* =========================
       DATATABLE CUSTOM UI
    ========================== */
    .mf-dt-top {
        padding: 18px;
        border-bottom: 1px solid #F1EEFE;
        background: linear-gradient(180deg,#fff,#FCFBFF);
    }

    .mf-dt-bottom {
        padding: 15px 18px;
        border-top: 1px solid #F1EEFE;
        background: #FCFBFF;
    }

    .mf-dt-search {
        position: relative;
    }

    .mf-dt-search i {
        position: absolute;
        left: 13px;
        top: 50%;
        transform: translateY(-50%);
        color: #9CA3AF;
        z-index: 2;
    }

    .mf-dt-search input {
        width: 100%;
        height: 40px;
        border: 1px solid #E9E5FC;
        border-radius: 11px;
        padding: 8px 12px 8px 38px;
        background: #FBFAFF;
        outline: none;
        font-size: .82rem;
        transition: .2s;
    }

    .mf-dt-search input:focus {
        border-color: #8B5CF6;
        box-shadow: 0 0 0 3px rgba(139,92,246,.10);
        background: #fff;
    }

    .mf-dt-length {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: .78rem;
        color: #6B7280;
        font-weight: 600;
    }

    .mf-dt-length select {
        border: 1px solid #E9E5FC;
        border-radius: 9px;
        padding: 6px 30px 6px 10px;
        background: #FBFAFF;
        color: #4B5563;
        outline: none;
    }

    .mf-dt-info {
        color: #9CA3AF;
        font-size: .76rem;
        font-weight: 600;
    }

    /* Pagination */
    .dataTables_wrapper .dataTables_paginate {
        margin-top: 0 !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button {
        min-width: 34px;
        height: 34px;
        line-height: 20px !important;
        padding: 6px 9px !important;
        margin: 0 2px;
        border: 1px solid #E9E5FC !important;
        border-radius: 9px !important;
        background: #fff !important;
        color: #6B7280 !important;
        font-size: .75rem;
        font-weight: 700;
        transition: .2s;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: #F5F3FF !important;
        color: #6D28D9 !important;
        border-color: #DDD6FE !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: linear-gradient(135deg,#7C3AED,#6366F1) !important;
        color: #fff !important;
        border-color: transparent !important;
        box-shadow: 0 5px 12px rgba(124,58,237,.20);
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
        opacity: .4;
    }

    /* Sorting */
    table.dataTable thead th {
        position: relative;
    }

    table.dataTable thead .sorting:before,
    table.dataTable thead .sorting:after,
    table.dataTable thead .sorting_asc:before,
    table.dataTable thead .sorting_asc:after,
    table.dataTable thead .sorting_desc:before,
    table.dataTable thead .sorting_desc:after {
        color: #8B5CF6 !important;
    }

    /* Mobile */
    @media(max-width: 767px) {
        .mf-page-header {
            padding: 20px;
        }

        .mf-page-header h4 {
            font-size: 1rem;
        }

        .mf-dt-top .row,
        .mf-dt-bottom .row {
            gap: 12px;
        }

        .mf-dt-search {
            margin-top: 5px;
        }

        .mf-dt-bottom {
            text-align: center;
        }

        .mf-dt-bottom .dataTables_info {
            margin-bottom: 10px;
        }
    }
</style>


{{-- =========================
     HEADER
========================= --}}
<div class="mf-page-header animate__animated animate__fadeIn">

    <div>
        <h4 class="mb-0">
            <i class="fas fa-user-hard-hat mr-2"></i>
            Fitters
        </h4>

        <small>
            Manual Entry
            <i class="fas fa-angle-right mx-1"></i>
            Fitter Management
        </small>
    </div>

    @can('manual_fitter_create')
        <a href="{{ route('admin.manual-fitters.create') }}"
           class="mf-btn">
            <i class="fas fa-plus mr-1"></i>
            New Fitter
        </a>
    @endcan

</div>


{{-- =========================
     SUCCESS MESSAGE
========================= --}}
@if(session('status'))
    <div class="alert alert-success border-0 shadow-sm">
        <i class="fas fa-check-circle mr-1"></i>
        {{ session('status') }}
    </div>
@endif


{{-- =========================
     SERVER SIDE FILTER
========================= --}}
<div class="mf-filter-bar">

    <form method="GET"
          action="{{ route('admin.manual-fitters.index') }}"
          class="row g-2 align-items-end">

        <div class="col-lg col-md-4 col-6 mb-2">

            <span class="mf-filter-label">
                Search
            </span>

            <input type="text"
                   name="search"
                   class="form-control form-control-sm"
                   placeholder="Name or Phone"
                   value="{{ request('search') }}">

        </div>


        <div class="col-lg col-md-4 col-6 mb-2">

            <span class="mf-filter-label">
                State
            </span>

            <select name="state"
                    class="form-select form-select-sm">

                <option value="">
                    All States
                </option>

                @foreach($states as $state)

                    <option value="{{ $state }}"
                        {{ request('state') == $state ? 'selected' : '' }}>
                        {{ $state }}
                    </option>

                @endforeach

            </select>

        </div>


        <div class="col-lg col-md-4 col-6 mb-2">

            <span class="mf-filter-label">
                Status
            </span>

            <select name="status"
                    class="form-select form-select-sm">

                <option value="">
                    All
                </option>

                <option value="active"
                    {{ request('status') == 'active' ? 'selected' : '' }}>
                    Active
                </option>

                <option value="inactive"
                    {{ request('status') == 'inactive' ? 'selected' : '' }}>
                    Inactive
                </option>

            </select>

        </div>


        <div class="col-lg-auto col-6 mb-2">

            <button type="submit"
                    class="mf-btn btn-sm w-100"
                    style="background:linear-gradient(135deg,#7C3AED,#6366F1);border:none;">

                <i class="fas fa-filter mr-1"></i>
                Apply

            </button>

        </div>


        <div class="col-lg-auto col-6 mb-2">

            <a href="{{ route('admin.manual-fitters.index') }}"
               class="btn btn-light btn-sm w-100"
               style="border-radius:10px;">

                <i class="fas fa-redo-alt mr-1"></i>
                Reset

            </a>

        </div>

    </form>

</div>


{{-- =========================
     TABLE
========================= --}}
<div class="card mf-chart-card">

    <div class="card-body p-0">

        <div class="table-responsive mf-table-wrapper">

            <table id="fittersDataTable"
                   class="table mf-table">

                <thead>

                    <tr>

                        <th>#</th>

                        <th>Fitter</th>

                        <th>Phone</th>

                        <th>State</th>

                        <th>District</th>

                        <th>City</th>

                        <th>Pincode</th>

                        <th>Status</th>

                        <th class="text-right">
                            Action
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @foreach($fitters as $fitter)

                        <tr>

                            {{-- ID --}}
                            <td>
                                <span class="text-muted font-weight-bold">
                                    {{ $fitter->id }}
                                </span>
                            </td>


                            {{-- NAME --}}
                            <td>

                                <div class="mf-user">

                                    <div class="mf-avatar">

                                        {{ strtoupper(substr($fitter->name, 0, 1)) }}

                                    </div>

                                    <div>

                                        <div class="mf-user-name">
                                            {{ $fitter->name }}
                                        </div>

                                        <div class="mf-user-id">
                                            FITTER #{{ $fitter->id }}
                                        </div>

                                    </div>

                                </div>

                            </td>


                            {{-- PHONE --}}
                            <td>

                                <span class="font-weight-600">

                                    <i class="fas fa-phone-alt text-success mr-1"></i>

                                    {{ $fitter->phone }}

                                </span>

                            </td>


                            {{-- STATE --}}
                            <td>

                                <span class="mf-location">

                                    <i class="fas fa-map-marker-alt"></i>

                                    {{ $fitter->state ?: '-' }}

                                </span>

                            </td>


                            {{-- DISTRICT --}}
                            <td>

                                <span class="mf-location">

                                    {{ $fitter->district ?: '-' }}

                                </span>

                            </td>


                            {{-- CITY --}}
                            <td>

                                <span class="mf-location">

                                    {{ $fitter->city ?: '-' }}

                                </span>

                            </td>


                            {{-- PINCODE --}}
                            <td>

                                <span class="badge badge-light px-2 py-1">

                                    {{ $fitter->pincode ?: '-' }}

                                </span>

                            </td>


                            {{-- STATUS --}}
                            <td>

                                @if($fitter->status == 'active')

                                    <span class="mf-badge-active">

                                        <span class="mf-status-dot"></span>

                                        Active

                                    </span>

                                @else

                                    <span class="mf-badge-inactive">

                                        <span class="mf-status-dot"></span>

                                        Inactive

                                    </span>

                                @endif

                            </td>


                            {{-- ACTION --}}
                            <td>

                                <div class="mf-actions">

                                    @can('manual_fitter_show')

                                        <a href="{{ route('admin.manual-fitters.show', $fitter->id) }}"
                                           class="btn btn-sm btn-outline-info mf-action-btn"
                                           title="View">

                                            <i class="fas fa-eye"></i>

                                        </a>

                                    @endcan


                                    @can('manual_fitter_edit')

                                        <a href="{{ route('admin.manual-fitters.edit', $fitter->id) }}"
                                           class="btn btn-sm btn-outline-primary mf-action-btn"
                                           title="Edit">

                                            <i class="fas fa-edit"></i>

                                        </a>

                                    @endcan


                                    @can('manual_fitter_delete')

                                        <form action="{{ route('admin.manual-fitters.destroy', $fitter->id) }}"
                                              method="POST"
                                              class="d-inline"
                                              onsubmit="return confirm('Pakka delete karna hai?')">

                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                    class="btn btn-sm btn-outline-danger mf-action-btn"
                                                    title="Delete">

                                                <i class="fas fa-trash"></i>

                                            </button>

                                        </form>

                                    @endcan

                                </div>

                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>

        </div>

    </div>

</div>


{{-- =========================
     DATATABLE CDN
========================= --}}
<link rel="stylesheet"
      href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">

<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>


<script>

$(document).ready(function () {

    $('#fittersDataTable').DataTable({

        // Default rows
        pageLength: 10,

        // Available rows
        lengthMenu: [
            [10, 25, 50, 100, -1],
            [10, 25, 50, 100, "All"]
        ],

        // Default sorting
        order: [
            [0, 'desc']
        ],

        // Disable sorting on action column
        columnDefs: [
            {
                orderable: false,
                searchable: false,
                targets: 8
            }
        ],

        // Custom layout
        dom:
            '<"mf-dt-top"' +
                '<"row align-items-center"' +
                    '<"col-lg-5 col-md-5"l>' +
                    '<"col-lg-7 col-md-7"f>' +
                '>' +
            '>' +

            'rt' +

            '<"mf-dt-bottom"' +
                '<"row align-items-center"' +
                    '<"col-md-6"i>' +
                    '<"col-md-6 text-md-right"p>' +
                '>' +
            '>',

        language: {

            search: "",

            searchPlaceholder: "Search fitter, phone, city...",

            lengthMenu: "_MENU_",

            info: "Showing _START_ – _END_ of _TOTAL_ fitters",

            infoEmpty: "No fitters available",

            zeroRecords:
                "🔍 No matching fitter found",

            emptyTable:
                "👷 No fitter available",

            paginate: {

                first: "«",

                last: "»",

                next: "›",

                previous: "‹"

            }

        },

        initComplete: function () {

            // Search wrapper
            $('.dataTables_filter').addClass('mf-dt-search');

            $('.dataTables_filter label').contents().filter(function () {
                return this.nodeType === 3;
            }).remove();

            // Add search icon
            $('.dataTables_filter').prepend(
                '<i class="fas fa-search"></i>'
            );

            // Length styling
            $('.dataTables_length').addClass('mf-dt-length');

            // Info styling
            $('.dataTables_info').addClass('mf-dt-info');

        }

    });

});

</script>

@endsection

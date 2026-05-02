@extends('layouts.admin')
@section('content')

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<style>
    .gps-dashboard {
        --gps-surface: linear-gradient(145deg, #0b1f34 0%, #132d48 55%, #0f1828 100%);
        --gps-card-border: rgba(84, 223, 255, 0.15);
        --gps-neon: #5ff3ff;
        --gps-accent: #ff9e43;
        --gps-soft: #94b7d4;
    }

    .gps-hero {
        position: relative;
        overflow: hidden;
        border: 1px solid var(--gps-card-border);
        border-radius: 24px;
        background: var(--gps-surface);
        box-shadow: 0 28px 60px rgba(7, 24, 46, 0.28);
        color: #fff;
        padding: 28px;
        margin-bottom: 22px;
    }

    .gps-hero::before,
    .gps-hero::after {
        content: "";
        position: absolute;
        inset: 0;
        pointer-events: none;
    }

    .gps-hero::before {
        background:
            linear-gradient(rgba(95, 243, 255, 0.07) 1px, transparent 1px),
            linear-gradient(90deg, rgba(95, 243, 255, 0.07) 1px, transparent 1px);
        background-size: 42px 42px;
        mask-image: linear-gradient(180deg, rgba(0, 0, 0, 0.8), transparent 90%);
    }

    .gps-hero::after {
        background:
            radial-gradient(circle at 15% 20%, rgba(255, 158, 67, 0.18), transparent 24%),
            radial-gradient(circle at 85% 15%, rgba(95, 243, 255, 0.2), transparent 20%),
            radial-gradient(circle at 70% 78%, rgba(255, 92, 141, 0.14), transparent 22%);
    }

    .gps-hero__eyebrow {
        color: var(--gps-neon);
        letter-spacing: 0.42rem;
        text-transform: uppercase;
        font-size: 0.74rem;
        margin-bottom: 8px;
    }

    .gps-hero__title {
        font-family: Bahnschrift, "Trebuchet MS", sans-serif;
        font-size: 2rem;
        font-weight: 700;
        letter-spacing: 0.06rem;
        margin-bottom: 8px;
    }

    .gps-hero__text {
        max-width: 620px;
        color: rgba(255, 255, 255, 0.72);
        margin-bottom: 0;
    }

    .gps-stat {
        position: relative;
        border-radius: 20px;
        padding: 20px 22px;
        min-height: 142px;
        border: 1px solid rgba(255, 255, 255, 0.08);
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.08), rgba(7, 17, 31, 0.4));
        color: #fff;
        overflow: hidden;
        box-shadow: 0 18px 36px rgba(8, 20, 38, 0.18);
    }

    .gps-stat::after {
        content: "";
        position: absolute;
        width: 110px;
        height: 110px;
        right: -22px;
        bottom: -28px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.06);
    }

    .gps-stat__label {
        font-size: 0.78rem;
        letter-spacing: 0.22rem;
        text-transform: uppercase;
        opacity: 0.82;
    }

    .gps-stat__value {
        font-family: Bahnschrift, "Trebuchet MS", sans-serif;
        font-size: 2rem;
        font-weight: 700;
        margin: 14px 0 10px;
    }

    .gps-stat__meta {
        color: rgba(255, 255, 255, 0.72);
        font-size: 0.92rem;
    }

    .gps-table-card {
        border: 0;
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 24px 50px rgba(15, 31, 54, 0.1);
    }

    .gps-table-card .card-header {
        border-bottom: 0;
        padding: 20px 24px;
        background: linear-gradient(90deg, #0f2f4f, #12335f 48%, #1b5770);
        color: #fff;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 14px;
    }

    .gps-table-card .card-body {
        padding: 22px;
        background: linear-gradient(180deg, #fbfdff, #eef4fb);
    }

    .gps-table-card .table thead th {
        border-top: 0;
        background: #e7f0f7;
        color: #12335f;
        font-size: 0.82rem;
        letter-spacing: 0.08rem;
        text-transform: uppercase;
    }

    .gps-table-card .table td {
        vertical-align: middle;
    }

    @media (max-width: 991px) {
        .gps-hero__title {
            font-size: 1.55rem;
        }
    }
</style>

<div class="gps-dashboard">
    <div class="gps-hero">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <div class="gps-hero__eyebrow">Track . Secure . Dispatch</div>
                <div class="gps-hero__title">GPS Smart Card Control Center</div>
                <p class="gps-hero__text">
                    Har GPS device ke saath unique 16-digit smart card issue kijiye, batch-wise generate kijiye, aur validity cycle ko one place se manage kijiye.
                </p>
            </div>
            <div class="col-lg-4 text-lg-right mt-3 mt-lg-0">
                <a href="{{ route('admin.gps-cards.create') }}" class="btn btn-warning btn-lg px-4">
                    <i class="fas fa-plus-circle mr-1"></i> Generate Cards
                </a>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-6 col-xl-3 mb-3">
            <div class="gps-stat" style="background: linear-gradient(145deg, #0e3150, #154367 65%, #1f5d84);">
                <div class="gps-stat__label">Total Cards</div>
                <div class="gps-stat__value">{{ number_format($stats['total']) }}</div>
                <div class="gps-stat__meta">System mein registered sabhi GPS smart cards.</div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3 mb-3">
            <div class="gps-stat" style="background: linear-gradient(145deg, #0a4e4b, #10706a 60%, #14a092);">
                <div class="gps-stat__label">Active Pool</div>
                <div class="gps-stat__value">{{ number_format($stats['active']) }}</div>
                <div class="gps-stat__meta">Current validity window ke andar active cards.</div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3 mb-3">
            <div class="gps-stat" style="background: linear-gradient(145deg, #5c2b1d, #9a4827 60%, #d97836);">
                <div class="gps-stat__label">Expired Cards</div>
                <div class="gps-stat__value">{{ number_format($stats['expired']) }}</div>
                <div class="gps-stat__meta">Jinki validity month already complete ho chuki hai.</div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3 mb-3">
            <div class="gps-stat" style="background: linear-gradient(145deg, #35235c, #5540a1 60%, #7a6cf0);">
                <div class="gps-stat__label">Batch Series</div>
                <div class="gps-stat__value">{{ number_format($stats['batches']) }}</div>
                <div class="gps-stat__meta">Bulk generation se bane distinct dispatch batches.</div>
            </div>
        </div>
    </div>

    <div class="card gps-table-card">
        <div class="card-header">
            <div>
                <strong>Issued GPS Smart Cards</strong>
                <div class="small text-white-50">Batch, model, validity, status aur operator details ek table view mein.</div>
            </div>
            <a href="{{ route('admin.gps-cards.create') }}" class="btn btn-light">
                <i class="fas fa-microchip mr-1"></i> New Batch
            </a>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-hover ajaxTable datatable datatable-GpsCard">
                <thead>
                    <tr>
                        <th width="10"></th>
                        <th>ID</th>
                        <th>Batch Code</th>
                        <th>Product Model</th>
                        <th>Card Number</th>
                        <th>Card Holder</th>
                        <th>Valid From</th>
                        <th>Valid To</th>
                        <th>Usage</th>
                        <th>Status</th>
                        <th>Print</th>
                        <th>Created By</th>
                        <th>&nbsp;</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

@endsection

@section('scripts')
@parent
<script>
    $(function () {
        let dtOverrideGlobals = {
            processing: true,
            serverSide: true,
            retrieve: true,
            aaSorting: [],
            ajax: "{{ route('admin.gps-cards.index') }}",
            columns: [
                { data: 'placeholder', name: 'placeholder', sortable: false, searchable: false },
                { data: 'id', name: 'gps_cards.id' },
                { data: 'batch_code', name: 'gps_cards.batch_code' },
                { data: 'product_model', name: 'product_models.product_model' },
                { data: 'card_number', name: 'gps_cards.card_number' },
                { data: 'card_holder_name', name: 'gps_cards.card_holder_name' },
                { data: 'valid_from', name: 'gps_cards.valid_from' },
                { data: 'valid_to', name: 'gps_cards.valid_to' },
                { data: 'usage_status', name: 'usage_status', orderable: false, searchable: false },
                { data: 'status', name: 'gps_cards.status' },
                { data: 'print_status', name: 'print_status', orderable: false, searchable: false },
                { data: 'created_by', name: 'creators.name' },
                { data: 'actions', name: 'actions', orderable: false, searchable: false }
            ],
            orderCellsTop: true,
            order: [[1, 'desc']],
            pageLength: 50
        };

        $('.datatable-GpsCard').DataTable(dtOverrideGlobals);
    });
</script>
@endsection

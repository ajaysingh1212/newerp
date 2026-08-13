@extends('layouts.admin')

@section('content')
@can('redeem_code_create')
<div style="margin-bottom: 10px;" class="row">
    <div class="col-lg-12">
        <a class="btn btn-success" href="{{ route('admin.redeem-codes.create') }}">
            Generate Redeem Code
        </a>
    </div>
</div>
@endcan

<style>
    .redeem-kpi { border-radius: 8px; padding: 16px; color: #fff; box-shadow: 0 8px 22px rgba(15, 23, 42, .12); }
    .redeem-kpi small { display:block; opacity:.82; font-weight:600; letter-spacing:.02em; }
    .redeem-code-pill { font-family: monospace; letter-spacing: .08em; font-weight: 800; background:#111827; color:#fff; padding:7px 10px; border-radius:6px; display:inline-block; }
    .status-chip { border-radius: 999px; padding: 5px 10px; font-size: 12px; font-weight: 700; }
    .chip-active { background:#dcfce7; color:#166534; }
    .chip-used { background:#fee2e2; color:#991b1b; }
    .chip-wait { background:#e0f2fe; color:#075985; }
    .chip-expired {
    background: #ffedd5;
    color: #c2410c;
    }
</style>

@php
    $totalCodes = $redeemCodes->count();
    $activeCodes = $redeemCodes->where('status', 'active')->where('use_status', 'not_used')->count();
    $usedCodes = $redeemCodes->where('use_status', 'used')->count();
@endphp

<div class="row mb-3">
    <div class="col-md-4 mb-2"><div class="redeem-kpi" style="background:#0f766e;"><small>Total Codes</small><h3 class="mb-0">{{ $totalCodes }}</h3></div></div>
    <div class="col-md-4 mb-2"><div class="redeem-kpi" style="background:#2563eb;"><small>Ready To Use</small><h3 class="mb-0">{{ $activeCodes }}</h3></div></div>
    <div class="col-md-4 mb-2"><div class="redeem-kpi" style="background:#7c2d12;"><small>Used Codes</small><h3 class="mb-0">{{ $usedCodes }}</h3></div></div>
</div>

<div class="card">
    <div class="card-header">Redeem Code List</div>
    <div class="card-body">
        @include('watermark')
        <table class="table table-bordered table-striped table-hover datatable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Code</th>
                    <th>Plan</th>
                    <th>Discount</th>
                    <th>Valid Up To</th>
                    <th>Status</th>
                    <th>Used By</th>
                    <th>Created By</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody>
                @foreach($redeemCodes as $redeemCode)
                    <tr>
                        <td>{{ $redeemCode->id }}</td>
                        <td><span class="redeem-code-pill">{{ $redeemCode->code }} <button type="button" class="copy-redeem-code " data-code="{{ $redeemCode->code }}" title="copy code"> <i class="fas fa-copy"></i></button></span></td>
                        <style>

                            .redeem-code-pill {
                                display: inline-flex;
                                align-items: center;
                                gap: 8px;
                                padding: 5px 10px;
                                border-radius: 6px;
                            }

                            .copy-redeem-code {
                                border: 0;
                                background: transparent;
                                padding: 0;
                                cursor: pointer;
                                color: #555;
                                font-size: 14px;
                            }

                            .copy-redeem-code:hover {
                                color: #007bff;
                            }

                        </style>
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                const copyButtons = document.querySelectorAll('.copy-redeem-code');
                                copyButtons.forEach(button => {
                                    button.addEventListener('click', function() {
                                        const code = this.getAttribute('data-code');
                                        navigator.clipboard.writeText(code).then(() => {
                                            alert('Redeem code copied to clipboard: ' + code);
                                        }).catch(err => {
                                            console.error('Failed to copy text: ', err);
                                        });
                                    });
                                });
                            });
                        </script>
                        <td>{{ $redeemCode->rechargePlan?->type }}<br><strong>{{ $redeemCode->rechargePlan?->plan_name }}</strong></td>
                        <td>
                            {{ $redeemCode->discount_type === 'percent' ? number_format($redeemCode->discount_value, 2) . '%' : 'Rs ' . number_format($redeemCode->discount_value, 2) }}
                            <br><small>Max: Rs {{ number_format($redeemCode->discount_amount, 2) }}</small>
                        </td>
                        <td>{{ $redeemCode->valid_up_to ? \Carbon\Carbon::parse($redeemCode->valid_up_to)->format('d-m-Y') : 'N/A' }}</td>
                        <td>
                            @php
                                $isUsed = $redeemCode->use_status === 'used';

                                $isExpired = !$isUsed
                                    && $redeemCode->valid_up_to
                                    && \Carbon\Carbon::parse($redeemCode->valid_up_to)->isPast();
                            @endphp

                            @if($isUsed)
                                <span class="status-chip chip-used">
                                    Used
                                </span>
                            @elseif($isExpired)
                                <span class="status-chip chip-expired">
                                    Expired
                                </span>
                            @else
                                <span class="status-chip chip-active">
                                    Active
                                </span>

                                <span class="status-chip chip-wait">
                                    Not Used
                                </span>
                            @endif
                        </td>
                        <td>
                            {{ $redeemCode->usedBy?->name ?? 'Not used' }}
                            @if($redeemCode->usedBy)
                                <br><small>{{ $redeemCode->usedBy?->roles?->pluck('title')->implode(', ') }}</small>
                            @endif
                        </td>
                        <td>{{ $redeemCode->creator_name }}<br><small>{{ $redeemCode->creator_role }}</small></td>
                        <td>
                            @can('redeem_code_show')
                                <a class="btn btn-xs btn-primary" href="{{ route('admin.redeem-codes.show', $redeemCode->id) }}">{{ trans('global.view') }}</a>
                            @endcan
                            @can('redeem_code_delete')
                                @if($redeemCode->use_status !== 'used')
                                    <form action="{{ route('admin.redeem-codes.destroy', $redeemCode->id) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <input type="submit" class="btn btn-xs btn-danger" value="{{ trans('global.delete') }}">
                                    </form>
                                @endif
                            @endcan
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
@parent
<script>
    $(function () {
        $('.datatable').DataTable({ pageLength: 100, order: [[0, 'desc']] });
    });
</script>
@endsection

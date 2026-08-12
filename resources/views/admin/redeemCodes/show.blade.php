@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-header">Redeem Code Details</div>
    <div class="card-body">
        @include('watermark')
        <table class="table table-bordered table-striped">
            <tbody>
                <tr><th>ID</th><td>{{ $redeemCode->id }}</td></tr>
                <tr><th>Code</th><td><strong style="font-family:monospace; letter-spacing:.08em;">{{ $redeemCode->code }}</strong></td></tr>
                <tr><th>Recharge Plan</th><td>{{ $redeemCode->rechargePlan?->type }} - {{ $redeemCode->rechargePlan?->plan_name }}</td></tr>
                <tr><th>Valid Up To</th><td>{{ $redeemCode->valid_up_to ? \Carbon\Carbon::parse($redeemCode->valid_up_to)->format('d-m-Y') : 'N/A' }}</td></tr>
                <tr><th>Discount</th><td>{{ $redeemCode->discount_type === 'percent' ? number_format($redeemCode->discount_value, 2) . '%' : 'Rs ' . number_format($redeemCode->discount_value, 2) }}</td></tr>
                <tr><th>Status</th><td>{{ ucfirst($redeemCode->status) }} / {{ str_replace('_', ' ', ucfirst($redeemCode->use_status)) }}</td></tr>
                <tr><th>Used By</th><td>{{ $redeemCode->usedBy?->name ?? 'Not used' }} @if($redeemCode->usedBy)<br>{{ $redeemCode->usedBy?->roles?->pluck('title')->implode(', ') }}@endif</td></tr>
                <tr><th>Used At</th><td>{{ $redeemCode->used_at ? \Carbon\Carbon::parse($redeemCode->used_at)->format('d-m-Y H:i') : 'N/A' }}</td></tr>
                <tr><th>Recharge Request</th><td>{{ $redeemCode->recharge_request_id ? '#' . $redeemCode->recharge_request_id : 'N/A' }}</td></tr>
                <tr><th>Created By</th><td>{{ $redeemCode->creator_name }}<br>{{ $redeemCode->creator_role }}</td></tr>
                <tr><th>Created At</th><td>{{ $redeemCode->created_at ? \Carbon\Carbon::parse($redeemCode->created_at)->format('d-m-Y H:i') : 'N/A' }}</td></tr>
            </tbody>
        </table>
        <a class="btn btn-default" href="{{ route('admin.redeem-codes.index') }}">{{ trans('global.back_to_list') }}</a>
    </div>
</div>
@endsection

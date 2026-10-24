@extends('admin.business-management.layout')
@section('business-title', 'Device profile')
@section('business-description', 'A complete view of the device, its party assignment and activation record.')
@section('business-icon', 'fa-microchip')
@section('business-content')
<div class="card"><div class="card-header"><div><h4><i class="fas fa-microchip bm-section-icon"></i>{{ $businessDevice->product?->name }}</h4><p class="bm-mono">{{ $businessDevice->vts_number }}</p></div><span class="bm-status {{ $businessDevice->status }}">{{ ucfirst($businessDevice->status) }}</span></div><div class="card-body">
    <table class="table" data-business-table="compact"><thead><tr><th>Field</th><th>Device information</th></tr></thead><tbody>
        <tr><td>Party</td><td>{{ $businessDevice->party?->name }}</td></tr>
        <tr><td>Location</td><td>{{ $businessDevice->party?->state }} / {{ $businessDevice->party?->district }} / {{ $businessDevice->party?->city }}</td></tr>
        <tr><td>Device / model</td><td>{{ $businessDevice->product?->name }}</td></tr>
        <tr><td>VTS number</td><td class="bm-mono">{{ $businessDevice->vts_number }}</td></tr>
        <tr><td>SIM number</td><td class="bm-mono">{{ $businessDevice->sim_number }}</td></tr>
        <tr><td>Sales price</td><td>{{ number_format($businessDevice->sales_price, 2) }}</td></tr>
        <tr><td>Assigned date</td><td>{{ $businessDevice->assigned_at->format('Y-m-d') }}</td></tr>
        <tr><td>Status</td><td>{{ ucfirst($businessDevice->status) }}</td></tr>
        <tr><td>Activated by</td><td>{{ $businessDevice->activator?->name ?? 'Awaiting activation' }}</td></tr>
        <tr><td>Activated at</td><td>{{ $businessDevice->activated_at?->format('Y-m-d H:i:s') ?? '—' }}</td></tr>
    </tbody></table>
    <div class="bm-form-footer"><a class="btn btn-light" href="{{ route('admin.business-management.index', ['party_id' => $businessDevice->manual_party_id]) }}"><i class="fas fa-arrow-left mr-1"></i> Back to inventory</a>@include('admin.business-management.actions', ['device' => $businessDevice])</div>
</div></div>
@endsection

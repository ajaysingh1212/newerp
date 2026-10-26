@extends('admin.business-management.layout')
@section('business-title', $businessDevice ? 'Edit device' : 'Add devices')
@section('business-description', 'Assign devices to a party, enter their details and watch the sales total update as you work.')
@section('business-icon', 'fa-plus-circle')
@section('business-content')
<div class="card">
    <div class="card-header"><div><h4><i class="fas fa-layer-group bm-section-icon"></i>{{ $businessDevice ? 'Device details' : 'Prepare your device allocation' }}</h4><p>Choose the party and quantity, then complete one row for each device.</p></div><span class="bm-status {{ $businessDevice?->status ?? 'pending' }}">{{ ucfirst($businessDevice?->status ?? 'pending') }}</span></div>
    <div class="card-body">
        <form id="device-form" method="POST" action="{{ $businessDevice ? route('admin.business-management.update', $businessDevice) : route('admin.business-management.store') }}">
            @csrf
            @if($businessDevice) @method('PUT') @endif
            <div class="form-row">
                <div class="form-group col-md-6"><label for="manual_party_id">Party</label><select required class="form-control select2" id="manual_party_id" name="manual_party_id"><option value="">Select party</option>@foreach($parties as $party)<option value="{{ $party->id }}" {{ (string)old('manual_party_id', $businessDevice?->manual_party_id ?? request('party_id')) === (string)$party->id ? 'selected' : '' }}>{{ $party->name }}</option>@endforeach</select></div>
                <div class="form-group col-md-3"><label for="assigned_at">Assigned date</label><input required class="form-control" type="date" id="assigned_at" name="assigned_at" value="{{ old('assigned_at', $businessDevice?->assigned_at?->format('Y-m-d') ?? now()->format('Y-m-d')) }}"></div>
                <div class="form-group col-md-3"><label for="quantity">Quantity (1–1000)</label><input required class="form-control" type="number" id="quantity" name="quantity" min="1" max="{{ $businessDevice ? 1 : 1000 }}" value="{{ old('quantity', 1) }}" {{ $businessDevice ? 'readonly' : '' }}></div>
            </div>
            @if($businessDevice)
                <div class="form-group"><label for="device-status">Device status</label><select required class="form-control" id="device-status" name="status"><option value="pending" {{ old('status', $businessDevice->status) === 'pending' ? 'selected' : '' }}>Pending</option>@if($businessDevice->status === 'activated' || Gate::allows('business_management_activate'))<option value="activated" {{ old('status', $businessDevice->status) === 'activated' ? 'selected' : '' }}>Activated</option>@endif</select><small class="form-text text-muted">Changing to Pending clears the activation user and time.</small></div>
            @else
                <div class="bm-note"><i class="fas fa-info-circle"></i><span>New devices are saved as Pending. Activate them from the inventory to record who activated each device and when.</span></div>
            @endif
            <table id="entry-table" class="table table-striped"><thead><tr><th>#</th><th>Device / model</th><th>VTS number</th><th>SIM number</th><th>Sales price</th><th>Status</th></tr></thead><tbody id="device-rows"></tbody></table>
            <div class="bm-total"><span>Total sales value · updates live</span><strong id="sales-total">0.00</strong></div>
            <div class="bm-form-footer"><a class="btn btn-light" href="{{ route('admin.business-management.index') }}">Cancel</a><button class="btn btn-primary"><i class="fas fa-check mr-1"></i> {{ $businessDevice ? 'Save changes' : 'Save devices' }}</button></div>
        </form>
    </div>
</div>
<template id="device-row-template"><tr><td class="row-number"></td><td><select class="form-control" data-field="manual_product_id" required><option value="">Select device</option>@foreach($products as $product)<option value="{{ $product->id }}">{{ $product->name }}</option>@endforeach</select></td><td><input class="form-control" data-field="vts_number" maxlength="100" required></td><td><input class="form-control" data-field="sim_number" maxlength="100" required></td><td><input class="form-control sales-price" data-field="sales_price" type="number" min="0" max="9999999999.99" step="0.01" required></td><td>{{ $businessDevice?->status ?? 'pending' }}</td></tr></template>
@endsection
@section('business-scripts')
<script>
(() => {
    @php($savedDevices = old('devices', $businessDevice ? [$businessDevice->only(['manual_product_id', 'vts_number', 'sim_number', 'sales_price'])] : []))
    const saved = @json($savedDevices);
    const rows = document.getElementById('device-rows');
    const quantity = document.getElementById('quantity');
    const cache = Array.isArray(saved) ? saved : Object.values(saved);
    const entryTable = document.getElementById('entry-table');
    function total() {
        let cents = 0;
        rows.querySelectorAll('.sales-price').forEach(input => { cents += Math.round((Number(input.value) || 0) * 100); });
        document.getElementById('sales-total').textContent = (cents / 100).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    }
    function render() {
        BusinessTables.destroy(entryTable);
        Array.from(rows.children).forEach((row, index) => {
            cache[index] = {};
            row.querySelectorAll('[data-field]').forEach(input => { cache[index][input.dataset.field] = input.value; });
        });
        const count = Number(quantity.value);
        if (!Number.isInteger(count) || count < 1 || count > Number(quantity.max)) return;
        rows.replaceChildren();
        for (let index = 0; index < count; index++) {
            const row = document.getElementById('device-row-template').content.cloneNode(true);
            row.querySelector('.row-number').textContent = index + 1;
            row.querySelectorAll('[data-field]').forEach(input => {
                input.name = `devices[${index}][${input.dataset.field}]`;
                input.value = cache[index]?.[input.dataset.field] ?? '';
            });
            rows.appendChild(row);
        }
        total();
        BusinessTables.init(entryTable, {paging: false, searching: false, ordering: false, info: false, dom: 't'});
    }
    quantity.addEventListener('input', render);
    rows.addEventListener('input', total);
    const status = document.getElementById('device-status');
    if (status) {
        const badge = document.querySelector('.card-header .bm-status');
        const updateStatus = () => {
            rows.querySelectorAll('tr').forEach(row => { row.lastElementChild.textContent = status.value; });
            badge.textContent = status.options[status.selectedIndex].text;
            badge.className = 'bm-status ' + status.value;
        };
        status.addEventListener('change', updateStatus);
        updateStatus();
    }
    document.getElementById('device-form').addEventListener('submit', () => {
        const devices = Array.from(rows.children).map(row => {
            const device = {};
            row.querySelectorAll('[data-field]').forEach(input => { device[input.dataset.field] = input.value; });
            return device;
        });
        const payload = document.createElement('input');
        payload.type = 'hidden';
        payload.name = 'devices_payload';
        payload.value = JSON.stringify(devices);
        document.getElementById('device-form').appendChild(payload);
        rows.querySelectorAll('[data-field]').forEach(input => { input.disabled = true; });
    });
    render();
    if (status) status.dispatchEvent(new Event('change'));
})();
</script>
@endsection

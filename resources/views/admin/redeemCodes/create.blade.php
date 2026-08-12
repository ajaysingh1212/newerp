@extends('layouts.admin')

@section('content')
<style>
    .redeem-shell { background:#f8fafc; border:1px solid #e5e7eb; border-radius:8px; overflow:hidden; }
    .redeem-hero { background:#0f172a; color:#fff; padding:22px; }
    .redeem-code-preview { font-family:monospace; font-size:28px; letter-spacing:.08em; font-weight:900; background:#fff; color:#0f172a; border-radius:8px; padding:14px 18px; display:inline-block; border:1px dashed #94a3b8; }
    .type-option { border:1px solid #d1d5db; border-radius:8px; padding:14px; cursor:pointer; height:100%; background:#fff; }
    .type-option input { margin-right:8px; }
</style>

<div class="card">
    <div class="card-header">Generate Redeem Code</div>
    <div class="card-body">
        @include('watermark')
        <form method="POST" action="{{ route('admin.redeem-codes.store') }}">
            @csrf
            <div class="redeem-shell mb-4">
                <div class="redeem-hero d-flex flex-wrap justify-content-between align-items-center">
                    <div>
                        <h3 class="mb-1">Recharge Discount Code</h3>
                        <p class="mb-0 text-light">Select plan, validity date, discount type and value.</p>
                    </div>
                    <div class="redeem-code-preview mt-3 mt-md-0" id="codePreview">{{ old('code', $generatedCode) }}</div>
                </div>

                <div class="p-3">
                    <div class="row">
                        <div class="form-group col-lg-6">
                            <label class="required" for="recharge_plan_id">Recharge Plan</label>
                            <select class="form-control select2 {{ $errors->has('recharge_plan_id') ? 'is-invalid' : '' }}" name="recharge_plan_id" id="recharge_plan_id" required>
                                <option value="">Please select</option>
                                @foreach($rechargePlans as $id => $label)
                                    <option value="{{ $id }}" {{ old('recharge_plan_id') == $id ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @if($errors->has('recharge_plan_id'))<div class="invalid-feedback">{{ $errors->first('recharge_plan_id') }}</div>@endif
                        </div>

                        <div class="form-group col-lg-3">
                            <label class="required" for="valid_up_to">Valid Up To</label>
                            <input class="form-control {{ $errors->has('valid_up_to') ? 'is-invalid' : '' }}" type="date" name="valid_up_to" id="valid_up_to" value="{{ old('valid_up_to') }}" min="{{ now()->format('Y-m-d') }}" required>
                            @if($errors->has('valid_up_to'))<div class="invalid-feedback">{{ $errors->first('valid_up_to') }}</div>@endif
                        </div>

                        <div class="form-group col-lg-3">
                            <label class="required" for="code">Redeem Code</label>
                            <div class="input-group">
                                <input class="form-control {{ $errors->has('code') ? 'is-invalid' : '' }}" type="text" name="code" id="code" value="{{ old('code', $generatedCode) }}" required>
                                <div class="input-group-append">
                                    <button class="btn btn-dark" type="button" id="regenCode">Generate</button>
                                </div>
                            </div>
                            @if($errors->has('code'))<div class="invalid-feedback d-block">{{ $errors->first('code') }}</div>@endif
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-lg-6">
                            <label class="required">Discount Type</label>
                            <div class="row">
                                <div class="col-sm-6 mb-2">
                                    <label class="type-option w-100">
                                        <input type="radio" name="discount_type" value="flat" {{ old('discount_type', 'flat') === 'flat' ? 'checked' : '' }}>
                                        Flat Amount
                                        <small class="d-block text-muted">Direct rupee amount minus hoga.</small>
                                    </label>
                                </div>
                                <div class="col-sm-6 mb-2">
                                    <label class="type-option w-100">
                                        <input type="radio" name="discount_type" value="percent" {{ old('discount_type') === 'percent' ? 'checked' : '' }}>
                                        Percentage
                                        <small class="d-block text-muted">Plan price ke hisab se discount.</small>
                                    </label>
                                </div>
                            </div>
                            @if($errors->has('discount_type'))<div class="text-danger">{{ $errors->first('discount_type') }}</div>@endif
                        </div>

                        <div class="form-group col-lg-6">
                            <label class="required" for="discount_value" id="discountValueLabel">Discount Value</label>
                            <input class="form-control {{ $errors->has('discount_value') ? 'is-invalid' : '' }}" type="number" name="discount_value" id="discount_value" value="{{ old('discount_value') }}" step="0.01" min="0.01" required>
                            @if($errors->has('discount_value'))<div class="invalid-feedback">{{ $errors->first('discount_value') }}</div>@endif
                        </div>
                    </div>
                </div>
            </div>

            <button class="btn btn-primary" type="submit">Save Redeem Code</button>
            <a class="btn btn-default" href="{{ route('admin.redeem-codes.index') }}">{{ trans('global.back_to_list') }}</a>
        </form>
    </div>
</div>

<script>
    function makeCode() {
        const digits = String(Math.floor(Math.random() * 100000)).padStart(5, '0');
        const letters = Array.from({ length: 5 }, () => String.fromCharCode(65 + Math.floor(Math.random() * 26))).join('');
        return `ET-${digits}${letters}`;
    }

    function syncDiscountLabel() {
        const type = document.querySelector('input[name="discount_type"]:checked')?.value || 'flat';
        document.getElementById('discountValueLabel').textContent = type === 'percent' ? 'Discount Percent (%)' : 'Discount Amount (Rs)';
        document.getElementById('discount_value').max = type === 'percent' ? '100' : '';
    }

    document.getElementById('regenCode').addEventListener('click', function () {
        const code = makeCode();
        document.getElementById('code').value = code;
        document.getElementById('codePreview').textContent = code;
    });

    document.getElementById('code').addEventListener('input', function () {
        this.value = this.value.toUpperCase();
        document.getElementById('codePreview').textContent = this.value;
    });

    document.querySelectorAll('input[name="discount_type"]').forEach(function (input) {
        input.addEventListener('change', syncDiscountLabel);
    });

    syncDiscountLabel();
</script>
@endsection

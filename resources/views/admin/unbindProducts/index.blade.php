@extends('layouts.admin')
@section('content')
<div class="card">
    <div class="card-header">Unbind Product</div>

    <div class="card-body">
        @include('watermark')
        <label for="product_id">Select Product (SKU - VTS)</label>
        <select id="product_id" class="form-control">
            <option value="">-- Select --</option>
            @foreach($products as $product)
                <option value="{{ $product->id }}">
                    {{ $product->sku }} - {{ $product->vts->vts_number ?? 'N/A' }}
                </option>
            @endforeach
        </select>

        <div id="product-details" class="mt-4" style="display: none;">
            <h5>Product Details</h5>
            <ul>
                <li><strong>Model:</strong> <span id="model_name"></span></li>
                <li><strong>IMEI:</strong> <span id="imei_number"></span></li>
                <li><strong>VTS:</strong> <span id="vts_number"></span></li>
                <li><strong>SIM Number:</strong> <span id="sim_number"></span></li>

                <li><strong>Warranty:</strong> <span id="warranty"></span></li>
                <li><strong>Subscription:</strong> <span id="subscription"></span></li>
                <li><strong>AMC:</strong> <span id="amc"></span></li>
            </ul>
            <button class="btn btn-danger" id="unbind-btn">Unbind</button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.getElementById('product_id').addEventListener('change', function () {
    let productId = this.value;
    if (!productId) {
        document.getElementById('product-details').style.display = 'none';
        return;
    }

    fetch("{{ route('admin.unbind.products.details') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': "{{ csrf_token() }}"
        },
        body: JSON.stringify({ product_id: productId })
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('model_name').innerText = data.product_model?.product_model ?? 'N/A';
        document.getElementById('imei_number').innerText = data.imei?.imei_number ?? 'N/A';
       document.getElementById('sim_number').innerText = data.vts?.sim_number ?? 'N/A';
        document.getElementById('vts_number').innerText = data.vts?.vts_number ?? 'N/A';
        document.getElementById('warranty').innerText = data.warranty;
        document.getElementById('subscription').innerText = data.subscription;
        document.getElementById('amc').innerText = data.amc;

        document.getElementById('unbind-btn').setAttribute('data-product-id', productId);
        document.getElementById('product-details').style.display = 'block';
    });
});

document.getElementById('unbind-btn').addEventListener('click', function () {
    let productId = this.getAttribute('data-product-id');

    if (!confirm('Are you sure you want to unbind this product?')) return;

    fetch("{{ route('admin.unbind.products.unbind') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': "{{ csrf_token() }}"
        },
        body: JSON.stringify({ product_id: productId })
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        location.reload(); // page reload to refresh list
    });
});
</script>
@endsection

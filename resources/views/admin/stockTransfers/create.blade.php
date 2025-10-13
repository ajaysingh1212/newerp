@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('cruds.stockTransfer.title_singular') }}
    </div>

    <div class="card-body">
        @include('watermark')
        <form method="POST" action="{{ route('admin.stock-transfers.store') }}" enctype="multipart/form-data">
            @csrf

            {{-- Top Row Inputs --}}
            <div class="row mb-4">
                <div class="col-md-4">
                    <label class="required" for="transfer_date">{{ trans('cruds.stockTransfer.fields.transfer_date') }}</label>
                    <input class="form-control date {{ $errors->has('transfer_date') ? 'is-invalid' : '' }}" type="text" name="transfer_date" id="transfer_date" value="{{ old('transfer_date') }}" required>
                </div>

                <div class="col-md-4">
                    <label class="required" for="select_user_id">{{ trans('cruds.stockTransfer.fields.select_user') }}</label>
                    <select class="form-control select2 {{ $errors->has('select_user_id') ? 'is-invalid' : '' }}" name="reseller_id" id="select_user_id" required>
                        @foreach($party_types as $id => $entry)
                            <option value="{{ $id }}" {{ old('select_user_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                        @endforeach
                    </select>
                    @if($errors->has('select_user_id'))
                        <div class="invalid-feedback">{{ $errors->first('select_user_id') }}</div>
                    @endif
                </div>

                <div class="col-md-4">
                    <label for="reseller_id">{{ trans('cruds.stockTransfer.fields.reseller') }}</label>
                    <select class="form-control select2" name="select_user_id" id="reseller_id">
                        <option value="">{{ trans('global.pleaseSelect') }}</option>
                    </select>
                </div>
       
            </div>

            {{-- Main Row --}}
            <div class="row">
                <div class="col-md-6">
                    <h5>Selected Products</h5>
                    <ul id="product-list" class="list-group p-3">
                        {{-- Dynamic product rows will be appended here --}}
                    </ul>
                </div>

                <div class="col-md-6">
                    <h5>Add Product</h5>
                    <div class="form-group">
                        <label class="required" for="select_product">{{ trans('cruds.stockTransfer.fields.select_product') }}</label>
                        <select class="form-control select2 {{ $errors->has('select_product') ? 'is-invalid' : '' }}" name="select_product" id="select_product">
                            <option value="">{{ trans('global.pleaseSelect') }}</option>
                            @foreach($select_products as $id => $product)
                                <option value="{{ $id }}">{{ $product }}</option>
                            @endforeach
                        </select>

                        @if($errors->has('select_product'))
                            <div class="invalid-feedback">{{ $errors->first('select_product') }}</div>
                        @endif
                    </div>

                    <div id="product-details" style="display:none;">
                        <div class="row">
                            <div class="form-group col-lg-4">
                                <label>Warranty</label>
                                <input type="text" class="form-control" id="warranty" readonly>
                            </div>
                            <div class="form-group col-lg-4">
                                <label>AMC</label>
                                <input type="text" class="form-control" id="amc" readonly>
                            </div>
                            <div class="form-group col-lg-4">
                                <label>MRP</label>
                                <input type="text" class="form-control" id="mrp" readonly>
                            </div>
                            <div class="form-group col-lg-4">
                                <label>Role Price</label>
                                <input type="text" class="form-control" id="role_price" readonly>
                            </div>
                            <div class="form-group col-lg-4">
                                <label>Discount Type</label>
                                <select class="form-control" id="discount_type" >
                                    <option value="value">Value</option>
                                    <option value="percentage">Percentage</option>
                                </select>
                            </div>
                            <div class="form-group col-lg-4">
                                <label>Discount</label>
                                <input type="number" step="0.01" class="form-control" id="discount_value" placeholder="Enter discount" >
                            </div>
                        </div>

                        <button type="button" class="btn btn-primary" id="add_product_btn">Add Product</button>
                        <div class="text-danger mt-1" id="product-error" style="display:none;"></div>
                    </div>
                </div>
            </div>

            {{-- Product Summary --}}
            <div class="mt-4">
                <h5>Product Summary</h5>
                <table class="table table-bordered" id="summary-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Warranty</th>
                            <th>AMC</th>
                            <th>MRP</th>
                            <th>Price</th>
                            <th>Discount</th>
                            <th>Base Price</th>
                            <th>CGST (9%)</th>
                            <th>SGST (9%)</th>
                            <th>Total Tax</th>
                            <th>Final Price (incl. tax)</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody></tbody>
                </table>
            </div>

            <div class="form-group mt-3">
                <button class="btn btn-danger" type="submit">{{ trans('global.save') }}</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
@parent
<script>

$(document).ready(function () {
    let productList = new Set();

    $('#select_user_id').on('change', function () {
        let roleId = $(this).val();
        $('#reseller_id').empty().append('<option value="">' + @json(trans("global.pleaseSelect")) + '</option>');

        if (roleId) {
            $.get('{{ route("admin.get.users.by.role") }}', { role_id: roleId }, function (data) {
                $.each(data, function (id, user) {
                    let userInfo = user.name + ' (' + user.mobile_number + ')';
                    $('#reseller_id').append('<option value="' + id + '">' + userInfo + '</option>');
                });
            });
        }
    });

    $('#select_product').on('change', function () {
        let productId = $(this).val();
        let roleId = $('#select_user_id').val();

        if (productId && roleId) {
            $.get('{{ route("admin.get.product.details") }}', {
                product_id: productId,
                role_id: roleId
            }, function (data) {
                if (data.error) {
                    alert(data.error);
                } else {
                    $('#warranty').val(data.warranty);
                    $('#amc').val(data.amc);
                    $('#mrp').val(data.mrp);
                    $('#role_price').val(data.price);
                    $('#product-details').show();
                }
            });
        }
    });

    $('#add_product_btn').on('click', function () {
        const productId = $('#select_product').val();
        const productName = $('#select_product option:selected').text();
        const warranty = $('#warranty').val();
        const amc = $('#amc').val();
        const mrp = $('#mrp').val();
        const rolePrice = parseFloat($('#role_price').val());
        const discountType = $('#discount_type').val();
        const discountValue = parseFloat($('#discount_value').val());

        if (!productId || productList.has(productId)) {
            $('#product-error').text("Product already added or not selected").show();
            return;
        }
        $('#product-error').hide();

        let discountAmount = discountType === 'percentage'
            ? (rolePrice * discountValue / 100)
            : discountValue;

        const finalPrice = (rolePrice - discountAmount).toFixed(2);
        const basePrice = (finalPrice / 1.18).toFixed(2);
        const cgst = (finalPrice * 0.09 / 1.18).toFixed(2);
        const sgst = (finalPrice * 0.09 / 1.18).toFixed(2);
        const totalTax = (parseFloat(cgst) + parseFloat(sgst)).toFixed(2);

        $('#summary-table tbody').append(`
            <tr data-product-id="${productId}">
                <td>${productName}<input type="hidden" name="products[${productId}][id]" value="${productId}"></td>
                <td><input type="hidden" name="products[${productId}][warranty]" value="${warranty}">${warranty}</td>
                <td><input type="hidden" name="products[${productId}][amc]" value="${amc}">${amc}</td>
                <td><input type="hidden" name="products[${productId}][mrp]" value="${mrp}">${mrp}</td>
                <td><input type="hidden" name="products[${productId}][role_price]" value="${rolePrice}">${rolePrice}</td>
                <td>
                    <input type="hidden" name="products[${productId}][discount_type]" value="${discountType}">
                    <input type="hidden" name="products[${productId}][discount_value]" value="${discountValue}">
                    ${discountType === 'percentage' ? discountValue + '%' : discountValue}
                </td>
                <td>
                    <input type="hidden" name="products[${productId}][base_price]" value="${basePrice}">${basePrice}
                </td>
                <td>
                    <input type="hidden" name="products[${productId}][cgst]" value="${cgst}">${cgst}
                </td>
                <td>
                    <input type="hidden" name="products[${productId}][sgst]" value="${sgst}">${sgst}
                </td>
                <td>
                    <input type="hidden" name="products[${productId}][total_tax]" value="${totalTax}">${totalTax}
                </td>
                <td>
                    <input type="hidden" name="products[${productId}][final_price]" value="${finalPrice}">${finalPrice}
                </td>
                <td>
                    <button type="button" class="btn btn-sm btn-danger remove-product">Remove</button>
                </td>
            </tr>
        `);

        $('#product-list').append(`
            <li class="list-group-item d-flex justify-content-between align-items-center" data-product-id="${productId}">
                ${productName}
                <button type="button" class="btn btn-sm btn-outline-danger remove-left-product">×</button>
            </li>
        `);

        productList.add(productId);
        $('#select_product').val('').trigger('change');
        $('#product-details').hide();
        $('#discount_value').val('');
    });

    $('#summary-table').on('click', '.remove-product', function () {
        const row = $(this).closest('tr');
        const productId = row.data('product-id');
        productList.delete(String(productId));
        row.remove();
        $('#product-list').find(`li[data-product-id="${productId}"]`).remove();
    });

    $('#product-list').on('click', '.remove-left-product', function () {
        const listItem = $(this).closest('li');
        const productId = listItem.data('product-id');
        listItem.remove();
        productList.delete(String(productId));
        $('#summary-table').find(`tr[data-product-id="${productId}"]`).remove();
    });
});


</script>


@endsection

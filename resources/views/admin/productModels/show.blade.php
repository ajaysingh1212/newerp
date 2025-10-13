@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.productModel.title') }}
    </div>

    <div class="card-body">
        @include('watermark')
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.product-models.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.productModel.fields.id') }}
                        </th>
                        <td>
                            {{ $productModel->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.productModel.fields.product_model') }}
                        </th>
                        <td>
                            {{ $productModel->product_model }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.productModel.fields.warranty') }}
                        </th>
                        <td>
                            {{ $productModel->warranty }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.productModel.fields.subscription') }}
                        </th>
                        <td>
                            {{ $productModel->subscription }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.productModel.fields.amc') }}
                        </th>
                        <td>
                            {{ $productModel->amc }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.productModel.fields.mrp') }}
                        </th>
                        <td>
                            {{ $productModel->mrp }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.productModel.fields.cnf_price') }}
                        </th>
                        <td>
                            {{ $productModel->cnf_price }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.productModel.fields.distributor_price') }}
                        </th>
                        <td>
                            {{ $productModel->distributor_price }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.productModel.fields.dealer_price') }}
                        </th>
                        <td>
                            {{ $productModel->dealer_price }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.productModel.fields.customer_price') }}
                        </th>
                        <td>
                            {{ $productModel->customer_price }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.productModel.fields.status') }}
                        </th>
                        <td>
                            {{ App\Models\ProductModel::STATUS_SELECT[$productModel->status] ?? '' }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.product-models.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        {{ trans('global.relatedData') }}
    </div>
    <ul class="nav nav-tabs" role="tablist" id="relationship-tabs">
        <li class="nav-item">
            <a class="nav-link" href="#product_model_product_masters" role="tab" data-toggle="tab">
                {{ trans('cruds.productMaster.title') }}
            </a>
        </li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane" role="tabpanel" id="product_model_product_masters">
            @includeIf('admin.productModels.relationships.productModelProductMasters', ['productMasters' => $productModel->productModelProductMasters])
        </div>
    </div>
</div>

@endsection
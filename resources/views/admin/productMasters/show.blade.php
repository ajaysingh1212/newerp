@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.productMaster.title') }}
    </div>

    <div class="card-body">
        @include('watermark')
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.product-masters.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.productMaster.fields.id') }}
                        </th>
                        <td>
                            {{ $productMaster->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.productMaster.fields.product_model') }}
                        </th>
                        <td>
                            {{ $productMaster->product_model->product_model ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.productMaster.fields.imei') }}
                        </th>
                        <td>
                            {{ $productMaster->imei->imei_number ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.productMaster.fields.vts') }}
                        </th>
                        <td>
                            {{ $productMaster->vts->vts_number ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.productMaster.fields.warranty') }}
                        </th>
                        <td>
                            {{ $productMaster->warranty }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.productMaster.fields.subscription') }}
                        </th>
                        <td>
                            {{ $productMaster->subscription }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.productMaster.fields.amc') }}
                        </th>
                        <td>
                            {{ $productMaster->amc }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.productMaster.fields.status') }}
                        </th>
                        <td>
                            {{ App\Models\ProductMaster::STATUS_SELECT[$productMaster->status] ?? '' }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.product-masters.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection
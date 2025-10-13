@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.imeiMaster.title') }}
    </div>

    <div class="card-body">
        @include('watermark')
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.imei-masters.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.imeiMaster.fields.id') }}
                        </th>
                        <td>
                            {{ $imeiMaster->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.imeiMaster.fields.imei_model') }}
                        </th>
                        <td>
                            {{ $imeiMaster->imei_model->imei_model_number ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.imeiMaster.fields.imei_number') }}
                        </th>
                        <td>
                            {{ $imeiMaster->imei_number }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.imeiMaster.fields.status') }}
                        </th>
                        <td>
                            {{ App\Models\ImeiMaster::STATUS_SELECT[$imeiMaster->status] ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.imeiMaster.fields.product_status') }}
                        </th>
                        <td>
                            {{ App\Models\ImeiMaster::PRODUCT_STATUS_SELECT[$imeiMaster->product_status] ?? '' }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.imei-masters.index') }}">
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
            <a class="nav-link" href="#imei_product_masters" role="tab" data-toggle="tab">
                {{ trans('cruds.productMaster.title') }}
            </a>
        </li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane" role="tabpanel" id="imei_product_masters">
            @includeIf('admin.imeiMasters.relationships.imeiProductMasters', ['productMasters' => $imeiMaster->imeiProductMasters])
        </div>
    </div>
</div>

@endsection
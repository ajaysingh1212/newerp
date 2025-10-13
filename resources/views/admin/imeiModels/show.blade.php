@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.imeiModel.title') }}
    </div>

    <div class="card-body">
        @include('watermark')
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.imei-models.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.imeiModel.fields.id') }}
                        </th>
                        <td>
                            {{ $imeiModel->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.imeiModel.fields.imei_model_number') }}
                        </th>
                        <td>
                            {{ $imeiModel->imei_model_number }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.imeiModel.fields.status') }}
                        </th>
                        <td>
                            {{ App\Models\ImeiModel::STATUS_SELECT[$imeiModel->status] ?? '' }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.imei-models.index') }}">
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
            <a class="nav-link" href="#imei_model_imei_masters" role="tab" data-toggle="tab">
                {{ trans('cruds.imeiMaster.title') }}
            </a>
        </li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane" role="tabpanel" id="imei_model_imei_masters">
            @includeIf('admin.imeiModels.relationships.imeiModelImeiMasters', ['imeiMasters' => $imeiModel->imeiModelImeiMasters])
        </div>
    </div>
</div>

@endsection
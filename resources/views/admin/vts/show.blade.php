@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.vt.title') }}
    </div>

    <div class="card-body">
        @include('watermark')
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.vts.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.vt.fields.id') }}
                        </th>
                        <td>
                            {{ $vt->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.vt.fields.vts_number') }}
                        </th>
                        <td>
                            {{ $vt->vts_number }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.vt.fields.sim_number') }}
                        </th>
                        <td>
                            {{ $vt->sim_number }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.vt.fields.operator') }}
                        </th>
                        <td>
                            {{ $vt->operator }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.vt.fields.product_status') }}
                        </th>
                        <td>
                            {{ App\Models\Vt::PRODUCT_STATUS_SELECT[$vt->product_status] ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.vt.fields.status') }}
                        </th>
                        <td>
                            {{ App\Models\Vt::STATUS_SELECT[$vt->status] ?? '' }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.vts.index') }}">
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
            <a class="nav-link" href="#vts_product_masters" role="tab" data-toggle="tab">
                {{ trans('cruds.productMaster.title') }}
            </a>
        </li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane" role="tabpanel" id="vts_product_masters">
            @includeIf('admin.vts.relationships.vtsProductMasters', ['productMasters' => $vt->vtsProductMasters])
        </div>
    </div>
</div>

@endsection
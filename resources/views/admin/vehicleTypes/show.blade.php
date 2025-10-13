@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.vehicleType.title') }}
    </div>

    <div class="card-body">
        @include('watermark')
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.vehicle-types.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.vehicleType.fields.id') }}
                        </th>
                        <td>
                            {{ $vehicleType->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.vehicleType.fields.vehicle_type') }}
                        </th>
                        <td>
                            {{ $vehicleType->vehicle_type }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.vehicleType.fields.vehicle_icon') }}
                        </th>
                        <td>
                            @if($vehicleType->vehicle_icon)
                                <a href="{{ $vehicleType->vehicle_icon->getUrl() }}" target="_blank" style="display: inline-block">
                                    <img src="{{ $vehicleType->vehicle_icon->getUrl('thumb') }}">
                                </a>
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.vehicle-types.index') }}">
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
            <a class="nav-link" href="#vehicle_type_activation_requests" role="tab" data-toggle="tab">
                {{ trans('cruds.activationRequest.title') }}
            </a>
        </li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane" role="tabpanel" id="vehicle_type_activation_requests">
            @includeIf('admin.vehicleTypes.relationships.vehicleTypeActivationRequests', ['activationRequests' => $vehicleType->vehicleTypeActivationRequests])
        </div>
    </div>
</div>

@endsection
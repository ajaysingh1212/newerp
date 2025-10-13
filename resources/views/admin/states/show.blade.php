@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.state.title') }}
    </div>

    <div class="card-body">
        @include('watermark')
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.states.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.state.fields.id') }}
                        </th>
                        <td>
                            {{ $state->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.state.fields.state_name') }}
                        </th>
                        <td>
                            {{ $state->state_name }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.state.fields.country') }}
                        </th>
                        <td>
                            {{ $state->country }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.state.fields.status') }}
                        </th>
                        <td>
                            {{ App\Models\State::STATUS_SELECT[$state->status] ?? '' }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.states.index') }}">
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
            <a class="nav-link" href="#select_state_districts" role="tab" data-toggle="tab">
                {{ trans('cruds.district.title') }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#state_activation_requests" role="tab" data-toggle="tab">
                {{ trans('cruds.activationRequest.title') }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#state_users" role="tab" data-toggle="tab">
                {{ trans('cruds.user.title') }}
            </a>
        </li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane" role="tabpanel" id="select_state_districts">
            @includeIf('admin.states.relationships.selectStateDistricts', ['districts' => $state->selectStateDistricts])
        </div>
        <div class="tab-pane" role="tabpanel" id="state_activation_requests">
            @includeIf('admin.states.relationships.stateActivationRequests', ['activationRequests' => $state->stateActivationRequests])
        </div>
        <div class="tab-pane" role="tabpanel" id="state_users">
            @includeIf('admin.states.relationships.stateUsers', ['users' => $state->stateUsers])
        </div>
    </div>
</div>

@endsection
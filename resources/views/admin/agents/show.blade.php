@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.agent.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.agents.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.agent.fields.id') }}
                        </th>
                        <td>
                            {{ $agent->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.agent.fields.full_name') }}
                        </th>
                        <td>
                            {{ $agent->full_name }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.agent.fields.phone_number') }}
                        </th>
                        <td>
                            {{ $agent->phone_number }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.agent.fields.whatsapp_number') }}
                        </th>
                        <td>
                            {{ $agent->whatsapp_number }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.agent.fields.email') }}
                        </th>
                        <td>
                            {{ $agent->email }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.agent.fields.pin_code') }}
                        </th>
                        <td>
                            {{ $agent->pin_code }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.agent.fields.state') }}
                        </th>
                        <td>
                            {{ $agent->state }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.agent.fields.city') }}
                        </th>
                        <td>
                            {{ $agent->city }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.agent.fields.district') }}
                        </th>
                        <td>
                            {{ $agent->district }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.agent.fields.present_address') }}
                        </th>
                        <td>
                            {!! $agent->present_address !!}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.agent.fields.parmanent_address') }}
                        </th>
                        <td>
                            {!! $agent->parmanent_address !!}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.agent.fields.aadhar_front') }}
                        </th>
                        <td>
                            @if($agent->aadhar_front)
                                <a href="{{ $agent->aadhar_front->getUrl() }}" target="_blank">
                                    {{ trans('global.view_file') }}
                                </a>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.agent.fields.aadhar_back') }}
                        </th>
                        <td>
                            @if($agent->aadhar_back)
                                <a href="{{ $agent->aadhar_back->getUrl() }}" target="_blank">
                                    {{ trans('global.view_file') }}
                                </a>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.agent.fields.pan_card') }}
                        </th>
                        <td>
                            @if($agent->pan_card)
                                <a href="{{ $agent->pan_card->getUrl() }}" target="_blank">
                                    {{ trans('global.view_file') }}
                                </a>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.agent.fields.additional_document') }}
                        </th>
                        <td>
                            @foreach($agent->additional_document as $key => $media)
                                <a href="{{ $media->getUrl() }}" target="_blank">
                                    {{ trans('global.view_file') }}
                                </a>
                            @endforeach
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.agent.fields.status') }}
                        </th>
                        <td>
                            {{ App\Models\Agent::STATUS_SELECT[$agent->status] ?? '' }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.agents.index') }}">
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
            <a class="nav-link" href="#select_agent_investments" role="tab" data-toggle="tab">
                {{ trans('cruds.investment.title') }}
            </a>
        </li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane" role="tabpanel" id="select_agent_investments">
            @includeIf('admin.agents.relationships.selectAgentInvestments', ['investments' => $agent->selectAgentInvestments])
        </div>
    </div>
</div>

@endsection
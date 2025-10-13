@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.user.title') }}
    </div>

    <div class="card-body">
        @include('watermark')
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.users.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.user.fields.id') }}
                        </th>
                        <td>
                            {{ $user->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.user.fields.name') }}
                        </th>
                        <td>
                            {{ $user->name }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.user.fields.company_name') }}
                        </th>
                        <td>
                            {{ $user->company_name }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.user.fields.email') }}
                        </th>
                        <td>
                            {{ $user->email }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.user.fields.gst_number') }}
                        </th>
                        <td>
                            {{ $user->gst_number }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.user.fields.date_inc') }}
                        </th>
                        <td>
                            {{ $user->date_inc }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.user.fields.date_joining') }}
                        </th>
                        <td>
                            {{ $user->date_joining }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.user.fields.mobile_number') }}
                        </th>
                        <td>
                            {{ $user->mobile_number }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.user.fields.whatsapp_number') }}
                        </th>
                        <td>
                            {{ $user->whatsapp_number }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.user.fields.state') }}
                        </th>
                        <td>
                            {{ $user->state->state_name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.user.fields.district') }}
                        </th>
                        <td>
                            {{ $user->district->districts ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.user.fields.pin_code') }}
                        </th>
                        <td>
                            {{ $user->pin_code }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.user.fields.full_address') }}
                        </th>
                        <td>
                            {!! $user->full_address !!}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.user.fields.bank_name') }}
                        </th>
                        <td>
                            {{ $user->bank_name }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.user.fields.branch_name') }}
                        </th>
                        <td>
                            {{ $user->branch_name }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.user.fields.ifsc') }}
                        </th>
                        <td>
                            {{ $user->ifsc }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.user.fields.ac_holder_name') }}
                        </th>
                        <td>
                            {{ $user->ac_holder_name }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.user.fields.pan_number') }}
                        </th>
                        <td>
                            {{ $user->pan_number }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.user.fields.profile_image') }}
                        </th>
                        <td>
                            @if($user->profile_image)
                                <a href="{{ $user->profile_image->getUrl() }}" target="_blank" style="display: inline-block">
                                    <img src="{{ $user->profile_image->getUrl('thumb') }}">
                                </a>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.user.fields.upload_signature') }}
                        </th>
                        <td>
                            @if($user->upload_signature)
                                <a href="{{ $user->upload_signature->getUrl() }}" target="_blank" style="display: inline-block">
                                    <img src="{{ $user->upload_signature->getUrl('thumb') }}">
                                </a>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.user.fields.upload_pan_aadhar') }}
                        </th>
                        <td>
                            @if($user->upload_pan_aadhar)
                                <a href="{{ $user->upload_pan_aadhar->getUrl() }}" target="_blank">
                                    {{ trans('global.view_file') }}
                                </a>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.user.fields.passbook_statement') }}
                        </th>
                        <td>
                            @if($user->passbook_statement)
                                <a href="{{ $user->passbook_statement->getUrl() }}" target="_blank">
                                    {{ trans('global.view_file') }}
                                </a>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.user.fields.shop_photo') }}
                        </th>
                        <td>
                            @if($user->shop_photo)
                                <a href="{{ $user->shop_photo->getUrl() }}" target="_blank" style="display: inline-block">
                                    <img src="{{ $user->shop_photo->getUrl('thumb') }}">
                                </a>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.user.fields.gst_certificate') }}
                        </th>
                        <td>
                            @if($user->gst_certificate)
                                <a href="{{ $user->gst_certificate->getUrl() }}" target="_blank">
                                    {{ trans('global.view_file') }}
                                </a>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.user.fields.status') }}
                        </th>
                        <td>
                            {{ App\Models\User::STATUS_SELECT[$user->status] ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.user.fields.roles') }}
                        </th>
                        <td>
                            @foreach($user->roles as $key => $roles)
                                <span class="label label-info">{{ $roles->title }}</span>
                            @endforeach
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.user.fields.email_verified_at') }}
                        </th>
                        <td>
                            {{ $user->email_verified_at }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.users.index') }}">
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
            <a class="nav-link" href="#select_party_activation_requests" role="tab" data-toggle="tab">
                {{ trans('cruds.activationRequest.title') }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#select_user_attach_veichles" role="tab" data-toggle="tab">
                {{ trans('cruds.attachVeichle.title') }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#user_recharge_requests" role="tab" data-toggle="tab">
                {{ trans('cruds.rechargeRequest.title') }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#reseller_stock_transfers" role="tab" data-toggle="tab">
                {{ trans('cruds.stockTransfer.title') }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#user_user_alerts" role="tab" data-toggle="tab">
                {{ trans('cruds.userAlert.title') }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#select_party_check_party_stocks" role="tab" data-toggle="tab">
                {{ trans('cruds.checkPartyStock.title') }}
            </a>
        </li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane" role="tabpanel" id="select_party_activation_requests">
            @includeIf('admin.users.relationships.selectPartyActivationRequests', ['activationRequests' => $user->selectPartyActivationRequests])
        </div>
        <div class="tab-pane" role="tabpanel" id="select_user_attach_veichles">
            @includeIf('admin.users.relationships.selectUserAttachVeichles', ['attachVeichles' => $user->selectUserAttachVeichles])
        </div>
        <div class="tab-pane" role="tabpanel" id="user_recharge_requests">
            @includeIf('admin.users.relationships.userRechargeRequests', ['rechargeRequests' => $user->userRechargeRequests])
        </div>
        <div class="tab-pane" role="tabpanel" id="reseller_stock_transfers">
            @includeIf('admin.users.relationships.resellerStockTransfers', ['stockTransfers' => $user->resellerStockTransfers])
        </div>
        <div class="tab-pane" role="tabpanel" id="user_user_alerts">
            @includeIf('admin.users.relationships.userUserAlerts', ['userAlerts' => $user->userUserAlerts])
        </div>
        <div class="tab-pane" role="tabpanel" id="select_party_check_party_stocks">
            @includeIf('admin.users.relationships.selectPartyCheckPartyStocks', ['checkPartyStocks' => $user->selectPartyCheckPartyStocks])
        </div>
    </div>
</div>

@endsection
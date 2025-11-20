@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.loginLog.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.login-logs.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.loginLog.fields.id') }}
                        </th>
                        <td>
                            {{ $loginLog->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.loginLog.fields.use') }}
                        </th>
                        <td>
                            {{ $loginLog->use->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.loginLog.fields.ip_address') }}
                        </th>
                        <td>
                            {{ $loginLog->ip_address }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.loginLog.fields.device') }}
                        </th>
                        <td>
                            {{ $loginLog->device }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.loginLog.fields.location') }}
                        </th>
                        <td>
                            {{ $loginLog->location }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.loginLog.fields.logged_in_at') }}
                        </th>
                        <td>
                            {{ $loginLog->logged_in_at }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.login-logs.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection
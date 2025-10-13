@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.appDownload.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.app-downloads.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.appDownload.fields.id') }}
                        </th>
                        <td>
                            {{ $appDownload->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.appDownload.fields.title') }}
                        </th>
                        <td>
                            {{ $appDownload->title }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.appDownload.fields.user') }}
                        </th>
                        <td>
                            {{ $appDownload->user }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.appDownload.fields.password') }}
                        </th>
                        <td>
                            {{ $appDownload->password }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.appDownload.fields.appurl') }}
                        </th>
                        <td>
                            {{ $appDownload->appurl }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.appDownload.fields.appfile') }}
                        </th>
                        <td>
                            @if($appDownload->appfile)
                                <a href="{{ $appDownload->appfile->getUrl() }}" target="_blank">
                                    {{ trans('global.view_file') }}
                                </a>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.appDownload.fields.discriptio') }}
                        </th>
                        <td>
                            {!! $appDownload->discriptio !!}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.app-downloads.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection
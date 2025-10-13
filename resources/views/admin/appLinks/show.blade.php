@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.appLink.title') }}
    </div>

    <div class="card-body">
         @include('watermark')
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.app-links.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.appLink.fields.id') }}
                        </th>
                        <td>
                            {{ $appLink->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.appLink.fields.title') }}
                        </th>
                        <td>
                            {{ $appLink->title }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.appLink.fields.link') }}
                        </th>
                        <td>
                            {{ $appLink->link }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.app-links.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection
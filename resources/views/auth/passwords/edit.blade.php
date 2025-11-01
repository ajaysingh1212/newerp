@extends('layouts.admin')
@section('content')

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                {{ trans('global.my_profile') }}
            </div>

            <div class="card-body">
                <form method="POST" action="{{ route("profile.password.updateProfile") }}">
                    @csrf
                    <div class="form-group">
                        <label class="required" for="name">{{ trans('cruds.user.fields.name') }}</label>
                        <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text" name="name" id="name" value="{{ old('name', auth()->user()->name) }}" required>
                        @if($errors->has('name'))
                            <div class="invalid-feedback">
                                {{ $errors->first('name') }}
                            </div>
                        @endif
                    </div>
                    <div class="form-group">
                        <label class="required" for="title">{{ trans('cruds.user.fields.email') }}</label>
                        <input class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}" type="text" name="email" id="email" value="{{ old('email', auth()->user()->email) }}" required>
                        @if($errors->has('email'))
                            <div class="invalid-feedback">
                                {{ $errors->first('email') }}
                            </div>
                        @endif
                    </div>
                    <div class="form-group">
                        <button class="btn btn-danger" type="submit">
                            {{ trans('global.save') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                {{ trans('global.change_password') }}
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route("profile.password.update") }}">
                    @csrf
                    <div class="form-group">
                        <label class="required" for="password">New {{ trans('cruds.user.fields.password') }}</label>
                        <input class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}" type="password" name="password" id="password" required>
                        @if($errors->has('password'))
                            <span class="text-danger">{{ $errors->first('password') }}</span>
                        @endif
                    </div>
                    <div class="form-group">
                        <label class="required" for="password_confirmation">Repeat New {{ trans('cruds.user.fields.password') }}</label>
                        <input class="form-control {{ $errors->has('password_confirmation') ? 'is-invalid' : '' }}" type="password" name="password_confirmation" id="password_confirmation" required>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-danger" type="submit">
                            {{ trans('global.save') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="row">
    {{-- Status Section --}}
    <div class="col-md-6">
        <div class="card border-info">
            <div class="card-header bg-info text-white">
                <strong>Account Deletion Status</strong>
            </div>
            <div class="card-body">
                @if(isset($deletionRequest))
                    <table class="table table-bordered">
                        <tr>
                            <th>ID</th>
                            <td>{{ $deletionRequest->id }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                @if($deletionRequest->status == 'pending')
                                    <span class="badge badge-warning">Pending</span>
                                @elseif($deletionRequest->status == 'approved')
                                    <span class="badge badge-success">Approved</span>
                                @else
                                    <span class="badge badge-danger">Rejected</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Reason (You Provided)</th>
                            <td>{{ $deletionRequest->reason ?? '—' }}</td>
                        </tr>
                        <tr>
                            <th>Admin Note</th>
                            <td>{{ $deletionRequest->admin_note ?? '—' }}</td>
                        </tr>
                        <tr>
                            <th>Requested At</th>
                            <td>{{ $deletionRequest->created_at->format('d M Y h:i A') }}</td>
                        </tr>
                        <tr>
                            <th>Processed At</th>
                            <td>
                                {{ $deletionRequest->approved_at ? $deletionRequest->approved_at->format('d M Y h:i A') : '—' }}

                            </td>
                        </tr>
                        <tr>
                            <th>Approved By</th>
                            <td>
                                {{ $deletionRequest->approver->name ?? '—' }}
                            </td>
                        </tr>
                    </table>
                @else
                    <p class="text-muted">No deletion request submitted yet.</p>
                @endif
            </div>
        </div>
    </div>

</div>
@endsection
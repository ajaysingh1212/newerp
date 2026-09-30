@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="fas fa-mobile-alt mr-2"></i>App List</span>
        @can('manual_app_create')
            <a href="{{ route('admin.manual-apps.create') }}" class="btn btn-success btn-sm"><i class="fas fa-plus mr-1"></i>Add App</a>
        @endcan
    </div>
    <div class="card-body">
        @if(session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif
        <form method="GET" class="form-inline mb-3">
            <input type="search" name="search" value="{{ request('search') }}" class="form-control form-control-sm mr-2" placeholder="Search app name">
            <button class="btn btn-primary btn-sm" type="submit"><i class="fas fa-search"></i></button>
        </form>
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
                <thead>
                    <tr><th>#</th><th>App Name</th><th>Description</th><th>Status</th><th class="text-right">Action</th></tr>
                </thead>
                <tbody>
                    @forelse($apps as $app)
                        <tr>
                            <td>{{ $app->id }}</td>
                            <td>{{ $app->name }}</td>
                            <td>{{ \Illuminate\Support\Str::limit($app->description, 80) }}</td>
                            <td><span class="badge {{ $app->status === 'active' ? 'badge-success' : 'badge-secondary' }}">{{ ucfirst($app->status) }}</span></td>
                            <td class="text-right">
                                @can('manual_app_show')
                                    <a href="{{ route('admin.manual-apps.show', $app) }}" class="btn btn-sm btn-outline-info" title="View"><i class="fas fa-eye"></i></a>
                                @endcan
                                @can('manual_app_edit')
                                    <a href="{{ route('admin.manual-apps.edit', $app) }}" class="btn btn-sm btn-outline-primary" title="Edit"><i class="fas fa-edit"></i></a>
                                @endcan
                                @can('manual_app_delete')
                                    <form action="{{ route('admin.manual-apps.destroy', $app) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this app?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" title="Delete"><i class="fas fa-trash"></i></button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">No app found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $apps->links() }}
    </div>
</div>
@endsection

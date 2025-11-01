@extends('layouts.admin')
@section('content')
<div class="card">
    <div class="card-header">Account Deletion Requests</div>
    <div class="card-body">
        <table class="table">
            <thead>
                <tr><th>ID</th><th>User</th><th>Status</th><th>Created</th><th>Actions</th></tr>
            </thead>
            <tbody>
                @foreach($requests as $r)
                <tr>
                    <td>{{ $r->id }}</td>
                    <td>{{ $r->user->name }} ({{ $r->user->email }})</td>
                    <td>{{ ucfirst($r->status) }}</td>
                    <td>{{ $r->created_at->format('d M Y H:i') }}</td>
                    <td>
                        <a href="{{ route('admin.deletion.requests.show', $r->id) }}" class="btn btn-sm btn-primary">View</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        {{ $requests->links() }}
    </div>
</div>
@endsection

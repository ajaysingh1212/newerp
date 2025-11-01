@extends('layouts.admin')
@section('content')
<div class="card">
    <div class="card-header">Request #{{ $req->id }}</div>
    <div class="card-body">
        <p><strong>User:</strong> {{ $req->user->name }} ({{ $req->user->email }})</p>
        <p><strong>Reason:</strong> {{ $req->reason ?: '—' }}</p>
        <p><strong>Status:</strong> {{ $req->status }}</p>

        @if($req->status === 'pending')
        <form method="POST" action="{{ route('admin.deletion.requests.approve', $req->id) }}" class="d-inline">
            @csrf
            <div class="form-group">
                <textarea name="admin_note" class="form-control" placeholder="Admin note (optional)"></textarea>
            </div>
            <button class="btn btn-success" type="submit">Approve & Delete Account</button>
        </form>

        <form method="POST" action="{{ route('admin.deletion.requests.reject', $req->id) }}" class="d-inline">
            @csrf
            <div class="form-group">
                <input type="hidden" name="admin_note" value="Rejected by admin">
            </div>
            <button class="btn btn-danger" type="submit">Reject</button>
        </form>
        @else
            <p><strong>Processed by:</strong> {{ optional($req->approver)->name }} at {{ optional($req->approved_at)->format('d M Y H:i') }}</p>
            <p><strong>Admin note:</strong> {{ $req->admin_note }}</p>
        @endif
    </div>
</div>
@endsection

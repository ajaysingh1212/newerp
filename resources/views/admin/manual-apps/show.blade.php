@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-header"><i class="fas fa-mobile-alt mr-2"></i>{{ $manualApp->name }}</div>
    <div class="card-body">
        <p><strong>Description:</strong><br>{{ $manualApp->description ?: '-' }}</p>
        <p><strong>Status:</strong> {{ ucfirst($manualApp->status) }}</p>
        <a href="{{ route('admin.manual-apps.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left mr-1"></i>Back</a>
    </div>
</div>
@endsection

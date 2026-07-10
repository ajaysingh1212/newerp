@extends('layouts.admin')

@section('content')
<style>
    .me-card { border: none; border-radius: 14px; box-shadow: 0 6px 24px rgba(124,58,237,.08); }
    .me-header { background: linear-gradient(135deg, #7C3AED 0%, #6366F1 100%); padding: 20px 24px; color: #fff; border-radius: 14px 14px 0 0; }
</style>
<div class="card me-card animate__animated animate__fadeIn">
    <div class="me-header"><h4 class="mb-0"><i class="fas fa-box mr-2"></i>{{ $manualProduct->name }}</h4></div>
    <div class="card-body">
        <p><strong>Description:</strong><br>{{ $manualProduct->description ?? '-' }}</p>
        <p><strong>Status:</strong> {{ ucfirst($manualProduct->status) }}</p>
        <a href="{{ route('admin.manual-products.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left mr-1"></i> Back</a>
    </div>
</div>
@endsection

@extends('layouts.admin')

@section('content')
<style>
    .me-card { border: none; border-radius: 14px; box-shadow: 0 6px 24px rgba(124,58,237,.08); }
    .me-header { background: linear-gradient(135deg, #7C3AED 0%, #6366F1 100%); padding: 20px 24px; color: #fff; border-radius: 14px 14px 0 0; }
    .me-btn-nav { background: linear-gradient(135deg, #7C3AED, #6366F1); border: none; color: #fff; border-radius: 8px; padding: 10px 24px; }
    .me-form-group label { font-weight: 600; color: #4B5563; font-size: .85rem; }
    .me-form-group .required::after { content: ' *'; color: #EF4444; }
</style>

<div class="card me-card animate__animated animate__fadeIn">
    <div class="me-header"><h4 class="mb-0"><i class="fas fa-box mr-2"></i>Add Product</h4></div>
    <div class="card-body p-4">
        @if($errors->any())
            <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
        @endif
        <form action="{{ route('admin.manual-products.store') }}" method="POST">
            @csrf
            <div class="me-form-group mb-3">
                <label class="required">Product Name</label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
            </div>
            <div class="me-form-group mb-3">
                <label>Description</label>
                <textarea name="description" rows="4" class="form-control">{{ old('description') }}</textarea>
            </div>
            <button type="submit" class="me-btn-nav"><i class="fas fa-check mr-1"></i> Save Product</button>
            <a href="{{ route('admin.manual-products.index') }}" class="btn btn-light ml-2">Cancel</a>
        </form>
    </div>
</div>
@endsection

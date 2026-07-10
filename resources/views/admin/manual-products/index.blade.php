@extends('layouts.admin')

@section('content')
<style>
    .me-card { border: none; border-radius: 14px; box-shadow: 0 6px 24px rgba(124,58,237,.08); }
    .me-header { background: linear-gradient(135deg, #7C3AED 0%, #6366F1 100%); padding: 20px 24px; color: #fff; border-radius: 14px 14px 0 0; }
    .me-btn { background: linear-gradient(135deg, #7C3AED, #6366F1); border: none; color: #fff; border-radius: 8px; padding: 8px 18px; }
    .me-btn:hover { color: #fff; }
    .me-table thead th { background: #F5F3FF; color: #5B21B6; border: none; font-weight: 600; }
</style>

<div class="card me-card animate__animated animate__fadeIn">
    <div class="me-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h4 class="mb-0"><i class="fas fa-box mr-2"></i>Product</h4>
            <small>Manual Entry &raquo; Product List</small>
        </div>
        @can('manual_product_create')
            <a href="{{ route('admin.manual-products.create') }}" class="me-btn"><i class="fas fa-plus mr-1"></i> Add Product</a>
        @endcan
    </div>
    <div class="card-body">
        @if(session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif
        <div class="table-responsive">
            <table class="table me-table">
                <thead>
                    <tr><th>#</th><th>Name</th><th>Description</th><th>Status</th><th class="text-right">Action</th></tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr>
                            <td>{{ $product->id }}</td>
                            <td>{{ $product->name }}</td>
                            <td>{{ \Illuminate\Support\Str::limit($product->description, 80) }}</td>
                            <td><span class="badge {{ $product->status == 'active' ? 'badge-success' : 'badge-secondary' }}">{{ ucfirst($product->status) }}</span></td>
                            <td class="text-right">
                                @can('manual_product_show')
                                    <a href="{{ route('admin.manual-products.show', $product->id) }}" class="btn btn-sm btn-outline-info"><i class="fas fa-eye"></i></a>
                                @endcan
                                @can('manual_product_edit')
                                    <a href="{{ route('admin.manual-products.edit', $product->id) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                                @endcan
                                @can('manual_product_delete')
                                    <form action="{{ route('admin.manual-products.destroy', $product->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Pakka delete karna hai?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center py-4 text-muted">Koi product nahi mila.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $products->links() }}
    </div>
</div>
@endsection

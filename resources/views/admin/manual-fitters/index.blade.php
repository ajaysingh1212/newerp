@extends('layouts.admin')

@section('content')
<style>
    .mf-page-header {
        background: linear-gradient(135deg, #7C3AED 0%, #6366F1 55%, #4F46E5 100%);
        border-radius: 20px; padding: 24px 28px; color: #fff; margin-bottom: 20px;
        box-shadow: 0 16px 40px rgba(99,53,237,.25);
        display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;
    }
    .mf-btn { background: rgba(255,255,255,.15); backdrop-filter: blur(6px); border: 1px solid rgba(255,255,255,.35); color: #fff; border-radius: 10px; padding: 9px 20px; font-weight: 600; transition: .2s; }
    .mf-btn:hover { background: #fff; color: #5B21B6; }
    .mf-filter-bar { background: #fff; border-radius: 16px; padding: 16px; box-shadow: 0 8px 24px rgba(124,58,237,.08); margin-bottom: 20px; border: 1px solid #F1EEFE; }
    .mf-filter-bar .form-select, .mf-filter-bar .form-control { border-radius: 10px; border: 1px solid #E9E5FC; font-size: .85rem; font-weight: 500; color: #4B5563; padding: 9px 14px; background: #FBFAFF; }
    .mf-filter-label { font-size: .7rem; text-transform: uppercase; letter-spacing: .04em; color: #9CA3AF; font-weight: 700; margin-bottom: 4px; display: block; }
    .mf-table thead th { background: #F5F3FF; color: #5B21B6; border: none; font-weight: 700; font-size: .76rem; text-transform: uppercase; letter-spacing: .03em; white-space: nowrap; }
    .mf-table tbody td { font-size: .85rem; vertical-align: middle; }
    .mf-table tbody tr:hover { background: #FAF5FF; }
    .mf-badge-active { background: #A7F3D0; color: #065F46; padding: 4px 12px; border-radius: 12px; font-size: .72rem; font-weight: 700; }
    .mf-badge-inactive { background: #FECACA; color: #991B1B; padding: 4px 12px; border-radius: 12px; font-size: .72rem; font-weight: 700; }
    .mf-chart-card { border: none; border-radius: 16px; box-shadow: 0 8px 22px rgba(124,58,237,.08); }
</style>

<div class="mf-page-header animate__animated animate__fadeIn">
    <div>
        <h4 class="mb-0"><i class="fas fa-user-hard-hat mr-2"></i>Fitters</h4>
        <small>Manual Entry &raquo; Fitter</small>
    </div>
    @can('manual_fitter_create')
        <a href="{{ route('admin.manual-fitters.create') }}" class="mf-btn"><i class="fas fa-plus mr-1"></i> New Fitter</a>
    @endcan
</div>

@if(session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
@endif

<div class="mf-filter-bar">
    <form method="GET" action="{{ route('admin.manual-fitters.index') }}" class="row g-2 align-items-end">
        <div class="col-lg col-md-4 col-6 mb-2">
            <span class="mf-filter-label">Search</span>
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Name or Phone" value="{{ request('search') }}">
        </div>
        <div class="col-lg col-md-4 col-6 mb-2">
            <span class="mf-filter-label">State</span>
            <select name="state" class="form-select form-select-sm">
                <option value="">All States</option>
                @foreach($states as $state)
                    <option value="{{ $state }}" {{ request('state') == $state ? 'selected' : '' }}>{{ $state }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-lg col-md-4 col-6 mb-2">
            <span class="mf-filter-label">Status</span>
            <select name="status" class="form-select form-select-sm">
                <option value="">All</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>
        <div class="col-lg-auto col-6 mb-2">
            <button type="submit" class="mf-btn btn-sm w-100" style="background:linear-gradient(135deg,#7C3AED,#6366F1); border:none;"><i class="fas fa-filter mr-1"></i> Apply</button>
        </div>
        <div class="col-lg-auto col-6 mb-2">
            <a href="{{ route('admin.manual-fitters.index') }}" class="btn btn-light btn-sm w-100">Reset</a>
        </div>
    </form>
</div>

<div class="card mf-chart-card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table mf-table" >
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>State</th>
                        <th>District</th>
                        <th>City</th>
                        <th>Pincode</th>
                        <th>Status</th>
                        <th class="text-right">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($fitters as $fitter)
                        <tr>
                            <td>{{ $fitter->id }}</td>
                            <td>{{ $fitter->name }}</td>
                            <td>{{ $fitter->phone }}</td>
                            <td>{{ $fitter->state }}</td>
                            <td>{{ $fitter->district }}</td>
                            <td>{{ $fitter->city }}</td>
                            <td>{{ $fitter->pincode }}</td>
                            <td>
                                <span class="{{ $fitter->status == 'active' ? 'mf-badge-active' : 'mf-badge-inactive' }}">
                                    {{ ucfirst($fitter->status) }}
                                </span>
                            </td>
                            <td class="text-right">
                                @can('manual_fitter_show')
                                    <a href="{{ route('admin.manual-fitters.show', $fitter->id) }}" class="btn btn-sm btn-outline-info"><i class="fas fa-eye"></i></a>
                                @endcan
                                @can('manual_fitter_edit')
                                    <a href="{{ route('admin.manual-fitters.edit', $fitter->id) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                                @endcan
                                @can('manual_fitter_delete')
                                    <form action="{{ route('admin.manual-fitters.destroy', $fitter->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Pakka delete karna hai?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="text-center py-4 text-muted">Koi fitter nahi mila.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $fitters->links() }}
    </div>
</div>
@endsection

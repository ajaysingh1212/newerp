@extends('layouts.admin')

@section('content')
<style>
    .me-card { border: none; border-radius: 14px; box-shadow: 0 6px 24px rgba(124,58,237,.08); overflow: hidden; }
    .me-header { background: linear-gradient(135deg, #7C3AED 0%, #6366F1 100%); padding: 20px 24px; color: #fff; }
    .me-header h4 { margin: 0; font-weight: 600; }
    .me-header small { opacity: .85; }
    .me-btn { background: linear-gradient(135deg, #7C3AED, #6366F1); border: none; color: #fff; border-radius: 8px; padding: 8px 18px; transition: .2s; }
    .me-btn:hover { transform: translateY(-1px); box-shadow: 0 6px 16px rgba(124,58,237,.35); color:#fff; }
    .me-table thead th { background: #F5F3FF; color: #5B21B6; border: none; font-weight: 600; font-size: .85rem; }
    .me-table tbody tr { transition: .15s; }
    .me-table tbody tr:hover { background: #FAF5FF; }
    .badge-me { background: #EDE9FE; color: #6D28D9; border-radius: 20px; padding: 4px 10px; font-size: .75rem; }
    @media (max-width: 576px) { .me-header { padding: 16px; } }
</style>

<div class="card me-card animate__animated animate__fadeIn">
    <div class="me-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h4><i class="fas fa-user-tie mr-2"></i>Party</h4>
            <small>Manual Entry &raquo; Party List</small>
        </div>
        <div class="d-flex gap-2">
            @can('manual_party_create')
                <a href="{{ route('admin.manual-parties.create') }}" class="me-btn"><i class="fas fa-plus mr-1"></i> Add Party</a>
            @endcan
        </div>
    </div>

    <div class="card-body">
        <form method="GET" class="mb-3">
            <div class="input-group" style="max-width: 320px;">
                <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search name, phone, GST...">
                <div class="input-group-append">
                    <button class="btn btn-outline-secondary"><i class="fas fa-search"></i></button>
                </div>
            </div>
        </form>

        @if(session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        <div class="table-responsive">
            <table class="table me-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>State / District / City</th>
                        <th>GST</th>
                        <th>Status</th>
                        <th class="text-right">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($parties as $party)
                        <tr>
                            <td>{{ $party->id }}</td>
                            <td>{{ $party->name }}</td>
                            <td>{{ $party->phone }}</td>
                            <td>
                                <span class="badge-me">{{ $party->state ?? '-' }}</span>
                                <span class="badge-me">{{ $party->district ?? '-' }}</span>
                                <span class="badge-me">{{ $party->city ?? '-' }}</span>
                            </td>
                            <td>{{ $party->gst_number ?? '-' }}</td>
                            <td>
                                <span class="badge {{ $party->status == 'active' ? 'badge-success' : 'badge-secondary' }}">{{ ucfirst($party->status) }}</span>
                            </td>
                            <td class="text-right">
                                @can('manual_party_show')
                                    <a href="{{ route('admin.manual-parties.show', $party->id) }}" class="btn btn-sm btn-outline-info"><i class="fas fa-eye"></i></a>
                                @endcan
                                @can('manual_party_edit')
                                    <a href="{{ route('admin.manual-parties.edit', $party->id) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                                @endcan
                                @can('manual_party_delete')
                                    <form action="{{ route('admin.manual-parties.destroy', $party->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure? / Pakka delete karna hai?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center py-4 text-muted">Koi party nahi mili.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $parties->links() }}
    </div>
</div>
@endsection

@extends('layouts.admin')

@section('content')
<style>
/* 🔹 Table styling enhancements */
#deletionRequestsTable {
    border-radius: 0.5rem;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
}

#deletionRequestsTable thead {
    background: #f8f9fa;
    border-bottom: 2px solid #dee2e6;
}

#deletionRequestsTable tbody tr:hover {
    background-color: #f2f6ff !important;
}

#deletionRequestsTable td,
#deletionRequestsTable th {
    vertical-align: middle !important;
}

/* 🔹 Badge styling for status */
.badge {
    padding: 6px 10px;
    font-size: 0.85rem;
    border-radius: 0.35rem;
}

/* 🔹 Button hover improvements */
.btn-outline-primary:hover {
    background-color: #0d6efd;
    color: #fff;
}

/* 🔹 Card and header style */
.card-header {
    font-size: 1.1rem;
    letter-spacing: 0.3px;
}
</style>

<div class="card shadow-sm border-0">
    <div class="card-header bg-primary text-white fw-bold d-flex justify-content-between align-items-center">
        <span><i class="fa fa-user-times me-2"></i> Account Deletion Requests</span>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table id="deletionRequestsTable" class="table table-hover table-bordered align-middle mb-0">
                <thead class="table-light text-center">
                    <tr>
                        <th width="5%">ID</th>
                        <th width="30%">User</th>
                        <th width="15%">Status</th>
                        <th width="25%">Created</th>
                        <th width="15%">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($requests as $r)
                        <tr>
                            <td class="fw-semibold text-secondary text-center">{{ $r->id }}</td>
                            <td>
                                <div class="fw-semibold">{{ $r->user->name }}</div>
                                <small class="text-muted">{{ $r->user->email }}</small>
                            </td>
                            <td class="text-center">
                                <span class="badge 
                                    @if($r->status === 'pending') bg-warning text-dark 
                                    @elseif($r->status === 'approved') bg-success 
                                    @elseif($r->status === 'rejected') bg-danger 
                                    @else bg-secondary 
                                    @endif">
                                    {{ ucfirst($r->status) }}
                                </span>
                            </td>
                            <td class="text-center">{{ $r->created_at->format('d M Y, h:i A') }}</td>
                            <td class="text-center">
                                <a href="{{ route('admin.deletion.requests.show', $r->id) }}" 
                                   class="btn btn-sm btn-outline-primary px-3">
                                    <i class="fa fa-eye me-1"></i> View
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(function () {
    $('#deletionRequestsTable').DataTable({
        pageLength: 10,
        order: [[0, 'desc']],
        columnDefs: [
            { orderable: false, targets: [4] }
        ],
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search requests...",
            lengthMenu: "Show _MENU_ entries",
            paginate: {
                next: '<i class="fa fa-angle-right"></i>',
                previous: '<i class="fa fa-angle-left"></i>'
            },
            info: "Showing _START_ to _END_ of _TOTAL_ entries",
            infoEmpty: "No records available"
        }
    });
});
</script>
@endsection

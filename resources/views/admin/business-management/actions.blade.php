<div class="bm-actions" style="flex-wrap:nowrap">
    @can('business_management_show')<a class="btn btn-xs btn-light" href="{{ route('admin.business-management.show', $device) }}" title="View device"><i class="fas fa-eye"></i><span class="sr-only">View</span></a>@endcan
    @can('business_management_edit')<a class="btn btn-xs btn-info" href="{{ route('admin.business-management.edit', $device) }}" title="Edit device"><i class="fas fa-pen"></i><span class="sr-only">Edit</span></a>@endcan
    @can('business_management_activate')
        @if($device->status === 'pending')<form method="POST" action="{{ route('admin.business-management.activate', $device) }}">@csrf<button class="btn btn-xs btn-success" title="Activate device"><i class="fas fa-power-off"></i> Activate</button></form>@endif
    @endcan
    @can('business_management_delete')<form method="POST" action="{{ route('admin.business-management.destroy', $device) }}" onsubmit="return confirm('Delete this device?')">@csrf @method('DELETE')<button class="btn btn-xs btn-danger" title="Delete device"><i class="fas fa-trash-alt"></i><span class="sr-only">Delete</span></button></form>@endcan
</div>

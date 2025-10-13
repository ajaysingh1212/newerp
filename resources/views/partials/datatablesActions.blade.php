
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

<div style="display: flex; justify-content: space-between; align-items: center;">
    <!-- Left side buttons -->
    <div>
        @can($viewGate)
            <a class="btn btn-xs btn-primary" href="{{ route('admin.' . $crudRoutePart . '.show', $row->id) }}">
                {{ trans('global.view') }}
            </a>
        @endcan

        @can($editGate)
            <a class="btn btn-xs btn-info" href="{{ route('admin.' . $crudRoutePart . '.edit', $row->id) }}">
                {{ trans('global.edit') }}
            </a>
        @endcan

        @can($deleteGate)
            <form action="{{ route('admin.' . $crudRoutePart . '.destroy', $row->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                @method('DELETE')
                @csrf
                <input type="submit" class="btn btn-xs btn-danger" value="{{ trans('global.delete') }}">
            </form>
        @endcan
    </div>

    <!-- Right side activate button -->
     <div class="">
   @if (
    $row->created_by && 
    $row->created_by->status_cmd === 'enable' &&
    in_array($row->status, ['pending', 'processing'])
)
    <a class="px-3 fs-1" href="{{ route('admin.activation-requests.activate', $row->id) }}">
        <i class="bi bi-arrow-left-square-fill" style="font-size: 24px; color: green"></i>
    </a>
@endif

</div>

</div>

@extends('layouts.admin')
@section('content')

@can('user_create')
<div style="margin-bottom: 10px;" class="row">
    <div class="col-lg-12">
        <a class="btn btn-success" href="{{ route('admin.users.create') }}">
            {{ trans('global.add') }} {{ trans('cruds.user.title_singular') }}
        </a>
    </div>
</div>
@endcan

<div class="card shadow-lg border-0">
    <div class="card-header bg-primary text-white">
        <strong>{{ trans('cruds.user.title_singular') }} {{ trans('global.list') }}</strong>
    </div>

    <div class="card-body">
        <table class="table table-bordered table-striped table-hover w-100 table-sm datatable datatable-User">
            <thead class="bg-light">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Mobile</th>
                    <th>Status</th>
                    <th>Roles</th>
                    <th>Vehicle Count</th>
                    <th>Vehicle Numbers (KYC Status)</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->mobile_number }}</td>
                    <td>{{ $user->status ? \App\Models\User::STATUS_SELECT[$user->status] : '' }}</td>
                    <td>
                        @foreach($user->roles as $role)
                            <span class="badge bg-info">{{ $role->title }}</span>
                        @endforeach
                    </td>

                    <td>{{ $user->vehicles->count() }}</td>
                    <td>
                        @if($user->vehicles->count() > 0)
                            @foreach($user->vehicles as $vehicle)
                                <span class="{{ $vehicle->kyc_status == 'Completed' ? 'text-success' : 'text-danger' }}">
                                    {{ $vehicle->vehicle_number }} ({{ $vehicle->kyc_status }})
                                </span>@if(!$loop->last), @endif
                            @endforeach
                        @else
                            <span class="text-danger">No Vehicle</span>
                        @endif
                    </td>
                    <td>
                        @can('user_show')
                            <a class="btn btn-xs btn-primary" href="{{ route('admin.users.show', $user->id) }}">
                                {{ trans('global.view') }}
                            </a>
                        @endcan
                        @can('user_edit')
                            <a class="btn btn-xs btn-info" href="{{ route('admin.users.edit', $user->id) }}">
                                {{ trans('global.edit') }}
                            </a>
                        @endcan
                        @can('user_delete')
                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <input type="submit" class="btn btn-xs btn-danger" value="{{ trans('global.delete') }}" onclick="return confirm('{{ trans('global.areYouSure') }}');">
                            </form>
                        @endcan
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
@parent
<script>
$(function () {
    let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
    @can('user_delete')
    let deleteButtonTrans = '{{ trans('global.datatables.delete') }}';
    let deleteButton = {
        text: deleteButtonTrans,
        url: "{{ route('admin.users.massDestroy') }}",
        className: 'btn-danger',
        action: function (e, dt, node, config) {
            var ids = $.map(dt.rows({ selected: true }).data(), function (entry) {
                return entry.id
            });
            if (ids.length === 0) {
                alert('{{ trans('global.datatables.zero_selected') }}')
                return
            }
            if (confirm('{{ trans('global.areYouSure') }}')) {
                $.ajax({
                    headers: {'x-csrf-token': _token},
                    method: 'POST',
                    url: config.url,
                    data: { ids: ids, _method: 'DELETE' }
                }).done(function () { location.reload() })
            }
        }
    }
    dtButtons.push(deleteButton)
    @endcan

    $('.datatable-User').DataTable({
        buttons: dtButtons,
        orderCellsTop: true,
        order: [[1, 'desc']],
        pageLength: 100,
    });
});
</script>
@endsection

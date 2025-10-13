@extends('layouts.admin')

@section('content')
@can('check_party_stock_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">

        </div>
    </div>
@endcan

<div class="card">
    @include('watermark')
    <div class="card-header">
        {{ trans('cruds.checkPartyStock.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-6">
                <label>Role</label>
                <select id="filter-role" class="form-control">
                    <option value="">-- Select Role --</option>
                    @foreach(\Spatie\Permission\Models\Role::all() as $role)
                        <option value="{{ $role->id }}">{{ $role->title }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label>User</label>
                <select id="filter-user" class="form-control">
                    <option value="">-- Select User --</option>
                </select>
            </div>

        </div>

        <table class="table table-bordered table-striped table-hover ajaxTable datatable datatable-CheckPartyStock">
            <thead>
                <tr>
                    <th width="10">#</th>
      
                    
                  
                    <th>SKU</th>
                    <th>Product Model</th>
                    <th>IMEI Number</th>
                    <th>VTS</th>
                  
                </tr>
            </thead>
        </table>
    </div>
</div>
@endsection

@section('scripts')
@parent
<script>
$(function () {
    let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons);

    let table = $('.datatable-CheckPartyStock').DataTable({
        buttons: dtButtons,
        processing: true,
        serverSide: true,
        retrieve: true,
        aaSorting: [],
        ajax: {
            url: "{{ route('admin.check-party-stocks.index') }}",
            data: function (d) {
                d.role = $('#filter-role').val();
                d.user_id = $('#filter-user').val();
                d.start_date = $('#start-date').val();
                d.end_date = $('#end-date').val();
            }
        },
        columns: [
            { data: 'placeholder', name: 'placeholder' },

         
            
            { data: 'sku', name: 'sku' },
            { data: 'product_model', name: 'productById.product_model.product_model' },
            { data: 'imei_number', name: 'productById.imei.imei_number' },
            { data: 'vts_name', name: 'productById.vts.vts_number' },
           
        ],
        orderCellsTop: true,
        order: [[4, 'desc']],
        pageLength: 25,
    });

    $('#filter-role, #filter-user, #start-date, #end-date').on('change', function () {
        table.ajax.reload();
    });

    $('#filter-role').on('change', function () {
        var roleId = $(this).val();
        $('#filter-user').html('<option value="">-- Loading --</option>');
        if (roleId) {
            $.ajax({
                url: "{{ route('admin.users.byRole') }}",
                data: { role: roleId },
                success: function (data) {
                    $('#filter-user').empty().append('<option value="">-- Select User --</option>');
                    $.each(data, function (key, user) {
                        $('#filter-user').append('<option value="'+ user.id +'">'+ user.name +'</option>');
                    });
                }
            });
        } else {
            $('#filter-user').html('<option value="">-- Select User --</option>');
        }
    });

});
</script>
@endsection

@extends('layouts.admin')

@section('content')
@can('activation_request_create')
    <div class="row mb-2">
        <div class="col-lg-12 d-flex gap-2">
            <a class="btn btn-success" href="{{ route('admin.activation-requests.create') }}">
                {{ trans('global.add') }} {{ trans('cruds.activationRequest.title_singular') }}
            </a>
            <button class="btn btn-warning" data-toggle="modal" data-target="#csvImportModal">
                {{ trans('global.app_csvImport') }}
            </button>
        </div>
    </div>
    @include('csvImport.modal', ['model' => 'ActivationRequest', 'route' => 'admin.activation-requests.parseCsvImport'])
@endcan

<div class="card">
    @include('watermark')

    <div class="card-header">
        {{ trans('cruds.activationRequest.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <table class="table table-bordered table-striped table-hover datatable datatable-ActivationRequest">
            <thead>
                <tr>
                    <th>check</th>
                    <th>ID</th>
                    
                    <th>Party Name</th>
                    <th>Email</th>
                    <th>Product Details</th>
                    <th>Request Date</th>
                    
                    <th>Vehicle Reg No</th>
                    <th>Device Details (AMC / Warranty / Subscription)</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                @foreach($activationRequests as $row)
                    <tr>
                        <td></td>
                        <td>{{ $row->id }}</td>
                       
                        <td>{{ $row->select_party->name ?? '' }}</td>
                        <td>{{ $row->select_party->email ?? '' }}</td>
                        
                        {{-- Product Details --}}
                        <td>
                            @if($row->product_master)
                                @php
                                    $p = $row->product_master;
                                @endphp
                                <strong>SKU:</strong> {{ $p->sku ?? 'N/A' }}<br>
                                <a href="javascript:void(0);" class="view-more-toggle" data-target="pdetails-{{ $row->id }}">View More</a>
                                <div id="pdetails-{{ $row->id }}" style="display:none; margin-top:5px;">
                                    <strong>Model:</strong> {{ $p->product_model->product_model ?? 'N/A' }}<br>
                                    <strong>IMEI:</strong> {{ $p->imei->imei_number ?? 'N/A' }}<br>
                                    <strong>VTS:</strong> {{ $p->vts->vts_number ?? 'N/A' }}
                                </div>
                            @else
                                No Product Info
                            @endif
                        </td>

                        <td>{{ $row->request_date ? date('d M Y', strtotime($row->request_date)) : '' }}</td>
                        
                        <td>{{ $row->vehicle_reg_no ?? '' }}</td>

                        {{-- Device Details --}}
                  <style>
    @keyframes blink {
        50% { opacity: 0; }
    }
    .blink {
        animation: blink 1s step-start infinite;
        font-weight: 600;
    }
</style>

<td>
    @if($row->product_master && $row->product_master->product_model)
        @php
            $now = \Carbon\Carbon::now();
            $requestDate = $row->request_date ? \Carbon\Carbon::parse($row->request_date) : null;
            $model = $row->product_master->product_model;

            $getDaysLeft = function ($baseDate, $months) use ($now) {
                if (!$baseDate || !$months) return ['days' => '-', 'class' => ''];

                $expiryDate = (clone $baseDate)->addMonths($months);
                $days = $now->diffInDays($expiryDate, false);

                $class = $days < 0 ? 'text-danger blink' : 'text-success blink';
                return ['days' => $days, 'class' => $class];
            };

            $amc = $getDaysLeft($requestDate, $model->amc ?? 0);
            $warranty = $getDaysLeft($requestDate, $model->warranty ?? 0);
            $sub = $getDaysLeft($requestDate, $model->subscription ?? 0);
        @endphp

        <strong>AMC:</strong>
        <span class="{{ $amc['class'] }}">
            {{ $amc['days'] === '-' ? '-' : ($amc['days'] >= 0 ? $amc['days'].' days left' : abs($amc['days']).' days expired') }}
        </span><br>

        <strong>Warranty:</strong>
        <span class="{{ $warranty['class'] }}">
            {{ $warranty['days'] === '-' ? '-' : ($warranty['days'] >= 0 ? $warranty['days'].' days left' : abs($warranty['days']).' days expired') }}
        </span><br>

        <strong>Subscription:</strong>
        <span class="{{ $sub['class'] }}">
            {{ $sub['days'] === '-' ? '-' : ($sub['days'] >= 0 ? $sub['days'].' days left' : abs($sub['days']).' days expired') }}
        </span>
    @else
        <em>-</em>
    @endif
</td>




                        {{-- Status --}}
                        <td>
                            @php
                                $statusClass = match($row->status) {
                                    'pending' => 'badge badge-warning',
                                    'processing' => 'badge badge-primary',
                                    'activated' => 'badge badge-success',
                                    'rejected' => 'badge badge-danger',
                                    default => 'badge badge-secondary'
                                };
                            @endphp
                            <span class="{{ $statusClass }}">{{ ucfirst($row->status ?? '') }}</span>
                        </td>

                        {{-- Actions --}}
                        <td>
                            @include('partials.datatablesActions', [
                                'viewGate' => 'activation_request_show',
                                'editGate' => 'activation_request_edit',
                                'deleteGate' => 'activation_request_delete',
                                'crudRoutePart' => 'activation-requests',
                                'row' => $row
                            ])
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
    // “View More” Toggle for Product Details
    $(document).on('click', '.view-more-toggle', function () {
        let target = $(this).data('target');
        $('#' + target).slideToggle(200);
    });

    // Initialize DataTable (client-side only)
    $('.datatable-ActivationRequest').DataTable({
        order: [[0, 'desc']],
        pageLength: 25
    });
});
</script>
@endsection

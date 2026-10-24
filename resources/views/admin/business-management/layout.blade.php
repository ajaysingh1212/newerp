@extends('layouts.admin')
@section('styles')
@parent
<link rel="stylesheet" href="{{ asset('css/business-management.css') }}">
@endsection
@section('content')
<div class="bm-workspace">
    <header class="bm-hero">
        <div><div class="bm-eyebrow"><span class="bm-live-dot"></span> BUSINESS OPERATIONS</div><h1>@yield('business-title', 'Business Management')</h1><p>@yield('business-description', 'One workspace for your parties, devices and activations.')</p></div>
        <div class="bm-hero-icon" aria-hidden="true"><i class="fas @yield('business-icon', 'fa-briefcase')"></i></div>
    </header>
    <nav class="bm-navigation" aria-label="Business Management navigation">
        @can('business_management_access')<a class="{{ request()->routeIs('admin.business-management.index', 'admin.business-management.show') ? 'is-active' : '' }}" href="{{ route('admin.business-management.index') }}"><i class="fas fa-layer-group"></i> Device inventory</a>@endcan
        @can('business_management_create')<a class="{{ request()->routeIs('admin.business-management.create', 'admin.business-management.edit') ? 'is-active' : '' }}" href="{{ route('admin.business-management.create') }}"><i class="fas fa-plus-circle"></i> Add devices</a>@endcan
        @can('business_management_import')<a class="{{ request()->routeIs('admin.business-management.import*') ? 'is-active' : '' }}" href="{{ route('admin.business-management.importForm') }}"><i class="fas fa-file-import"></i> Import workspace</a>@endcan
        @can('business_management_report')<a class="{{ request()->routeIs('admin.business-management.report') ? 'is-active' : '' }}" href="{{ route('admin.business-management.report') }}"><i class="fas fa-chart-pie"></i> Reports & insights</a>@endcan
        @can('business_management_report')<a class="{{ request()->routeIs('admin.business-imports.*') ? 'is-active' : '' }}" href="{{ route('admin.business-imports.index') }}"><i class="fas fa-book"></i> Imported source ledger</a>@endcan
    </nav>
    @include('admin.business-management.alerts')
    @yield('business-content')
</div>
@endsection
@section('scripts')
@parent
<script src="{{ asset('js/business-management.js') }}"></script>
@yield('business-scripts')
@endsection

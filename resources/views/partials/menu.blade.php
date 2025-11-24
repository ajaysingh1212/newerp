<style>

.img__wrapper{


  overflow:hidden;
}
.img__wrapper img{
  width: 100%;
}
.sold_out {
    top: 2em;
    left: -4em;
    color: #fff;
    display: block;
    position:absolute;
    text-align: center;
    text-decoration: none;
    letter-spacing: .06em;
    background-color: #A00;
    padding: 0.5em 5em 0.4em 5em;
    text-shadow: 0 0 0.75em #444;
    box-shadow: 0 0 0.5em rgba(0,0,0,0.5);
    font: bold 16px/1.2em Arial, Sans-Serif;
    -webkit-text-shadow: 0 0 0.75em #444;
    -webkit-box-shadow: 0 0 0.5em rgba(0,0,0,0.5);
    -webkit-transform: rotate(-45deg) scale(0.75,1);
    z-index:10;
}
.sold_out:before {
    content: '';
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    position: absolute;
    margin: -0.3em -5em;
    transform: scale(0.7);
    -webkit-transform: scale(0.7);
    border: 2px rgba(255,255,255,0.7) dashed;
}
/* Sidebar Card Container */
/* Sidebar card styling */
.sidebar-card {
    background-color: #ffffff !important;
    border-radius: 10px !important;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1) !important;
    padding: 5px !important;
    margin: 20px 20px !important;
    position:fixed !important;
}

/* Parent nav item with red background when open */
.nav-sidebar .nav-item.menu-open {
    background-color:rgba(72, 66, 66, 0.45) !important;
    color: #fff !important;
    border-radius: 0.5rem !important;
    
}

.sidebar-light-primary .nav-sidebar>.nav-item.menu-open>.nav-link{
    background-color:rgb(8, 74, 226) !important;
    color: #fff !important;
    border-radius: 0.5rem !important;
}


/* Icon and text inside open nav item */
.nav-sidebar .nav-item.menu-open > .nav-link i,
.nav-sidebar .nav-item.menu-open > .nav-link p {
    color: #fff !important;
}

/* Hover effect for parent nav items */
.nav-sidebar .nav-link:hover {
    background-color:rgb(93, 81, 83) !important; /* Slightly darker red */
    color: #fff !important;
}

/* Submenu links */
.nav-sidebar .nav-treeview .nav-link {
    padding-left: 2.5rem !important;
    color:rgb(15, 14, 14) !important; /* Dark text */
    font-weight: 400 !important;
}

/* Submenu hover */
.nav-sidebar .nav-treeview .nav-link:hover {
    background-color: #e9ecef !important;
    color: #000 !important;
}

/* Active submenu link */
.nav-sidebar .nav-treeview .nav-link.active {
    background-color:rgba(65, 28, 31, 0.44) !important;
    color: #fff !important;
    font-weight: 500 !important;
}

/* Active submenu icon */
.nav-sidebar .nav-treeview .nav-link.active i {
    color: #fff !important;
}

/* Optional: remove dark sidebar background if needed */
.main-sidebar {
    background-color: #fff !important;
    color: #000 !important;
}



</style>
<aside class="main-sidebar sidebar-light-primary elevation-4 sidebar-card">
    <!-- Brand Logo -->
    <a href="#" class="brand-link">
        <div class="img__wrapper">
            <img src="http://www.savoy-sharm.com/media-room/images/hi-res/king-bed-room-accommodation-savoy-luxury-5-stars-accommodation-sharm-el-sheikh.jpg" alt="" />
            <a class="sold_out" href=""> ({{ Auth::user()->roles->pluck('title')->join(', ') }})</a>
           
        </div>
        <span class="brand-text " style="position: absolute;top:50px;left:70px;color:black; font-weight: bold">{{ trans('panel.site_title') }}</span>

    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user (optional) -->

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs("admin.home") ? "active" : "" }}" href="{{ route("admin.home") }}">
                        <i class="fas fa-fw fa-tachometer-alt nav-icon">
                        </i>
                        <p>
                            {{ trans('global.dashboard') }}
                        </p>
                    </a>
                </li>
                @can('user_management_access')
                    <li class="nav-item has-treeview {{ request()->is("admin/permissions*") ? "menu-open" : "" }} {{ request()->is("admin/roles*") ? "menu-open" : "" }} {{ request()->is("admin/users*") ? "menu-open" : "" }} {{ request()->is("admin/teams*") ? "menu-open" : "" }}">
                        <a class="nav-link nav-dropdown-toggle {{ request()->is("admin/permissions*") ? "active" : "" }} {{ request()->is("admin/roles*") ? "active" : "" }} {{ request()->is("admin/users*") ? "active" : "" }} {{ request()->is("admin/teams*") ? "active" : "" }}" href="#">
                            <i class="fa-fw nav-icon fas fa-users">

                            </i>
                            <p>
                                {{ trans('cruds.userManagement.title') }}
                                <i class="right fa fa-fw fa-angle-left nav-icon"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('permission_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.permissions.index") }}" class="nav-link {{ request()->is("admin/permissions") || request()->is("admin/permissions/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-unlock-alt">

                                        </i>
                                        <p>
                                            {{ trans('cruds.permission.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('role_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.roles.index") }}" class="nav-link {{ request()->is("admin/roles") || request()->is("admin/roles/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-briefcase">

                                        </i>
                                        <p>
                                            {{ trans('cruds.role.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('user_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.users.index") }}" class="nav-link {{ request()->is("admin/users") || request()->is("admin/users/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-user">

                                        </i>
                                        <p>
                                            {{ trans('cruds.user.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('team_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.teams.index") }}" class="nav-link {{ request()->is("admin/teams") || request()->is("admin/teams/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-users">

                                        </i>
                                        <p>
                                            {{ trans('cruds.team.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('deletion_request_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.deletion.requests.index") }}" class="nav-link {{ request()->is("admin/teams") || request()->is("admin/teams/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-user-times">

                                        </i>
                                        <p>
                                            {{ trans('cruds.deletionRequest.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan

                @can('investor_access')
                    <li class="nav-item has-treeview {{ request()->is("admin/registrations*") ? "menu-open" : "" }} {{ request()->is("admin/investments*") ? "menu-open" : "" }} {{ request()->is("admin/withdrawal-requests*") ? "menu-open" : "" }}">
                        <a class="nav-link nav-dropdown-toggle {{ request()->is("admin/registrations*") ? "active" : "" }} {{ request()->is("admin/investments*") ? "active" : "" }} {{ request()->is("admin/withdrawal-requests*") ? "active" : "" }}" href="#">
                            <i class="fa-fw nav-icon fas fa-user">

                            </i>
                            <p>
                                {{ trans('cruds.investor.title') }}
                                <i class="right fa fa-fw fa-angle-left nav-icon"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('registration_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.registrations.index") }}" class="nav-link {{ request()->is("admin/registrations") || request()->is("admin/registrations/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-address-card">

                                        </i>
                                        <p>
                                            {{ trans('cruds.registration.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('investment_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.investments.index") }}" class="nav-link {{ request()->is("admin/investments") || request()->is("admin/investments/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-hand-holding-usd">

                                        </i>
                                        <p>
                                            {{ trans('cruds.investment.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('withdrawal_request_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.withdrawal-requests.index") }}" class="nav-link {{ request()->is("admin/withdrawal-requests") || request()->is("admin/withdrawal-requests/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fab fa-amazon-pay">

                                        </i>
                                        <p>
                                            {{ trans('cruds.withdrawalRequest.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan
                @can('investment_plan_access')
                    <li class="nav-item has-treeview {{ request()->is("admin/plans*") ? "menu-open" : "" }} {{ request()->is("admin/monthly-payout-records*") ? "menu-open" : "" }} {{ request()->is("admin/investor-transactions*") ? "menu-open" : "" }} {{ request()->is("admin/login-logs*") ? "menu-open" : "" }}">
                        <a class="nav-link nav-dropdown-toggle {{ request()->is("admin/plans*") ? "active" : "" }} {{ request()->is("admin/monthly-payout-records*") ? "active" : "" }} {{ request()->is("admin/investor-transactions*") ? "active" : "" }} {{ request()->is("admin/login-logs*") ? "active" : "" }}" href="#">
                            <i class="fa-fw nav-icon fas fa-spa">

                            </i>
                            <p>
                                {{ trans('cruds.investmentPlan.title') }}
                                <i class="right fa fa-fw fa-angle-left nav-icon"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('plan_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.plans.index") }}" class="nav-link {{ request()->is("admin/plans") || request()->is("admin/plans/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fab fa-pagelines">

                                        </i>
                                        <p>
                                            {{ trans('cruds.plan.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('monthly_payout_record_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.monthly-payout-records.index") }}" class="nav-link {{ request()->is("admin/monthly-payout-records") || request()->is("admin/monthly-payout-records/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-exchange-alt">

                                        </i>
                                        <p>
                                            {{ trans('cruds.monthlyPayoutRecord.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('investor_transaction_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.investor-transactions.index") }}" class="nav-link {{ request()->is("admin/investor-transactions") || request()->is("admin/investor-transactions/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-hand-holding-heart">

                                        </i>
                                        <p>
                                            {{ trans('cruds.investorTransaction.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('login_log_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.login-logs.index") }}" class="nav-link {{ request()->is("admin/login-logs") || request()->is("admin/login-logs/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-fingerprint">

                                        </i>
                                        <p>
                                            {{ trans('cruds.loginLog.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan
                @can('product_access')
                    <li class="nav-item has-treeview {{ request()->is("admin/vts*") ? "menu-open" : "" }} {{ request()->is("admin/imei-models*") ? "menu-open" : "" }} {{ request()->is("admin/imei-masters*") ? "menu-open" : "" }} {{ request()->is("admin/product-models*") ? "menu-open" : "" }} {{ request()->is("admin/product-masters*") ? "menu-open" : "" }} {{ request()->is("admin/unbind-products*") ? "menu-open" : "" }}">
                        <a class="nav-link nav-dropdown-toggle {{ request()->is("admin/vts*") ? "active" : "" }} {{ request()->is("admin/imei-models*") ? "active" : "" }} {{ request()->is("admin/imei-masters*") ? "active" : "" }} {{ request()->is("admin/product-models*") ? "active" : "" }} {{ request()->is("admin/product-masters*") ? "active" : "" }} {{ request()->is("admin/unbind-products*") ? "active" : "" }}" href="#">
                            <i class="fa-fw nav-icon fab fa-product-hunt">

                            </i>
                            <p>
                                {{ trans('cruds.product.title') }}
                                <i class="right fa fa-fw fa-angle-left nav-icon"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('vt_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.vts.index") }}" class="nav-link {{ request()->is("admin/vts") || request()->is("admin/vts/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fab fa-audible">

                                        </i>
                                        <p>
                                            {{ trans('cruds.vt.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('imei_model_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.imei-models.index") }}" class="nav-link {{ request()->is("admin/imei-models") || request()->is("admin/imei-models/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-align-justify">

                                        </i>
                                        <p>
                                            {{ trans('cruds.imeiModel.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('imei_master_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.imei-masters.index") }}" class="nav-link {{ request()->is("admin/imei-masters") || request()->is("admin/imei-masters/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-indent">

                                        </i>
                                        <p>
                                            {{ trans('cruds.imeiMaster.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('product_model_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.product-models.index") }}" class="nav-link {{ request()->is("admin/product-models") || request()->is("admin/product-models/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-bezier-curve">

                                        </i>
                                        <p>
                                            {{ trans('cruds.productModel.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('product_master_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.product-masters.index") }}" class="nav-link {{ request()->is("admin/product-masters") || request()->is("admin/product-masters/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-chart-line">

                                        </i>
                                        <p>
                                            {{ trans('cruds.productMaster.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('unbind_product_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.unbind-products.index") }}" class="nav-link {{ request()->is("admin/unbind-products") || request()->is("admin/unbind-products/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-arrows-alt-h">

                                        </i>
                                        <p>
                                            {{ trans('cruds.unbindProduct.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan
                @can('stock_access')
                    <li class="nav-item has-treeview {{ request()->is("admin/current-stocks*") ? "menu-open" : "" }} {{ request()->is("admin/stock-transfers*") ? "menu-open" : "" }} {{ request()->is("admin/check-party-stocks*") ? "menu-open" : "" }}">
                        <a class="nav-link nav-dropdown-toggle {{ request()->is("admin/current-stocks*") ? "active" : "" }} {{ request()->is("admin/stock-transfers*") ? "active" : "" }} {{ request()->is("admin/check-party-stocks*") ? "active" : "" }}" href="#">
                            <i class="fa-fw nav-icon far fa-hdd">

                            </i>
                            <p>
                                {{ trans('cruds.stock.title') }}
                                <i class="right fa fa-fw fa-angle-left nav-icon"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('current_stock_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.current-stocks.index") }}" class="nav-link {{ request()->is("admin/current-stocks") || request()->is("admin/current-stocks/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-box-open">

                                        </i>
                                        <p>
                                            {{ trans('cruds.currentStock.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('stock_transfer_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.stock-transfers.index") }}" class="nav-link {{ request()->is("admin/stock-transfers") || request()->is("admin/stock-transfers/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-exchange-alt">

                                        </i>
                                        <p>
                                            {{ trans('cruds.stockTransfer.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('check_party_stock_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.check-party-stocks.index") }}" class="nav-link {{ request()->is("admin/check-party-stocks") || request()->is("admin/check-party-stocks/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-hands-helping">

                                        </i>
                                        <p>
                                            {{ trans('cruds.checkPartyStock.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan
                @can('activation_access')
                    <li class="nav-item has-treeview {{ request()->is("admin/activation-requests*") ? "menu-open" : "" }} {{ request()->is("admin/attach-veichles*") ? "menu-open" : "" }}">
                        <a class="nav-link nav-dropdown-toggle {{ request()->is("admin/activation-requests*") ? "active" : "" }} {{ request()->is("admin/attach-veichles*") ? "active" : "" }}" href="#">
                            <i class="fa-fw nav-icon fas fa-chart-line">

                            </i>
                            <p>
                                {{ trans('cruds.activation.title') }}
                                <i class="right fa fa-fw fa-angle-left nav-icon"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('activation_request_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.activation-requests.index") }}" class="nav-link {{ request()->is("admin/activation-requests") || request()->is("admin/activation-requests/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-chart-line">

                                        </i>
                                        <p>
                                            {{ trans('cruds.activationRequest.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('attach_veichle_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.attach-veichles.index") }}" class="nav-link {{ request()->is("admin/attach-veichles") || request()->is("admin/attach-veichles/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-taxi">

                                        </i>
                                        <p>
                                            {{ trans('cruds.attachVeichle.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan
@can('complain_management_access')
                    <li class="nav-item has-treeview {{ request()->is("admin/complain-categories*") ? "menu-open" : "" }} {{ request()->is("admin/check-complains*") ? "menu-open" : "" }}">
                        <a class="nav-link nav-dropdown-toggle {{ request()->is("admin/complain-categories*") ? "active" : "" }} {{ request()->is("admin/check-complains*") ? "active" : "" }}" href="#">
                            <i class="fa-fw nav-icon fab fa-accusoft">

                            </i>
                            <p>
                                {{ trans('cruds.complainManagement.title') }}
                                <i class="right fa fa-fw fa-angle-left nav-icon"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('complain_category_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.complain-categories.index") }}" class="nav-link {{ request()->is("admin/complain-categories") || request()->is("admin/complain-categories/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-list-alt">

                                        </i>
                                        <p>
                                            {{ trans('cruds.complainCategory.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('check_complain_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.check-complains.index") }}" class="nav-link {{ request()->is("admin/check-complains") || request()->is("admin/check-complains/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon far fa-list-alt">

                                        </i>
                                        <p>
                                            {{ trans('cruds.checkComplain.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan
              
              @can('master_access')
                    <li class="nav-item has-treeview {{ request()->is("admin/*") ? "menu-open" : "" }} {{ request()->is("admin/*") ? "menu-open" : "" }} {{ request()->is("admin/app-links*") ? "menu-open" : "" }} {{ request()->is("admin/app-downloads*") ? "menu-open" : "" }}">
                        <a class="nav-link nav-dropdown-toggle {{ request()->is("admin/*") ? "active" : "" }} {{ request()->is("admin/*") ? "active" : "" }} {{ request()->is("admin/app-links*") ? "active" : "" }} {{ request()->is("admin/app-downloads*") ? "active" : "" }}" href="#">
                            <i class="fa-fw nav-icon fab fa-mastodon">

                            </i>
                            <p>
                                {{ trans('cruds.master.title') }}
                                <i class="right fa fa-fw fa-angle-left nav-icon"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('location_access')
                                <li class="nav-item has-treeview {{ request()->is("admin/states*") ? "menu-open" : "" }} {{ request()->is("admin/districts*") ? "menu-open" : "" }}">
                                    <a class="nav-link nav-dropdown-toggle {{ request()->is("admin/states*") ? "active" : "" }} {{ request()->is("admin/districts*") ? "active" : "" }}" href="#">
                                        <i class="fa-fw nav-icon fas fa-location-arrow">

                                        </i>
                                        <p>
                                            {{ trans('cruds.location.title') }}
                                            <i class="right fa fa-fw fa-angle-left nav-icon"></i>
                                        </p>
                                    </a>
                                    <ul class="nav nav-treeview">
                                        @can('state_access')
                                            <li class="nav-item">
                                                <a href="{{ route("admin.states.index") }}" class="nav-link {{ request()->is("admin/states") || request()->is("admin/states/*") ? "active" : "" }}">
                                                    <i class="fa-fw nav-icon fas fa-map-signs">

                                                    </i>
                                                    <p>
                                                        {{ trans('cruds.state.title') }}
                                                    </p>
                                                </a>
                                            </li>
                                        @endcan
                                        @can('district_access')
                                            <li class="nav-item">
                                                <a href="{{ route("admin.districts.index") }}" class="nav-link {{ request()->is("admin/districts") || request()->is("admin/districts/*") ? "active" : "" }}">
                                                    <i class="fa-fw nav-icon fas fa-street-view">

                                                    </i>
                                                    <p>
                                                        {{ trans('cruds.district.title') }}
                                                    </p>
                                                </a>
                                            </li>
                                        @endcan
                                    </ul>
                                </li>
                            @endcan
                            @can('vehicle_access')
                                <li class="nav-item has-treeview {{ request()->is("admin/vehicle-types*") ? "menu-open" : "" }}">
                                    <a class="nav-link nav-dropdown-toggle {{ request()->is("admin/vehicle-types*") ? "active" : "" }}" href="#">
                                        <i class="fa-fw nav-icon fas fa-taxi">

                                        </i>
                                        <p>
                                            {{ trans('cruds.vehicle.title') }}
                                            <i class="right fa fa-fw fa-angle-left nav-icon"></i>
                                        </p>
                                    </a>
                                    <ul class="nav nav-treeview">
                                        @can('vehicle_type_access')
                                            <li class="nav-item">
                                                <a href="{{ route("admin.vehicle-types.index") }}" class="nav-link {{ request()->is("admin/vehicle-types") || request()->is("admin/vehicle-types/*") ? "active" : "" }}">
                                                    <i class="fa-fw nav-icon fas fa-car">

                                                    </i>
                                                    <p>
                                                        {{ trans('cruds.vehicleType.title') }}
                                                    </p>
                                                </a>
                                            </li>
                                        @endcan
                                    </ul>
                                </li>
                            @endcan
                            @can('app_link_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.app-links.index") }}" class="nav-link {{ request()->is("admin/app-links") || request()->is("admin/app-links/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-mobile">

                                        </i>
                                        <p>
                                            {{ trans('cruds.appLink.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('app_download_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.app-downloads.index") }}" class="nav-link {{ request()->is("admin/app-downloads") || request()->is("admin/app-downloads/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fab fa-app-store">

                                        </i>
                                        <p>
                                            {{ trans('cruds.appDownload.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan
                @can('expense_management_access')
                    <li class="nav-item has-treeview {{ request()->is("admin/expense-categories*") ? "menu-open" : "" }} {{ request()->is("admin/income-categories*") ? "menu-open" : "" }} {{ request()->is("admin/expenses*") ? "menu-open" : "" }} {{ request()->is("admin/incomes*") ? "menu-open" : "" }} {{ request()->is("admin/expense-reports*") ? "menu-open" : "" }}">
                        <a class="nav-link nav-dropdown-toggle {{ request()->is("admin/expense-categories*") ? "active" : "" }} {{ request()->is("admin/income-categories*") ? "active" : "" }} {{ request()->is("admin/expenses*") ? "active" : "" }} {{ request()->is("admin/incomes*") ? "active" : "" }} {{ request()->is("admin/expense-reports*") ? "active" : "" }}" href="#">
                            <i class="fa-fw nav-icon fas fa-money-bill">

                            </i>
                            <p>
                                {{ trans('cruds.expenseManagement.title') }}
                                <i class="right fa fa-fw fa-angle-left nav-icon"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('expense_category_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.expense-categories.index") }}" class="nav-link {{ request()->is("admin/expense-categories") || request()->is("admin/expense-categories/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-list">

                                        </i>
                                        <p>
                                            {{ trans('cruds.expenseCategory.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('income_category_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.income-categories.index") }}" class="nav-link {{ request()->is("admin/income-categories") || request()->is("admin/income-categories/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-list">

                                        </i>
                                        <p>
                                            {{ trans('cruds.incomeCategory.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('expense_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.expenses.index") }}" class="nav-link {{ request()->is("admin/expenses") || request()->is("admin/expenses/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-arrow-circle-right">

                                        </i>
                                        <p>
                                            {{ trans('cruds.expense.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('income_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.incomes.index") }}" class="nav-link {{ request()->is("admin/incomes") || request()->is("admin/incomes/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-arrow-circle-right">

                                        </i>
                                        <p>
                                            {{ trans('cruds.income.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('expense_report_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.expense-reports.index") }}" class="nav-link {{ request()->is("admin/expense-reports") || request()->is("admin/expense-reports/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-chart-line">

                                        </i>
                                        <p>
                                            {{ trans('cruds.expenseReport.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan
                @can('client_management_setting_access')
                    <li class="nav-item has-treeview {{ request()->is("admin/currencies*") ? "menu-open" : "" }} {{ request()->is("admin/transaction-types*") ? "menu-open" : "" }} {{ request()->is("admin/income-sources*") ? "menu-open" : "" }} {{ request()->is("admin/client-statuses*") ? "menu-open" : "" }} {{ request()->is("admin/project-statuses*") ? "menu-open" : "" }}">
                        <a class="nav-link nav-dropdown-toggle {{ request()->is("admin/currencies*") ? "active" : "" }} {{ request()->is("admin/transaction-types*") ? "active" : "" }} {{ request()->is("admin/income-sources*") ? "active" : "" }} {{ request()->is("admin/client-statuses*") ? "active" : "" }} {{ request()->is("admin/project-statuses*") ? "active" : "" }}" href="#">
                            <i class="fa-fw nav-icon fas fa-cog">

                            </i>
                            <p>
                                {{ trans('cruds.clientManagementSetting.title') }}
                                <i class="right fa fa-fw fa-angle-left nav-icon"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('currency_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.currencies.index") }}" class="nav-link {{ request()->is("admin/currencies") || request()->is("admin/currencies/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-money-bill">

                                        </i>
                                        <p>
                                            {{ trans('cruds.currency.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('transaction_type_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.transaction-types.index") }}" class="nav-link {{ request()->is("admin/transaction-types") || request()->is("admin/transaction-types/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-money-check">

                                        </i>
                                        <p>
                                            {{ trans('cruds.transactionType.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('income_source_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.income-sources.index") }}" class="nav-link {{ request()->is("admin/income-sources") || request()->is("admin/income-sources/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-database">

                                        </i>
                                        <p>
                                            {{ trans('cruds.incomeSource.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('client_status_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.client-statuses.index") }}" class="nav-link {{ request()->is("admin/client-statuses") || request()->is("admin/client-statuses/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-server">

                                        </i>
                                        <p>
                                            {{ trans('cruds.clientStatus.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('project_status_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.project-statuses.index") }}" class="nav-link {{ request()->is("admin/project-statuses") || request()->is("admin/project-statuses/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-server">

                                        </i>
                                        <p>
                                            {{ trans('cruds.projectStatus.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan
                @can('client_management_access')
                    <li class="nav-item has-treeview {{ request()->is("admin/clients*") ? "menu-open" : "" }} {{ request()->is("admin/projects*") ? "menu-open" : "" }} {{ request()->is("admin/notes*") ? "menu-open" : "" }} {{ request()->is("admin/documents*") ? "menu-open" : "" }} {{ request()->is("admin/transactions*") ? "menu-open" : "" }} {{ request()->is("admin/client-reports*") ? "menu-open" : "" }}">
                        <a class="nav-link nav-dropdown-toggle {{ request()->is("admin/clients*") ? "active" : "" }} {{ request()->is("admin/projects*") ? "active" : "" }} {{ request()->is("admin/notes*") ? "active" : "" }} {{ request()->is("admin/documents*") ? "active" : "" }} {{ request()->is("admin/transactions*") ? "active" : "" }} {{ request()->is("admin/client-reports*") ? "active" : "" }}" href="#">
                            <i class="fa-fw nav-icon fas fa-briefcase">

                            </i>
                            <p>
                                {{ trans('cruds.clientManagement.title') }}
                                <i class="right fa fa-fw fa-angle-left nav-icon"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('client_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.clients.index") }}" class="nav-link {{ request()->is("admin/clients") || request()->is("admin/clients/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-user-plus">

                                        </i>
                                        <p>
                                            {{ trans('cruds.client.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('project_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.projects.index") }}" class="nav-link {{ request()->is("admin/projects") || request()->is("admin/projects/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-briefcase">

                                        </i>
                                        <p>
                                            {{ trans('cruds.project.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('note_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.notes.index") }}" class="nav-link {{ request()->is("admin/notes") || request()->is("admin/notes/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-sticky-note">

                                        </i>
                                        <p>
                                            {{ trans('cruds.note.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('document_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.documents.index") }}" class="nav-link {{ request()->is("admin/documents") || request()->is("admin/documents/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-file-alt">

                                        </i>
                                        <p>
                                            {{ trans('cruds.document.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('transaction_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.transactions.index") }}" class="nav-link {{ request()->is("admin/transactions") || request()->is("admin/transactions/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-credit-card">

                                        </i>
                                        <p>
                                            {{ trans('cruds.transaction.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('client_report_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.client-reports.index") }}" class="nav-link {{ request()->is("admin/client-reports") || request()->is("admin/client-reports/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-chart-line">

                                        </i>
                                        <p>
                                            {{ trans('cruds.clientReport.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan
                @can('asset_management_access')
                    <li class="nav-item has-treeview {{ request()->is("admin/asset-categories*") ? "menu-open" : "" }} {{ request()->is("admin/asset-locations*") ? "menu-open" : "" }} {{ request()->is("admin/asset-statuses*") ? "menu-open" : "" }} {{ request()->is("admin/assets*") ? "menu-open" : "" }} {{ request()->is("admin/assets-histories*") ? "menu-open" : "" }}">
                        <a class="nav-link nav-dropdown-toggle {{ request()->is("admin/asset-categories*") ? "active" : "" }} {{ request()->is("admin/asset-locations*") ? "active" : "" }} {{ request()->is("admin/asset-statuses*") ? "active" : "" }} {{ request()->is("admin/assets*") ? "active" : "" }} {{ request()->is("admin/assets-histories*") ? "active" : "" }}" href="#">
                            <i class="fa-fw nav-icon fas fa-book">

                            </i>
                            <p>
                                {{ trans('cruds.assetManagement.title') }}
                                <i class="right fa fa-fw fa-angle-left nav-icon"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('asset_category_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.asset-categories.index") }}" class="nav-link {{ request()->is("admin/asset-categories") || request()->is("admin/asset-categories/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-tags">

                                        </i>
                                        <p>
                                            {{ trans('cruds.assetCategory.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('asset_location_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.asset-locations.index") }}" class="nav-link {{ request()->is("admin/asset-locations") || request()->is("admin/asset-locations/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-map-marker">

                                        </i>
                                        <p>
                                            {{ trans('cruds.assetLocation.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('asset_status_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.asset-statuses.index") }}" class="nav-link {{ request()->is("admin/asset-statuses") || request()->is("admin/asset-statuses/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-server">

                                        </i>
                                        <p>
                                            {{ trans('cruds.assetStatus.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('asset_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.assets.index") }}" class="nav-link {{ request()->is("admin/assets") || request()->is("admin/assets/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-book">

                                        </i>
                                        <p>
                                            {{ trans('cruds.asset.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('assets_history_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.assets-histories.index") }}" class="nav-link {{ request()->is("admin/assets-histories") || request()->is("admin/assets-histories/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-th-list">

                                        </i>
                                        <p>
                                            {{ trans('cruds.assetsHistory.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan
                @can('user_alert_access')
                    <li class="nav-item">
                        <a href="{{ route("admin.user-alerts.index") }}" class="nav-link {{ request()->is("admin/user-alerts") || request()->is("admin/user-alerts/*") ? "active" : "" }}">
                            <i class="fa-fw nav-icon fas fa-bell">

                            </i>
                            <p>
                                {{ trans('cruds.userAlert.title') }}
                            </p>
                        </a>
                    </li>
                @endcan
                @can('recharge_access')
                    <li class="nav-item has-treeview {{ request()->is("admin/recharge-plans*") ? "menu-open" : "" }} {{ request()->is("admin/recharge-requests*") ? "menu-open" : "" }}">
                        <a class="nav-link nav-dropdown-toggle {{ request()->is("admin/recharge-plans*") ? "active" : "" }} {{ request()->is("admin/recharge-requests*") ? "active" : "" }}" href="#">
                            <i class="fa-fw nav-icon fas fa-file-invoice-dollar">

                            </i>
                            <p>
                                {{ trans('cruds.recharge.title') }}
                                <i class="right fa fa-fw fa-angle-left nav-icon"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('recharge_plan_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.recharge-plans.index") }}" class="nav-link {{ request()->is("admin/recharge-plans") || request()->is("admin/recharge-plans/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-spa">

                                        </i>
                                        <p>
                                            {{ trans('cruds.rechargePlan.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('recharge_request_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.recharge-requests.index") }}" class="nav-link {{ request()->is("admin/recharge-requests") || request()->is("admin/recharge-requests/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-cogs">

                                        </i>
                                        <p>
                                            {{ trans('cruds.rechargeRequest.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan

                            @can('kyc_recharge_access')
    <li class="nav-item">
        <a href="{{ route('admin.kyc-recharges.index') }}" class="nav-link {{ request()->is('admin/kyc-recharges') || request()->is('admin/kyc-recharges/*') ? 'active' : '' }}">
            <i class="fa-fw nav-icon fas fa-id-card"></i>
            <p>
                {{ trans('cruds.kycRecharge.title') }}
            </p>
        </a>
    </li>
@endcan

                        </ul>
                    </li>
                @endcan

                @can('report_access')
    <li class="nav-item has-treeview {{ request()->is('admin/stock-history*') ? 'menu-open' : '' }}">
        <a class="nav-link nav-dropdown-toggle {{ request()->is('admin/stock-history*') ? 'active' : '' }}" href="#">
            <i class="fa-fw nav-icon fas fa-chart-line"></i>
            <p>
                {{ trans('cruds.report.title') }}
                <i class="right fa fa-fw fa-angle-left nav-icon"></i>
            </p>
        </a>

        <ul class="nav nav-treeview">
            @can('stock_history_access')
                <li class="nav-item">
                    <a href="{{ route('admin.reports.stock-history') }}"
                       class="nav-link {{ request()->is('admin/stock-history') || request()->is('admin/stock-history/*') ? 'active' : '' }}">
                        <i class="fa-fw nav-icon fas fa-warehouse"></i>
                        <p>{{ trans('cruds.stockHistory.title') }}</p>
                    </a>
                </li>
            @endcan
        </ul>
    </li>
@endcan


                @can('add_vehicle_access')
                    <li class="nav-item has-treeview {{ request()->is("admin/add-customer-vehicles*") ? "menu-open" : "" }}">
                        <a class="nav-link nav-dropdown-toggle {{ request()->is("admin/add-customer-vehicles*") ? "active" : "" }}" href="#">
                            <i class="fa-fw nav-icon fas fa-car">

                            </i>
                            <p>
                                {{ trans('cruds.addVehicle.title') }}
                                <i class="right fa fa-fw fa-angle-left nav-icon"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('add_customer_vehicle_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.add-customer-vehicles.index") }}" class="nav-link {{ request()->is("admin/add-customer-vehicles") || request()->is("admin/add-customer-vehicles/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-car">

                                        </i>
                                        <p>
                                            {{ trans('cruds.addCustomerVehicle.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan

                @can('delete_data_access')
    <li class="nav-item has-treeview {{ request()->is('admin/delete-data*') ? 'menu-open' : '' }}">
        <a class="nav-link nav-dropdown-toggle {{ request()->is('admin/delete-data*') ? 'active' : '' }}" href="#">
            <i class="fa-fw nav-icon fas fa-trash-alt"></i>
            <p>
                Delete Data
                <i class="right fa fa-fw fa-angle-left nav-icon"></i>
            </p>
        </a>

        <ul class="nav nav-treeview">
            @can('delete_data_access')
                <li class="nav-item">
                    <a href="{{ route('admin.delete-data.index') }}"
                       class="nav-link {{ request()->is('admin/delete-data') || request()->is('admin/delete-data/*') ? 'active' : '' }}">
                        <i class="fa-fw nav-icon fas fa-list"></i>
                        <p>All Deleted Data</p>
                    </a>
                </li>
            @endcan
        </ul>
    </li>
@endcan

                @php($unread = \App\Models\QaTopic::unreadCount())
                    <li class="nav-item">
                        <a href="{{ route("admin.messenger.index") }}" class="{{ request()->is("admin/messenger") || request()->is("admin/messenger/*") ? "active" : "" }} nav-link">
                            <i class="fa-fw fa fa-envelope nav-icon">

                            </i>
                            <p>{{ trans('global.messages') }}</p>
                            @if($unread > 0)
                                <strong>( {{ $unread }} )</strong>
                            @endif

                        </a>
                    </li>
                    @if(\Illuminate\Support\Facades\Schema::hasColumn('teams', 'owner_id') && \App\Models\Team::where('owner_id', auth()->user()->id)->exists())
                        <li class="nav-item">
                            <a class="{{ request()->is("admin/team-members") || request()->is("admin/team-members/*") ? "active" : "" }} nav-link" href="{{ route("admin.team-members.index") }}">
                                <i class="fa-fw fa fa-users nav-icon">
                                </i>
                                <p>
                                    {{ trans("global.team-members") }}
                                </p>
                            </a>
                        </li>
                    @endif
                    @if(file_exists(app_path('Http/Controllers/Auth/ChangePasswordController.php')))
                        @can('profile_password_edit')
                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('profile/password') || request()->is('profile/password/*') ? 'active' : '' }}" href="{{ route('profile.password.edit') }}">
                                    <i class="fa-fw fas fa-key nav-icon">
                                    </i>
                                    <p>
                                        {{ trans('global.change_password') }}
                                    </p>
                                </a>
                            </li>
                        @endcan
                    @endif

            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
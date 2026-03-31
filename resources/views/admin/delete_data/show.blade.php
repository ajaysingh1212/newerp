@extends('layouts.admin')

@section('content')

<div class="container-fluid">

    <div class="card shadow">

        <div class="card-header bg-dark text-white">
            <h5>Deletion Data Details (ID: {{ $deleteData->id }})</h5>
        </div>

        <div class="card-body">

            <div class="row">

                {{-- BASIC --}}
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-header bg-primary text-white">Basic Info</div>
                        <div class="card-body">

                            <p><b>User:</b> {{ $deleteData->user_name }}</p>
                            <p><b>Mobile:</b> {{ $deleteData->number }}</p>
                            <p><b>Email:</b> {{ $deleteData->email }}</p>
                            <p><b>Product:</b> {{ $deleteData->product }}</p>
                            <p><b>Counter:</b> {{ $deleteData->counter_name }}</p>

                        </div>
                    </div>
                </div>

                {{-- OWNER --}}
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-header bg-success text-white">Owner</div>
                        <div class="card-body">

                            <p><b>Owner Name:</b> {{ $deleteData->owner_name }}</p>
                            <p><b>Owner Phone:</b> {{ $deleteData->owner_phone }}</p>

                        </div>
                    </div>
                </div>

                {{-- VEHICLE --}}
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-header bg-info text-white">Vehicle</div>
                        <div class="card-body">

                            <p><b>Vehicle:</b> {{ $deleteData->vehicle_no }}</p>
                            <p><b>IMEI:</b> {{ $deleteData->imei_no }}</p>
                            <p><b>VTS:</b> {{ $deleteData->vts_no }}</p>
                            <p><b>SIM:</b> {{ $deleteData->sim_number }}</p>

                        </div>
                    </div>
                </div>

                {{-- DATES --}}
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-header bg-warning">Dates</div>
                        <div class="card-body">

                            <p><b>Fitting:</b> {{ $deleteData->date_of_fitting }}</p>
                            <p><b>Expiry:</b> {{ $deleteData->expiry_date }}</p>
                            <p><b>Delete:</b> {{ $deleteData->delete_date }}</p>

                        </div>
                    </div>
                </div>

                {{-- REASON --}}
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header bg-danger text-white">Reason</div>
                        <div class="card-body">

                            {{ $deleteData->reason_for_deletion }}

                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>

</div>

@endsection

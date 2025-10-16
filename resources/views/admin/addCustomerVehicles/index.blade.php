@extends('layouts.admin')

@section('content')
<style>
.vehicle-card {
    border: 1px solid #ddd;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 20px;
    background: #f8f9fa;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
.status-label { font-weight: bold; }
.status-label.expired { color: red; }
.status-label.active { color: green; }
.vehicle-actions a, .vehicle-actions button {
    display: block;
    margin-bottom: 10px;
    width: 100%;
}
@keyframes blinker { 50% { opacity: 0; } }
.blink {
    animation: blinker 1s linear infinite;
    background-color: #fff3cd;
    padding: 10px;
    border-radius: 5px;
    margin-top: 10px;
}
</style>

<div class="container mt-4">
   <div class="d-flex justify-content-between align-items-center mb-4">
    <form action="{{ route('admin.add-customer-vehicles.index') }}" method="GET" class="d-flex" style="max-width: 300px;">
        <input type="text" name="search" value="{{ request('search') }}" class="form-control me-2" placeholder="Search Vehicle..." />
        <button class="btn btn-primary" type="submit">Search</button>
    </form>
    <a class="btn btn-success" href="{{ route('admin.add-customer-vehicles.create') }}">+ Add Vehicle</a>
</div>

@forelse($vehicles as $vehicle)
@php
    $sharedWith = DB::table('vehicle_sharing')
        ->join('users', 'vehicle_sharing.sharing_user_id', '=', 'users.id')
        ->where('vehicle_sharing.vehicle_id', $vehicle['id'])
        ->select('users.name', 'users.email', 'users.id')
        ->get();

    $sharedBy = DB::table('vehicle_sharing')
        ->join('add_customer_vehicles', 'vehicle_sharing.vehicle_id', '=', 'add_customer_vehicles.id')
        ->join('users', 'add_customer_vehicles.user_id', '=', 'users.id')
        ->where('vehicle_sharing.sharing_user_id', auth()->id())
        ->where('vehicle_sharing.vehicle_id', $vehicle['id'])
        ->select('users.name as owner_name', 'users.email as owner_email')
        ->get();
@endphp

<div class="vehicle-card">
    <div class="row">
        <div class="col-md-4">
            @if(!empty($vehicle['vehicle_photos']))
                {!! $vehicle['vehicle_photos'] !!}
            @else
                <img src="{{ asset('img/99192.gif') }}" alt="Default Car Image" style="width: 300px;">
            @endif
            <strong>Vehicle Model:</strong> {{ $vehicle['vehicle_model'] }}<br>
            <strong>Vehicle Number:</strong> {{ $vehicle['vehicle_number'] }}
        </div>

        <div class="col-md-5">
            <div class="row">
                <div class="col-md-4">
                    <strong>Subscription:</strong><br>
                    {{ $vehicle['subscription_date'] ?? 'N/A' }}<br>
                    @if($vehicle['subscription_expired'])
                        <span class="status-label expired">Expired</span>
                    @else
                        <span class="status-label active">{{ $vehicle['subscription_remaining_days'] }} Days Left</span>
                    @endif
                </div>
                <div class="col-md-4">
                    <strong>AMC:</strong><br>
                    {{ $vehicle['amc_date'] ?? 'N/A' }}<br>
                    @if($vehicle['amc_expired'])
                        <span class="status-label expired">Expired</span>
                    @else
                        <span class="status-label active">{{ $vehicle['amc_remaining_days'] }} Days Left</span>
                    @endif
                </div>
                <div class="col-md-4">
                    <strong>Warranty:</strong><br>
                    {{ $vehicle['warranty_date'] ?? 'N/A' }}<br>
                    @if($vehicle['warranty_expired'])
                        <span class="status-label expired">Expired</span>
                    @else
                        <span class="status-label active">{{ $vehicle['warranty_remaining_days'] }} Days Left</span>
                    @endif
                </div>
            </div>

            <p class="mt-3 mb-1"><strong>User ID:</strong> {{ $vehicle['user_id'] }}</p>
            <p class="mb-1">
                <strong>Password:</strong>
                <span id="password-{{ $vehicle['id'] }}">••••••••</span>
                <button type="button" class="btn btn-sm btn-outline-primary"
                    onclick="requestPassword('{{ $vehicle['id'] }}', '{{ $vehicle['password'] }}')">Show Password</button>
            </p>

            <strong>KYC Status:</strong>
            @if($vehicle['kyc_status'] == 'completed')
                <span class="badge bg-success">KYC Completed</span>
            @else
                <a href="{{ route('admin.kyc-recharges.create', ['vehicle_number' => $vehicle['vehicle_number']]) }}" class="badge bg-warning text-dark blink text-decoration-none">
                    KYC Pending – Click to Pay
                </a>
            @endif

            {{-- Shared With --}}
            @if($sharedWith->count())
                <div class="blink">
                    <strong>Shared With:</strong>
                    <ul class="mb-0">
                        @foreach($sharedWith as $user)
                            <li id="shared-user-{{ $vehicle['id'] }}-{{ $user->id }}">
                                {{ $user->name }} ({{ $user->email }})
                                <button type="button" class="btn btn-sm btn-danger ms-2"
                                    onclick="setRemoveModalData('{{ $vehicle['id'] }}', '{{ $user->id }}')">
                                    Remove
                                </button>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Shared By --}}
            @if($sharedBy->count())
                <div class="blink">
                    <strong>Shared By:</strong>
                    <ul class="mb-0">
                        @foreach($sharedBy as $owner)
                            <li>{{ $owner->owner_name }} ({{ $owner->owner_email }})</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <div class="col-md-3 vehicle-actions d-flex flex-column justify-content-center text-center">
            <p class="text-dark">{{ $vehicle['app_url'] }}</p>
            <a href="" class="btn btn-warning btn-sm">✏️ App</a>
            <a href="{{ route('admin.add-customer-vehicles.edit', $vehicle['id']) }}" class="btn btn-warning btn-sm">✏️ Edit</a>
            <a href="{{ route('admin.add-customer-vehicles.show', $vehicle['id']) }}" class="btn btn-info btn-sm">👁️ View</a>
            @if($vehicle['app_link'])
                <a href="{{ $vehicle['app_link'] }}" target="_blank" class="btn btn-primary btn-sm">📍 Track</a>
            @else
                <button class="btn btn-secondary btn-sm" disabled>❌ No Link</button>
            @endif
        </div>
    </div>
</div>

@empty
<div class="alert alert-warning">No vehicles found.</div>
@endforelse
</div>

<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Password Modal
function requestPassword(id, vehiclePassword) {
    Swal.fire({
        title: 'Enter Your Password',
        input: 'password',
        inputPlaceholder: 'Enter password',
        showCancelButton: true,
        confirmButtonText: 'Verify & Show',
        preConfirm: (password) => {
            return fetch("{{ route('admin.validate-password') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": '{{ csrf_token() }}',
                },
                body: JSON.stringify({ password: password }),
            })
            .then(res => res.json())
            .then(data => {
                if (!data.valid) {
                    throw new Error('Invalid password');
                }
                return data;
            })
            .catch(error => {
                Swal.showValidationMessage(error.message);
            });
        }
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('password-' + id).innerText = vehiclePassword;
            Swal.fire('Success!', 'Password revealed.', 'success');
        }
    });
}

// Remove Sharing
function setRemoveModalData(vehicleId, userId) {
    Swal.fire({
        title: 'Enter Your Password to Confirm Removal',
        input: 'password',
        inputPlaceholder: 'Enter password',
        showCancelButton: true,
        confirmButtonText: 'Verify & Remove',
        preConfirm: (password) => {
            return fetch("{{ route('admin.validate-password') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": '{{ csrf_token() }}',
                },
                body: JSON.stringify({ password: password }),
            })
            .then(res => res.json())
            .then(data => {
                if (!data.valid) {
                    throw new Error('Invalid password');
                }
                return data;
            })
            .catch(error => {
                Swal.showValidationMessage(error.message);
            });
        }
    }).then((result) => {
        if (result.isConfirmed) {
            fetch("{{ route('admin.vehicle-sharing.remove') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": '{{ csrf_token() }}',
                },
                body: JSON.stringify({
                    vehicle_id: vehicleId,
                    sharing_user_id: userId
                }),
            })
            .then(res => {
                if (!res.ok) throw new Error("Network error");
                return res.json();
            })
            .then(data => {
                if (data.success) {
                    document.getElementById(`shared-user-${vehicleId}-${userId}`).remove();
                    Swal.fire('Removed!', 'Vehicle sharing has been removed.', 'success');
                } else {
                    Swal.fire('Error', data.message || 'Failed to remove sharing.', 'error');
                }
            })
            .catch(err => {
                Swal.fire('Error', 'An error occurred while removing sharing.', 'error');
                console.error(err);
            });
        }
    });
}
</script>

@endsection

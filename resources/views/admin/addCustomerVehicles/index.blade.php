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

.status-label {
    font-weight: bold;
}

.status-label.expired {
    color: red;
}

.status-label.active {
    color: green;
}

.vehicle-actions a,
.vehicle-actions button {
    display: block;
    margin-bottom: 10px;
    transform: none;
    width: 100%;
}
</style>

<div class="container mt-4">
   <div class="d-flex justify-content-between align-items-center mb-4">
    <!-- Search bar -->
    <form action="{{ route('admin.add-customer-vehicles.index') }}" method="GET" class="d-flex" style="max-width: 300px;">
        <input type="text" name="search" value="{{ request('search') }}" 
               class="form-control me-2" placeholder="Search Vehicle..." />
        <button class="btn btn-primary" type="submit">Search</button>
    </form>

    <!-- Add button -->
    <a class="btn btn-success" href="{{ route('admin.add-customer-vehicles.create') }}">+ Add Vehicle</a>
</div>


    @forelse($vehicles as $vehicle)
    <div class="vehicle-card">
        <div class="row">
            <div class="col-md-4">
                @if(!empty($vehicle['vehicle_photos']))
                    {!! $vehicle['vehicle_photos'] !!}
                @else
                    <img src="{{ asset('img/car.png') }}" alt="Default Car Image" style="width: 300px;">
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

                <p class="mt-3 mb-1"><strong class="text-secondary">User ID:</strong> {{ $vehicle['user_id'] }}</p>
                <p class="mb-1">
                    <strong class="text-secondary">Password:</strong>
                    <span id="password-{{ $vehicle['id'] }}">••••••••</span>
                    <button type="button" class="btn btn-sm btn-outline-primary"
                        onclick="requestPassword('{{ $vehicle['id'] }}', '{{ $vehicle['password'] }}')">Show Password</button>
                </p>
            </div>
            <div class="col-md-3 vehicle-actions d-flex flex-column justify-content-center text-center">
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

<!-- Password Modal -->

<!-- Password Modal -->
<div class="modal fade" id="confirmPasswordModal" tabindex="-1" role="dialog" aria-labelledby="confirmPasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form id="confirmPasswordForm">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmPasswordModalLabel">Confirm Your Password</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="vehiclePasswordToShow">
                    <input type="hidden" id="targetPasswordElementId">
                    <div class="form-group">
                        <label for="currentPassword">Enter Your Password</label>
                        <input type="password" class="form-control" id="currentPassword" name="currentPassword" required>
                        <div class="invalid-feedback">Incorrect password</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Verify & Show</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Script (move inside content to ensure it loads) -->
<script>
    function requestPassword(id, vehiclePassword) {
        document.getElementById('vehiclePasswordToShow').value = vehiclePassword;
        document.getElementById('targetPasswordElementId').value = 'password-' + id;
        document.getElementById('currentPassword').value = '';
        $('#confirmPasswordModal').modal('show');
    }

    document.getElementById('confirmPasswordForm').addEventListener('submit', function (e) {
        e.preventDefault();
        const enteredPassword = document.getElementById('currentPassword').value;
        const vehiclePassword = document.getElementById('vehiclePasswordToShow').value;
        const targetId = document.getElementById('targetPasswordElementId').value;

        fetch("{{ route('admin.validate-password') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": '{{ csrf_token() }}',
            },
            body: JSON.stringify({ password: enteredPassword }),
        })
        .then(res => res.json())
        .then(data => {
            if (data.valid) {
                document.getElementById(targetId).innerText = vehiclePassword;
                $('#confirmPasswordModal').modal('hide');
            } else {
                document.getElementById('currentPassword').classList.add('is-invalid');
            }
        });
    });
</script>

@endsection

@push('scripts')
<script>
function requestPassword(id, vehiclePassword) {
    $('#vehiclePasswordToShow').val(vehiclePassword);
    $('#targetPasswordElementId').val('password-' + id);
    $('#currentPassword').val('');
    $('#confirmPasswordModal').modal('show');
}

$('#confirmPasswordForm').on('submit', function(e) {
    e.preventDefault();
    const enteredPassword = $('#currentPassword').val();
    const vehiclePassword = $('#vehiclePasswordToShow').val();
    const targetId = $('#targetPasswordElementId').val();

    $.ajax({
        url: "{{ route('admin.validate-password') }}",
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            password: enteredPassword
        },
        success: function(res) {
            if (res.valid) {
                $('#' + targetId).text(vehiclePassword);
                $('#confirmPasswordModal').modal('hide');
            } else {
                $('#currentPassword').addClass('is-invalid');
            }
        }
    });
});
</script>
@endpush

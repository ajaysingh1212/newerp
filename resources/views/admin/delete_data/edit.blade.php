@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <div class="card shadow-lg border-0 rounded-3">
        <div class="card-header bg-warning text-dark">
            <h4 class="mb-0">✏️ Edit Deletion Data Record</h4>
        </div>

        <div class="card-body">

            {{-- Success / Error --}}
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            {{-- Validation --}}
            @if($errors->any())
                <div class="alert alert-danger">
                    <strong>Whoops!</strong>
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.delete-data.update', $deleteData->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">

                    {{-- OLD FIELDS --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">User Name *</label>
                        <input type="text" name="user_name" class="form-control"
                               value="{{ old('user_name', $deleteData->user_name) }}" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Number</label>
                        <input type="text" name="number" class="form-control"
                               value="{{ old('number', $deleteData->number) }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Email</label>
                        <input type="email" name="email" class="form-control"
                               value="{{ old('email', $deleteData->email) }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Product</label>
                        <input type="text" name="product" class="form-control"
                               value="{{ old('product', $deleteData->product) }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Counter Name</label>
                        <input type="text" name="counter_name" class="form-control"
                               value="{{ old('counter_name', $deleteData->counter_name) }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Vehicle No</label>
                        <input type="text" name="vehicle_no" class="form-control"
                               value="{{ old('vehicle_no', $deleteData->vehicle_no) }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">IMEI No</label>
                        <input type="text" name="imei_no" class="form-control"
                               value="{{ old('imei_no', $deleteData->imei_no) }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">VTS No</label>
                        <input type="text" name="vts_no" class="form-control"
                               value="{{ old('vts_no', $deleteData->vts_no) }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Delete Date</label>
                        <input type="datetime-local" name="delete_date" class="form-control"
                               value="{{ old('delete_date', $deleteData->delete_date) }}">
                    </div>

                    {{-- NEW FIELDS --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Owner Name</label>
                        <input type="text" name="owner_name" class="form-control"
                               value="{{ old('owner_name', $deleteData->owner_name) }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Owner Phone</label>
                        <input type="text" name="owner_phone" class="form-control"
                               value="{{ old('owner_phone', $deleteData->owner_phone) }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Date of Fitting</label>
                        <input type="date" name="date_of_fitting" class="form-control"
                               value="{{ old('date_of_fitting', $deleteData->date_of_fitting) }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Expiry Date</label>
                        <input type="date" name="expiry_date" class="form-control"
                               value="{{ old('expiry_date', $deleteData->expiry_date) }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">SIM Number</label>
                        <input type="text" name="sim_number" class="form-control"
                               value="{{ old('sim_number', $deleteData->sim_number) }}">
                    </div>

                    <div class="col-md-12 mb-3">
                        <label class="form-label fw-bold">Reason for Deletion</label>
                        <textarea name="reason_for_deletion" class="form-control" rows="3">{{ old('reason_for_deletion', $deleteData->reason_for_deletion) }}</textarea>
                    </div>

                </div>

                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('admin.delete-data.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>

                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Update Record
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection

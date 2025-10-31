@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <div class="card shadow-lg border-0 rounded-3">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">➕ Add New Delete Data Record</h4>
        </div>

        <div class="card-body">
            {{-- ✅ Show Success or Error Messages --}}
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            {{-- ✅ Validation Errors --}}
            @if($errors->any())
                <div class="alert alert-danger">
                    <strong>Whoops!</strong> Please fix the following errors:<br><br>
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- ✅ Form Start --}}
            <form action="{{ route('admin.delete-data.store') }}" method="POST">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="user_name" class="form-label fw-bold">User Name *</label>
                        <input type="text" name="user_name" id="user_name" class="form-control" value="{{ old('user_name') }}" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="number" class="form-label fw-bold">Number</label>
                        <input type="text" name="number" id="number" class="form-control" value="{{ old('number') }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label fw-bold">Email</label>
                        <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="product" class="form-label fw-bold">Product</label>
                        <input type="text" name="product" id="product" class="form-control" value="{{ old('product') }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="counter_name" class="form-label fw-bold">Counter Name</label>
                        <input type="text" name="counter_name" id="counter_name" class="form-control" value="{{ old('counter_name') }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="vehicle_no" class="form-label fw-bold">Vehicle No</label>
                        <input type="text" name="vehicle_no" id="vehicle_no" class="form-control" value="{{ old('vehicle_no') }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="imei_no" class="form-label fw-bold">IMEI No</label>
                        <input type="text" name="imei_no" id="imei_no" class="form-control" value="{{ old('imei_no') }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="vts_no" class="form-label fw-bold">VTS No</label>
                        <input type="text" name="vts_no" id="vts_no" class="form-control" value="{{ old('vts_no') }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="delete_date" class="form-label fw-bold">Delete Date</label>
                        <input type="datetime-local" name="delete_date" id="delete_date" class="form-control" value="{{ old('delete_date') }}">
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('admin.delete-data.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>

                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Save Record
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

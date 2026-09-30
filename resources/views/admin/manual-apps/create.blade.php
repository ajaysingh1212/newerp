@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-header"><i class="fas fa-mobile-alt mr-2"></i>Add App</div>
    <div class="card-body">
        @if($errors->any())
            <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
        @endif
        <form action="{{ route('admin.manual-apps.store') }}" method="POST">
            @csrf
            @include('admin.manual-apps.form')
            <button class="btn btn-primary" type="submit"><i class="fas fa-save mr-1"></i>Save App</button>
            <a href="{{ route('admin.manual-apps.index') }}" class="btn btn-light">Cancel</a>
        </form>
    </div>
</div>
@endsection

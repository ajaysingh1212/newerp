@extends('layouts.admin')
@section('content')

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                {{ trans('global.edit') }} {{ trans('cruds.rechargeRequest.title_singular') }}
            </div>

            <div class="card-body">
                @include('watermark')
                <form method="POST" action="{{ route('admin.recharge-requests.update', [$rechargeRequest->id]) }}" enctype="multipart/form-data">
                    @method('PUT')
                    @csrf

                    {{-- User Selection --}}
                    <div class="form-group">
                        <label for="user_id">{{ trans('cruds.rechargeRequest.fields.user') }}</label>
                        <select class="form-control select2 {{ $errors->has('user') ? 'is-invalid' : '' }}" name="user_id" id="user_id">
                            @foreach($users as $id => $entry)
                                <option value="{{ $id }}" {{ (old('user_id', $rechargeRequest->user_id) == $id) ? 'selected' : '' }}>{{ $entry }}</option>
                            @endforeach
                        </select>
                        @if($errors->has('user'))
                            <span class="text-danger">{{ $errors->first('user') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.rechargeRequest.fields.user_helper') }}</span>
                    </div>

                    {{-- Customer Details Card --}}
                    <div class="card mb-3" id="customer-details-card">
                        <div class="card-header bg-info text-white">
                            Customer Details
                        </div>
                        <div class="card-body" id="customer-details-body">
                            @if($rechargeRequest->user)
                                <p><strong>Name:</strong> {{ $rechargeRequest->user->name }}</p>
                                <p><strong>Email:</strong> {{ $rechargeRequest->user->email }}</p>
                                <p><strong>Phone:</strong> {{ $rechargeRequest->user->phone }}</p>
                                <p><strong>Roles:</strong> {{ $rechargeRequest->user->roles->pluck('title')->implode(', ') }}</p>
                            @else
                                <p>No customer selected</p>
                            @endif
                        </div>
                    </div>
                    {{-- Payment Status --}}
                    <div class="form-group">
                        <label for="payment_status">{{ trans('cruds.rechargeRequest.fields.payment_status') }}</label>
                        <select class="form-control {{ $errors->has('payment_status') ? 'is-invalid' : '' }}" name="payment_status" id="payment_status">
                            <option value="pending" {{ old('payment_status', $rechargeRequest->payment_status) == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="success" {{ old('payment_status', $rechargeRequest->payment_status) == 'success' ? 'selected' : '' }}>Success</option>
                            <option value="failed" {{ old('payment_status', $rechargeRequest->payment_status) == 'failed' ? 'selected' : '' }}>Failed</option>
                            <option value="refunded" {{ old('payment_status', $rechargeRequest->payment_status) == 'refunded' ? 'selected' : '' }}>Refunded</option>
                        </select>

                        @if($errors->has('payment_status'))
                            <span class="text-danger">{{ $errors->first('payment_status') }}</span>
                        @endif
                       
                    </div>

                    {{-- Vehicle Number --}}
                    <div class="form-group">
                        <label class="required" for="vehicle_number">{{ trans('cruds.rechargeRequest.fields.vehicle_number') }}</label>
                        <input class="form-control {{ $errors->has('vehicle_number') ? 'is-invalid' : '' }}" type="text" name="vehicle_number" id="vehicle_number" value="{{ old('vehicle_number', $rechargeRequest->vehicle_number) }}" required>
                        @if($errors->has('vehicle_number'))
                            <span class="text-danger">{{ $errors->first('vehicle_number') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.rechargeRequest.fields.vehicle_number_helper') }}</span>
                    </div>

                    <input type="text" name="vehicle_id" value="{{ old('vehicle_id', $rechargeRequest->id) }}" hidden>

                    {{-- Product --}}

                    {{-- Recharge Plan --}}
                    <div class="form-group">
                        <label for="select_recharge_id">{{ trans('cruds.rechargeRequest.fields.select_recharge') }}</label>
                        <select class="form-control select2 {{ $errors->has('select_recharge') ? 'is-invalid' : '' }}" name="select_recharge_id" id="select_recharge_id">
                            @foreach($select_recharges as $id => $entry)
                                <option value="{{ $id }}" {{ (old('select_recharge_id', $rechargeRequest->select_recharge_id) == $id) ? 'selected' : '' }}>{{ $entry }}</option>
                            @endforeach
                        </select>
                        @if($errors->has('select_recharge'))
                            <span class="text-danger">{{ $errors->first('select_recharge') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.rechargeRequest.fields.select_recharge_helper') }}</span>
                    </div>

                    {{-- Plan Details Card --}}
                    <div class="card mb-3" id="plan-details-card">
                        <div class="card-header bg-success text-white">
                            Plan Details
                        </div>
                        <div class="card-body" id="plan-details-body">
                            @if($rechargeRequest->select_recharge)
                                <p><strong>Type:</strong> {{ $rechargeRequest->select_recharge->type }}</p>
                                <p><strong>Name:</strong> {{ $rechargeRequest->select_recharge->plan_name }}</p>
                                <p><strong>Price:</strong> ₹{{ $rechargeRequest->select_recharge->price }}</p>
                                <p><strong>Subscription Duration:</strong> {{ $rechargeRequest->select_recharge->subscription_duration ?? 0 }} months</p>
                                <p><strong>Warranty:</strong> {{ $rechargeRequest->select_recharge->warranty_duration ?? 0 }} months</p>
                                <p><strong>AMC:</strong> {{ $rechargeRequest->select_recharge->amc_duration ?? 0 }} months</p>
                            @else
                                <p>No plan selected</p>
                            @endif
                        </div>
                    </div>

                    {{-- Notes --}}
                    <div class="form-group">
                        <label for="notes">{{ trans('cruds.rechargeRequest.fields.notes') }}</label>
                        <textarea class="form-control ckeditor {{ $errors->has('notes') ? 'is-invalid' : '' }}" name="notes" id="notes">{!! old('notes', $rechargeRequest->notes) !!}</textarea>
                        @if($errors->has('notes'))
                            <span class="text-danger">{{ $errors->first('notes') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.rechargeRequest.fields.notes_helper') }}</span>
                    </div>

                    {{-- Vehicle Status --}}
                    <div class="form-group">
                        <label for="vehicle_status">{{ trans('cruds.rechargeRequest.fields.vehicle_status') }}</label>
                        <select class="form-control {{ $errors->has('vehicle_status') ? 'is-invalid' : '' }}" name="vehicle_status" id="vehicle_status">
                            <option value="Processing" {{ old('vehicle_status', $rechargeRequest->vehicle_status) == 'Processing' ? 'selected' : '' }}>Processing</option>
                            <option value="Live" {{ old('vehicle_status', $rechargeRequest->vehicle_status) == 'Live' ? 'selected' : '' }}>Live</option>
                        </select>
                        @if($errors->has('vehicle_status'))
                            <span class="text-danger">{{ $errors->first('vehicle_status') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.rechargeRequest.fields.vehicle_status_helper') }}</span>
                    </div>

                    {{-- Attachment --}}
                    <div class="form-group">
                        <label for="attechment">{{ trans('cruds.rechargeRequest.fields.attechment') }}</label>
                        <div class="needsclick dropzone {{ $errors->has('attechment') ? 'is-invalid' : '' }}" id="attechment-dropzone"></div>
                        @if($errors->has('attechment'))
                            <span class="text-danger">{{ $errors->first('attechment') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.rechargeRequest.fields.attechment_helper') }}</span>
                    </div>

                    {{-- Submit --}}
                    <div class="form-group">
                        <button class="btn btn-danger" type="submit">
                            {{ trans('global.save') }}
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    $(document).ready(function () {
        // CKEditor initialization
        function SimpleUploadAdapter(editor) {
            editor.plugins.get('FileRepository').createUploadAdapter = function(loader) {
                return {
                    upload: function() {
                        return loader.file.then(function(file) {
                            return new Promise(function(resolve, reject) {
                                var xhr = new XMLHttpRequest();
                                xhr.open('POST', '{{ route('admin.recharge-requests.storeCKEditorImages') }}', true);
                                xhr.setRequestHeader('x-csrf-token', window._token);
                                xhr.setRequestHeader('Accept', 'application/json');
                                xhr.responseType = 'json';

                                xhr.addEventListener('error', function() { reject('Upload failed') });
                                xhr.addEventListener('abort', function() { reject() });
                                xhr.addEventListener('load', function() {
                                    var response = xhr.response;
                                    if (!response || xhr.status !== 201) {
                                        return reject('Upload failed');
                                    }
                                    $('form').append('<input type="hidden" name="ck-media[]" value="' + response.id + '">');
                                    resolve({ default: response.url });
                                });

                                var data = new FormData();
                                data.append('upload', file);
                                data.append('crud_id', '{{ $rechargeRequest->id ?? 0 }}');
                                xhr.send(data);
                            });
                        })
                    }
                };
            }
        }

        document.querySelectorAll('.ckeditor').forEach(editorElement => {
            ClassicEditor.create(editorElement, { extraPlugins: [SimpleUploadAdapter] });
        });

        // Dropzone initialization
        Dropzone.options.attechmentDropzone = {
            url: '{{ route('admin.recharge-requests.storeMedia') }}',
            maxFilesize: 20,
            maxFiles: 1,
            addRemoveLinks: true,
            headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
            success: function(file, response) {
                $('form').find('input[name="attechment"]').remove()
                $('form').append('<input type="hidden" name="attechment" value="' + response.name + '">')
            },
            removedfile: function(file) {
                file.previewElement.remove()
                $('form').find('input[name="attechment"]').remove()
            },
            init: function() {
                @if(isset($rechargeRequest) && $rechargeRequest->attechment)
                    var file = {!! json_encode($rechargeRequest->attechment) !!};
                    this.options.addedfile.call(this, file)
                    file.previewElement.classList.add('dz-complete')
                    $('form').append('<input type="hidden" name="attechment" value="' + file.file_name + '">')
                @endif
            }
        };

        // Update customer details dynamically
        $('#user_id').on('change', function() {
            var userId = $(this).val();
            if (!userId) {
                $('#customer-details-body').html('<p>No customer selected</p>');
                return;
            }
            $.get('/admin/users/' + userId + '/details', function(data) {
                var html = '<p><strong>Name:</strong> ' + data.name + '</p>';
                html += '<p><strong>Email:</strong> ' + data.email + '</p>';
                html += '<p><strong>Phone:</strong> ' + data.phone + '</p>';
                html += '<p><strong>Roles:</strong> ' + data.roles.join(', ') + '</p>';
                $('#customer-details-body').html(html);
            });
        });

        // Update plan details dynamically
        $('#select_recharge_id').on('change', function() {
            var planId = $(this).val();
            if (!planId) {
                $('#plan-details-body').html('<p>No plan selected</p>');
                return;
            }
            $.get('/admin/recharge-plans/' + planId + '/details', function(data) {
                var html = '<p><strong>Type:</strong> ' + data.type + '</p>';
                html += '<p><strong>Name:</strong> ' + data.plan_name + '</p>';
                html += '<p><strong>Price:</strong> ₹' + data.price + '</p>';
                html += '<p><strong>Subscription Duration:</strong> ' + (data.subscription_duration ?? 0) + ' months</p>';
                html += '<p><strong>Warranty:</strong> ' + (data.warranty_duration ?? 0) + ' months</p>';
                html += '<p><strong>AMC:</strong> ' + (data.amc_duration ?? 0) + ' months</p>';
                $('#plan-details-body').html(html);
            });
        });

    });
</script>
@endsection

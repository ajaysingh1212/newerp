@extends('layouts.admin')

@section('content')
    <div class="card">
        <div class="card-header">
            {{ trans('global.create') }} {{ trans('cruds.productMaster.title_singular') }}
        </div>

        <div class="card-body">
            @include('watermark')
            <form method="POST" action="{{ route('admin.product-masters.store') }}" enctype="multipart/form-data" class="row">
                @csrf
                <div class="card px-3">
                <h4 class= "text-center mt-2 py-2 bg-1">Create Product Master</h4>
            <div class="row">
                <!-- Product Model Dropdown -->
                <div class="form-group col-md-4">
                    <label for="product_model_id">{{ trans('cruds.productMaster.fields.product_model') }}</label>
                    <select id="product_model_id" name="product_model_id" class="form-control">
                        @foreach($product_models as $model)
                            <option value="{{ $model->id }}" data-warranty="{{ $model->warranty }}" data-subscription="{{ $model->subscription }}" data-amc="{{ $model->amc }}">
                                {{ $model->product_model }}
                            </option>
                        @endforeach
                    </select>
                    @error('product_model_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- IMEI Search Dropdown -->
                <div class="form-group col-md-4">
                    <label for="imei_search">{{ trans('cruds.productMaster.fields.imei') }}</label>
                    <select id="imei_id" name="imei_id" class="form-control">
                        @foreach($imeis as $id => $imei)
                            <option value="{{ $id }}">{{ $imei }}</option>
                        @endforeach
                    </select>
                    @error('imei_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- VTS Search Dropdown -->
                <div class="form-group col-md-4">
                    <label for="vts_search">{{ trans('cruds.productMaster.fields.vts') }}</label>
                    <select id="vts_id" name="vts_id" class="form-control">
                        @foreach($vts as $id => $vts_number)
                            <option value="{{ $id }}">{{ $vts_number }}</option>
                        @endforeach
                    </select>
                    @error('vts_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Product Model Details (populated dynamically) -->
                <div class="form-group col-md-4">
                    <label for="warranty">{{ trans('cruds.productMaster.fields.warranty') }}</label>
                    <input type="text" id="warranty" name="warranty" class="form-control" readonly>
                </div>

                <div class="form-group col-md-4">
                    <label for="subscription">{{ trans('cruds.productMaster.fields.subscription') }}</label>
                    <input type="text" id="subscription" name="subscription" class="form-control" readonly>
                </div>

                <div class="form-group col-md-4">
                    <label for="amc">{{ trans('cruds.productMaster.fields.amc') }}</label>
                    <input type="text" id="amc" name="amc" class="form-control" readonly>
                </div>

                <!-- Status Dropdown -->
                <div class="form-group col-md-12">
                    <label class="required">{{ trans('cruds.productMaster.fields.status') }}</label>
                    <select class="form-control {{ $errors->has('status') ? 'is-invalid' : '' }}" name="status" id="status" required>
                        <option value disabled {{ old('status', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                        @foreach(App\Models\ProductMaster::STATUS_SELECT as $key => $label)
                            <option value="{{ $key }}" {{ old('status', 'enable') === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @if($errors->has('status'))
                        <span class="text-danger">{{ $errors->first('status') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.productMaster.fields.status_helper') }}</span>
                </div>
                 </div>
                  </div>
                
                <button type="submit" class="btn btn-primary">
                    {{ trans('global.save') }}
                </button>
            </form>
        </div>
    </div>


@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            // Initialize Select2 for IMEI and VTS dropdowns
            $('#imei_id').select2({
                placeholder: "Search IMEI",
                allowClear: true
            });

            $('#vts_id').select2({
                placeholder: "Search VTS",
                allowClear: true
            });

            // jQuery to dynamically populate product model details
            $('#product_model_id').on('change', function () {
                var selectedOption = $(this).find('option:selected');
                var warranty = selectedOption.data('warranty');
                var subscription = selectedOption.data('subscription');
                var amc = selectedOption.data('amc');

                $('#warranty').val(warranty);
                $('#subscription').val(subscription);
                $('#amc').val(amc);
            });

            // Initialize values for product model details (if any default model is selected)
            var selectedOption = $('#product_model_id').find('option:selected');
            var warranty = selectedOption.data('warranty');
            var subscription = selectedOption.data('subscription');
            var amc = selectedOption.data('amc');

            $('#warranty').val(warranty);
            $('#subscription').val(subscription);
            $('#amc').val(amc);
        });
    </script>
@endsection

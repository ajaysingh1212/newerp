@extends('layouts.admin')


@section('content')
<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('cruds.checkComplain.title_singular') }}
    </div>

    <div class="card-body">
        @include('watermark')
        <form method="POST" action="{{ route("admin.check-complains.store") }}" enctype="multipart/form-data">
            @csrf
            
            <!-- Complain Multi-select -->
            <div class="form-group">
                <label class="required" for="select_complains">{{ trans('cruds.checkComplain.fields.select_complain') }}</label>
                <div style="padding-bottom: 4px">
                    <span class="btn btn-info btn-xs select-all" style="border-radius: 0">{{ trans('global.select_all') }}</span>
                    <span class="btn btn-info btn-xs deselect-all" style="border-radius: 0">{{ trans('global.deselect_all') }}</span>
                </div>
                <select class="form-control select2" name="select_complains[]" id="select_complains" multiple required>
                    @foreach($select_complains as $id => $select_complain)
                        <option value="{{ $id }}">{{ $select_complain }}</option>
                    @endforeach
                </select>
            </div>
            @php
                $isAdmin = auth()->user()->roles->contains(function ($role) {
                    return in_array($role->title, ['CNF', 'Distributer', 'Dealer']);
                });
            @endphp

            @if($isAdmin)
                <div class="form-group">
                    <label for="notes">{{ trans('cruds.checkComplain.fields.notes') }}</label>
                    <textarea class="form-control ckeditor {{ $errors->has('notes') ? 'is-invalid' : '' }}" name="notes" id="notes">{!! old('notes') !!}</textarea>
                    @if($errors->has('notes'))
                        <span class="text-danger">{{ $errors->first('notes') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.checkComplain.fields.notes_helper') }}</span>
                </div>
                <div class="form-group">
                <label for="reason">{{ trans('cruds.checkComplain.fields.reason') }}</label>
                <textarea class="form-control ckeditor {{ $errors->has('reason') ? 'is-invalid' : '' }}" name="reason" id="reason">{!! old('reason') !!}</textarea>
                @if($errors->has('reason'))
                    <span class="text-danger">{{ $errors->first('reason') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.checkComplain.fields.reason_helper') }}</span>
                </div>

                <div class="form-group">
                    <label for="attechment">{{ trans('cruds.checkComplain.fields.attechment') }}</label>
                    <div class="needsclick dropzone {{ $errors->has('attechment') ? 'is-invalid' : '' }}" id="attechment-dropzone"></div>
                    @if($errors->has('attechment'))
                        <span class="text-danger">{{ $errors->first('attechment') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.checkComplain.fields.attechment_helper') }}</span>
                </div> 
                
            @endif
            @php
                $isCustomer = auth()->user()->roles->contains('title', 'Customer');
            @endphp
            @if($isCustomer)
            <!-- Vehicle Select -->
            <div class="form-group">
                <label for="vehicle_select">Select Vehicle</label>
                <select id="vehicle_select" name="vehicle_id" class="form-control">
                    <option value="">Select Vehicle</option>
                    @foreach($vehicles as $vehicle)
                        <option value="{{ $vehicle->id }}"
                            data-vehicle_number="{{ $vehicle->vehicle_number }}"
                            data-customer_name="{{ $vehicle->creator->name ?? '' }}"
                            data-customer_phone="{{ $vehicle->creator->mobile_number ?? '' }}"
                            data-created_at="{{ $vehicle->created_at->format('Y-m-d') }}"
                            data-status="{{ $vehicle->status }}"
                            data-vehicle_color="{{ $vehicle->vehicle_color }}"
                            data-images='@json($vehicle->media->map(fn($m) => ["id" => $m->id, "file_name" => $m->file_name]))'>
                            {{ $vehicle->vehicle_number }}
                        </option>
                    @endforeach

                </select>
            </div>

                <div id="vehicle_details_card" class="card mt-3 shadow-sm vehicle-card" style="display:none; border-radius: 0.75rem; overflow: hidden;">
                <div class="row no-gutters">
                    <!-- Images -->
                    <div class="col-md-3 d-flex align-items-center justify-content-center bg-light p-3">
                        <div id="vehicle_images" class="d-flex flex-wrap gap-2 justify-content-center align-items-center" style="min-height: 150px; max-height: 200px; overflow-y: auto;"></div>
                    </div>
                    <!-- Details -->
                    <div class="col-md-6">
                        <div class="card-body">
                            <h5 class="card-title mb-3">Vehicle Details</h5>
                            <p><strong>Created At:</strong> <span id="detail_created_at"></span></p>
                            <p><strong>Status:</strong> <span id="detail_status"></span></p>
                            <p><strong>Vehicle Color:</strong> <span id="detail_vehicle_color"></span></p>

                            <div class="form-check mt-3">
                                <input type="checkbox" class="form-check-input" id="vehicle_check_toggle">
                                <label class="form-check-label" for="vehicle_check_toggle">Check to autofill below fields</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Autofilled Inputs -->
            <div id="autofill_fields" class="mt-3" style="display: none;">
                <div class="form-group">
                    <label for="autofill_vehicle_number">Vehicle Number</label>
                    <input type="text" class="form-control" name="vehicle_no" id="autofill_vehicle_number" readonly>
                </div>
                <div class="form-group">
                    <label for="autofill_customer_name">Customer Name</label>
                    <input type="text" class="form-control" name="customer_name" id="autofill_customer_name" readonly>
                </div>
                <div class="form-group">
                    <label for="autofill_customer_phone">Phone Number</label>
                    <input type="text" class="form-control" name="phone_number" id="autofill_customer_phone" readonly>
                </div>
                <div class="form-group">
                <label for="reason">{{ trans('cruds.checkComplain.fields.reason') }}</label>
                <textarea class="form-control ckeditor {{ $errors->has('reason') ? 'is-invalid' : '' }}" name="reason" id="reason">{!! old('reason') !!}</textarea>
                @if($errors->has('reason'))
                    <span class="text-danger">{{ $errors->first('reason') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.checkComplain.fields.reason_helper') }}</span>
            </div>
            
            <div class="form-group">
                <label for="notes">{{ trans('cruds.checkComplain.fields.notes') }}</label>
                <textarea class="form-control ckeditor {{ $errors->has('notes') ? 'is-invalid' : '' }}" name="notes" id="notes">{!! old('notes') !!}</textarea>
                @if($errors->has('notes'))
                    <span class="text-danger">{{ $errors->first('notes') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.checkComplain.fields.notes_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="attechment">{{ trans('cruds.checkComplain.fields.attechment') }}</label>
                <div class="needsclick dropzone {{ $errors->has('attechment') ? 'is-invalid' : '' }}" id="attechment-dropzone">
                </div>
                @if($errors->has('attechment'))
                    <span class="text-danger">{{ $errors->first('attechment') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.checkComplain.fields.attechment_helper') }}</span>
            </div>
            </div>
            <span class="help-block">{{ trans('cruds.checkComplain.fields.phone_number_helper') }}</span>
            </div>

            @endif
            <!-- Vehicle Details Card -->

            <!-- Submit -->
            <div class="form-group mt-4">
                <button class="btn btn-primary" type="submit">
                    {{ trans('global.save') }}
                </button>
            </div>
        </form>
    </div>
</div>

@php
    $appUrl = config('app.url');
@endphp

<!-- Styles -->
<style>
    .vehicle-card {
        box-shadow: 0 2px 6px rgba(0,0,0,0.15);
        transition: box-shadow 0.3s ease;
    }

    .vehicle-card:hover {
        box-shadow: 0 8px 20px rgba(0,0,0,0.3);
    }

    #vehicle_images img.vehicle-image {
        max-width: 100px;
        border-radius: 0.5rem;
        margin: 6px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.1);
        transition: transform 0.2s ease;
    }

    #vehicle_images img.vehicle-image:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 10px rgba(0,0,0,0.3);
    }
</style>

<!-- Script -->
<script>
    const APP_URL = "{{ $appUrl }}";

    document.getElementById('vehicle_select').addEventListener('change', function () {
        const selectedOption = this.options[this.selectedIndex];
        const card = document.getElementById('vehicle_details_card');
        const checkBox = document.getElementById('vehicle_check_toggle');

        if (!selectedOption.value) {
            card.style.display = 'none';
            document.getElementById('autofill_fields').style.display = 'none';
            checkBox.checked = false;
            return;
        }

        card.style.display = 'block';

        document.getElementById('detail_created_at').innerText = selectedOption.getAttribute('data-created_at') || 'N/A';
        document.getElementById('detail_status').innerText = selectedOption.getAttribute('data-status') || 'N/A';
        document.getElementById('detail_vehicle_color').innerText = selectedOption.getAttribute('data-vehicle_color') || 'N/A';

        // Images
        const imagesContainer = document.getElementById('vehicle_images');
        imagesContainer.innerHTML = '';
        let images = [];

        try {
            images = JSON.parse(selectedOption.getAttribute('data-images')) || [];
        } catch (e) {
            images = [];
        }

        if (images.length > 0) {
            images.forEach(function (media) {
                const imageUrl = `${APP_URL}/storage/${media.id}/${media.file_name}`;
                const img = document.createElement('img');
                img.src = imageUrl;
                img.alt = 'Vehicle Image';
                img.className = 'vehicle-image';
                imagesContainer.appendChild(img);
            });
        } else {
            imagesContainer.innerHTML = `<div style="width: 100%; height: 150px; display: flex; align-items: center; justify-content: center; color: #6c757d;">No Images Available</div>`;
        }

        // Reset checkbox and autofill fields
        checkBox.checked = false;
        document.getElementById('autofill_fields').style.display = 'none';
    });

    document.getElementById('vehicle_check_toggle').addEventListener('change', function () {
        const selectedOption = document.getElementById('vehicle_select').options[document.getElementById('vehicle_select').selectedIndex];
        const autofillSection = document.getElementById('autofill_fields');

        if (this.checked) {
            document.getElementById('autofill_vehicle_number').value = selectedOption.getAttribute('data-vehicle_number') || '';
            document.getElementById('autofill_customer_name').value = selectedOption.getAttribute('data-customer_name') || '';
            document.getElementById('autofill_customer_phone').value = selectedOption.getAttribute('data-customer_phone') || '';
            autofillSection.style.display = 'block';
        } else {
            document.getElementById('autofill_vehicle_number').value = '';
            document.getElementById('autofill_customer_name').value = '';
            document.getElementById('autofill_customer_phone').value = '';
            autofillSection.style.display = 'none';
        }
    });
</script>
@endsection


@section('scripts')
<script>
    $(document).ready(function () {
  function SimpleUploadAdapter(editor) {
    editor.plugins.get('FileRepository').createUploadAdapter = function(loader) {
      return {
        upload: function() {
          return loader.file
            .then(function (file) {
              return new Promise(function(resolve, reject) {
                // Init request
                var xhr = new XMLHttpRequest();
                xhr.open('POST', '{{ route('admin.check-complains.storeCKEditorImages') }}', true);
                xhr.setRequestHeader('x-csrf-token', window._token);
                xhr.setRequestHeader('Accept', 'application/json');
                xhr.responseType = 'json';

                // Init listeners
                var genericErrorText = `Couldn't upload file: ${ file.name }.`;
                xhr.addEventListener('error', function() { reject(genericErrorText) });
                xhr.addEventListener('abort', function() { reject() });
                xhr.addEventListener('load', function() {
                  var response = xhr.response;

                  if (!response || xhr.status !== 201) {
                    return reject(response && response.message ? `${genericErrorText}\n${xhr.status} ${response.message}` : `${genericErrorText}\n ${xhr.status} ${xhr.statusText}`);
                  }

                  $('form').append('<input type="hidden" name="ck-media[]" value="' + response.id + '">');

                  resolve({ default: response.url });
                });

                if (xhr.upload) {
                  xhr.upload.addEventListener('progress', function(e) {
                    if (e.lengthComputable) {
                      loader.uploadTotal = e.total;
                      loader.uploaded = e.loaded;
                    }
                  });
                }

                // Send request
                var data = new FormData();
                data.append('upload', file);
                data.append('crud_id', '{{ $checkComplain->id ?? 0 }}');
                xhr.send(data);
              });
            })
        }
      };
    }
  }

  var allEditors = document.querySelectorAll('.ckeditor');
  for (var i = 0; i < allEditors.length; ++i) {
    ClassicEditor.create(
      allEditors[i], {
        extraPlugins: [SimpleUploadAdapter]
      }
    );
  }
});
</script>

<script>
    var uploadedAttechmentMap = {}
Dropzone.options.attechmentDropzone = {
    url: '{{ route('admin.check-complains.storeMedia') }}',
    maxFilesize: 50, // MB
    addRemoveLinks: true,
    headers: {
      'X-CSRF-TOKEN': "{{ csrf_token() }}"
    },
    params: {
      size: 50
    },
    success: function (file, response) {
      $('form').append('<input type="hidden" name="attechment[]" value="' + response.name + '">')
      uploadedAttechmentMap[file.name] = response.name
    },
    removedfile: function (file) {
      file.previewElement.remove()
      var name = ''
      if (typeof file.file_name !== 'undefined') {
        name = file.file_name
      } else {
        name = uploadedAttechmentMap[file.name]
      }
      $('form').find('input[name="attechment[]"][value="' + name + '"]').remove()
    },
    init: function () {
@if(isset($checkComplain) && $checkComplain->attechment)
          var files =
            {!! json_encode($checkComplain->attechment) !!}
              for (var i in files) {
              var file = files[i]
              this.options.addedfile.call(this, file)
              file.previewElement.classList.add('dz-complete')
              $('form').append('<input type="hidden" name="attechment[]" value="' + file.file_name + '">')
            }
@endif
    },
     error: function (file, response) {
         if ($.type(response) === 'string') {
             var message = response //dropzone sends it's own error messages in string
         } else {
             var message = response.errors.file
         }
         file.previewElement.classList.add('dz-error')
         _ref = file.previewElement.querySelectorAll('[data-dz-errormessage]')
         _results = []
         for (_i = 0, _len = _ref.length; _i < _len; _i++) {
             node = _ref[_i]
             _results.push(node.textContent = message)
         }

         return _results
     }
}
</script>
@endsection
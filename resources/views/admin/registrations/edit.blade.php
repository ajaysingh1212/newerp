{{-- resources/views/admin/registrations/edit.blade.php --}}
@extends('layouts.admin')
@section('content')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.css"/>

<style>
  .card { border-radius:12px; box-shadow:0 8px 24px rgba(0,0,0,0.06); border:none; }
  .card-header{ background:linear-gradient(90deg,#0b63d7,#084a9b); color:#fff; padding:14px; font-weight:700; }
  .step-card{ background:#fff; padding:18px; border-radius:10px; margin-bottom:12px; }
  .thumb-row { display:flex; gap:10px; margin-bottom:8px; flex-wrap:wrap; }
  .thumb { width:80px; height:60px; border-radius:6px; object-fit:cover; border:1px solid #ddd; }
  .aadhaar-block, .pan-block{ height:40px; text-align:center; font-weight:700; letter-spacing:3px; border-radius:8px; border:1px solid #e2e8f0; }
</style>

<div class="card">
  <div class="card-header">SMI Investor Registration — Edit (ID: {{ $registration->id }})</div>
  <div class="card-body">
    <form method="POST" action="{{ route('admin.registrations.update', [$registration->id]) }}" enctype="multipart/form-data" id="registrationEditForm">
      @csrf
      @method('PUT')

      <div class="step-card" id="step1">
        <div class="row">
          <div class="col-md-4 mb-3">
            <label class="required-star">Registration No.</label>
            <input type="text" class="form-control" name="reg" value="{{ old('reg', $registration->reg) }}" readonly>
          </div>

          <div class="col-md-4 mb-3">
            <label class="required-star">Investor</label>
            <select class="form-control select2" name="investor_id" required>
              @foreach($investors as $id => $entry)
                <option value="{{ $id }}" {{ (old('investor_id', $registration->investor_id) == $id) ? 'selected' : '' }}>{{ $entry }}</option>
              @endforeach
            </select>
          </div>

          <div class="col-md-4 mb-3">
            <label>Referral Code</label>
            <input type="text" class="form-control" name="referral_code" value="{{ old('referral_code', $registration->referral_code) }}">
          </div>

          {{-- Aadhaar blocks prefilled --}}
          @php
            $aad = old('aadhaar_number', $registration->aadhaar_number ?? '');
            $aad_parts = str_split($aad, 4);
            while(count($aad_parts) < 3) $aad_parts[] = '';
            $pan = old('pan_number', $registration->pan_number ?? '');
            $pan_p1 = substr($pan,0,5) ?: '';
            $pan_p2 = substr($pan,5,4) ?: '';
            $pan_p3 = substr($pan,9,1) ?: '';
          @endphp

          <div class="col-md-4 mb-3">
            <label class="required-star">Aadhaar Number</label>
            <div class="d-flex gap-2">
              <input class="form-control aadhaar-block" maxlength="4" value="{{ $aad_parts[0] }}">
              <input class="form-control aadhaar-block" maxlength="4" value="{{ $aad_parts[1] }}">
              <input class="form-control aadhaar-block" maxlength="4" value="{{ $aad_parts[2] }}">
            </div>
            <input type="hidden" name="aadhaar_number" id="aadhaar_number_edit" value="{{ old('aadhaar_number', $registration->aadhaar_number) }}">
          </div>

          <div class="col-md-4 mb-3">
            <label class="required-star">PAN Number</label>
            <div class="d-flex gap-2">
              <input class="form-control pan-block" maxlength="5" value="{{ $pan_p1 }}" style="text-transform:uppercase">
              <input class="form-control pan-block" maxlength="4" value="{{ $pan_p2 }}">
              <input class="form-control pan-block" maxlength="1" value="{{ $pan_p3 }}" style="text-transform:uppercase">
            </div>
            <input type="hidden" name="pan_number" id="pan_number_edit" value="{{ old('pan_number', $registration->pan_number) }}">
          </div>

          <div class="col-md-4 mb-3">
            <label>Date of Birth</label>
            <input class="form-control date" name="dob" value="{{ old('dob', $registration->dob) }}">
          </div>

          <div class="col-md-4 mb-3">
            <label class="required-star">Gender</label>
            <select class="form-control" name="gender" required>
              <option value="">Please select</option>
              @foreach(App\Models\Registration::GENDER_SELECT as $k => $v)
                <option value="{{ $k }}" {{ old('gender', $registration->gender) === (string)$k ? 'selected' : '' }}>{{ $v }}</option>
              @endforeach
            </select>
          </div>

          <div class="col-md-8 mb-3">
            <label>Father Name</label>
            <input class="form-control" name="father_name" value="{{ old('father_name', $registration->father_name) }}">
          </div>

        </div>

        <div class="text-right">
          <button type="button" class="btn btn-primary" onclick="showStepEdit(2)">Next →</button>
        </div>
      </div>

      <div class="step-card d-none" id="step2">
        <div class="row">
          <div class="col-md-6 mb-3">
            <label>Address Line 1</label>
            <textarea class="form-control ckeditor" name="address_line_1">{!! old('address_line_1', $registration->address_line_1) !!}</textarea>
          </div>
          <div class="col-md-6 mb-3">
            <label>Address Line 2</label>
            <textarea class="form-control ckeditor" name="address_line_2">{!! old('address_line_2', $registration->address_line_2) !!}</textarea>
          </div>

          <div class="col-md-4 mb-3">
            <label class="required-star">Pincode</label>
            <input class="form-control" name="pincode" id="edit_pincode" value="{{ old('pincode', $registration->pincode) }}" required>
          </div>

          <div class="col-md-4 mb-3">
            <label>City</label>
            <input class="form-control" name="city" id="edit_city" value="{{ old('city', $registration->city) }}">
          </div>

          <div class="col-md-4 mb-3">
            <label>State</label>
            <input class="form-control" name="state" id="edit_state" value="{{ old('state', $registration->state) }}">
          </div>

          <div class="col-md-12 mb-3">
            <label class="required-star">Country</label>
            <input class="form-control" name="country" id="edit_country" value="{{ old('country', $registration->country) }}" required>
          </div>
        </div>

        <div class="text-right">
          <button type="button" class="btn btn-secondary" onclick="showStepEdit(1)">← Back</button>
          <button type="button" class="btn btn-primary" onclick="showStepEdit(3)">Next →</button>
        </div>
      </div>

      <div class="step-card d-none" id="step3">
        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="required-star">Account Holder Name</label>
            <input class="form-control" name="bank_account_holder_name" value="{{ old('bank_account_holder_name', $registration->bank_account_holder_name) }}" required>
          </div>

          <div class="col-md-6 mb-3">
            <label class="required-star">Account Number</label>
            <input class="form-control" name="bank_account_number" value="{{ old('bank_account_number', $registration->bank_account_number) }}" required>
          </div>

          <div class="col-md-4 mb-3">
            <label class="required-star">IFSC Code</label>
            <input class="form-control" name="ifsc_code" id="edit_ifsc" value="{{ old('ifsc_code', $registration->ifsc_code) }}" required>
          </div>

          <div class="col-md-4 mb-3">
            <label>Bank Name</label>
            <input class="form-control" name="bank_name" id="edit_bank_name" value="{{ old('bank_name', $registration->bank_name) }}">
          </div>

          <div class="col-md-4 mb-3">
            <label>Branch</label>
            <input class="form-control" name="bank_branch" id="edit_bank_branch" value="{{ old('bank_branch', $registration->bank_branch) }}">
          </div>

        </div>

        <div class="text-right">
          <button type="button" class="btn btn-secondary" onclick="showStepEdit(2)">← Back</button>
          <button type="button" class="btn btn-primary" onclick="showStepEdit(4)">Next →</button>
        </div>
      </div>

      <div class="step-card d-none" id="step4">
        <div class="row">

          <div class="col-md-6 mb-3">
            <label class="required-star">PAN Card Image</label>
            <div class="thumb-row">
              @foreach($registration->pan_card_image as $file)
                <img src="{{ $file->url ?? $file->getUrl() }}" class="thumb" alt="">
              @endforeach
            </div>
            <div class="dropzone" id="pan_card_image_dropzone_edit"></div>
          </div>

          <div class="col-md-6 mb-3">
            <label class="required-star">Aadhaar Front</label>
            <div class="thumb-row">
              @if($registration->aadhaar_front_image)
                <img src="{{ $registration->aadhaar_front_image->url ?? $registration->aadhaar_front_image->getUrl() }}" class="thumb" alt="">
              @endif
            </div>
            <div class="dropzone" id="aadhaar_front_dropzone_edit"></div>
          </div>

          <div class="col-md-6 mb-3">
            <label class="required-star">Aadhaar Back</label>
            <div class="thumb-row">
              @if($registration->aadhaar_back_image)
                <img src="{{ $registration->aadhaar_back_image->url ?? $registration->aadhaar_back_image->getUrl() }}" class="thumb" alt="">
              @endif
            </div>
            <div class="dropzone" id="aadhaar_back_dropzone_edit"></div>
          </div>

          <div class="col-md-6 mb-3">
            <label>Profile Photo</label>
            <div class="thumb-row">
              @if($registration->profile_image)
                <img src="{{ $registration->profile_image->url ?? $registration->profile_image->getUrl() }}" class="thumb" alt="">
              @endif
            </div>
            <div class="dropzone" id="profile_dropzone_edit"></div>
          </div>

          <div class="col-md-6 mb-3">
            <label>Signature</label>
            <div class="thumb-row">
              @if($registration->signature_image)
                <img src="{{ $registration->signature_image->url ?? $registration->signature_image->getUrl() }}" class="thumb" alt="">
              @endif
            </div>
            <div class="dropzone" id="signature_dropzone_edit"></div>
          </div>

        </div>

        <div class="text-right">
          <button type="button" class="btn btn-secondary" onclick="showStepEdit(3)">← Back</button>
          <button type="button" class="btn btn-primary" onclick="showStepEdit(5)">Next →</button>
        </div>
      </div>

      <div class="step-card d-none" id="step5">
        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="required-star">Income Range</label>
            <select class="form-control" name="income_range" required>
              @foreach(App\Models\Registration::INCOME_RANGE_SELECT as $k => $v)
                <option value="{{ $k }}" {{ old('income_range', $registration->income_range) === (string)$k ? 'selected' : '' }}>{{ $v }}</option>
              @endforeach
            </select>
          </div>

          <div class="col-md-6 mb-3">
            <label class="required-star">Occupation</label>
            <select class="form-control" name="occupation" required>
              @foreach(App\Models\Registration::OCCUPATION_SELECT as $k => $v)
                <option value="{{ $k }}" {{ old('occupation', $registration->occupation) === (string)$k ? 'selected' : '' }}>{{ $v }}</option>
              @endforeach
            </select>
          </div>

          <div class="col-md-6 mb-3">
            <label>Risk Profile</label>
            <select class="form-control" name="risk_profile">
              @foreach(App\Models\Registration::RISK_PROFILE_SELECT as $k => $v)
                <option value="{{ $k }}" {{ old('risk_profile', $registration->risk_profile) === (string)$k ? 'selected' : '' }}>{{ $v }}</option>
              @endforeach
            </select>
          </div>

          <div class="col-md-6 mb-3">
            <label>Investment Experience</label>
            <select class="form-control" name="investment_experience">
              @foreach(App\Models\Registration::INVESTMENT_EXPERIENCE_SELECT as $k => $v)
                <option value="{{ $k }}" {{ old('investment_experience', $registration->investment_experience) === (string)$k ? 'selected' : '' }}>{{ $v }}</option>
              @endforeach
            </select>
          </div>

          @if(auth()->user() && auth()->user()->roles->first()->title === 'Admin')
            <div class="col-md-6 mb-3">
              <label>KYC Status</label>
              <select class="form-control" name="kyc_status">
                @foreach(App\Models\Registration::KYC_STATUS_SELECT as $k => $v)
                  <option value="{{ $k }}" {{ old('kyc_status', $registration->kyc_status) === (string)$k ? 'selected' : '' }}>{{ $v }}</option>
                @endforeach
              </select>
            </div>

            <div class="col-md-6 mb-3">
              <label>Account Status</label>
              <select class="form-control" name="account_status">
                @foreach(App\Models\Registration::ACCOUNT_STATUS_SELECT as $k => $v)
                  <option value="{{ $k }}" {{ old('account_status', $registration->account_status) === (string)$k ? 'selected' : '' }}>{{ $v }}</option>
                @endforeach
              </select>
            </div>

            <div class="col-md-6 mb-3">
              <label>Is Email Verified</label><br>
              @foreach(App\Models\Registration::IS_EMAIL_VERIFIED_RADIO as $k => $v)
                <label class="mr-3"><input type="radio" name="is_email_verified" value="{{ $k }}" {{ old('is_email_verified', $registration->is_email_verified) === (string)$k ? 'checked' : '' }}> {{ $v }}</label>
              @endforeach
            </div>

            <div class="col-md-6 mb-3">
              <label>Is Phone Verified</label><br>
              @foreach(App\Models\Registration::IS_PHONE_VERIFIED_RADIO as $k => $v)
                <label class="mr-3"><input type="radio" name="is_phone_verified" value="{{ $k }}" {{ old('is_phone_verified', $registration->is_phone_verified) === (string)$k ? 'checked' : '' }}> {{ $v }}</label>
              @endforeach
            </div>
          @endif

        </div>

        <div class="text-right">
          <button type="button" class="btn btn-secondary" onclick="showStepEdit(4)">← Back</button>
          <button type="submit" class="btn btn-success">Save Changes</button>
        </div>
      </div>

    </form>
  </div>
</div>

@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.js"></script>
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>

<script>
window._token = '{{ csrf_token() }}';
Dropzone.autoDiscover = false;

/* Step functions (edit) */
function showStepEdit(n){
  document.querySelectorAll('.step-card').forEach(c => c.classList.add('d-none'));
  document.getElementById('step' + n).classList.remove('d-none');
}
document.addEventListener('DOMContentLoaded', () => showStepEdit(1));

/* Aadhaar/PAN block behavior (edit) */
(function(){
  const aad = document.querySelectorAll('#registrationEditForm .aadhaar-block');
  if (aad.length) {
    aad.forEach((el,i) => el.addEventListener('input', () => {
      el.value = el.value.replace(/\D/g,'');
      if (el.value.length === 4 && i < aad.length -1) aad[i+1].focus();
      document.getElementById('aadhaar_number_edit').value = Array.from(aad).map(x=>x.value).join('');
    }));
  }
  const pan = document.querySelectorAll('#registrationEditForm .pan-block');
  if (pan.length) {
    pan.forEach((el,i) => el.addEventListener('input', () => {
      el.value = el.value.toUpperCase();
      if (i === 0) el.value = el.value.replace(/[^A-Z]/g,'');
      if (i === 1) el.value = el.value.replace(/[^0-9]/g,'');
      if (i === 2) el.value = el.value.replace(/[^A-Z]/g,'');
      if (el.value.length === el.maxLength && i < pan.length -1) pan[i+1].focus();
      document.getElementById('pan_number_edit').value = Array.from(pan).map(x=>x.value).join('');
    }));
  }
})();

/* CKEditor adapter (edit) */
function SimpleUploadAdapterEdit(editor){
  editor.plugins.get('FileRepository').createUploadAdapter = function(loader){
    return {
      upload: function(){
        return loader.file.then(file => new Promise((resolve, reject) => {
          const xhr = new XMLHttpRequest();
          xhr.open('POST','{{ route('admin.registrations.storeCKEditorImages') }}', true);
          xhr.setRequestHeader('x-csrf-token', window._token);
          xhr.setRequestHeader('Accept', 'application/json');
          xhr.responseType = 'json';
          xhr.addEventListener('load', function(){
            const res = xhr.response;
            if (!res || xhr.status !== 201) return reject('Upload failed');
            document.getElementById('registrationEditForm').insertAdjacentHTML('beforeend',
              `<input type="hidden" name="ck-media[]" value="${res.id}">`);
            resolve({ default: res.url });
          });
          xhr.addEventListener('error', () => reject('Upload failed'));
          const data = new FormData();
          data.append('upload', file);
          data.append('crud_id', '{{ $registration->id }}');
          xhr.send(data);
        }));
      }
    };
  };
}
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.ckeditor').forEach(el => {
    ClassicEditor.create(el, { extraPlugins: [ SimpleUploadAdapterEdit ] }).catch(e => console.error(e));
  });
});

/* IFSC & PIN autofill (edit) */
document.getElementById('edit_ifsc')?.addEventListener('input', function(){
  const v = this.value.trim().toUpperCase();
  if (v.length === 11) {
    fetch(`https://ifsc.razorpay.com/${v}`).then(r => r.json()).then(data => {
      document.getElementById('edit_bank_name').value = data.BANK ?? '';
      document.getElementById('edit_bank_branch').value = data.BRANCH ?? '';
      if (data.ADDRESS) {
        const parts = data.ADDRESS.split(',');
        if (parts.length >= 2) document.getElementById('edit_city').value = parts[1].trim();
      }
    }).catch(()=>{/*ignore*/});
  }
});

document.getElementById('edit_pincode')?.addEventListener('input', function(){
  const v = this.value.trim();
  if (v.length === 6) {
    fetch(`https://api.postalpincode.in/pincode/${v}`).then(r => r.json()).then(res => {
      if (res && res[0] && res[0].Status === 'Success' && res[0].PostOffice.length) {
        const p = res[0].PostOffice[0];
        document.getElementById('edit_city').value = p.Block ?? p.District ?? '';
        document.getElementById('edit_state').value = p.State ?? '';
        document.getElementById('edit_country').value = p.Country ?? '';
        document.querySelector("textarea[name='address_line_1']").value = `${p.Name}, ${p.Block}, ${p.District}, ${p.State}`;
      }
    }).catch(()=>{/*ignore*/});
  }
});

/* Dropzone helper for edit (preload thumbs shown outside) */
function createDropzoneEdit(selector, { maxFiles = null, hiddenInputName }) {
  const dz = new Dropzone(selector, {
    url: "{{ route('admin.registrations.storeMedia') }}",
    paramName: "file",
    maxFilesize: 20,
    maxFiles: maxFiles,
    headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
    addRemoveLinks: true
  });

  dz.on('success', function(file, response){
    const form = document.getElementById('registrationEditForm');
    const input = document.createElement('input'); input.type='hidden'; input.name = hiddenInputName; input.value = response.name; form.appendChild(input);
  });

  dz.on('removedfile', function(file){
    const form = document.getElementById('registrationEditForm');
    const name = file.upload?.filename ?? file.name;
    form.querySelectorAll(`input[name="${hiddenInputName}"]`).forEach(inp => { if (inp.value === name) inp.remove(); });
  });

  return dz;
}

/* init edit dropzones */
document.addEventListener('DOMContentLoaded', function(){
  createDropzoneEdit("#pan_card_image_dropzone_edit", { maxFiles: null, hiddenInputName: 'pan_card_image[]' });
  createDropzoneEdit("#aadhaar_front_dropzone_edit", { maxFiles: 1, hiddenInputName: 'aadhaar_front_image[]' });
  createDropzoneEdit("#aadhaar_back_dropzone_edit", { maxFiles: 1, hiddenInputName: 'aadhaar_back_image[]' });
  createDropzoneEdit("#profile_dropzone_edit", { maxFiles: 1, hiddenInputName: 'profile_image[]' });
  createDropzoneEdit("#signature_dropzone_edit", { maxFiles: 1, hiddenInputName: 'signature_image[]' });
});
</script>
@endsection

{{-- resources/views/admin/registrations/create.blade.php --}}
@extends('layouts.admin')
@section('content')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.css"/>

<style>
  /* Small, clean theme for form */
  .card { border-radius:12px; box-shadow:0 8px 24px rgba(0,0,0,0.06); border:none; }
  .card-header{ background:linear-gradient(90deg,#0b63d7,#084a9b); color:#fff; font-weight:700; padding:14px; }
  .step-card{ background:#fff; padding:18px; border-radius:10px; margin-bottom:12px; }
  label{ font-weight:600; }
  .required-star::after{ content:" *"; color:red; }
  .aadhaar-block, .pan-block{ height:40px; text-align:center; font-weight:700; letter-spacing:3px; border-radius:8px; border:1px solid #e2e8f0; }
  .thumb-row { display:flex; gap:10px; margin-bottom:8px; flex-wrap:wrap; }
  .thumb { width:80px; height:60px; border-radius:6px; object-fit:cover; border:1px solid #ddd; }
  .dropzone { border-radius:8px !important; background: #fbfdff !important; }
</style>

<div class="card">
  <div class="card-header">SMI Investor Registration — Create</div>
  <div class="card-body">
    <form method="POST" action="{{ route('admin.registrations.store') }}" enctype="multipart/form-data" id="registrationCreateForm">
      @csrf

      <div class="step-card" id="step1">
        <div class="row">
          <div class="col-md-4 mb-3">
            <label class="required-star">Registration No.</label>
            <input type="text" class="form-control" id="reg_no" name="reg" readonly>
          </div>

          <div class="col-md-4 mb-3">
            <label class="required-star">Investor</label>
            <select class="form-control select2" name="investor_id" required>
              @foreach($investors as $id => $entry)
                <option value="{{ $id }}">{{ $entry }}</option>
              @endforeach
            </select>
          </div>

          <div class="col-md-4 mb-3">
            <label>Referral Code</label>
            <input type="text" class="form-control" name="referral_code">
          </div>

          <div class="col-md-4 mb-3">
            <label class="required-star">Aadhaar Number</label>
            <div class="d-flex gap-2">
              <input class="form-control aadhaar-block" maxlength="4" />
              <input class="form-control aadhaar-block" maxlength="4" />
              <input class="form-control aadhaar-block" maxlength="4" />
            </div>
            <input type="hidden" name="aadhaar_number" id="aadhaar_number">
          </div>

          <div class="col-md-4 mb-3">
            <label class="required-star">PAN Number</label>
            <div class="d-flex gap-2">
              <input class="form-control pan-block" maxlength="5" style="text-transform:uppercase"/>
              <input class="form-control pan-block" maxlength="4"/>
              <input class="form-control pan-block" maxlength="1" style="text-transform:uppercase"/>
            </div>
            <input type="hidden" name="pan_number" id="pan_number">
          </div>

          <div class="col-md-4 mb-3">
            <label>Date of Birth</label>
            <input type="text" class="form-control date" name="dob" placeholder="DD-MM-YYYY">
          </div>

          <div class="col-md-4 mb-3">
            <label class="required-star">Gender</label>
            <select class="form-control" name="gender" required>
              <option value="">Please select</option>
              @foreach(App\Models\Registration::GENDER_SELECT as $k => $v)
                <option value="{{ $k }}">{{ $v }}</option>
              @endforeach
            </select>
          </div>

          <div class="col-md-8 mb-3">
            <label>Father Name</label>
            <input type="text" class="form-control" name="father_name">
          </div>
        </div>

        <div class="text-right">
          <button type="button" class="btn btn-primary" onclick="showStep(2)">Next →</button>
        </div>
      </div>

      <div class="step-card d-none" id="step2">
        <div class="row">
          <div class="col-md-6 mb-3">
            <label>Address Line 1</label>
            <textarea class="form-control ckeditor" name="address_line_1"></textarea>
          </div>
          <div class="col-md-6 mb-3">
            <label>Address Line 2</label>
            <textarea class="form-control ckeditor" name="address_line_2"></textarea>
          </div>

          <div class="col-md-4 mb-3">
            <label class="required-star">Pincode</label>
            <input class="form-control" name="pincode" id="create_pincode" required>
          </div>

          <div class="col-md-4 mb-3">
            <label>City</label>
            <input class="form-control" name="city" id="create_city">
          </div>

          <div class="col-md-4 mb-3">
            <label>State</label>
            <input class="form-control" name="state" id="create_state">
          </div>

          <div class="col-md-12 mb-3">
            <label class="required-star">Country</label>
            <input class="form-control" name="country" id="create_country" required>
          </div>
        </div>

        <div class="text-right">
          <button type="button" class="btn btn-secondary" onclick="showStep(1)">← Back</button>
          <button type="button" class="btn btn-primary" onclick="showStep(3)">Next →</button>
        </div>
      </div>

      <div class="step-card d-none" id="step3">
        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="required-star">Account Holder Name</label>
            <input class="form-control" name="bank_account_holder_name" required>
          </div>

          <div class="col-md-6 mb-3">
            <label class="required-star">Account Number</label>
            <input class="form-control" name="bank_account_number" required>
          </div>

          <div class="col-md-4 mb-3">
            <label class="required-star">IFSC Code</label>
            <input class="form-control" name="ifsc_code" id="create_ifsc" required>
          </div>

          <div class="col-md-4 mb-3">
            <label>Bank Name</label>
            <input class="form-control" name="bank_name" id="create_bank_name">
          </div>

          <div class="col-md-4 mb-3">
            <label>Branch</label>
            <input class="form-control" name="bank_branch" id="create_bank_branch">
          </div>
        </div>

        <div class="text-right">
          <button type="button" class="btn btn-secondary" onclick="showStep(2)">← Back</button>
          <button type="button" class="btn btn-primary" onclick="showStep(4)">Next →</button>
        </div>
      </div>

      <div class="step-card d-none" id="step4">
        <div class="row">

          <div class="col-md-6 mb-3">
            <label class="required-star">PAN Card Image</label>

            {{-- thumbnails area (create has none initially) --}}
            <div class="thumb-row" id="pan_thumbs"></div>

            <div class="dropzone" id="pan_card_image_dropzone"></div>
          </div>

          <div class="col-md-6 mb-3">
            <label class="required-star">Aadhaar Front</label>
            <div class="thumb-row" id="aadhaar_front_thumbs"></div>
            <div class="dropzone" id="aadhaar_front_dropzone"></div>
          </div>

          <div class="col-md-6 mb-3">
            <label class="required-star">Aadhaar Back</label>
            <div class="thumb-row" id="aadhaar_back_thumbs"></div>
            <div class="dropzone" id="aadhaar_back_dropzone"></div>
          </div>

          <div class="col-md-6 mb-3">
            <label>Profile Photo</label>
            <div class="thumb-row" id="profile_thumbs"></div>
            <div class="dropzone" id="profile_dropzone"></div>
          </div>

          <div class="col-md-6 mb-3">
            <label>Signature</label>
            <div class="thumb-row" id="signature_thumbs"></div>
            <div class="dropzone" id="signature_dropzone"></div>
          </div>

        </div>

        <div class="text-right">
          <button type="button" class="btn btn-secondary" onclick="showStep(3)">← Back</button>
          <button type="button" class="btn btn-primary" onclick="showStep(5)">Next →</button>
        </div>
      </div>

      <div class="step-card d-none" id="step5">
        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="required-star">Income Range</label>
            <select class="form-control" name="income_range" required>
              @foreach(App\Models\Registration::INCOME_RANGE_SELECT as $k => $v)
                <option value="{{ $k }}">{{ $v }}</option>
              @endforeach
            </select>
          </div>

          <div class="col-md-6 mb-3">
            <label class="required-star">Occupation</label>
            <select class="form-control" name="occupation" required>
              @foreach(App\Models\Registration::OCCUPATION_SELECT as $k => $v)
                <option value="{{ $k }}">{{ $v }}</option>
              @endforeach
            </select>
          </div>

          <div class="col-md-6 mb-3">
            <label>Risk Profile</label>
            <select class="form-control" name="risk_profile">
              @foreach(App\Models\Registration::RISK_PROFILE_SELECT as $k => $v)
                <option value="{{ $k }}">{{ $v }}</option>
              @endforeach
            </select>
          </div>

          <div class="col-md-6 mb-3">
            <label>Investment Experience</label>
            <select class="form-control" name="investment_experience">
              @foreach(App\Models\Registration::INVESTMENT_EXPERIENCE_SELECT as $k => $v)
                <option value="{{ $k }}">{{ $v }}</option>
              @endforeach
            </select>
          </div>
        </div>

        <div class="text-right">
          <button type="button" class="btn btn-secondary" onclick="showStep(4)">← Back</button>
          <button type="submit" class="btn btn-success">Submit Registration ✔</button>
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

/* Step navigation */
function showStep(n){
  document.querySelectorAll('.step-card').forEach(c => c.classList.add('d-none'));
  document.getElementById('step' + n).classList.remove('d-none');
  window.scrollTo({ top: 0, behavior: 'smooth' });
}

/* Reg no */
document.addEventListener('DOMContentLoaded', () => {
  const regEl = document.getElementById('reg_no');
  if (regEl && !regEl.value) regEl.value = Math.floor(1000000000 + Math.random()*9000000000);
});

/* Aadhaar/PAN blocks behaviour */
(function(){
  const aad = document.querySelectorAll('.aadhaar-block');
  aad.forEach((el, i) => el.addEventListener('input', () => {
    el.value = el.value.replace(/\D/g,'');
    if (el.value.length === 4 && i < aad.length -1) aad[i+1].focus();
    document.getElementById('aadhaar_number').value = Array.from(aad).map(x=>x.value).join('');
  }));

  const pan = document.querySelectorAll('.pan-block');
  pan.forEach((el, i) => el.addEventListener('input', () => {
    el.value = el.value.toUpperCase();
    if (i === 0) el.value = el.value.replace(/[^A-Z]/g,'');
    if (i === 1) el.value = el.value.replace(/[^0-9]/g,'');
    if (i === 2) el.value = el.value.replace(/[^A-Z]/g,'');
    if (el.value.length === el.maxLength && i < pan.length -1) pan[i+1].focus();
    document.getElementById('pan_number').value = Array.from(pan).map(x=>x.value).join('');
  }));
})();

/* CKEditor adapter */
function SimpleUploadAdapter(editor) {
  editor.plugins.get('FileRepository').createUploadAdapter = function(loader) {
    return {
      upload: function() {
        return loader.file.then(file => new Promise((resolve, reject) => {
          const xhr = new XMLHttpRequest();
          xhr.open('POST', '{{ route('admin.registrations.storeCKEditorImages') }}', true);
          xhr.setRequestHeader('x-csrf-token', window._token);
          xhr.setRequestHeader('Accept', 'application/json');
          xhr.responseType = 'json';
          xhr.addEventListener('load', function() {
            const res = xhr.response;
            if (!res || xhr.status !== 201) return reject('Upload failed');
            document.getElementById('registrationCreateForm').insertAdjacentHTML('beforeend',
              `<input type="hidden" name="ck-media[]" value="${res.id}">`);
            resolve({ default: res.url });
          });
          xhr.addEventListener('error', () => reject('Upload failed'));
          const data = new FormData();
          data.append('upload', file);
          data.append('crud_id', 0);
          xhr.send(data);
        }));
      }
    };
  };
}
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.ckeditor').forEach(el => {
    ClassicEditor.create(el, { extraPlugins: [ SimpleUploadAdapter ] }).catch(e => console.error(e));
  });
});

/* IFSC autofill (create) */
document.getElementById('create_ifsc')?.addEventListener('input', function(){
  const v = this.value.trim().toUpperCase();
  if (v.length === 11) {
    fetch(`https://ifsc.razorpay.com/${v}`).then(r => r.json()).then(data => {
      document.getElementById('create_bank_name').value = data.BANK ?? '';
      document.getElementById('create_bank_branch').value = data.BRANCH ?? '';
      if (data.ADDRESS) {
        const parts = data.ADDRESS.split(',');
        if (parts.length >= 2) document.getElementById('create_city').value = parts[1].trim();
      }
    }).catch(()=>{/* ignore */});
  }
});

/* PIN autofill (create) */
document.getElementById('create_pincode')?.addEventListener('input', function(){
  const v = this.value.trim();
  if (v.length === 6) {
    fetch(`https://api.postalpincode.in/pincode/${v}`).then(r => r.json()).then(res => {
      if (res && res[0] && res[0].Status === 'Success' && res[0].PostOffice.length) {
        const p = res[0].PostOffice[0];
        document.getElementById('create_city').value = p.Block ?? p.District ?? '';
        document.getElementById('create_state').value = p.State ?? '';
        document.getElementById('create_country').value = p.Country ?? '';
        document.querySelector("textarea[name='address_line_1']").value = `${p.Name}, ${p.Block}, ${p.District}, ${p.State}`;
      }
    }).catch(()=>{/*ignore*/});
  }
});

/* Dropzone helper */
function createDropzone(selector, { maxFiles = null, thumbnailsContainer = null, hiddenInputName }) {
  const dz = new Dropzone(selector, {
    url: "{{ route('admin.registrations.storeMedia') }}",
    paramName: "file",
    maxFilesize: 20,
    maxFiles: maxFiles,
    headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
    addRemoveLinks: true,
    init: function(){},
  });

  dz.on('success', function(file, response){
    // append hidden input as array always
    const form = document.getElementById('registrationCreateForm');
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = hiddenInputName;
    input.value = response.name;
    form.appendChild(input);

    if (thumbnailsContainer && response && response.url) {
      const img = document.createElement('img');
      img.src = response.url ?? '/';
      img.className = 'thumb';
      thumbnailsContainer.appendChild(img);
    } else if (thumbnailsContainer && response.name) {
      // thumbnails can only show if backend returned a public url - storeMedia currently returns name.
      // you can enhance storeMedia to return url as well; if not, file previews will be available in Dropzone itself.
    }
  });

  dz.on('removedfile', function(file){
    // remove corresponding hidden input(s)
    const form = document.getElementById('registrationCreateForm');
    // If Dropzone provided file.name or file.upload.filename, match both possibilities
    const name = file.upload?.filename ?? file.name;
    form.querySelectorAll(`input[name="${hiddenInputName}"]`).forEach(inp => {
      if (inp.value === name) inp.remove();
    });
  });

  return dz;
}

/* Create dropzones (create page) */
document.addEventListener('DOMContentLoaded', function(){
  const panThumbs = document.getElementById('pan_thumbs');
  const aadFThumbs = document.getElementById('aadhaar_front_thumbs');
  const aadBThumbs = document.getElementById('aadhaar_back_thumbs');
  const profThumbs = document.getElementById('profile_thumbs');
  const signThumbs = document.getElementById('signature_thumbs');

  createDropzone("#pan_card_image_dropzone", { maxFiles: null, thumbnailsContainer: panThumbs, hiddenInputName: 'pan_card_image[]' });
  createDropzone("#aadhaar_front_dropzone", { maxFiles: 1, thumbnailsContainer: aadFThumbs, hiddenInputName: 'aadhaar_front_image[]' });
  createDropzone("#aadhaar_back_dropzone", { maxFiles: 1, thumbnailsContainer: aadBThumbs, hiddenInputName: 'aadhaar_back_image[]' });
  createDropzone("#profile_dropzone", { maxFiles: 1, thumbnailsContainer: profThumbs, hiddenInputName: 'profile_image[]' });
  createDropzone("#signature_dropzone", { maxFiles: 1, thumbnailsContainer: signThumbs, hiddenInputName: 'signature_image[]' });
});
</script>
@endsection

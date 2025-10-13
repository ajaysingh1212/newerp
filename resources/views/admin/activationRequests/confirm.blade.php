@extends('layouts.admin')
@section('content')



<meta name="csrf-token" content="{{ csrf_token() }}">

{{-- Bootstrap Icons --}}
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

{{-- Header --}}
<div class="">
    <div class="card-body">
         @include('watermark')
        <div class="row align-items-center">
            <div class="col-md-6">
    @php
        $operator = strtolower(optional($product->vts)->operator);
    @endphp

    @if ($operator === 'airtel')
        <img src="{{ asset('img/airlogo.png') }}" alt="Airtel" style="border-radius: 0.5rem; width: 100px; height: auto;">
    @elseif ($operator === 'vodafone')
        <img src="{{ asset('img/vodalogo.png') }}" alt="Vodafone" style="border-radius: 0.5rem; width: 100px; height: auto;">
    @else
        {{ $product->vts->operator ?? 'N/A' }}
    @endif
            </div>
            <div class="col-md-6 text-right mt-2 mt-md-0">
                <i class="bi bi-person mr-2" style="font-size: 24px;"></i>
                <i class="bi bi-pencil mr-2" style="font-size: 24px;"></i> 
                <i class="bi bi-box-arrow-right mr-2" style="font-size: 24px;"></i>
            </div>
        </div>
    </div>
</div>

{{-- SIM Selection Card --}}
<div class="card mt-3">
    <div class="card-body px-5">
        <div class="row align-items-center">
            <div class="col-md-6 d-flex">
                <select class="form-control w-25 mr-2" name="sim_provider">
                    <option value="MSISDN">MSISDN</option>
                </select>
                <input type="text" class="form-control" name="sim_number" value="98XXXXXXXX" placeholder="Enter SIM number" style="width: 75%; border:none; border-bottom: 1px solid #ccc;">
            </div>
        </div>
    </div>
</div>

{{-- Table --}}
<div class="table-responsive mt-3">
    <table class="table table-bordered table-striped">
        <thead style="background-color:rgba(131, 187, 242, 0.29)">
            <tr>
                <th></th>
                <th>Operator</th>
                <th>Sim Number</th>
                <th>MSISDN</th>
                <th>Activation Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><input type="checkbox" name="selected_ids[]" value="{{ $activationRequest->id }}"></td>

    {{-- SIM Number from VTS relation --}}
<td>{{ optional($product->vts)->operator ?? 'N/A' }}</td>
<td>{{ optional($product->vts)->sim_number ?? 'N/A' }}</td>

{{-- MSISDN --}}
<td>{{ optional($product->vts)->vts_number ?? 'N/A' }}</td>

    {{-- Activation Date --}}
 <td>
{{ $activationRequest->request_date ? \Carbon\Carbon::parse($activationRequest->request_date)->format('d-m-Y') : 'N/A' }}

</td>



                <td>
                    <button type="button" class="btn btn-sm btn-success px-3 py-2 fw-bold" style="font-size: 16px;" data-toggle="modal" data-target="#confirmModal">Sim Activate</button>
                </td>
            </tr>
        </tbody>
    </table>
</div>

{{-- Confirmation Modal --}}
<div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-white">
        <h5 class="modal-title text-danger font-weight-bold">Confirmation</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="outline: none;">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          <div class="form-check mb-3">
              <input class="form-check-input" type="checkbox" id="confirmCheckbox">
              <label class="form-check-label" for="confirmCheckbox" style="font-size: 14px;">
                  We authorize Bharti Airtel Ltd. to commence billing of services and charge rental for numbers being activated with effect from <strong>{{ now()->format('d-m-Y') }}</strong>
              </label>
          </div>
      </div>
      <div class="modal-footer">
          <button class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
          <button class="btn btn-danger btn-sm" id="confirmOkBtn" disabled>Ok</button>
      </div>
    </div>
  </div>
</div>

{{-- Command Buttons Section (Hidden Initially) --}}
<div id="commandCardSection" class="card p-4 mt-4" style="display: none;">
    <h5 class="mb-3 font-weight-bold">Command Actions</h5>
    <div class="row" id="commandButtonContainer">
        @php
            $commands = [
                'GMT COMMAND',
                'APN COMMAND',
                'SERVER COMMAND',
                'PARAM COMMAND',
                'STATUS COMMAND',
                'HBT COMMAND',
                'RESET FACTORY'
            ];
        @endphp

        @foreach ($commands as $index => $command)
            <div class="col-6 col-md-2 mb-3">
                <form method="POST" action="#" class="command-form">
                    @csrf
                    <input type="hidden" name="command" value="{{ $command }}">
                    <button type="submit" class="btn w-100 command-btn {{ $index === 0 ? 'btn-success' : 'btn-secondary' }}" 
                        data-index="{{ $index }}" {{ $index !== 0 ? 'disabled' : '' }}>
                        {{ $command }}
                    </button>
                </form>
            </div>
        @endforeach
    </div>
</div>

<!-- HBT Command Modal -->
<div class="modal fade" id="hbtModal" tabindex="-1" role="dialog" aria-labelledby="hbtModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-white">
        <h5 class="modal-title text-primary font-weight-bold">Information</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="outline: none;">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p class="mb-0">Your Sim activation will be alive in 10 minutes to 4 hours.</p>
      </div>
      <div class="modal-footer">
<form action="{{ route('admin.activation-requests.command', $activationRequest->id) }}" method="POST" style="display: inline;">
    @csrf
    <button type="submit" class="btn btn-primary btn-sm" id="hbtOkBtn">OK</button>
</form>
      </div>
    </div>
  </div>
</div>
<style>
@keyframes blinkRedBlack {
    0%, 100% {
        color: red;
    }
    50% {
        color: black;
    }
}

.blink-red-black {
    animation: blinkRedBlack 1s infinite;
}
</style>


{{-- JS Logic --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const forms = document.querySelectorAll('.command-form');

    forms.forEach((form, i) => {
        const btn = form.querySelector('button');

        form.addEventListener('submit', function (e) {
            e.preventDefault();

            const command = form.querySelector('input[name="command"]').value;

            if (!btn.disabled && btn.classList.contains('btn-success')) {
                if (confirm("Are you sure to send this command?")) {

                    // Special handling for HBT COMMAND
                    if (command === 'HBT COMMAND') {
                        $('#hbtModal').modal('show');
                        return;
                    }

                    // Normal processing flow
                    btn.classList.remove('btn-success');
                    btn.classList.add('btn-secondary');
                    btn.disabled = true;

                  const processingText = document.createElement('small');
processingText.className = " d-block mt-1 processing-text blink-red-black";
processingText.innerText = "Processing...";
btn.insertAdjacentElement('afterend', processingText);


                    // Enable next button
                    const nextForm = forms[i + 1];
                    if (nextForm) {
                        const nextBtn = nextForm.querySelector('button');
                        nextBtn.classList.remove('btn-secondary');
                        nextBtn.classList.add('btn-success');
                        nextBtn.disabled = false;
                    }
                }
            }
        });
    });

    // Modal Checkbox Logic
    const confirmCheckbox = document.getElementById('confirmCheckbox');
    const confirmBtn = document.getElementById('confirmOkBtn');
    const cardSection = document.getElementById('commandCardSection');

    confirmCheckbox.addEventListener('change', function () {
        confirmBtn.disabled = !this.checked;
    });

    confirmBtn.addEventListener('click', function () {
        $('#confirmModal').modal('hide');
        cardSection.style.display = 'block';
    });

    
});
</script>

@endsection
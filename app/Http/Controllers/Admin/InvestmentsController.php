<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Requests\MassDestroyInvestmentRequest;
use App\Http\Requests\StoreInvestmentRequest;
use App\Http\Requests\UpdateInvestmentRequest;
use App\Models\Investment;
use App\Models\Plan;
use App\Models\Registration;
use Carbon\Carbon;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class InvestmentsController extends Controller
{
    use CsvImportTrait;

    public function index()
    {
        abort_if(Gate::denies('investment_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $investments = Investment::with(['select_investor', 'select_plan', 'created_by'])->get();

        return view('admin.investments.index', compact('investments'));
    }

    public function create()
    {
        abort_if(Gate::denies('investment_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $user = Auth::user();
        $userRole = $user->roles->first()->title ?? null;

        // Plans (for cards)
        $plans = Plan::all();

        // registrations for dropdown + full details mapping (for admin)
        if ($userRole === 'Admin') {
            // dropdown list
            $select_investors = Registration::pluck('reg', 'id')->prepend(trans('global.pleaseSelect'), '');

            // full objects for client-side mapping
            $registrations = Registration::select(
                'id',
                'reg',
                'referral_code',
                'aadhaar_number',
                'pan_number',
                'dob',
                'gender',
                'father_name',
                'address_line_1',
                'address_line_2',
                'pincode',
                'city',
                'state',
                'country',
                'bank_account_holder_name',
                'bank_account_number',
                'ifsc_code',
                'bank_name',
                'bank_branch',
                'income_range',
                'occupation',
                'risk_profile',
                'investment_experience',
                'kyc_status',
                'account_status',
                'is_email_verified',
                'is_phone_verified'
            )->get();
            $selected_investor = null;
        } else {
            // non-admin: find registration by investor_id == current user id
            $select_investors = collect(); // empty to avoid blade errors
            $registrations = collect();

            $selected_investor = Registration::where('investor_id', $user->id)
                ->select(
                    'id',
                    'reg',
                    'referral_code',
                    'aadhaar_number',
                    'pan_number',
                    'dob',
                    'gender',
                    'father_name',
                    'address_line_1',
                    'address_line_2',
                    'pincode',
                    'city',
                    'state',
                    'country',
                    'bank_account_holder_name',
                    'bank_account_number',
                    'ifsc_code',
                    'bank_name',
                    'bank_branch',
                    'income_range',
                    'occupation',
                    'risk_profile',
                    'investment_experience',
                    'kyc_status',
                    'account_status',
                    'is_email_verified',
                    'is_phone_verified'
                )->first();

            if ($selected_investor) {
                $registrations = collect([$selected_investor]);
            }
        }

        return view('admin.investments.create', compact('select_investors', 'plans', 'registrations', 'selected_investor', 'plans'));
    }

    public function store(StoreInvestmentRequest $request)
    {
        abort_if(Gate::denies('investment_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $data = $request->all();

        // ----------- STATUS ALWAYS PENDING -----------
        $data['status'] = 'pending';

        // ----------- INVESTOR VALIDATION -----------
        $investorId = $request->input('select_investor_id');
        if (!$investorId) {
            return back()->withErrors(['select_investor_id' => 'Investor is required'])->withInput();
        }

        $registration = Registration::find($investorId);
        if (!$registration) {
            return back()->withErrors(['select_investor_id' => 'Selected investor not found'])->withInput();
        }

        // ----------- INVESTOR VERIFICATION CHECKS -----------
        $kycOk     = strtolower($registration->kyc_status ?? '') === 'verified';
        $accountOk = strtolower($registration->account_status ?? '') === 'active';
        $emailOk   = strtolower($registration->is_email_verified ?? '') === 'yes';
        $phoneOk   = strtolower($registration->is_phone_verified ?? '') === 'yes';

        if (!($kycOk && $accountOk && $emailOk && $phoneOk)) {
            return back()->withErrors([
                'verification' => 
                    'Investor account does not meet required verifications. Required: 
                    KYC = Verified, Account = Active, Email Verified = Yes, Phone Verified = Yes.'
            ])->withInput();
        }

        // ----------- PLAN VALIDATION -----------
        if (!$request->input('select_plan_id')) {
            return back()->withErrors(['select_plan_id' => 'Plan is required'])->withInput();
        }

        // ----------- DATE CONVERSIONS (d-m-Y → Y-m-d) -----------
        if (!empty($data['start_date'])) {
            try {
                $data['start_date'] = Carbon::createFromFormat('d-m-Y', $data['start_date'])->format('Y-m-d');
            } catch (\Exception $e) {}
        }

        if (!empty($data['lockin_end_date'])) {
            try {
                $data['lockin_end_date'] = Carbon::createFromFormat('d-m-Y', $data['lockin_end_date'])->format('Y-m-d');
            } catch (\Exception $e) {}
        }

        if (!empty($data['next_payout_date'])) {
            try {
                $data['next_payout_date'] = Carbon::createFromFormat('d-m-Y', $data['next_payout_date'])->format('Y-m-d');
            } catch (\Exception $e) {}
        }

        // ----------- FINAL CREATE -----------
        $investment = Investment::create($data);

        return redirect()->route('admin.investments.index')->with('success', 'Investment created successfully.');
    }


    public function edit(Investment $investment)
    {
        abort_if(Gate::denies('investment_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $select_investors = Registration::pluck('reg', 'id')->prepend(trans('global.pleaseSelect'), '');

        $select_plans = Plan::pluck('plan_name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $registrations = Registration::with('media')->get(); // For Investor Card + Profile Image

        $plans = Plan::all(); // For plan cards grid UI

        $investment->load('select_investor', 'select_plan', 'created_by');

        return view('admin.investments.edit', compact(
            'investment',
            'select_investors',
            'select_plans',
            'registrations',
            'plans'
        ));
    }


    public function update(UpdateInvestmentRequest $request, Investment $investment)
    {
        abort_if(Gate::denies('investment_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $data = $request->all();

        // STATUS ALWAYS PENDING EVEN ON UPDATE
        $data['status'] = 'Pending';

        // DATE CONVERSION d-m-Y → Y-m-d
        if (!empty($data['start_date'])) {
            try {
                $data['start_date'] = Carbon::createFromFormat('d-m-Y', $data['start_date'])->format('Y-m-d');
            } catch (\Exception $e) {}
        }

        if (!empty($data['lockin_end_date'])) {
            try {
                $data['lockin_end_date'] = Carbon::createFromFormat('d-m-Y', $data['lockin_end_date'])->format('Y-m-d');
            } catch (\Exception $e) {}
        }

        if (!empty($data['next_payout_date'])) {
            try {
                $data['next_payout_date'] = Carbon::createFromFormat('d-m-Y', $data['next_payout_date'])->format('Y-m-d');
            } catch (\Exception $e) {}
        }

        // APPLY UPDATE
        $investment->update($data);

        return redirect()->route('admin.investments.index')->with('success', 'Investment updated successfully.');
    }


    public function show(Investment $investment)
    {
        abort_if(Gate::denies('investment_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $investment->load('select_investor', 'select_plan', 'created_by', 'investmentMonthlyPayoutRecords', 'investmentWithdrawalRequests', 'investorInvestorTransactions');

        return view('admin.investments.show', compact('investment'));
    }

    public function destroy(Investment $investment)
    {
        abort_if(Gate::denies('investment_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $investment->delete();

        return back()->with('success','Investment deleted.');
    }

    public function massDestroy(MassDestroyInvestmentRequest $request)
    {
        $investments = Investment::find(request('ids'));

        foreach ($investments as $investment) {
            $investment->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}

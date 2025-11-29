<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Requests\MassDestroyInvestmentRequest;
use App\Http\Requests\StoreInvestmentRequest;
use App\Http\Requests\UpdateInvestmentRequest;
use App\Models\Investment;
use App\Models\InvestorTransaction;
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

        $user = Auth::user();
        $role = $user->roles->first()->title ?? null;

        if ($role === 'Admin') {
            $investments = Investment::with(['select_investor', 'select_plan', 'created_by'])
                ->orderBy('id', 'DESC')
                ->get();

            return view('admin.investments.index', compact('investments'));
        }

        $registration = Registration::where('investor_id', $user->id)->first();

        if (!$registration) {
            return view('admin.investments.index', [
                'investments' => [],
                'message' => 'आपने अभी तक कोई registration नहीं किया है।'
            ]);
        }

        $investments = Investment::with(['select_investor', 'select_plan', 'created_by'])
            ->where('select_investor_id', $registration->id)
            ->orderBy('id', 'DESC')
            ->get();

        if ($investments->count() === 0) {
            return view('admin.investments.index', [
                'investments' => [],
                'message' => 'आपने अभी तक कोई investment नहीं किया है।'
            ]);
        }

        return view('admin.investments.index', compact('investments'));
    }


    public function create()
    {
        abort_if(Gate::denies('investment_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $user = Auth::user();
        $userRole = $user->roles->first()->title ?? null;

        $plans = Plan::all();

        if ($userRole === 'Admin') {

            $select_investors = Registration::pluck('reg', 'id')->prepend(trans('global.pleaseSelect'), '');

            $registrations = Registration::select(
                'id','reg','referral_code','aadhaar_number','pan_number',
                'dob','gender','father_name','address_line_1','address_line_2',
                'pincode','city','state','country',
                'bank_account_holder_name','bank_account_number','ifsc_code',
                'bank_name','bank_branch','income_range','occupation',
                'risk_profile','investment_experience','kyc_status',
                'account_status','is_email_verified','is_phone_verified'
            )->get();

            $selected_investor = null;

        } else {

            $select_investors = collect();
            $registrations = collect();

            $selected_investor = Registration::where('investor_id', $user->id)
                ->select(
                    'id','reg','referral_code','aadhaar_number','pan_number',
                    'dob','gender','father_name','address_line_1','address_line_2',
                    'pincode','city','state','country',
                    'bank_account_holder_name','bank_account_number','ifsc_code',
                    'bank_name','bank_branch','income_range','occupation',
                    'risk_profile','investment_experience','kyc_status',
                    'account_status','is_email_verified','is_phone_verified'
                )->first();

            if ($selected_investor) {
                $registrations = collect([$selected_investor]);
            }
        }

        return view('admin.investments.create', compact(
            'select_investors', 'plans', 'registrations', 'selected_investor'
        ));
    }



    public function store(StoreInvestmentRequest $request)
{
    abort_if(Gate::denies('investment_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

    $data = $request->all();
    $data['status'] = 'pending';

    $investorId = $request->select_investor_id;
    if (!$investorId) {
        return back()->withErrors(['select_investor_id' => 'Investor is required'])->withInput();
    }

    $registration = Registration::find($investorId);
    if (!$registration) {
        return back()->withErrors(['select_investor_id' => 'Selected investor not found'])->withInput();
    }

    // DATE FORMAT FIX
    foreach (['start_date','lockin_end_date','next_payout_date'] as $field) {
        if (!empty($data[$field])) {
            try {
                $data[$field] = Carbon::createFromFormat('d-m-Y', $data[$field])->format('Y-m-d');
            } catch (\Exception $e) {}
        }
    }

    $user = Auth::user();
    $data['created_by_id'] = $user->id;

    // ---- CREATE INVESTMENT ----
    $investment = Investment::create($data);

    // ---- CREATE TRANSACTION ----
    InvestorTransaction::create([
        'investor_id'      => $request->select_investor_id,
        'investment_id'    => $investment->id,
        'plan_id'          => $investment->select_plan_id,
        'transaction_type' => 'investment',
        'amount'           => $request->principal_amount,
        'narration'        => 'Investment created',
        'status'           => 'success',
        'created_by_id'    => auth()->id(),
    ]);

    return redirect()->route('admin.investments.index')->with('success', 'Investment created successfully.');
}




    public function edit(Investment $investment)
    {
        abort_if(Gate::denies('investment_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $user = Auth::user();
        $userRole = $user->roles->first()->title ?? null;

        $plans = Plan::all();

        if ($userRole === 'Admin') {

            $select_investors = Registration::pluck('reg', 'id')->prepend(trans('global.pleaseSelect'), '');

            $registrations = Registration::select(
                'id','reg','referral_code','aadhaar_number','pan_number',
                'dob','gender','father_name','address_line_1','address_line_2',
                'pincode','city','state','country',
                'bank_account_holder_name','bank_account_number','ifsc_code',
                'bank_name','bank_branch','income_range','occupation',
                'risk_profile','investment_experience','kyc_status',
                'account_status','is_email_verified','is_phone_verified'
            )->get();

            $selected_investor = $investment->select_investor;

        } else {

            $select_investors = collect();
            $registrations = collect();

            $selected_investor = Registration::where('investor_id', $user->id)
                ->select(
                    'id','reg','referral_code','aadhaar_number','pan_number',
                    'dob','gender','father_name','address_line_1','address_line_2',
                    'pincode','city','state','country',
                    'bank_account_holder_name','bank_account_number','ifsc_code',
                    'bank_name','bank_branch','income_range','occupation',
                    'risk_profile','investment_experience','kyc_status',
                    'account_status','is_email_verified','is_phone_verified'
                )->first();

            if ($selected_investor) {
                $registrations = collect([$selected_investor]);
            }
        }

        $investment->load('select_investor', 'select_plan', 'created_by');

        return view('admin.investments.edit', compact(
            'investment', 'select_investors', 'registrations', 'selected_investor', 'plans'
        ));
    }



public function update(UpdateInvestmentRequest $request, Investment $investment)
{
   
    abort_if(Gate::denies('investment_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

    $data = $request->all();
    $data['status'] = 'pending';

    // ---- DATE FIX ----
    foreach (['start_date','lockin_end_date','next_payout_date'] as $field) {
        if (!empty($data[$field])) {
            try {
                $data[$field] = Carbon::createFromFormat('d-m-Y', $data[$field])->format('Y-m-d');
            } catch (\Exception $e) {}
        }
    }

    // ---- UPDATE INVESTMENT ----
    $investment->update($data);

    // ---- CHECK IF TRANSACTION EXISTS ----
    $transaction = InvestorTransaction::where('investment_id', $investment->id)->first();

    if ($transaction) {
        // UPDATE
        $transaction->update([
            'investor_id'      => $request->select_investor_id,
            'transaction_type' => 'investment',
            'amount'           => $request->principal_amount,
            'narration'        => 'Investment updated',
            'status'           => 'success',
        ]);
    } else {
        // CREATE
        InvestorTransaction::create([
            'investor_id'      => $request->select_investor_id,
            'investment_id'    => $investment->id,
            'plan_id'          => $investment->select_plan_id,
            'transaction_type' => 'investment',
            'amount'           => $request->principal_amount,
            'narration'        => 'Investment updated',
            'status'           => 'success',
            'created_by_id'    => auth()->id(),
        ]);
    }

    return redirect()->route('admin.investments.index')
        ->with('success', 'Investment updated successfully.');
}




    public function show(Investment $investment)
    {
        abort_if(Gate::denies('investment_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $investment->load(
            'select_investor',
            'select_plan',
            'created_by',
            'investmentMonthlyPayoutRecords',
            'investmentWithdrawalRequests',
            'investorInvestorTransactions'
        );

        return view('admin.investments.show', compact('investment'));
    }




    public function destroy(Investment $investment)
    {
        abort_if(Gate::denies('investment_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $investment->delete();

        return back()->with('success', 'Investment deleted.');
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


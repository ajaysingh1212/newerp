<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DailyInterest;
use App\Models\Investment;
use App\Models\Registration;
use App\Models\WithdrawalRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class InvestmentsDetailesController extends Controller
{

    // ===================== PDF EXPORT =====================
    public function downloadPdf($id)
    {
        $investment = Investment::with([
            'select_investor.media',
            'select_plan',
            'select_investor.user',
            'investmentMonthlyPayoutRecords',
            'investmentWithdrawalRequests.media',
        ])->findOrFail($id);

        $dailyInterest = DailyInterest::where('investment_id', $id)
            ->orderBy('interest_date', 'ASC')
            ->get();

        $withdrawals = $investment->investmentWithdrawalRequests;

        $totalEarnedInterest = $dailyInterest->sum('daily_interest_amount');

        $approved_interest   = $withdrawals->where('status','approved')->where('type','interest')->sum('amount');
        $approved_principal  = $withdrawals->where('status','approved')->where('type','principal')->sum('amount');
        $approved_totaltype  = $withdrawals->where('status','approved')->where('type','total')->sum('amount');

        $pending_withdraw    = $withdrawals->where('status','pending')->sum('amount');
        $rejected_withdraw   = $withdrawals->where('status','rejected')->sum('amount');

        $principal = $investment->principal_amount;

        $remainingInterestCap    = max(0, $totalEarnedInterest - $approved_interest);
        $interest_from_totaltype = min($approved_totaltype, $remainingInterestCap);
        $principal_from_totaltype= max(0, $approved_totaltype - $interest_from_totaltype);

        $final_interest  = max(0, $totalEarnedInterest - $approved_interest - $interest_from_totaltype);
        $final_principal = max(0, $principal - $approved_principal - $principal_from_totaltype);

        return view('admin.investmentsDetailes.pdf', compact(
            'investment',
            'dailyInterest',
            'approved_interest',
            'approved_principal',
            'approved_totaltype',
            'pending_withdraw',
            'rejected_withdraw',
            'final_interest',
            'final_principal',
            'totalEarnedInterest'
        ));
    }


    // ===================== INDEX — MULTIPLE PLAN SUPPORT =====================
    public function index()
    {
        $user = Auth::user();
        $role = $user->roles->first()->title ?? null;

        // ----------- ADMIN → All investments -----------
        if ($role === 'Admin') {

            $investments = Investment::with('select_investor')
                ->orderBy('id', 'DESC')
                ->get();

            return view('admin.investmentsDetailes.index', [
                'investments' => $investments,
                'isAdmin'     => true,
                'message'     => null,
            ]);
        }

        // ----------- USER → MULTIPLE PLAN SUPPORT -----------
        $registrationIds = Registration::where('investor_id', $user->id)
            ->pluck('id');  

        if ($registrationIds->isEmpty()) {
            return view('admin.investmentsDetailes.index', [
                'investments' => [],
                'isAdmin'     => false,
                'message'     => 'You have not completed your registration yet.',
            ]);
        }

        // Fetch all investments for this investor
        $investments = Investment::with('select_investor')
            ->whereIn('select_investor_id', $registrationIds)
            ->orderBy('id', 'DESC')
            ->get();

        if ($investments->isEmpty()) {
            return view('admin.investmentsDetailes.index', [
                'investments' => [],
                'isAdmin'     => false,
                'message'     => 'You have not made any investments yet.',
            ]);
        }

        return view('admin.investmentsDetailes.index', [
            'investments' => $investments,
            'isAdmin'     => false,
            'message'     => null,
        ]);
    }


    // ===================== Fetch Single Investment Details =====================
public function fetchDetails($id)
{
    $investment = Investment::with([
        'select_investor.media',
        'select_plan',
        'select_investor.user',
        'investmentMonthlyPayoutRecords',
        'investmentWithdrawalRequests.media',
    ])->findOrFail($id);

    // Daily interest rows
    $dailyRows = DailyInterest::where('investment_id', $id)
        ->orderBy('interest_date', 'ASC')
        ->get();

    $totalEarnedInterest = $dailyRows->sum('daily_interest_amount');
    $daysPassed = $dailyRows->count();

    $principal = floatval($investment->principal_amount ?? 0);
    $secure    = floatval($investment->secure_interest_percent ?? 0);
    $market    = floatval($investment->market_interest_percent ?? 0);

    $totalPercent = $secure + $market;

    // Daily Interest Formula
    $dailyInterest = ($principal * $totalPercent / 100) / 30;

    // Safe date
    try {
        $safeStartDate = Carbon::parse($investment->start_date)->format('d-m-Y');
    } catch (\Exception $e) {
        $safeStartDate = Carbon::now()->format('d-m-Y');
    }

    // All approved withdrawals
    $approved = WithdrawalRequest::where('investment_id', $id)
        ->where('status', 'approved')
        ->orderBy('approved_at', 'ASC')
        ->get();

    $approved_interest   = $approved->where('type','interest')->sum('amount');
    $approved_principal  = $approved->where('type','principal')->sum('amount');
    $approved_totaltype  = $approved->where('type','total')->sum('amount');

    // Calculate impact of "total" withdrawals
    $remainingInterestCap = max(0, $totalEarnedInterest - $approved_interest);
    $interest_from_totaltype = min($approved_totaltype, $remainingInterestCap);
    $principal_from_totaltype = max(0, $approved_totaltype - $interest_from_totaltype);

    // Final remaining balances
    $final_interest = max(0, $totalEarnedInterest - $approved_interest - $interest_from_totaltype);
    $final_principal = max(0, $principal - $approved_principal - $principal_from_totaltype);

    // ✅ MAIN FIX — ADD total_approved
    $total_approved = $approved_interest + $approved_principal + $approved_totaltype;

    return response()->json([
        'investment'          => $investment,
        'dailyInterest'       => round($dailyInterest, 2),
        'daysPassed'          => $daysPassed,
        'totalEarnedInterest' => round($totalEarnedInterest, 2),

        'approvedSummary'     => [
            'total_approved'            => round($total_approved, 2), // ⭐ FIXED
            'approved_interest'         => round($approved_interest, 2),
            'approved_principal'        => round($approved_principal, 2),
            'approved_totaltype'        => round($approved_totaltype, 2),
            'interest_from_totaltype'   => round($interest_from_totaltype, 2),
            'principal_from_totaltype'  => round($principal_from_totaltype, 2),
            'final_interest'            => round($final_interest, 2),
            'final_principal'           => round($final_principal, 2),
        ],

        'safeStartDate' => $safeStartDate,
    ]);
}



    // ===================== Withdrawal Details =====================
    public function withdrawalDetails($id)
    {
        $withdrawal = WithdrawalRequest::with([
            'media',
            'investment.select_investor',
            'investment.select_investor.user'
        ])->findOrFail($id);

        return response()->json([
            'withdrawal'   => $withdrawal,
            'registration' => $withdrawal->investment->select_investor,
            'user'         => $withdrawal->investment->select_investor->user,
        ]);
    }


    // ===================== Approve/Reject Withdrawal =====================
    public function approveWithdrawal(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'withdrawal_id' => 'required|exists:withdrawal_requests,id',
            'status'        => 'required|in:approved,rejected,pending',
            'approved_at'   => 'nullable|date',
            'notes'         => 'nullable|string|max:2000',
            'remarks'       => 'nullable|string|max:2000',
            'attachment'    => 'nullable|file|mimes:pdf,jpeg,png,jpg|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors'  => $validator->errors()
            ], 422);
        }

        $withdrawal = WithdrawalRequest::findOrFail($request->withdrawal_id);
        $withdrawal->status = $request->status;

        if ($request->approved_at) {
            $withdrawal->approved_at = Carbon::parse($request->approved_at);
        } elseif ($request->status === 'approved' && !$withdrawal->approved_at) {
            $withdrawal->approved_at = Carbon::now();
        }

        $withdrawal->notes   = $request->notes;
        $withdrawal->remarks = $request->remarks;

        if (\Schema::hasColumn('withdrawal_requests', 'approved_by')) {
            $withdrawal->approved_by = Auth::id();
        }

        if (\Schema::hasColumn('withdrawal_requests', 'approved_by_id')) {
            $withdrawal->approved_by_id = Auth::id();
        }

        $withdrawal->save();

        if ($request->hasFile('attachment')) {
            try {
                $withdrawal->clearMediaCollection('withdrawal_attachments');
                $withdrawal->addMediaFromRequest('attachment')
                    ->toMediaCollection('withdrawal_attachments');
            } catch (\Exception $e) {
                Log::error('Attachment upload failed: '.$e->getMessage());
            }
        }

        return response()->json([
            'message'    => 'Withdrawal updated successfully',
            'withdrawal' => $withdrawal
        ]);
    }


    // ===================== Daily Interest =====================
    public function dailyInterest($id)
    {
        return DailyInterest::where('investment_id', $id)
            ->orderBy('interest_date', 'ASC')
            ->get(['interest_date', 'daily_interest_amount']);
    }


    // ===================== Pending Report =====================
    public function pendingReport()
    {
        $registrations = Registration::where('account_status', 'Active')->get();

        $investments = Investment::where('status', 'active')
            ->with(['select_investor', 'select_plan'])
            ->get();

        $withdrawals = WithdrawalRequest::where('status', 'pending')
            ->with(['select_investor', 'investment'])
            ->get();

        return view('admin.investmentsDetailes.pending_report', compact('registrations', 'investments', 'withdrawals'));
    }
}

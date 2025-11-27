<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DailyInterest;
use App\Models\Investment;
use App\Models\WithdrawalRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use PDF;

class InvestmentsDetailesController extends Controller
{

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

    $approved_interest = $withdrawals->where('status','approved')->where('type','interest')->sum('amount');
    $approved_principal = $withdrawals->where('status','approved')->where('type','principal')->sum('amount');
    $approved_totaltype = $withdrawals->where('status','approved')->where('type','total')->sum('amount');

    $pending_withdraw  = $withdrawals->where('status','pending')->sum('amount');
    $rejected_withdraw = $withdrawals->where('status','rejected')->sum('amount');

    $principal = $investment->principal_amount;

    $remainingInterestCap = max(0, $totalEarnedInterest - $approved_interest);
    $interest_from_totaltype  = min($approved_totaltype, $remainingInterestCap);
    $principal_from_totaltype = max(0, $approved_totaltype - $interest_from_totaltype);

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



public function index()
{
    $user = Auth::user();
    $role = $user->roles->first()->title ?? null;

    // 🔥 Admin → sab dekh sakta hai
    if ($role === 'Admin') {
        $investments = Investment::with('select_investor')
            ->orderBy('id', 'DESC')
            ->get();
    } 
    else {
        // 🔥 Non-Admin → sirf apni registrations → apne investments
        $registration = \App\Models\Registration::where('investor_id', $user->id)->first();

        if (!$registration) {
            return view('admin.investmentsDetailes.index', [
                'investments' => [],
                'message' => 'आपने अब तक कोई Registration या Investment नहीं किया है।'
            ]);
        }

        $investments = Investment::with('select_investor')
            ->where('select_investor_id', $registration->id)
            ->orderBy('id', 'DESC')
            ->get();

        if ($investments->count() == 0) {
            return view('admin.investmentsDetailes.index', [
                'investments' => [],
                'message' => 'आपने अब तक कोई Investment नहीं किया है।'
            ]);
        }
    }

    return view('admin.investmentsDetailes.index', compact('investments'));
}


    public function fetchDetails($id)
    {
        $investment = Investment::with([
            'select_investor.media',
            'select_plan',
            'select_investor.user',
            'investmentMonthlyPayoutRecords',
            'investmentWithdrawalRequests.media',
        ])->findOrFail($id);

        $dailyRows = DailyInterest::where('investment_id', $id)
            ->orderBy('interest_date', 'ASC')
            ->get();

        $totalEarnedInterest = $dailyRows->sum('daily_interest_amount');
        $daysPassed = $dailyRows->count();

        $principal = floatval($investment->principal_amount ?? 0);
        $secure = floatval($investment->secure_interest_percent ?? 0);
        $market = floatval($investment->market_interest_percent ?? 0);
        $totalPercent = $secure + $market;
        $dailyInterest = ($principal * $totalPercent / 100) / 30;

        $raw = $investment->start_date;
        $start = Carbon::now();

        if ($raw) {
            try { $start = Carbon::parse($raw); }
            catch (\Exception $e) {
                try { $start = Carbon::createFromFormat('d-m-Y', $raw); }
                catch (\Exception $e2) {
                    try { $start = Carbon::createFromFormat('Y-m-d', $raw); }
                    catch (\Exception $e3) { $start = Carbon::now(); }
                }
            }
        }

        $safeStartDate = $start->format('d-m-Y');

        $approved = WithdrawalRequest::where('investment_id', $id)
            ->where('status', 'approved')
            ->orderBy('approved_at', 'ASC')
            ->get();

        $approved_interest = $approved->where('type', 'interest')->sum('amount');
        $approved_principal = $approved->where('type', 'principal')->sum('amount');
        $approved_totaltype = $approved->where('type', 'total')->sum('amount');

        $remainingInterestCap = max(0, $totalEarnedInterest - $approved_interest);
        $interest_from_totaltype = min($approved_totaltype, $remainingInterestCap);
        $principal_from_totaltype = max(0, $approved_totaltype - $interest_from_totaltype);

        $final_interest = max(0, $totalEarnedInterest - $approved_interest - $interest_from_totaltype);
        $final_principal = max(0, $principal - $approved_principal - $principal_from_totaltype);

        $approvedSummary = [
            'total_approved'           => round($approved_interest + $approved_principal + $approved_totaltype, 2),
            'approved_interest'        => round($approved_interest, 2),
            'approved_principal'       => round($approved_principal, 2),
            'approved_totaltype'       => round($approved_totaltype, 2),
            'interest_from_totaltype'  => round($interest_from_totaltype, 2),
            'principal_from_totaltype' => round($principal_from_totaltype, 2),
            'final_interest'           => round($final_interest, 2),
            'final_principal'          => round($final_principal, 2),

            /* 🔥 NEW: MEDIA ATTACHMENT RETURNED */
            'approved_withdrawals'     => $approved->map(function ($w) {
                return [
                    'id'          => $w->id,
                    'amount'      => floatval($w->amount),
                    'type'        => $w->type,
                    'requested_at'=> $w->requested_at ? Carbon::parse($w->requested_at)->format('d-m-Y H:i') : null,
                    'approved_at' => $w->approved_at ? Carbon::parse($w->approved_at)->format('d-m-Y H:i') : null,
                    'notes'       => $w->notes,
                    'remarks'     => $w->remarks,

                    // 🔥 Add all media files
                    'media'       => $w->getMedia('withdrawal_attachments')->map(function($m){
                        return [
                            'file_name' => $m->file_name,
                            'url'       => $m->getUrl(),
                        ];
                    })
                ];
            })
        ];

        return response()->json([
            'investment'         => $investment,
            'dailyInterest'      => round($dailyInterest, 2),
            'daysPassed'         => $daysPassed,
            'totalEarnedInterest'=> round($totalEarnedInterest, 2),
            'approvedSummary'    => $approvedSummary,
            'safeStartDate'      => $safeStartDate,
        ]);
    }

    public function withdrawalDetails($id)
    {
        $withdrawal = WithdrawalRequest::with([
            'media',
            'investment.select_investor',
            'investment.select_investor.user'
        ])->findOrFail($id);

        $registration = $withdrawal->investment->select_investor ?? null;
        $investor_user = $registration ? $registration->user : null;

        return response()->json([
            'withdrawal'   => $withdrawal,
            'registration' => $registration,
            'user'         => $investor_user,
        ]);
    }

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
        } else {
            if ($request->status === 'approved' && !$withdrawal->approved_at) {
                $withdrawal->approved_at = Carbon::now();
            }
        }

        $withdrawal->notes = $request->notes;
        $withdrawal->remarks = $request->remarks;

        if (Auth::check()) {
            if (\Schema::hasColumn('withdrawal_requests', 'approved_by')) {
                $withdrawal->approved_by = Auth::id();
            }
            if (\Schema::hasColumn('withdrawal_requests', 'approved_by_id')) {
                $withdrawal->approved_by_id = Auth::id();
            }
        }

        $withdrawal->save();

        if ($request->hasFile('attachment')) {
            try {
                $withdrawal->clearMediaCollection('withdrawal_attachments');
                $withdrawal->addMediaFromRequest('attachment')
                    ->toMediaCollection('withdrawal_attachments');
            } catch (\Exception $e) {
                Log::error('Attachment upload failed: ' . $e->getMessage());
            }
        }

        return response()->json([
            'message'    => 'Withdrawal updated successfully',
            'withdrawal' => $withdrawal
        ]);
    }

    public function dailyInterest($id)
    {
        return DailyInterest::where('investment_id', $id)
            ->orderBy('interest_date', 'ASC')
            ->get(['interest_date', 'daily_interest_amount']);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyInvestmentsDetaileRequest;
use App\Http\Requests\StoreInvestmentsDetaileRequest;
use App\Http\Requests\UpdateInvestmentsDetaileRequest;
use App\Models\DailyInterest;
use App\Models\Investment;
use Gate;
use PDF;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InvestmentsDetailesController extends Controller
{

public function downloadPdf($id)
{
    $investment = Investment::with([
        'select_investor.media',
        'select_plan',
        'investmentMonthlyPayoutRecords',
        'investmentWithdrawalRequests.media'
    ])->findOrFail($id);

    $dailyInterest = DailyInterest::where('investment_id', $id)
        ->orderBy('interest_date', 'ASC')
        ->get();

    $pdf = PDF::loadView('admin.investments.pdf', compact('investment', 'dailyInterest'))
        ->setPaper('a4', 'portrait');

    return $pdf->download("Investment-Report-{$investment->id}.pdf");
}

    public function index()
{
    $investments = Investment::with('select_investor')->orderBy('id','DESC')->get();

    return view('admin.investmentsDetailes.index', compact('investments'));
}
public function fetchDetails($id)
{
    $investment = Investment::with([
        'select_investor.media',
        'select_plan',
        'investmentMonthlyPayoutRecords',
        'investmentWithdrawalRequests.media',
    ])->findOrFail($id);

    /* DAILY INTEREST CALCULATION */
    $principal = $investment->principal_amount;
    $secure = $investment->secure_interest_percent;
    $market = $investment->market_interest_percent;
    $totalPercent = $secure + $market;

    $dailyInterest = ($principal * $totalPercent / 100) / 30;   // 30 days month logic

    $start = \Carbon\Carbon::createFromFormat('d-m-Y', $investment->start_date);
    $today = \Carbon\Carbon::now();
    $daysDiff = $start->diffInDays($today);

    $totalEarnedInterest = $dailyInterest * $daysDiff;

    return response()->json([
        'investment' => $investment,
        'dailyInterest' => $dailyInterest,
        'daysPassed' => $daysDiff,
        'totalEarnedInterest' => $totalEarnedInterest,
    ]);
}

    public function create()
    {
        abort_if(Gate::denies('investments_detaile_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.investmentsDetailes.create');
    }

    public function store(StoreInvestmentsDetaileRequest $request)
    {
        $investmentsDetaile = InvestmentsDetaile::create($request->all());

        return redirect()->route('admin.investments-detailes.index');
    }

    public function edit(InvestmentsDetaile $investmentsDetaile)
    {
        abort_if(Gate::denies('investments_detaile_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.investmentsDetailes.edit', compact('investmentsDetaile'));
    }

    public function update(UpdateInvestmentsDetaileRequest $request, InvestmentsDetaile $investmentsDetaile)
    {
        $investmentsDetaile->update($request->all());

        return redirect()->route('admin.investments-detailes.index');
    }

    public function show(InvestmentsDetaile $investmentsDetaile)
    {
        abort_if(Gate::denies('investments_detaile_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.investmentsDetailes.show', compact('investmentsDetaile'));
    }

    public function destroy(InvestmentsDetaile $investmentsDetaile)
    {
        abort_if(Gate::denies('investments_detaile_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $investmentsDetaile->delete();

        return back();
    }

    public function massDestroy(MassDestroyInvestmentsDetaileRequest $request)
    {
        $investmentsDetailes = InvestmentsDetaile::find(request('ids'));

        foreach ($investmentsDetailes as $investmentsDetaile) {
            $investmentsDetaile->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
    public function dailyInterest($id)
{
    $data = DailyInterest::where('investment_id', $id)
        ->orderBy('interest_date', 'ASC')
        ->get(['interest_date', 'daily_interest_amount']);

    return response()->json($data);
}


}

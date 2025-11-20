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
use Gate;
use Illuminate\Http\Request;
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

        $select_investors = Registration::pluck('reg', 'id')->prepend(trans('global.pleaseSelect'), '');

        $select_plans = Plan::pluck('plan_name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.investments.create', compact('select_investors', 'select_plans'));
    }

    public function store(StoreInvestmentRequest $request)
    {
        $investment = Investment::create($request->all());

        return redirect()->route('admin.investments.index');
    }

    public function edit(Investment $investment)
    {
        abort_if(Gate::denies('investment_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $select_investors = Registration::pluck('reg', 'id')->prepend(trans('global.pleaseSelect'), '');

        $select_plans = Plan::pluck('plan_name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $investment->load('select_investor', 'select_plan', 'created_by');

        return view('admin.investments.edit', compact('investment', 'select_investors', 'select_plans'));
    }

    public function update(UpdateInvestmentRequest $request, Investment $investment)
    {
        $investment->update($request->all());

        return redirect()->route('admin.investments.index');
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

        return back();
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

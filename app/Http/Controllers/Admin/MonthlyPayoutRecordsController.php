<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Requests\MassDestroyMonthlyPayoutRecordRequest;
use App\Http\Requests\StoreMonthlyPayoutRecordRequest;
use App\Http\Requests\UpdateMonthlyPayoutRecordRequest;
use App\Models\Investment;
use App\Models\MonthlyPayoutRecord;
use App\Models\Registration;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MonthlyPayoutRecordsController extends Controller
{
    use CsvImportTrait;

    public function index()
    {
        abort_if(Gate::denies('monthly_payout_record_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $monthlyPayoutRecords = MonthlyPayoutRecord::with(['investment', 'investor', 'created_by'])->get();

        return view('admin.monthlyPayoutRecords.index', compact('monthlyPayoutRecords'));
    }

    public function create()
    {
        abort_if(Gate::denies('monthly_payout_record_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $investments = Investment::pluck('principal_amount', 'id')->prepend(trans('global.pleaseSelect'), '');

        $investors = Registration::pluck('reg', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.monthlyPayoutRecords.create', compact('investments', 'investors'));
    }

    public function store(StoreMonthlyPayoutRecordRequest $request)
    {
        $monthlyPayoutRecord = MonthlyPayoutRecord::create($request->all());

        return redirect()->route('admin.monthly-payout-records.index');
    }

    public function edit(MonthlyPayoutRecord $monthlyPayoutRecord)
    {
        abort_if(Gate::denies('monthly_payout_record_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $investments = Investment::pluck('principal_amount', 'id')->prepend(trans('global.pleaseSelect'), '');

        $investors = Registration::pluck('reg', 'id')->prepend(trans('global.pleaseSelect'), '');

        $monthlyPayoutRecord->load('investment', 'investor', 'created_by');

        return view('admin.monthlyPayoutRecords.edit', compact('investments', 'investors', 'monthlyPayoutRecord'));
    }

    public function update(UpdateMonthlyPayoutRecordRequest $request, MonthlyPayoutRecord $monthlyPayoutRecord)
    {
        $monthlyPayoutRecord->update($request->all());

        return redirect()->route('admin.monthly-payout-records.index');
    }

    public function show(MonthlyPayoutRecord $monthlyPayoutRecord)
    {
        abort_if(Gate::denies('monthly_payout_record_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $monthlyPayoutRecord->load('investment', 'investor', 'created_by');

        return view('admin.monthlyPayoutRecords.show', compact('monthlyPayoutRecord'));
    }

    public function destroy(MonthlyPayoutRecord $monthlyPayoutRecord)
    {
        abort_if(Gate::denies('monthly_payout_record_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $monthlyPayoutRecord->delete();

        return back();
    }

    public function massDestroy(MassDestroyMonthlyPayoutRecordRequest $request)
    {
        $monthlyPayoutRecords = MonthlyPayoutRecord::find(request('ids'));

        foreach ($monthlyPayoutRecords as $monthlyPayoutRecord) {
            $monthlyPayoutRecord->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}

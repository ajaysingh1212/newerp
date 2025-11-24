<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyInvestmentsDetaileRequest;
use App\Http\Requests\StoreInvestmentsDetaileRequest;
use App\Http\Requests\UpdateInvestmentsDetaileRequest;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InvestmentsDetailesController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('investments_detaile_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.investmentsDetailes.index');
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
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Requests\MassDestroyRechargePlanRequest;
use App\Http\Requests\StoreRechargePlanRequest;
use App\Http\Requests\UpdateRechargePlanRequest;
use App\Models\RechargePlan;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class RechargePlanController extends Controller
{
    use CsvImportTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('recharge_plan_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = RechargePlan::with(['team'])->select(sprintf('%s.*', (new RechargePlan)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'recharge_plan_show';
                $editGate      = 'recharge_plan_edit';
                $deleteGate    = 'recharge_plan_delete';
                $crudRoutePart = 'recharge-plans';

                return view('partials.datatablesActions', compact(
                    'viewGate',
                    'editGate',
                    'deleteGate',
                    'crudRoutePart',
                    'row'
                ));
            });

            $table->editColumn('id', function ($row) {
                return $row->id ? $row->id : '';
            });
            $table->editColumn('type', function ($row) {
                return $row->type ? $row->type : '';
            });
            $table->editColumn('plan_name', function ($row) {
                return $row->plan_name ? $row->plan_name : '';
            });
            $table->editColumn('amc_duration', function ($row) {
                return $row->amc_duration ? $row->amc_duration : '';
            });
            $table->editColumn('warranty_duration', function ($row) {
                return $row->warranty_duration ? $row->warranty_duration : '';
            });
            $table->editColumn('subscription_duration', function ($row) {
                return $row->subscription_duration ? $row->subscription_duration : '';
            });
            $table->editColumn('discription', function ($row) {
                return $row->discription ? $row->discription : '';
            });
            $table->editColumn('price', function ($row) {
                return $row->price ? $row->price : '';
            });

            $table->rawColumns(['actions', 'placeholder']);

            return $table->make(true);
        }

        return view('admin.rechargePlans.index');
    }

    public function create()
    {
        abort_if(Gate::denies('recharge_plan_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.rechargePlans.create');
    }

    public function store(StoreRechargePlanRequest $request)
    {
        $rechargePlan = RechargePlan::create($request->all());

        return redirect()->route('admin.recharge-plans.index');
    }

    public function edit(RechargePlan $rechargePlan)
    {
        abort_if(Gate::denies('recharge_plan_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $rechargePlan->load('team');

        return view('admin.rechargePlans.edit', compact('rechargePlan'));
    }

    public function update(UpdateRechargePlanRequest $request, RechargePlan $rechargePlan)
    {
        $rechargePlan->update($request->all());

        return redirect()->route('admin.recharge-plans.index');
    }

    public function show(RechargePlan $rechargePlan)
    {
        abort_if(Gate::denies('recharge_plan_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $rechargePlan->load('team', 'selectRechargeRechargeRequests');

        return view('admin.rechargePlans.show', compact('rechargePlan'));
    }

    public function destroy(RechargePlan $rechargePlan)
    {
        abort_if(Gate::denies('recharge_plan_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $rechargePlan->delete();

        return back();
    }

    public function massDestroy(MassDestroyRechargePlanRequest $request)
    {
        $rechargePlans = RechargePlan::find(request('ids'));

        foreach ($rechargePlans as $rechargePlan) {
            $rechargePlan->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
public function getPlanDetails($id)
{
    $plan = RechargePlan::find($id);

    if (!$plan) {
        return response()->json(['error' => 'Recharge Plan not found.'], 404);
    }

    return response()->json([
        'plan_name' => $plan->plan_name,
        'type' => $plan->type,
        'amc_duration' => $plan->amc_duration,
        'warranty_duration' => $plan->warranty_duration,
        'subscription_duration' => $plan->subscription_duration,
        'description' => $plan->discription,
        'price' => $plan->price,
    ]);
}


}
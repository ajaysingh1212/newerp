<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRechargePlanRequest;
use App\Http\Requests\UpdateRechargePlanRequest;
use App\Http\Resources\Admin\RechargePlanResource;
use App\Models\RechargePlan;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RechargePlanApiController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('recharge_plan_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new RechargePlanResource(RechargePlan::with(['team'])->get());
    }

    public function store(StoreRechargePlanRequest $request)
    {
        $rechargePlan = RechargePlan::create($request->all());

        return (new RechargePlanResource($rechargePlan))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(RechargePlan $rechargePlan)
    {
        abort_if(Gate::denies('recharge_plan_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new RechargePlanResource($rechargePlan->load(['team']));
    }

    public function update(UpdateRechargePlanRequest $request, RechargePlan $rechargePlan)
    {
        $rechargePlan->update($request->all());

        return (new RechargePlanResource($rechargePlan))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(RechargePlan $rechargePlan)
    {
        abort_if(Gate::denies('recharge_plan_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $rechargePlan->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
    
    
    // Get All Recharge Plans for Public API (No Auth Required)
    public function getAllPlans()
    {
        try {
            $plans = RechargePlan::all();
    
            return response()->json([
                'status' => true,
                'message' => 'All recharge plans fetched successfully.',
                'data' => RechargePlanResource::collection($plans)
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong.',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}

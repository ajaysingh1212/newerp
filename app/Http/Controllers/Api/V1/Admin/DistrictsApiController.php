<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDistrictRequest;
use App\Http\Requests\UpdateDistrictRequest;
use App\Http\Resources\Admin\DistrictResource;
use App\Models\District;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DistrictsApiController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('district_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new DistrictResource(District::with(['select_state', 'team'])->get());
    }

    public function store(StoreDistrictRequest $request)
    {
        $district = District::create($request->all());

        return (new DistrictResource($district))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(District $district)
    {
        abort_if(Gate::denies('district_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new DistrictResource($district->load(['select_state', 'team']));
    }

    public function update(UpdateDistrictRequest $request, District $district)
    {
        $district->update($request->all());

        return (new DistrictResource($district))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(District $district)
    {
        abort_if(Gate::denies('district_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $district->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function getAllDistricts(Request $request)
{
    // Agar frontend se state_id aaya ho to filter lagao
    $query = District::select('id', 'name', 'state_id')
        ->with('select_state:id,state_name');

    if ($request->has('state_id')) {
        $query->where('state_id', $request->state_id);
    }

    $districts = $query->orderBy('name', 'asc')->get();

    return response()->json([
        'status' => true,
        'message' => 'Districts fetched successfully',
        'data' => $districts
    ], 200);
}


}

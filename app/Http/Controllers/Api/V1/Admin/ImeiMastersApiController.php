<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreImeiMasterRequest;
use App\Http\Requests\UpdateImeiMasterRequest;
use App\Http\Resources\Admin\ImeiMasterResource;
use App\Models\ImeiMaster;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ImeiMastersApiController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('imei_master_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new ImeiMasterResource(ImeiMaster::with(['imei_model', 'team'])->get());
    }

    public function store(StoreImeiMasterRequest $request)
    {
        $imeiMaster = ImeiMaster::create($request->all());

        return (new ImeiMasterResource($imeiMaster))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(ImeiMaster $imeiMaster)
    {
        abort_if(Gate::denies('imei_master_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new ImeiMasterResource($imeiMaster->load(['imei_model', 'team']));
    }

    public function update(UpdateImeiMasterRequest $request, ImeiMaster $imeiMaster)
    {
        $imeiMaster->update($request->all());

        return (new ImeiMasterResource($imeiMaster))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(ImeiMaster $imeiMaster)
    {
        abort_if(Gate::denies('imei_master_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $imeiMaster->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}

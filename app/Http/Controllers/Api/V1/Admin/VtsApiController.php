<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVtRequest;
use App\Http\Requests\UpdateVtRequest;
use App\Http\Resources\Admin\VtResource;
use App\Models\Vt;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VtsApiController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('vt_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new VtResource(Vt::with(['team'])->get());
    }

    public function store(StoreVtRequest $request)
    {
        $vt = Vt::create($request->all());

        return (new VtResource($vt))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Vt $vt)
    {
        abort_if(Gate::denies('vt_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new VtResource($vt->load(['team']));
    }

    public function update(UpdateVtRequest $request, Vt $vt)
    {
        $vt->update($request->all());

        return (new VtResource($vt))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(Vt $vt)
    {
        abort_if(Gate::denies('vt_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $vt->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}

<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreImeiModelRequest;
use App\Http\Requests\UpdateImeiModelRequest;
use App\Http\Resources\Admin\ImeiModelResource;
use App\Models\ImeiModel;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ImeiModelsApiController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('imei_model_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new ImeiModelResource(ImeiModel::with(['team'])->get());
    }

    public function store(StoreImeiModelRequest $request)
    {
        $imeiModel = ImeiModel::create($request->all());

        return (new ImeiModelResource($imeiModel))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(ImeiModel $imeiModel)
    {
        abort_if(Gate::denies('imei_model_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new ImeiModelResource($imeiModel->load(['team']));
    }

    public function update(UpdateImeiModelRequest $request, ImeiModel $imeiModel)
    {
        $imeiModel->update($request->all());

        return (new ImeiModelResource($imeiModel))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(ImeiModel $imeiModel)
    {
        abort_if(Gate::denies('imei_model_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $imeiModel->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}

<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAttachVeichleRequest;
use App\Http\Requests\UpdateAttachVeichleRequest;
use App\Http\Resources\Admin\AttachVeichleResource;
use App\Models\AttachVeichle;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AttachVeichleApiController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('attach_veichle_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new AttachVeichleResource(AttachVeichle::with(['select_user', 'vehicles', 'team'])->get());
    }

    public function store(StoreAttachVeichleRequest $request)
    {
        $attachVeichle = AttachVeichle::create($request->all());
        $attachVeichle->vehicles()->sync($request->input('vehicles', []));

        return (new AttachVeichleResource($attachVeichle))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(AttachVeichle $attachVeichle)
    {
        abort_if(Gate::denies('attach_veichle_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new AttachVeichleResource($attachVeichle->load(['select_user', 'vehicles', 'team']));
    }

    public function update(UpdateAttachVeichleRequest $request, AttachVeichle $attachVeichle)
    {
        $attachVeichle->update($request->all());
        $attachVeichle->vehicles()->sync($request->input('vehicles', []));

        return (new AttachVeichleResource($attachVeichle))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(AttachVeichle $attachVeichle)
    {
        abort_if(Gate::denies('attach_veichle_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $attachVeichle->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}

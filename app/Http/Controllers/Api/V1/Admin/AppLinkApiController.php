<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAppLinkRequest;
use App\Http\Requests\UpdateAppLinkRequest;
use App\Http\Resources\Admin\AppLinkResource;
use App\Models\AppLink;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AppLinkApiController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('app_link_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new AppLinkResource(AppLink::with(['team'])->get());
    }

    public function store(StoreAppLinkRequest $request)
    {
        $appLink = AppLink::create($request->all());

        return (new AppLinkResource($appLink))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(AppLink $appLink)
    {
        abort_if(Gate::denies('app_link_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new AppLinkResource($appLink->load(['team']));
    }

    public function update(UpdateAppLinkRequest $request, AppLink $appLink)
    {
        $appLink->update($request->all());

        return (new AppLinkResource($appLink))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(AppLink $appLink)
    {
        abort_if(Gate::denies('app_link_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $appLink->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}

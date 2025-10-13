<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCheckPartyStockRequest;
use App\Http\Requests\UpdateCheckPartyStockRequest;
use App\Http\Resources\Admin\CheckPartyStockResource;
use App\Models\CheckPartyStock;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPartyStockApiController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('check_party_stock_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new CheckPartyStockResource(CheckPartyStock::with(['select_parties', 'team'])->get());
    }

    public function store(StoreCheckPartyStockRequest $request)
    {
        $checkPartyStock = CheckPartyStock::create($request->all());
        $checkPartyStock->select_parties()->sync($request->input('select_parties', []));

        return (new CheckPartyStockResource($checkPartyStock))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(CheckPartyStock $checkPartyStock)
    {
        abort_if(Gate::denies('check_party_stock_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new CheckPartyStockResource($checkPartyStock->load(['select_parties', 'team']));
    }

    public function update(UpdateCheckPartyStockRequest $request, CheckPartyStock $checkPartyStock)
    {
        $checkPartyStock->update($request->all());
        $checkPartyStock->select_parties()->sync($request->input('select_parties', []));

        return (new CheckPartyStockResource($checkPartyStock))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(CheckPartyStock $checkPartyStock)
    {
        abort_if(Gate::denies('check_party_stock_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $checkPartyStock->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}

<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStockTransferRequest;
use App\Http\Requests\UpdateStockTransferRequest;
use App\Http\Resources\Admin\StockTransferResource;
use App\Models\StockTransfer;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class StockTransferApiController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('stock_transfer_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new StockTransferResource(StockTransfer::with(['select_user', 'reseller', 'select_products', 'team'])->get());
    }

    public function store(StoreStockTransferRequest $request)
    {
        $stockTransfer = StockTransfer::create($request->all());
        $stockTransfer->select_products()->sync($request->input('select_products', []));

        return (new StockTransferResource($stockTransfer))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(StockTransfer $stockTransfer)
    {
        abort_if(Gate::denies('stock_transfer_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new StockTransferResource($stockTransfer->load(['select_user', 'reseller', 'select_products', 'team']));
    }

    public function update(UpdateStockTransferRequest $request, StockTransfer $stockTransfer)
    {
        $stockTransfer->update($request->all());
        $stockTransfer->select_products()->sync($request->input('select_products', []));

        return (new StockTransferResource($stockTransfer))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(StockTransfer $stockTransfer)
    {
        abort_if(Gate::denies('stock_transfer_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $stockTransfer->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}

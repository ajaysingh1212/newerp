<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductMasterRequest;
use App\Http\Requests\UpdateProductMasterRequest;
use App\Http\Resources\Admin\ProductMasterResource;
use App\Models\ProductMaster;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductMastersApiController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('product_master_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new ProductMasterResource(ProductMaster::with(['product_model', 'imei', 'vts', 'team'])->get());
    }

    public function store(StoreProductMasterRequest $request)
    {
        $productMaster = ProductMaster::create($request->all());

        return (new ProductMasterResource($productMaster))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(ProductMaster $productMaster)
    {
        abort_if(Gate::denies('product_master_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new ProductMasterResource($productMaster->load(['product_model', 'imei', 'vts', 'team']));
    }

    public function update(UpdateProductMasterRequest $request, ProductMaster $productMaster)
    {
        $productMaster->update($request->all());

        return (new ProductMasterResource($productMaster))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(ProductMaster $productMaster)
    {
        abort_if(Gate::denies('product_master_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $productMaster->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}

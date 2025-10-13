<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductModelRequest;
use App\Http\Requests\UpdateProductModelRequest;
use App\Http\Resources\Admin\ProductModelResource;
use App\Models\ProductModel;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductModelApiController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('product_model_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new ProductModelResource(ProductModel::with(['team'])->get());
    }

    public function store(StoreProductModelRequest $request)
    {
        $productModel = ProductModel::create($request->all());

        return (new ProductModelResource($productModel))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(ProductModel $productModel)
    {
        abort_if(Gate::denies('product_model_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new ProductModelResource($productModel->load(['team']));
    }

    public function update(UpdateProductModelRequest $request, ProductModel $productModel)
    {
        $productModel->update($request->all());

        return (new ProductModelResource($productModel))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(ProductModel $productModel)
    {
        abort_if(Gate::denies('product_model_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $productModel->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Requests\MassDestroyProductModelRequest;
use App\Http\Requests\StoreProductModelRequest;
use App\Http\Requests\UpdateProductModelRequest;
use App\Models\ProductModel;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class ProductModelController extends Controller
{
    use CsvImportTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('product_model_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = ProductModel::with(['team'])->select(sprintf('%s.*', (new ProductModel)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'product_model_show';
                $editGate      = 'product_model_edit';
                $deleteGate    = 'product_model_delete';
                $crudRoutePart = 'product-models';

                return view('partials.datatablesActions', compact(
                    'viewGate',
                    'editGate',
                    'deleteGate',
                    'crudRoutePart',
                    'row'
                ));
            });

            $table->editColumn('id', function ($row) {
                return $row->id ? $row->id : '';
            });
            $table->editColumn('product_model', function ($row) {
                return $row->product_model ? $row->product_model : '';
            });
            $table->editColumn('warranty', function ($row) {
                return $row->warranty ? $row->warranty : '';
            });
            $table->editColumn('subscription', function ($row) {
                return $row->subscription ? $row->subscription : '';
            });
            $table->editColumn('amc', function ($row) {
                return $row->amc ? $row->amc : '';
            });
            $table->editColumn('mrp', function ($row) {
                return $row->mrp ? $row->mrp : '';
            });
            $table->editColumn('cnf_price', function ($row) {
                return $row->cnf_price ? $row->cnf_price : '';
            });
            $table->editColumn('distributor_price', function ($row) {
                return $row->distributor_price ? $row->distributor_price : '';
            });
            $table->editColumn('dealer_price', function ($row) {
                return $row->dealer_price ? $row->dealer_price : '';
            });
            $table->editColumn('customer_price', function ($row) {
                return $row->customer_price ? $row->customer_price : '';
            });
            $table->editColumn('status', function ($row) {
                return $row->status ? ProductModel::STATUS_SELECT[$row->status] : '';
            });

            $table->rawColumns(['actions', 'placeholder']);

            return $table->make(true);
        }

        return view('admin.productModels.index');
    }

    public function create()
    {
        abort_if(Gate::denies('product_model_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.productModels.create');
    }

    public function store(StoreProductModelRequest $request)
    {
        $productModel = ProductModel::create($request->all());

        return redirect()->route('admin.product-models.index');
    }

    public function edit(ProductModel $productModel)
    {
        abort_if(Gate::denies('product_model_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $productModel->load('team');

        return view('admin.productModels.edit', compact('productModel'));
    }

    public function update(UpdateProductModelRequest $request, ProductModel $productModel)
    {
        $productModel->update($request->all());

        return redirect()->route('admin.product-models.index');
    }

    public function show(ProductModel $productModel)
    {
        abort_if(Gate::denies('product_model_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $productModel->load('team', 'productModelProductMasters');

        return view('admin.productModels.show', compact('productModel'));
    }

    public function destroy(ProductModel $productModel)
    {
        abort_if(Gate::denies('product_model_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $productModel->delete();

        return back();
    }

    public function massDestroy(MassDestroyProductModelRequest $request)
    {
        $productModels = ProductModel::find(request('ids'));

        foreach ($productModels as $productModel) {
            $productModel->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}

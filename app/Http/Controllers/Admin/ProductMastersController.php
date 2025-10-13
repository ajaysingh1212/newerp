<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Requests\MassDestroyProductMasterRequest;
use App\Http\Requests\StoreProductMasterRequest;
use App\Http\Requests\UpdateProductMasterRequest;
use App\Models\ImeiMaster;
use App\Models\ProductMaster;
use App\Models\ProductModel;
use App\Models\Vt;
use App\Models\CurrentStock;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class ProductMastersController extends Controller
{
    use CsvImportTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('product_master_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = ProductMaster::with(['product_model', 'imei', 'vts', 'team'])->select(sprintf('%s.*', (new ProductMaster)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'product_master_show';
                $editGate      = 'product_master_edit';
                $deleteGate    = 'product_master_delete';
                $crudRoutePart = 'product-masters';

                return view('partials.datatablesActions', compact(
                    'viewGate',
                    'editGate',
                    'deleteGate',
                    'crudRoutePart',
                    'row'
                ));
            });

            $table->editColumn('id', fn($row) => $row->id ?? '');
            $table->addColumn('product_model_product_model', fn($row) => $row->product_model?->product_model ?? '');

            $table->editColumn('product_model.status', fn($row) => $row->product_model?->status ?? '');
            $table->addColumn('imei_imei_number', fn($row) => $row->imei?->imei_number ?? '');
            $table->editColumn('imei.product_status', fn($row) => $row->imei?->product_status ?? '');
            $table->addColumn('vts_vts_number', fn($row) => $row->vts?->vts_number ?? '');
            $table->editColumn('vts.sim_number', fn($row) => $row->vts?->sim_number ?? '');
            $table->editColumn('warranty', fn($row) => $row->warranty ?? '');
            $table->editColumn('subscription', fn($row) => $row->subscription ?? '');
            $table->editColumn('amc', fn($row) => $row->amc ?? '');
            $table->editColumn('status', fn($row) => $row->status ? ProductMaster::STATUS_SELECT[$row->status] : '');

            $table->rawColumns(['actions', 'placeholder', 'product_model', 'imei', 'vts']);

            return $table->make(true);
        }

        return view('admin.productMasters.index');
    }

public function create()
{
    abort_if(Gate::denies('product_master_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

    // Fetch all the required product model details
    $product_models = ProductModel::all();
    
    $imeis = ImeiMaster::where('product_status', 'Not Used')->pluck('imei_number', 'id')->prepend(trans('global.pleaseSelect'), '');
    $vts = Vt::where('product_status', 'Not Formed')->pluck('vts_number', 'id')->prepend(trans('global.pleaseSelect'), '');

    // Pass the full product models with all the fields to the view
    return view('admin.productMasters.create', compact('imeis', 'product_models', 'vts'));
}

    public function store(StoreProductMasterRequest $request)
    {
        $data = $request->all();

        // Generate random 8-character SKU (4 letters + 4 digits)
        $sku = strtoupper(Str::random(4)) . mt_rand(1000, 9999);
        $data['sku'] = $sku;

        // Create product
        $productMaster = ProductMaster::create($data);
        

        // Update IMEI status to Used
        if (!empty($data['imei_id'])) {
            ImeiMaster::where('id', $data['imei_id'])->update(['product_status' => 'Used']);
        }

        // Update VTS status to Formed
        if (!empty($data['vts_id'])) {
            Vt::where('id', $data['vts_id'])->update(['product_status' => 'Formed']);
        }
            
        // Save to current_stocks
        CurrentStock::create([
            'product_id' => $productMaster->id,
            'sku' => $sku,
        ]);
        
        return redirect()->route('admin.product-masters.index');
    }

    public function edit(ProductMaster $productMaster)
    {
        abort_if(Gate::denies('product_master_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $product_models = ProductModel::pluck('product_model', 'id')->prepend(trans('global.pleaseSelect'), '');
        $imeis = ImeiMaster::where('product_status', 'Not Used')->orWhere('id', $productMaster->imei_id)->pluck('imei_number', 'id')->prepend(trans('global.pleaseSelect'), '');
        $vts = Vt::where('product_status', 'Not Formed')->orWhere('id', $productMaster->vts_id)->pluck('vts_number', 'id')->prepend(trans('global.pleaseSelect'), '');

        $productMaster->load('product_model', 'imei', 'vts', 'team');

        return view('admin.productMasters.edit', compact('imeis', 'productMaster', 'product_models', 'vts'));
    }

    public function update(UpdateProductMasterRequest $request, ProductMaster $productMaster)
    {
        $productMaster->update($request->all());
        return redirect()->route('admin.product-masters.index');
    }

    public function show(ProductMaster $productMaster)
    {
        abort_if(Gate::denies('product_master_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $productMaster->load('product_model', 'imei', 'vts', 'team');

        return view('admin.productMasters.show', compact('productMaster'));
    }

    public function destroy(ProductMaster $productMaster)
    {
        abort_if(Gate::denies('product_master_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $productMaster->delete();
        return back();
    }

    public function massDestroy(MassDestroyProductMasterRequest $request)
    {
        $productMasters = ProductMaster::find(request('ids'));

        foreach ($productMasters as $productMaster) {
            $productMaster->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function getModelDetails($id)
{
    \Log::info("Fetching product model details for ID: $id");  // Log the request

    $model = ProductModel::find($id);

    if (!$model) {
        return response()->json(['error' => 'Model not found'], 404);
    }

    return response()->json([
        'warranty'     => $model->warranty,
        'subscription' => $model->subscription,
        'amc'          => $model->amc,
    ]);
}

}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Requests\MassDestroyCurrentStockRequest;
use App\Http\Requests\StoreCurrentStockRequest;
use App\Http\Requests\UpdateCurrentStockRequest;
use App\Models\CurrentStock;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;
use App\Models\StockHistory;
use App\Models\User;
use PDF;

use App\Models\AddCustomerVehicle;
use App\Models\ActivationRequest;

class CurrentStockController extends Controller
{
    use CsvImportTrait;

public function index(Request $request)
{
    abort_if(Gate::denies('current_stock_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

    if ($request->ajax()) {
        $query = CurrentStock::with(['team', 'product.product_model', 'product.imei', 'product.vts'])
            ->where(function ($query) {
                $query->where(function ($q) {
                    $q->whereNull('transfer_user_id')
                      ->whereNull('transfer_id');
                })->orWhere(function ($q) {
                    $q->where('transfer_user_id', auth()->id());
                });
            })
            ->select(sprintf('%s.*', (new CurrentStock)->getTable()));

        $table = DataTables::of($query);

        $table->addColumn('placeholder', '&nbsp;');
        $table->addColumn('actions', '&nbsp;');

        $table->editColumn('actions', function ($row) {
            $viewGate = 'current_stock_show';
            $editGate = 'current_stock_edit';
            $deleteGate = 'current_stock_delete';
            $crudRoutePart = 'current-stocks';

            return view('partials.datatablesActions', compact(
                'viewGate',
                'editGate',
                'deleteGate',
                'crudRoutePart',
                'row'
            ));
        });

        $table->editColumn('id', function ($row) {
            return $row->id ?? '';
        });

        $table->editColumn('sku', function ($row) {
            return $row->sku ?? '';
        });

        $table->addColumn('product_model', function ($row) {
            return $row->product && $row->product->product_model
                ? $row->product->product_model->product_model
                : '';
        });

        $table->addColumn('imei_number', function ($row) {
            return $row->product && $row->product->imei
                ? $row->product->imei->imei_number
                : '';
        });

        $table->addColumn('vts_number', function ($row) {
            return $row->product && $row->product->vts
                ? $row->product->vts->vts_number
                : '';
        });

        $table->addColumn('sim_number', function ($row) {
            return $row->product && $row->product->vts
                ? $row->product->vts->sim_number
                : '';
        });

        $table->rawColumns(['actions', 'placeholder']);

        return $table->make(true);
    }

    return view('admin.currentStocks.index');
}



    public function create()
    {
        abort_if(Gate::denies('current_stock_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.currentStocks.create');
    }

    public function store(StoreCurrentStockRequest $request)
    {
        $currentStock = CurrentStock::create($request->all());

        return redirect()->route('admin.current-stocks.index');
    }

    public function edit(CurrentStock $currentStock)
    {
        abort_if(Gate::denies('current_stock_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $currentStock->load('team');

        return view('admin.currentStocks.edit', compact('currentStock'));
    }

    public function update(UpdateCurrentStockRequest $request, CurrentStock $currentStock)
    {
        $currentStock->update($request->all());

        return redirect()->route('admin.current-stocks.index');
    }

    public function show(CurrentStock $currentStock)
{
    abort_if(Gate::denies('current_stock_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

    $currentStock->load([
        'team',
        'product',                  // product by SKU relation (returns ProductMaster)
        'productRechargeRequests',
        'selectProductStockTransfers',
        'select_parties'
    ]);

    $productMaster = $currentStock->product;  // Get related ProductMaster

    return view('admin.currentStocks.show', compact('currentStock', 'productMaster'));
}



    public function destroy(CurrentStock $currentStock)
    {
        abort_if(Gate::denies('current_stock_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $currentStock->delete();

        return back();
    }

    public function massDestroy(MassDestroyCurrentStockRequest $request)
    {
        $currentStocks = CurrentStock::find(request('ids'));

        foreach ($currentStocks as $currentStock) {
            $currentStock->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

  public function stockHistory(Request $request)
{
     $stockHistories = StockHistory::all();

    return view('admin.reports.stock-history', compact('stockHistories'));
}

public function showHistory($id)
{
    $history = StockHistory::findOrFail($id);
    $user = User::find($history->select_party_id);
    $createdBy = User::find($history->created_by_id);

    $product = \App\Models\ProductMaster::with([
        'product_model:id,product_model',
        'imei:id,imei_number',
        'vts:id,vts_number,sim_number,operator,product_status,status',
    ])->find($history->product_id);

    $vehicle = null;
    if ($history->vehicle_id) {
$vehicle = AddCustomerVehicle::find($history->vehicle_id);     }

    // dd($vehicle);

    return view('admin.reports.stock-historyshow', compact('history', 'user', 'createdBy', 'product', 'vehicle'));
}


 public function download($id)
    {
        // Get the activation request with all related data
        $activationRequest = ActivationRequest::with([
            'user',
            'vehicle',
            'product.productModel',
            'product.imei',
            'product.vts',
            'history',
            'createdBy'
        ])->findOrFail($id);

        // Generate the PDF
        $pdf = Pdf::loadView('admin.reports.pdf', compact('activationRequest'));

        // Set the filename
        $filename = 'invoice_' . $activationRequest->activation_request_id . '.pdf';

        // Download the PDF
        return $pdf->download($filename);
    }

    /**
     * Show the printable invoice view
     */
    public function print($id)
    {
        // Get the activation request with all related data
        $activationRequest = ActivationRequest::with([
            'user',
            'vehicle',
            'product.productModel',
            'product.imei',
            'product.vts',
            'history',
            'createdBy'
        ])->findOrFail($id);
        dd($activationRequest);

        return view('admin.reports.print', compact('activationRequest'));
    }

    public function invoice($id)
{
    abort_if(Gate::denies('current_stock_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

    $currentStock = CurrentStock::with([
        'team',
        'product',
        'productRechargeRequests',
        'selectProductStockTransfers',
        'select_parties'
    ])->findOrFail($id);

    $productMaster = $currentStock->product;

    $pdf = Pdf::loadView('admin.currentStocks.invoice', compact('currentStock', 'productMaster'));

    return $pdf->download('current-stock-invoice-' . $currentStock->id . '.pdf');
}



}

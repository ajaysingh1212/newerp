<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Requests\MassDestroyCheckPartyStockRequest;
use App\Http\Requests\StoreCheckPartyStockRequest;
use App\Http\Requests\UpdateCheckPartyStockRequest;
use App\Models\CheckPartyStock;
use App\Models\User;
use App\Models\CurrentStock;
use Gate;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;


class CheckPartyStockController extends Controller
{
    use CsvImportTrait;

public function index(Request $request)
{
    abort_if(Gate::denies('check_party_stock_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

    if ($request->ajax()) {
        $query = CurrentStock::with([
            'select_parties',
            'team',
            'product', // based on SKU
            'productById.product_model', // based on product_id
            'productById.imei',
            'productById.vts',
        ]);

        // Role/user logic
        if ($request->user_id) {
            $query->where('transfer_user_id', $request->user_id);
        } elseif ($request->role === 'admin') {
            $query->whereNull('transfer_user_id');
        }

        // Date range filtering
        if ($request->start_date) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        return Datatables::of($query)
            ->addColumn('placeholder', '&nbsp;')
            ->addColumn('actions', function ($row) {
                $viewGate = 'check_party_stock_show';
                $editGate = 'check_party_stock_edit';
                $deleteGate = 'check_party_stock_delete';
                $crudRoutePart = 'check-party-stocks';

                return view('partials.datatablesActions', compact(
                    'viewGate', 'editGate', 'deleteGate', 'crudRoutePart', 'row'
                ));
            })
      
    
            // Product detail columns
            ->addColumn('sku', fn($row) => $row->sku ?? '')
            ->addColumn('product_model', fn($row) => $row->productById->product_model->product_model ?? '')
            ->addColumn('imei_number', fn($row) => $row->productById->imei->imei_number ?? '')
            ->addColumn('vts_name', fn($row) => $row->productById->vts->vts_number ?? '')

            ->rawColumns(['actions', 'placeholder'])
            ->make(true);
    }

    return view('admin.checkPartyStocks.index');
}


   

public function getUsersByRole(Request $request)
{
    $roleId = $request->input('role');
    if (!$roleId) {
        return response()->json([]);
    }

    // role_user se user_id nikalna
    $userIds = DB::table('role_user')->where('role_id', $roleId)->pluck('user_id');

    // users ka data nikalna
    $users = \App\Models\User::whereIn('id', $userIds)->select('id', 'name')->get();

    return response()->json($users);
}

}

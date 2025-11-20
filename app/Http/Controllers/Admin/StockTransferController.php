<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyStockTransferRequest;
use App\Http\Requests\StoreStockTransferRequest;
use App\Http\Requests\UpdateStockTransferRequest;
use App\Models\CurrentStock;
use App\Models\ProductMaster;
use App\Models\Role;
use App\Models\StockTransfer;
use App\Models\User;
use App\Models\State;
use App\Models\District;
use App\Models\VehicleType;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


class StockTransferController extends Controller
{

public function index(Request $request)
{
    abort_if(Gate::denies('stock_transfer_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

    if ($request->ajax()) {
        $query = StockTransfer::with([
            'select_user.roles',
            'reseller',
            'select_products',
        ])
        // ✅ Only show records where transfer_id matches logged-in user ID
        ->where('transfer_id', auth()->id())
        ->select(sprintf('%s.*', (new StockTransfer)->table));

        $table = Datatables::of($query);

        $table->addColumn('placeholder', '&nbsp;');
        $table->addColumn('actions', '&nbsp;');

        $table->editColumn('actions', function ($row) {
            $viewGate      = 'stock_transfer_show';
            $editGate      = 'stock_transfer_edit';
            $deleteGate    = 'stock_transfer_delete';
            $crudRoutePart = 'stock-transfers';

            return view('partials.datatablesActions', compact(
                'viewGate',
                'editGate',
                'deleteGate',
                'crudRoutePart',
                'row'
            ));
        });

        $table->addColumn('reseller_email', fn($row) => $row->reseller->email ?? '');
        $table->addColumn('reseller_company_name', fn($row) => $row->reseller->company_name ?? '');

        $table->editColumn('select_product', function ($row) {
            $labels = [];
            foreach ($row->select_products as $select_product) {
                $labels[] = sprintf('<span class="label label-info label-many">%s</span>', $select_product->sku);
            }
            return implode(' ', $labels);
        });

        $table->rawColumns(['actions', 'placeholder', 'select_product', 'reseller_email', 'reseller_company_name']);

        return $table->make(true);
    }

    return view('admin.stockTransfers.index');
}


public function create()
{
    abort_if(Gate::denies('activation_request_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

    $user = auth()->user();
    $userRole = $user->roles->first()->title ?? null;

    $party_types = collect();
    $select_parties = collect();

    if ($userRole === 'Admin') {
        // Admin sees all roles
        $party_types = Role::pluck('title', 'id')->prepend(trans('global.pleaseSelect'), '');

        // Admin can see all users
        $select_parties = User::all()
            ->mapWithKeys(function ($user) {
                return [$user->id => $user->name . ' (' . $user->mobile_number . ')'];
            })
            ->prepend(trans('global.pleaseSelect'), '');
    } else {
        // Other users see specific roles and only their created users
        if ($userRole === 'CNF') {
            $party_types = Role::whereIn('title', ['Distributer', 'Dealer', 'Customer'])
                ->pluck('title', 'id')
                ->prepend(trans('global.pleaseSelect'), '');
        } elseif ($userRole === 'Distributer') {
            $party_types = Role::whereIn('title', ['Dealer', 'Customer'])
                ->pluck('title', 'id')
                ->prepend(trans('global.pleaseSelect'), '');
        } elseif ($userRole === 'Dealer') {
            $party_types = Role::whereIn('title', ['Customer', 'Admin'])
                ->pluck('title', 'id')
                ->prepend(trans('global.pleaseSelect'), '');
        }

        $select_parties = User::where('created_by_id', $user->id)
            ->get()
            ->mapWithKeys(function ($user) {
                return [$user->id => $user->name . ' (' . $user->mobile_number . ')'];
            })
            ->prepend(trans('global.pleaseSelect'), '');
    }

    // Filter CurrentStock based on role and transfer_user_id
    if ($userRole === 'Admin') {
    $select_products = CurrentStock::where(function ($q) {
            $q->whereNull('transfer_user_id')
              ->orWhere('transfer_user_id', auth()->id());
        })
        ->with(['product.product_model', 'product.imei'])
        ->get()
        ->mapWithKeys(function ($stock) {
            $imei = $stock->product->imei->imei_number ?? 'N/A';
            $model = $stock->product->product_model;
            $modelDetails = $model ? " Model: {$model->product_model}" : '';

            return [$stock->id => $stock->sku . " (IMEI: $imei)$modelDetails"];
        });
    } else {
        $select_products = CurrentStock::where('transfer_user_id', $user->id)
            ->with(['product.product_model', 'product.imei'])
            ->get()
            ->mapWithKeys(function ($stock) {
                $imei = $stock->product->imei->imei_number ?? 'N/A';
                $model = $stock->product->product_model;
                $modelDetails = $model ? " Model: {$model->product_model}" : '';

                return [$stock->id => $stock->sku . " (IMEI: $imei)$modelDetails"];
            });
    }


    // Dropdowns for form
    $states = State::pluck('state_name', 'id')->prepend(trans('global.pleaseSelect'), '');
    $districts = District::pluck('districts', 'id')->prepend(trans('global.pleaseSelect'), '');
    $vehicle_types = VehicleType::pluck('vehicle_type', 'id')->prepend(trans('global.pleaseSelect'), '');

    return view('admin.stockTransfers.create', compact(
        'districts',
        'party_types',
        'select_parties',
        'states',
        'vehicle_types',
        'select_products'
    ));
}






public function store(Request $request)
{
    
    // Validate the incoming data
    $request->validate([
        'transfer_date' => 'required|date',
        'select_user_id' => 'required|exists:users,id',
        'reseller_id' => 'nullable',
        'products' => 'required|array',
        'products.*.id' => 'required|exists:current_stocks,id',
        'products.*.warranty' => 'required|string',
        'products.*.amc' => 'required|string',
        'products.*.mrp' => 'required|numeric',
        'products.*.role_price' => 'required|numeric',
        'products.*.discount_type' => 'required|string|in:value,percentage',
        'products.*.discount_value' => 'required|numeric',
        'products.*.base_price' => 'required|numeric',
        'products.*.cgst' => 'required|numeric',
        'products.*.sgst' => 'required|numeric',
        'products.*.total_tax' => 'required|numeric',
        'products.*.final_price' => 'required|numeric',
    ]);

    DB::beginTransaction();

    try {
        // Get the ID of the logged-in user
        $loggedInUserId = auth()->id();

        // Create the stock transfer record with transfer_id as the logged-in user
        $stockTransfer = StockTransfer::create([
            'transfer_date' => $request->transfer_date,
            'select_user_id' => $request->select_user_id,
            'reseller_id' => $request->reseller_id,
            'transfer_id' => $loggedInUserId,
        ]);

        // Loop through each product and attach it to the stock transfer
        foreach ($request->products as $productData) {
            // Attach product details to the pivot table
            $stockTransfer->select_products()->attach($productData['id'], [
                'warranty' => $productData['warranty'],
                'amc' => $productData['amc'],
                'mrp' => $productData['mrp'],
                'role_price' => $productData['role_price'],
                'discount_type' => $productData['discount_type'],
                'discount_value' => $productData['discount_value'],
                'base_price' => $productData['base_price'],
                'cgst' => $productData['cgst'],
                'sgst' => $productData['sgst'],
                'total_tax' => $productData['total_tax'],
                'final_price' => $productData['final_price'],
            ]);

            // Update current stock entry to reflect the transfer
            \App\Models\CurrentStock::where('id', $productData['id'])->update([
                'transfer_user_id' => $request->select_user_id,
                'transfer_id' => $loggedInUserId,
            ]);
        }

        DB::commit();

        return redirect()->route('admin.stock-transfers.index')->with('success', 'Stock Transfer created successfully.');
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->withErrors(['error' => 'Something went wrong: ' . $e->getMessage()]);
    }
}






    public function edit(StockTransfer $stockTransfer)
    {
        abort_if(Gate::denies('stock_transfer_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $select_users = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        $resellers = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        $select_products = CurrentStock::pluck('sku', 'id');

        $stockTransfer->load('select_user', 'reseller', 'select_products', 'team');

        return view('admin.stockTransfers.edit', compact('resellers', 'select_products', 'select_users', 'stockTransfer'));
    }

    public function update(UpdateStockTransferRequest $request, StockTransfer $stockTransfer)
    {
        $stockTransfer->update($request->all());
        $stockTransfer->select_products()->sync($request->input('select_products', []));
        return redirect()->route('admin.stock-transfers.index');
    }

    public function show(StockTransfer $stockTransfer)
    {
        abort_if(Gate::denies('stock_transfer_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $stockTransfer->load('select_user', 'reseller', 'select_products.product.product_model', 'team','transferUser');
        // dd($stockTransfer->select_products);

        return view('admin.stockTransfers.show', compact('stockTransfer'));
    }

    public function destroy(StockTransfer $stockTransfer)
    {
        abort_if(Gate::denies('stock_transfer_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $stockTransfer->delete();

        return back();
    }

    public function massDestroy(MassDestroyStockTransferRequest $request)
    {
        $stockTransfers = StockTransfer::find(request('ids'));

        foreach ($stockTransfers as $stockTransfer) {
            $stockTransfer->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

public function getUsersByRole(Request $request)
{
    $roleId = $request->input('role_id');
    $loggedInUser = auth()->user();

    $userRole = $loggedInUser->roles->first()->title ?? null;

    $usersQuery = User::whereHas('roles', function ($q) use ($roleId) {
        $q->where('id', $roleId);
    })->select('id', 'name', 'mobile_number');

    // ❌ Non-admin users see only created users
    // ✔ BUT Dealer must see Admin users also
    if ($userRole !== 'Admin') {

        // Dealer wants Admin → show all Admins
        $selectedRole = Role::find($roleId);
        if ($userRole === 'Dealer' && $selectedRole && $selectedRole->title === 'Admin') {
            // Skip filtering → show all Admin users
        } else {
            // Normal logic: show only users created by logged-in user
            $usersQuery->where('created_by_id', $loggedInUser->id);
        }
    }

    $users = $usersQuery->get();

    return response()->json(
        $users->mapWithKeys(function ($user) {
            return [$user->id => [
                'name' => $user->name,
                'mobile_number' => $user->mobile_number
            ]];
        })
    );
}


public function getProductDetails(Request $request)
{
    try {
        $currentStockId = $request->input('product_id');
        $roleId = $request->input('role_id');

        if (!$currentStockId || !$roleId) {
            return response()->json(['error' => 'Stock ID and Role ID are required'], 400);
        }

        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        // Check stock
        $currentStock = \App\Models\CurrentStock::where('id', $currentStockId)
            ->where(function ($query) use ($user) {
                $query->whereNull('transfer_user_id')
                      ->orWhere('transfer_user_id', $user->id);
            })
            ->first();

        if (!$currentStock) {
            return response()->json(['error' => 'Stock record not found for this user'], 404);
        }

        $productMaster = \App\Models\ProductMaster::find($currentStock->product_id);
        if (!$productMaster) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        $productModel = $productMaster->product_model;
        if (!$productModel) {
            return response()->json(['error' => 'Product model not found'], 404);
        }

        $role = \App\Models\Role::find($roleId);
        if (!$role) {
            return response()->json(['error' => 'Role not found'], 404);
        }

        $roleTitle = strtolower(trim($role->title));

        // Dealer → Admin transfer = Dealer price
        $price = match ($roleTitle) {
            'cnf'        => $productModel->cnf_price,
            'distributer'=> $productModel->distributor_price,
            'dealer'     => $productModel->dealer_price,
            'customer'   => $productModel->customer_price,
            'admin'      => $productModel->dealer_price, // IMPORTANT FIX
            default      => null,
        };

        if (is_null($price)) {
            return response()->json(['error' => 'Invalid price for this role'], 400);
        }

        return response()->json([
            'warranty' => $productModel->warranty,
            'amc' => $productModel->amc,
            'mrp' => $productModel->mrp,
            'price' => $price,
        ]);

    } catch (\Exception $e) {
        \Log::error("getProductDetails Error", [
            'message' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile(),
        ]);
        return response()->json(['error' => 'Something went wrong'], 500);
    }
}





}

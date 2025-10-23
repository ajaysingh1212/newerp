<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroyRechargeRequestRequest;
use App\Http\Requests\StoreRechargeRequestRequest;
use App\Http\Requests\UpdateRechargeRequestRequest;
use App\Models\CurrentStock;
use App\Models\RechargePlan;
use App\Models\RechargeRequest;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Team;
use Carbon\Carbon; 
use App\Models\AddCustomerVehicle;
use App\Models\Commission;
use App\Models\Recharge;
use App\Models\ProductMaster;
use Gate;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use PDF; // Assuming you have a PDF package installed, e.g., barryvdh/laravel-dompdf

class RechargeRequestController extends Controller
{
    use MediaUploadingTrait, CsvImportTrait;

public function index(Request $request)
{
    abort_if(Gate::denies('recharge_request_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

    if ($request->ajax()) {
        $user = auth()->user(); // Logged-in user

        $query = RechargeRequest::with(['user', 'product', 'select_recharge', 'team']);

        // Check if user is admin via roles table (Spatie)
        $isAdmin = $user->roles()->where('id', 1)->exists(); // Admin role ID = 1

        // Only apply filter for non-admins
        if (!$isAdmin) {
            $userId = $user->id;
            $query->where(function ($q) use ($userId) {
                $q->where('created_by_id', $userId)
                  ->orWhere('user_id', $userId);
            });
        }

        $query->select(sprintf('%s.*', (new RechargeRequest)->table));

        $table = Datatables::of($query);

        $table->addColumn('placeholder', '&nbsp;');
        $table->addColumn('actions', '&nbsp;');

        $table->editColumn('actions', function ($row) {
            $viewGate      = 'recharge_request_show';
            $editGate      = 'recharge_request_edit';
            $deleteGate    = 'recharge_request_delete';
            $crudRoutePart = 'recharge-requests';

            return view('partials.datatablesActions', compact(
                'viewGate',
                'editGate',
                'deleteGate',
                'crudRoutePart',
                'row'
            ));
        });

        $table->editColumn('id', fn($row) => $row->id ?? '');
        $table->addColumn('user_name', fn($row) => $row->user?->name ?? '');
        $table->editColumn('vehicle_number', fn($row) => $row->vehicle_number ? '<span style="text-transform: uppercase;">' . e($row->vehicle_number) . '</span>' : '');
        $table->addColumn('select_recharge_type', fn($row) => $row->select_recharge?->type ?? '');
        $table->editColumn('select_recharge.plan_name', fn($row) => $row->select_recharge?->plan_name ?? '');

        $table->editColumn('attechment', function ($row) {
            $media = $row->attechment;
            return $media
                ? '<a href="' . $media->getUrl() . '" target="_blank">' . trans('global.downloadFile') . '</a>'
                : '';
        });

        $table->rawColumns(['actions', 'placeholder', 'attechment','vehicle_number']);

        return $table->make(true);
    }

    return view('admin.rechargeRequests.index');
}





public function create()
{
    abort_if(Gate::denies('recharge_request_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

    $currentUser = Auth::user();
    $userRole = Role::where('title', 'Customer')->first();

    // Load users with createdBy + their roles
    if ($currentUser->roles()->where('role_id', $userRole->id)->exists()) {
        $users = User::with(['createdBy.roles']) // Load parent roles
            ->where('id', $currentUser->id)
            ->get();
    } else {
        $users = User::with(['createdBy.roles']) // Load parent roles
            ->whereHas('roles', function ($q) use ($userRole) {
                $q->where('role_id', $userRole->id);
            })->get();
    }

    $userOptions = $users->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

    $products = CurrentStock::pluck('sku', 'id')->prepend(trans('global.pleaseSelect'), '');

    $select_recharges = RechargePlan::all()->mapWithKeys(function ($plan) {
        return [
            $plan->id => "{$plan->type} - {$plan->plan_name} - ₹{$plan->price}"
        ];
    })->prepend(trans('global.pleaseSelect'), '');

    // Fetch vehicle data with media for each user
    $vehiclesData = [];
    foreach ($users as $user) {
        $vehicles = \App\Models\AddCustomerVehicle::with('media')
            ->where('created_by_id', $user->id)
            ->get()
            ->map(function ($vehicle) {
                return [
                    'id'             => $vehicle->id,               // Added vehicle ID here
                    'vehicle_number' => $vehicle->vehicle_number,
                    'owners_name'    => $vehicle->owners_name,
                    'status'         => $vehicle->status,
                    'created_at'     => $vehicle->created_at->format('d-m-Y'),
                    'activated'      => $vehicle->activated,
                    'images'         => $vehicle->getMedia('vehicle_photos')->map(function ($media) {
                        return asset('storage/' . $media->id . '/' . $media->file_name);
                    }),
                ];
            });

        $vehiclesData[$user->id] = $vehicles;
    }

    // Extract parent role titles for each user
    $parentRoles = [];
    foreach ($users as $user) {
        $parent = $user->createdBy;
        if ($parent && $parent->roles->isNotEmpty()) {
            $parentRoles[$user->id] = $parent->roles->pluck('title')->implode(', ');
        } else {
            $parentRoles[$user->id] = 'N/A';
        }
    }

    // Alias $vehiclesData as $customerVehicles for Blade compatibility
   // Example: get vehicles for current user
$customerVehicles = $vehiclesData[$currentUser->id] ?? collect();


    return view('admin.rechargeRequests.create', compact(
        'products',
        'select_recharges',
        'userOptions',
        'users',
        'vehiclesData',
        'parentRoles',
        'customerVehicles'  // <-- added this alias
    ));
}




public function store(Request $request)
{
    $request->validate([
        'user_id'               => 'required|exists:users,id',
        'vehicle_id'            => 'required|exists:add_customer_vehicles,id',
        'recharge_plan_id'      => 'nullable|exists:recharge_plans,id',
        'vehicle_number'        => 'required|string|max:255',
        'product_id'            => 'nullable|exists:current_stocks,id',
        'notes'                 => 'nullable|string',
        'payment_status'        => 'nullable|string|in:pending,success,failed',
        'payment_method'        => 'nullable|string|max:100',
        'razorpay_payment_id'   => 'nullable|string|max:255',
        'payment_amount'        => 'nullable|numeric',
        'redeem_amount'         => 'nullable|numeric',
        'amc_duration'          => 'nullable|numeric',
        'warranty_duration'     => 'nullable|numeric',
        'subscription_duration' => 'nullable|numeric',
    ]);

    $paymentId = $this->generatePaymentId();
    $customer = User::findOrFail($request->user_id);
    $rechargePlan = $request->recharge_plan_id ? RechargePlan::findOrFail($request->recharge_plan_id) : null;

    $price = $request->price ?? ($rechargePlan->price ?? 0);
    $redeem = $request->redeem_amount ?? 0;
    $finalAmount = $price - $redeem;

    $loggedInUser = auth()->user();
    $loggedInUserRole = strtolower(trim(optional($loggedInUser->roles()->first())->title ?? ''));

    $commissionAmount = 0;
    $dealerId = null;
    $distributorId = null;

    if ($price > 0) {
        if ($loggedInUserRole === 'dealer') {
            $commissionAmount = round($price * 0.20, 2);
            $dealerId = $loggedInUser->id;
        } elseif (in_array($loggedInUserRole, ['distributor', 'distributer'])) {
            $commissionAmount = round($price * 0.20, 2);
            $distributorId = $loggedInUser->id;
        }
    }

    $paymentMethod = $finalAmount <= 0 ? 'wallet' : ($request->payment_method ?? 'razorpay');
    $razorpayPaymentId = $finalAmount <= 0 ? 'WALLET_' . strtoupper(Str::random(10)) : $request->razorpay_payment_id;
    $paymentStatus = $finalAmount <= 0 ? 'success' : ($request->payment_status ?? 'pending');

    DB::beginTransaction();

    try {
        $vehicle = AddCustomerVehicle::findOrFail($request->vehicle_id);
        $now = Carbon::now();

        $hasPreviousRecharge = RechargeRequest::where('vehicle_number', $vehicle->vehicle_number)->exists();
        $activation = null;
        $productModel = null;

        if (!$hasPreviousRecharge) {
            $activation = DB::table('activation_requests')->where('vehicle_reg_no', $vehicle->vehicle_number)->first();

            if ($activation && $activation->product_id) {
                $productMaster = \App\Models\ProductMaster::withTrashed()->find($activation->product_id);
                if ($productMaster && $productMaster->product_model_id) {
                    $productModel = \App\Models\ProductModel::withTrashed()->find($productMaster->product_model_id);
                }
            }
        }

        $calculateRemainingDays = function ($activationDate, $months) use ($now) {
            if (!$activationDate || !$months) return 0;
            $start = Carbon::parse($activationDate);
            $expiry = $start->copy()->addMonths($months);
            $remainingDays = $now->diffInDays($expiry, false);
            return max(0, $remainingDays);
        };

        $calculateUpdatedDate = function ($currentDate, $activationDate, $months, $productDuration) use ($now, $calculateRemainingDays) {
            $extraDays = $calculateRemainingDays($activationDate, $productDuration);
            $base = $currentDate ? Carbon::parse($currentDate) : $now;

            if ($base->gt($now)) {
                return $base->addMonths($months)->addDays($extraDays);
            } else {
                return $now->copy()->addMonths($months)->addDays($extraDays);
            }
        };

        // Subscription
        $productSubMonths = $productModel->subscription ?? 0;
        $activationSub = $activation->subscription ?? null;
        $subDuration = $request->subscription_duration ?? ($rechargePlan->subscription_duration ?? 0);
        $finalSubscription = $calculateUpdatedDate($vehicle->subscription, $activationSub, $subDuration, $productSubMonths);
        $vehicle->subscription = $finalSubscription;

        // Warranty
        $productWarrantyMonths = $productModel->warranty ?? 0;
        $activationWarranty = $activation->warranty ?? null;
        $warrantyDuration = $request->warranty_duration ?? ($rechargePlan->warranty_duration ?? 0);
        $finalWarranty = $calculateUpdatedDate($vehicle->warranty, $activationWarranty, $warrantyDuration, $productWarrantyMonths);
        $vehicle->warranty = $finalWarranty;

        // AMC
        $productAmcMonths = $productModel->amc ?? 0;
        $activationAmc = $activation->amc ?? null;
        $amcDuration = $request->amc_duration ?? ($rechargePlan->amc_duration ?? 0);
        $finalAmc = $calculateUpdatedDate($vehicle->amc, $activationAmc, $amcDuration, $productAmcMonths);
        $vehicle->amc = $finalAmc;

        $vehicle->save();

        $rechargeRequest = RechargeRequest::create([
            'user_id'               => $request->user_id,
            'vehicle_number'        => $request->vehicle_number,
            'vehicle_id'            => $request->vehicle_id,
            'product_id'            => $request->product_id,
            'select_recharge_id'    => $request->recharge_plan_id,
            'notes'                 => $request->notes,
            'team_id'               => $loggedInUser->team_id ?? null,
            'payment_status'        => $paymentStatus,
            'payment_method'        => $paymentMethod,
            'razorpay_payment_id'   => $razorpayPaymentId,
            'payment_amount'        => $finalAmount,
            'redeem_amount'         => $redeem,
            'payment_date'          => now(),
            'payment_id'            => $paymentId,
            'created_by_id'         => $loggedInUser->id,
            'amc_duration'          => $finalAmc,
            'warranty_duration'     => $finalWarranty,
            'subscription_duration' => $finalSubscription,
        ]);

        Commission::create([
            'recharge_request_id'    => $rechargeRequest->id,
            'customer_id'            => $customer->id,
            'dealer_id'              => $dealerId,
            'distributor_id'         => $distributorId,
            'dealer_commission'      => $dealerId ? $commissionAmount : 0,
            'distributor_commission' => $distributorId ? $commissionAmount : 0,
            'vehicle_id'             => $request->vehicle_id,
            'vehicle_number'         => $request->vehicle_number,
        ]);

        DB::commit();

        return redirect()->route('admin.recharge-requests.index')
            ->with('success', 'Recharge request created successfully.');
    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->with('error', 'Something went wrong: ' . $e->getMessage());
    }
}



// 🔧 Custom function to generate random Payment ID like PAY_ABCD1234
private function generatePaymentId()
{
    $letters = strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 4));
    $digits = str_pad(mt_rand(0, 9999), 4, '0', STR_PAD_LEFT);
    return 'PAY_' . $letters . $digits;
}




    public function edit(RechargeRequest $rechargeRequest)
    {
        abort_if(Gate::denies('recharge_request_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $users = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $products = CurrentStock::pluck('sku', 'id')->prepend(trans('global.pleaseSelect'), '');

        $select_recharges = RechargePlan::pluck('type', 'id')->prepend(trans('global.pleaseSelect'), '');

        $rechargeRequest->load('user', 'product', 'select_recharge', 'team');

        return view('admin.rechargeRequests.edit', compact('products', 'rechargeRequest', 'select_recharges', 'users'));
    }

    public function update(UpdateRechargeRequestRequest $request, RechargeRequest $rechargeRequest)
    {
        $rechargeRequest->update($request->all());

        if ($request->input('attechment', false)) {
            if (! $rechargeRequest->attechment || $request->input('attechment') !== $rechargeRequest->attechment->file_name) {
                if ($rechargeRequest->attechment) {
                    $rechargeRequest->attechment->delete();
                }
                $rechargeRequest->addMedia(storage_path('tmp/uploads/' . basename($request->input('attechment'))))->toMediaCollection('attechment');
            }
        } elseif ($rechargeRequest->attechment) {
            $rechargeRequest->attechment->delete();
        }

        return redirect()->route('admin.recharge-requests.index');
    }

    public function show(RechargeRequest $rechargeRequest)
    {
        abort_if(Gate::denies('recharge_request_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $rechargeRequest->load('user', 'product', 'select_recharge', 'team', 'vehicle', 'created_by');

        return view('admin.rechargeRequests.show', compact('rechargeRequest'));
    }

    public function downloadPdf(RechargeRequest $rechargeRequest)
{
    abort_if(Gate::denies('recharge_request_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

    $rechargeRequest->load('user', 'product', 'select_recharge','vehicle','created_by'); 
    // dd($rechargeRequest->select_recharge);

    $pdf = \PDF::loadView('admin.rechargerequests.downloadPdf', compact('rechargeRequest'))
              ->setPaper('a4', 'portrait');

    return $pdf->download('RechargeRequest_'.$rechargeRequest->id.'.pdf');
}


    public function destroy(RechargeRequest $rechargeRequest)
    {
        abort_if(Gate::denies('recharge_request_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $rechargeRequest->delete();

        return back();
    }

    public function massDestroy(MassDestroyRechargeRequestRequest $request)
    {
        $rechargeRequests = RechargeRequest::find(request('ids'));

        foreach ($rechargeRequests as $rechargeRequest) {
            $rechargeRequest->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('recharge_request_create') && Gate::denies('recharge_request_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model         = new RechargeRequest();
        $model->id     = $request->input('crud_id', 0);
        $model->exists = true;
        $media         = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }

    public function getVehiclePhotos(AddCustomerVehicle $vehicle)
{
    // Security check: Ensure logged-in user owns this vehicle
    if ($vehicle->created_by_id !== auth()->id()) {
        return response()->json([], 403);
    }

    $mediaItems = $vehicle->getMedia('vehicle_photos'); // Assuming medialibrary collection name

    $photos = $mediaItems->map(function($media) {
        return [
            'id' => $media->id,
            'file_name' => $media->file_name,
            'url' => $media->getUrl(), // public URL for image
        ];
    });

    return response()->json($photos);
}
public function getCustomerVehicles($userId)
{
    $vehicles = AddCustomerVehicle::with('media')
        ->where('created_by_id', $userId)
        ->where('activated', 'Activated')
        ->select('id', 'vehicle_number', 'vehicle_model', 'select_vehicle_type_id', 'activated', 'request_date', 'product_id', 'vehicle_color')
        ->get();

    $productsData = [];
    $vehicleImages = [];

    foreach ($vehicles as $vehicle) {
        $product = $vehicle->product_id ? ProductMaster::find($vehicle->product_id) : null;

        $productsData[$vehicle->id] = [
            'warranty' => $product?->warranty ?? 0,
            'subscription' => $product?->subscription ?? 0,
            'amc' => $product?->amc ?? 0,
        ];

        $media = $vehicle->getMedia('vehicle_photos')->first();
        $vehicleImages[$vehicle->id] = $media ? $media->getUrl() : null;
    }

    return response()->json([
        'vehicles' => $vehicles->keyBy('id'),
        'productsData' => $productsData,
        'vehicleImages' => $vehicleImages,
    ]);
}




}

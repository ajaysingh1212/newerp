<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroyActivationRequestRequest;
use App\Http\Requests\StoreActivationRequestRequest;
use App\Http\Requests\UpdateActivationRequestRequest;
use App\Models\ActivationRequest;
use App\Models\CurrentStock;
use App\Models\District;
use App\Models\Role;
use App\Models\State;
use App\Models\User;
use App\Models\VehicleType;
use Gate;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\AddCustomerVehicle;
use App\Models\AppLink;
use Carbon\Carbon;
use App\Models\UserAlert;
use App\Models\StockHistory;
use App\Models\ProductMaster;
use Barryvdh\DomPDF\Facade\Pdf;

class ActivationRequestController extends Controller
{
    use MediaUploadingTrait, CsvImportTrait;

public function index(Request $request)
{
    abort_if(Gate::denies('activation_request_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

    if ($request->ajax()) {
        $user = auth()->user();
        $userRole = $user->roles->first()->title ?? null;

        $query = ActivationRequest::with([
            'party_type',
            'select_party',
            'product',
            'state',
            'district',
            'vehicle_type',
            'team',
            'product_master.product_model',
            'product_master.imei',
            'product_master.vts'
        ]);

        if (strtolower($userRole) !== 'admin') {
            $query->where(function ($query) use ($user) {
                $query->where('created_by_id', $user->id)
                      ->orWhere('select_party_id', $user->id);
            });
        }

        $query->select(sprintf('%s.*', (new ActivationRequest)->table));
        $table = Datatables::of($query);

        $table->addColumn('placeholder', '&nbsp;');
        $table->addColumn('actions', '&nbsp;');

        $table->editColumn('actions', function ($row) {
            $viewGate = 'activation_request_show';
            $editGate = 'activation_request_edit';
            $deleteGate = 'activation_request_delete';
            $crudRoutePart = 'activation-requests';

            return view('partials.datatablesActions', compact(
                'viewGate',
                'editGate',
                'deleteGate',
                'crudRoutePart',
                'row'
            ));
        });

        $table->editColumn('id', fn($row) => $row->id ?? '');
        $table->addColumn('party_type_title', fn($row) => $row->party_type->title ?? '');
        $table->addColumn('select_party_name', fn($row) => $row->select_party->name ?? '');
        $table->editColumn('select_party.email', fn($row) => $row->select_party->email ?? '');
        $table->addColumn('product_sku', fn($row) => $row->product->sku ?? '');
        $table->addColumn('product_name', fn($row) => $row->product->product_name ?? '');
        $table->editColumn('vehicle_model', fn($row) => $row->vehicle_model ?? '');
        $table->editColumn('vehicle_reg_no', fn($row) => $row->vehicle_reg_no ?? '');

        $table->editColumn('status', function ($row) {
            if (!$row->status) return '';

            $classes = [
                'pending'    => 'badge badge-warning',
                'processing' => 'badge badge-primary',
                'activated'  => 'badge badge-success',
                'rejected'   => 'badge badge-danger',
            ];

            $class = $classes[$row->status] ?? 'badge badge-secondary';
            return '<span class="' . $class . '">' . ucfirst($row->status) . '</span>';
        });

        $table->addColumn('product_details', function ($row) {
            $product = $row->product_master;

            if ($product) {
                $sku   = $product->sku ?? 'N/A';
                $model = $product->product_model->product_model ?? 'N/A';
                $imei  = $product->imei->imei_number ?? 'N/A';
                $vts   = $product->vts->vts_number ?? 'N/A';

                return '
                    <div>
                        <strong>SKU:</strong> ' . $sku . '<br>
                        <a href="javascript:void(0);" class="view-more-toggle" data-target="details-' . $row->id . '">View More</a>
                        <div id="details-' . $row->id . '" class="product-more-details" style="display:none; margin-top: 5px;">
                            <strong>Model:</strong> ' . $model . '<br>
                            <strong>IMEI:</strong> ' . $imei . '<br>
                            <strong>VTS:</strong> ' . $vts . '
                        </div>
                    </div>
                ';
            }

            return 'No Product Info';
        });



        $table->rawColumns([
            'actions',
            'placeholder',
            'party_type_title',
            'select_party_name',
            'product_name',
            'product_sku',
            'status',
            'product_details'
            // Remove extra fields unless you define them: 'state', 'district', 'vehicle_type', 'id_proofs', etc.
        ]);

        return $table->make(true);
    }

    return view('admin.activationRequests.index');
}



  public function confirmActivate($id)
{
    $activationRequest = ActivationRequest::findOrFail($id);

    // Assuming $activationRequest has a product_id or relation:
    $product = ProductMaster::find($activationRequest->product_id);

    // Or if there is a relationship on ActivationRequest:
    // $product = $activationRequest->product;

    return view('admin.activationRequests.confirm', compact('activationRequest', 'product'));
}

public function processCommand($id)
{
    $activationRequest = ActivationRequest::findOrFail($id);
    $activationRequest->status = 'processing';
    $activationRequest->save();

    return redirect()->route('admin.activation-requests.index')->with('success', 'Request moved to processing.');
}



public function create()
{

    abort_if(Gate::denies('activation_request_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

    $user = auth()->user();
    $userRole = $user->roles->first()->title ?? null;

    // Initialize the party types collection
    $party_types = collect();

    if ($userRole === 'Admin') {
        // Admin sees all roles
        $party_types = Role::pluck('title', 'id')->prepend(trans('global.pleaseSelect'), '');
        $select_parties = User::all()
            ->mapWithKeys(function ($user) {
                return [$user->id => $user->name . ' (' . $user->mobile_number . ')'];
            })
            ->prepend(trans('global.pleaseSelect'), '');
    } else {
        // Filter party types based on the user's role
        if ($userRole === 'CNF') {
            $party_types = Role::whereIn('title', ['Distributer', 'Dealer', 'Customer'])
                ->pluck('title', 'id')
                ->prepend(trans('global.pleaseSelect'), '');
        } elseif ($userRole === 'Distributer') {
            $party_types = Role::whereIn('title', ['Dealer', 'Customer'])
                ->pluck('title', 'id')
                ->prepend(trans('global.pleaseSelect'), '');
        } elseif ($userRole === 'Dealer') {
           $party_types = Role::whereIn('title', [ 'Customer'])
                ->pluck('title', 'id')
                ->prepend(trans('global.pleaseSelect'), '');
        }

        // Only show users created by the logged-in user
        $select_parties = User::where('created_by_id', $user->id)
            ->get()
            ->mapWithKeys(function ($user) {
                return [$user->id => $user->name . ' (' . $user->mobile_number . ')'];
            })
            ->prepend(trans('global.pleaseSelect'), '');
    }

    $select_products = CurrentStock::where(function ($query) {
        $query->whereNull('transfer_user_id')
            ->orWhere('transfer_user_id', auth()->id());
    })
    ->with(['product.imei'])
    ->get()
    ->mapWithKeys(function ($stock) {
        $imei = $stock->product->imei->imei_number ?? 'N/A';
        return [$stock->id => $stock->sku . ' (IMEI: ' . $imei . ')'];
    });


$users = User::whereHas('roles', function ($query) {
    $query->where('title', 'Customer');
})->get();


    // Fetching other necessary data for the form
    $products = CurrentStock::pluck('sku', 'id')->prepend(trans('global.pleaseSelect'), '');
    $states = State::pluck('state_name', 'id')->prepend(trans('global.pleaseSelect'), '');
    $disricts = District::pluck('districts', 'id')->prepend(trans('global.pleaseSelect'), '');
    $vehicle_types = VehicleType::pluck('vehicle_type', 'id')->prepend(trans('global.pleaseSelect'), '');

     $appLinks = AppLink::all(); 

    return view('admin.activationRequests.create', compact(
        'disricts',
        'party_types',
        'select_products', // Pass the products with IMEI to the view
        'select_parties',
        'states',
        'vehicle_types',
        'users',
        'appLinks' // Pass the app links to the view
    ));
}



public function store(StoreActivationRequestRequest $request)
    {
        $data = $request->all();

        // Format the request date
        $formattedDate = Carbon::createFromFormat('d-m-Y', $request->request_date)->format('Y-m-d');

        // Set additional fields
        $data['status'] = ActivationRequest::STATUS_PENDING;
        $data['created_by_id'] = Auth::id();
        $data['amc'] = $formattedDate;
        $data['warranty'] = $formattedDate;
        $data['subscription'] = $formattedDate;

        // Create the activation request
        $activationRequest = ActivationRequest::create($data);

        // Check if the vehicle already exists
        $existingVehicle = AddCustomerVehicle::where('vehicle_number', $activationRequest->vehicle_reg_no)->first();

        $vehicleData = [
            'owners_name'            => $activationRequest->customer_name,
            'vehicle_model'          => $activationRequest->vehicle_model,
            'engine_number'          => $activationRequest->engine_number,
            'chassis_number'         => $activationRequest->chassis_number,
            'vehicle_color'          => $activationRequest->vehicle_color,
            'product_id'             => $activationRequest->product_id,
            'request_date'           => $activationRequest->request_date,
            'activation_id'          => $activationRequest->id,
            'select_vehicle_type_id' => $activationRequest->vehicle_type_id,
            'status'                 => 'processing',
            'activated'              => 'Activated',
            'created_by_id'          => $activationRequest->customer_name,
            'app_link_id'            => $activationRequest->app_link_id,
            'amc'                    => $activationRequest->amc,
            'warranty'               => $activationRequest->warranty,
            'subscription'           => $activationRequest->subscription,
            'user_id'           => $activationRequest->user_id,
            'password'           => $activationRequest->password,
            'app_url'           => $activationRequest->app_url,
        ];
        
        if ($existingVehicle) {
            $existingVehicle->update($vehicleData);
            $customerVehicle = $existingVehicle;
        } else {
            $vehicleData['vehicle_number'] = $activationRequest->vehicle_reg_no;
            $customerVehicle = AddCustomerVehicle::create($vehicleData);
        }

        // Link vehicle to activation request
        $activationRequest->vehicle_id = $customerVehicle->id;
        $activationRequest->save();

        // Log in stock history
        StockHistory::create(StockHistory::fromActivation($activationRequest));

        // Remove product from current stock if found
        if ($activationRequest->product_id) {
            $stock = CurrentStock::where('product_id', $activationRequest->product_id)->first();
            if ($stock) {
                $stock->delete();
            } else {
                Log::warning("Stock not found with product_id: " . $activationRequest->product_id);
            }
        }

        // Sync media to activation request
        foreach (['id_proofs', 'vehicle_photos', 'product_images'] as $collection) {
            if ($request->input($collection, false)) {
                $activationRequest->addMedia(storage_path('tmp/uploads/' . basename($request->input($collection))))
                    ->toMediaCollection($collection);
            }
        }

        if ($media = $request->input('ck-media', false)) {
            \App\Models\Media::whereIn('id', $media)->update(['model_id' => $activationRequest->id]);
        }

        // Copy media to AddCustomerVehicle
        foreach (['id_proofs', 'vehicle_photos', 'product_images'] as $collection) {
            $newMedia = $activationRequest->getMedia($collection);
            if ($newMedia->isNotEmpty()) {
                $customerVehicle->clearMediaCollection($collection); // Remove old media
                foreach ($newMedia as $media) {
                    $customerVehicle
                        ->addMedia($media->getPath())
                        ->preservingOriginal()
                        ->toMediaCollection($collection);
                }
            }
        }

        // Send alert to admins
        $alert = UserAlert::create([
            'alert_text' => 'New activation request created',
            'alert_link' => route('admin.activation-requests.edit', $activationRequest->id),
        ]);

        // Attach alert to all admins
        $admins = User::whereHas('roles', function ($query) {
            $query->where('title', 'Admin');
        })->get();

        $alert->users()->sync($admins->pluck('id'));

        return redirect()->route('admin.activation-requests.index')
            ->with('success', 'Activation Request created and stock updated successfully.');
    }









    public function edit(ActivationRequest $activationRequest)
{
    abort_if(Gate::denies('activation_request_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

    $party_types = Role::pluck('title', 'id')->prepend(trans('global.pleaseSelect'), '');

    $select_parties = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

    $products = CurrentStock::all();

    $states = State::pluck('state_name', 'id')->prepend(trans('global.pleaseSelect'), '');

    $disricts = District::pluck('districts', 'id')->prepend(trans('global.pleaseSelect'), '');

    $vehicle_types = VehicleType::pluck('vehicle_type', 'id')->prepend(trans('global.pleaseSelect'), '');

    $activationRequest->load('party_type', 'select_party', 'product', 'state', 'disrict', 'vehicle_type', 'team');

    $appLinks = AppLink::all();

    // ✅ Add status options here
    $statusOptions = [
        'Pending' => 'Pending',
        'activated' => 'activated',
        'Rejected' => 'Rejected',
    ];

    return view('admin.activationRequests.edit', compact(
        'activationRequest',
        'disricts',
        'party_types',
        'products',
        'select_parties',
        'states',
        'vehicle_types',
        'appLinks',
        'statusOptions' // ✅ Pass to view
    ));
}


   public function update(UpdateActivationRequestRequest $request, ActivationRequest $activationRequest)
{
    $activationRequest->update($request->all());

    // ✅ If the status is "activated", update status in both models
    if ($request->input('status') === 'activated') {
        $activationRequest->update(['status' => 'activated']);

        // Update AddCustomerVehicle by activation_id
        $customerVehicle = AddCustomerVehicle::where('activation_id', $activationRequest->id)->first();
        if ($customerVehicle) {
            $customerVehicle->update(['status' => 'activated']);
        }

        // 🔄 Update AddCustomerVehicle by vehicle_reg_no with user_id, password, app_link_id
        $vehicleRegNo = $activationRequest->vehicle_reg_no;
        if ($vehicleRegNo) {
            $linkedVehicle = AddCustomerVehicle::where('vehicle_number', $vehicleRegNo)->first();

            if ($linkedVehicle) {
                $linkedVehicle->update([
                    'user_id'     => $activationRequest->user_id,
                    'password'    => $activationRequest->password,
                    'app_link_id' => $activationRequest->app_link_id,
                    'app_url' => $activationRequest->app_url,
                    
                ]);
              
            }
        }
    }

    // Handle ID Proofs
    if ($request->input('id_proofs', false)) {
        if (!$activationRequest->id_proofs || $request->input('id_proofs') !== $activationRequest->id_proofs->file_name) {
            if ($activationRequest->id_proofs) {
                $activationRequest->id_proofs->delete();
            }
            $activationRequest->addMedia(storage_path('tmp/uploads/' . basename($request->input('id_proofs'))))->toMediaCollection('id_proofs');
        }
    } elseif ($activationRequest->id_proofs) {
        $activationRequest->id_proofs->delete();
    }

    // Handle Customer Image
    if ($request->input('customer_image', false)) {
        if (!$activationRequest->customer_image || $request->input('customer_image') !== $activationRequest->customer_image->file_name) {
            if ($activationRequest->customer_image) {
                $activationRequest->customer_image->delete();
            }
            $activationRequest->addMedia(storage_path('tmp/uploads/' . basename($request->input('customer_image'))))->toMediaCollection('customer_image');
        }
    } elseif ($activationRequest->customer_image) {
        $activationRequest->customer_image->delete();
    }

    // Handle Vehicle Photos
    if ($request->input('vehicle_photos', false)) {
        if (!$activationRequest->vehicle_photos || $request->input('vehicle_photos') !== $activationRequest->vehicle_photos->file_name) {
            if ($activationRequest->vehicle_photos) {
                $activationRequest->vehicle_photos->delete();
            }
            $activationRequest->addMedia(storage_path('tmp/uploads/' . basename($request->input('vehicle_photos'))))->toMediaCollection('vehicle_photos');
        }
    } elseif ($activationRequest->vehicle_photos) {
        $activationRequest->vehicle_photos->delete();
    }

    // Handle Product Images
    if ($request->input('product_images', false)) {
        if (!$activationRequest->product_images || $request->input('product_images') !== $activationRequest->product_images->file_name) {
            if ($activationRequest->product_images) {
                $activationRequest->product_images->delete();
            }
            $activationRequest->addMedia(storage_path('tmp/uploads/' . basename($request->input('product_images'))))->toMediaCollection('product_images');
        }
    } elseif ($activationRequest->product_images) {
        $activationRequest->product_images->delete();
    }

    return redirect()->route('admin.activation-requests.index');
}


 public function show(ActivationRequest $activationRequest)
{
    abort_if(Gate::denies('activation_request_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

    $user = auth()->user();
    $roles = $user->roles->pluck('title')->toArray();

    if (!in_array('Admin', $roles) && $activationRequest->created_by_id !== $user->id) {
        abort(Response::HTTP_FORBIDDEN, '403 Forbidden');
    }

    $activationRequest->load(
        'party_type',
        'select_party',
        'product',
        'state',
        'disrict',
        'vehicle_type',
        'team',
        'vehicleAttachVeichles'
    );

    return view('admin.activationRequests.show', compact('activationRequest'));
}



    public function destroy(ActivationRequest $activationRequest)
    {
        abort_if(Gate::denies('activation_request_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $activationRequest->delete();

        return back();
    }

    public function massDestroy(MassDestroyActivationRequestRequest $request)
    {
        $activationRequests = ActivationRequest::find(request('ids'));

        foreach ($activationRequests as $activationRequest) {
            $activationRequest->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('activation_request_create') && Gate::denies('activation_request_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model         = new ActivationRequest();
        $model->id     = $request->input('crud_id', 0);
        $model->exists = true;
        $media         = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }

public function getUsersByRole(Request $request)
{
    $roleId = $request->input('role_id');

    // Query to fetch users with the selected role
    $users = User::whereHas('roles', function ($q) use ($roleId) {
        $q->where('id', $roleId);
    })
    ->select('id', 'name', 'mobile_number')
    ->get();

    // Map users to create options for select dropdown
    $options = $users->map(function ($user) {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'mobile_number' => $user->mobile_number
        ];
    });

    return response()->json(['options' => $options]);
}

public function getPartyProducts(Request $request)
{
    $userId = $request->user_id;

    if (!$userId) {
        return response()->json([]);
    }

    // Find the user and their role
    $user = User::with('roles')->find($userId);
    $userRole = $user->roles->first()->title ?? null;

    // Agar selected party ka role Customer hai to products login user ke hisaab se laenge
    if ($userRole === 'Customer') {
        $userId = auth()->id();
    }

    $stocks = CurrentStock::with('product.imei')
        ->where('transfer_user_id', $userId)
        ->get();

    $products = [];

    foreach ($stocks as $stock) {
        if ($stock->product && $stock->product->imei) {
            $imei = $stock->product->imei->imei_number ?? 'N/A';
            $products[] = [
                'id' => $stock->product->id,
                'text' => $stock->sku . ' (IMEI: ' . $imei . ')'
            ];
        }
    }

    return response()->json($products);
}


public function getUserDetails($id)
{
   $user = User::with(['state', 'district'])
    ->select('id', 'name', 'email', 'mobile_number', 'state_id', 'district_id') // include required fields
    ->findOrFail($id);

    $vehicles = AddCustomerVehicle::where('created_by_id', $id)
        ->with(['media', 'select_vehicle_type'])
        ->latest()
        ->get();

    $vehicleData = $vehicles->map(function ($vehicle) {
        return [
            'id' => $vehicle->id,
            'vehicle_number' => $vehicle->vehicle_number,
            'owners_name' => $vehicle->owners_name,
            'vehicle_model' => $vehicle->vehicle_model,
            'vehicle_color' => $vehicle->vehicle_color,
            'engine_number' => $vehicle->engine_number,
            'chassis_number' => $vehicle->chassis_number,
            'insurance_expiry_date' => $vehicle->insurance_expiry_date,
            'vehicle_photo' => $vehicle->getFirstMediaUrl('vehicle_photos') ?: null,
            'select_vehicle_type_name' => $vehicle->select_vehicle_type_id ?? '',
            'id_proofs' => $vehicle->getMedia('id_proofs')->map(function ($media) {
                return [
                    'url' => $media->getUrl(),
                    'thumbnail' => $media->getUrl('thumb'),
                ];
            }),
        ];
    });

    return response()->json([
        'mobile_number' => $user->mobile_number,
        'email' => $user->email,
        'address' => $user->full_address,
        'state' => $user->state->state_name ?? '',
        'district' => $user->district->districts ?? '',
        'vehicles' => $vehicleData,
    ]);
}
public function search(Request $request)
{
    $search = $request->input('q');
    
    $users = User::where('name', 'like', "%{$search}%")
                ->orWhere('mobile_number', 'like', "%{$search}%")
                ->select('id', 'name', 'mobile_number')
                ->limit(10)
                ->get();

    return response()->json($users);
}

public function downloadInvoice($id)
{
    $activationRequest = ActivationRequest::with([
        'state', 'district', 'vehicle_type', 'product_master.product_model', 
        'product_master.vts', 'party_type', 'select_party', 'created_by'
    ])->findOrFail($id);

    $pdf = Pdf::loadView('admin.activationrequests.invoice', compact('activationRequest'));

    // Set paper size and orientation to A4 Portrait, and load it with minimal margin
    return $pdf->setPaper('A4', 'portrait')->download('activation-invoice.pdf');
}



}
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroyAddCustomerVehicleRequest;
use App\Http\Requests\StoreAddCustomerVehicleRequest;
use App\Http\Requests\UpdateAddCustomerVehicleRequest;
use App\Models\AddCustomerVehicle;
use App\Models\VehicleType;
use Gate;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;




class AddCustomerVehicleController extends Controller
{
    use MediaUploadingTrait, CsvImportTrait;

public function index(Request $request)
{
    abort_if(Gate::denies('add_customer_vehicle_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

    $search = $request->input('search');

    // 1️⃣ Vehicles owned by the user
    $ownedVehicles = AddCustomerVehicle::with([
        'select_vehicle_type',
        'team',
        'product_master.product_model',
        'product_master.imei',
        'product_master.vts',
        'appLink',
    ])
    ->when(!auth()->user()->is_admin, function ($query) {
        $query->where('created_by_id', auth()->id());
    })
    ->when($search, function ($query) use ($search) {
        $query->where('vehicle_number', 'like', "%{$search}%")
              ->orWhereHas('product_master.imei', function ($sub) use ($search) {
                  $sub->where('imei_number', 'like', "%{$search}%");
              });
    })
    ->orderByDesc('id')
    ->get();

    // 2️⃣ Vehicles shared with logged-in user
    $sharedVehicleIds = DB::table('vehicle_sharing')
        ->where('sharing_user_id', auth()->id())
        ->pluck('vehicle_id')
        ->toArray();

    $sharedVehicles = AddCustomerVehicle::with([
        'select_vehicle_type',
        'team',
        'product_master.product_model',
        'product_master.imei',
        'product_master.vts',
        'appLink',
    ])
    ->whereIn('id', $sharedVehicleIds)
    ->when($search, function ($query) use ($search) {
        $query->where('vehicle_number', 'like', "%{$search}%")
              ->orWhereHas('product_master.imei', function ($sub) use ($search) {
                  $sub->where('imei_number', 'like', "%{$search}%");
              });
    })
    ->orderByDesc('id')
    ->get();

    // 3️⃣ Merge owned + shared
    $vehicles = $ownedVehicles->merge($sharedVehicles)->unique('id');

    $now = \Carbon\Carbon::now();

    $formatExpiryInfo = function ($date) use ($now) {
        if (!$date) return ['date' => null, 'days_left' => null, 'expired' => false];

        try {
            $parsed = \Carbon\Carbon::parse($date);
            $daysLeft = $now->diffInDays($parsed, false);

            return [
                'date' => $parsed->format('Y-m-d'),
                'days_left' => $daysLeft > 0 ? $daysLeft : 0,
                'expired' => $daysLeft < 0,
            ];
        } catch (\Exception $e) {
            return ['date' => null, 'days_left' => null, 'expired' => false];
        }
    };

    $data = $vehicles->map(function ($vehicle) use ($now, $formatExpiryInfo) {
        $hasRecharge = \DB::table('recharge_requests')
            ->where('vehicle_number', $vehicle->vehicle_number)
            ->exists();
            $hasKyc = \DB::table('kyc_recharges')
    ->where('vehicle_number', $vehicle->vehicle_number)
    ->exists();

        $subscription = $amc = $warranty = null;

        if ($hasRecharge) {
            $subscription = $formatExpiryInfo($vehicle->subscription);
            $amc = $formatExpiryInfo($vehicle->amc);
            $warranty = $formatExpiryInfo($vehicle->warranty);
        } else {
            $activation = \DB::table('activation_requests')
                ->where('vehicle_reg_no', $vehicle->vehicle_number)
                ->first();

            if ($activation) {
                $productMaster = \App\Models\ProductMaster::withTrashed()->find($activation->product_id);
                $productModel = optional($productMaster)->product_model;

                $activationDate = \Carbon\Carbon::parse($activation->request_date ?? $activation->created_at ?? now());

                $subscriptionDate = is_numeric(optional($productModel)->subscription)
                    ? $activationDate->copy()->addMonths(optional($productModel)->subscription)
                    : optional($productModel)->subscription;

                $amcDate = is_numeric(optional($productModel)->amc)
                    ? $activationDate->copy()->addMonths(optional($productModel)->amc)
                    : optional($productModel)->amc;

                $warrantyDate = is_numeric(optional($productModel)->warranty)
                    ? $activationDate->copy()->addMonths(optional($productModel)->warranty)
                    : optional($productModel)->warranty;

                $subscription = $formatExpiryInfo($subscriptionDate);
                $amc = $formatExpiryInfo($amcDate);
                $warranty = $formatExpiryInfo($warrantyDate);
            }
        }

        return [
            'id' => $vehicle->id,
            'vehicle_model' => $vehicle->vehicle_model,
            'vehicle_number' => $vehicle->vehicle_number,
            'user_id' => $vehicle->user_id,
            'password' => $vehicle->password,
            'status' => $vehicle->status,
            'app_link' => optional($vehicle->appLink)->link,
            'vehicle_photos' => $vehicle->getFirstMediaUrl('vehicle_photos')
                ? '<img src="' . $vehicle->getFirstMediaUrl('vehicle_photos') . '" width="100%">'
                : '',
            'subscription_date' => $subscription['date'] ?? null,
            'subscription_remaining_days' => $subscription['days_left'] ?? null,
            'subscription_expired' => $subscription['expired'] ?? false,
            'amc_date' => $amc['date'] ?? null,
            'amc_remaining_days' => $amc['days_left'] ?? null,
            'amc_expired' => $amc['expired'] ?? false,
            'warranty_date' => $warranty['date'] ?? null,
            'warranty_remaining_days' => $warranty['days_left'] ?? null,
            'warranty_expired' => $warranty['expired'] ?? false,
            'request_date' => optional($vehicle->rechargeRequest()->latest()->first())->created_at?->format('Y-m-d'),
             'kyc_status' => $hasKyc ? 'completed' : 'pending',
        ];
    });

    return view('admin.addCustomerVehicles.index', ['vehicles' => $data]);
}




    public function create()
    {
        abort_if(Gate::denies('add_customer_vehicle_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $select_vehicle_types = VehicleType::pluck('vehicle_type', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.addCustomerVehicles.create', compact('select_vehicle_types'));
    }

public function store(StoreAddCustomerVehicleRequest $request)
{
    $data = $request->all();
    $data['created_by_id'] = Auth::id();
    $data['activated'] = 'not-activated'; // Set default value
     $data['status'] = 'Pending';          // Set default status value

    $addCustomerVehicle = AddCustomerVehicle::create($data);

    foreach ($request->input('insurance', []) as $file) {
        $addCustomerVehicle->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('insurance');
    }

    foreach ($request->input('pollution', []) as $file) {
        $addCustomerVehicle->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('pollution');
    }

    foreach ($request->input('registration_certificate', []) as $file) {
        $addCustomerVehicle->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('registration_certificate');
    }

    if ($request->input('id_proofs', false)) {
        $addCustomerVehicle->addMedia(storage_path('tmp/uploads/' . basename($request->input('id_proofs'))))->toMediaCollection('id_proofs');
    }

    if ($request->input('vehicle_photos', false)) {
        $addCustomerVehicle->addMedia(storage_path('tmp/uploads/' . basename($request->input('vehicle_photos'))))->toMediaCollection('vehicle_photos');
    }

    if ($request->input('product_images', false)) {
        $addCustomerVehicle->addMedia(storage_path('tmp/uploads/' . basename($request->input('product_images'))))->toMediaCollection('product_images');
    }

    if ($media = $request->input('ck-media', false)) {
        Media::whereIn('id', $media)->update(['model_id' => $addCustomerVehicle->id]);
    }

    return redirect()->route('admin.add-customer-vehicles.index');
}


    public function edit(AddCustomerVehicle $addCustomerVehicle)
    {
        abort_if(Gate::denies('add_customer_vehicle_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $select_vehicle_types = VehicleType::pluck('vehicle_type', 'id')->prepend(trans('global.pleaseSelect'), '');

        $addCustomerVehicle->load('select_vehicle_type', 'team');

        return view('admin.addCustomerVehicles.edit', compact('addCustomerVehicle', 'select_vehicle_types'));
    }

    public function update(UpdateAddCustomerVehicleRequest $request, AddCustomerVehicle $addCustomerVehicle)
    {
        $addCustomerVehicle->update($request->all());

        if (count($addCustomerVehicle->insurance) > 0) {
            foreach ($addCustomerVehicle->insurance as $media) {
                if (! in_array($media->file_name, $request->input('insurance', []))) {
                    $media->delete();
                }
            }
        }
        $media = $addCustomerVehicle->insurance->pluck('file_name')->toArray();
        foreach ($request->input('insurance', []) as $file) {
            if (count($media) === 0 || ! in_array($file, $media)) {
                $addCustomerVehicle->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('insurance');
            }
        }

        if (count($addCustomerVehicle->pollution) > 0) {
            foreach ($addCustomerVehicle->pollution as $media) {
                if (! in_array($media->file_name, $request->input('pollution', []))) {
                    $media->delete();
                }
            }
        }
        $media = $addCustomerVehicle->pollution->pluck('file_name')->toArray();
        foreach ($request->input('pollution', []) as $file) {
            if (count($media) === 0 || ! in_array($file, $media)) {
                $addCustomerVehicle->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('pollution');
            }
        }

        if (count($addCustomerVehicle->registration_certificate) > 0) {
            foreach ($addCustomerVehicle->registration_certificate as $media) {
                if (! in_array($media->file_name, $request->input('registration_certificate', []))) {
                    $media->delete();
                }
            }
        }
        $media = $addCustomerVehicle->registration_certificate->pluck('file_name')->toArray();
        foreach ($request->input('registration_certificate', []) as $file) {
            if (count($media) === 0 || ! in_array($file, $media)) {
                $addCustomerVehicle->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('registration_certificate');
            }
        }

        if ($request->input('id_proofs', false)) {
            if (! $addCustomerVehicle->id_proofs || $request->input('id_proofs') !== $addCustomerVehicle->id_proofs->file_name) {
                if ($addCustomerVehicle->id_proofs) {
                    $addCustomerVehicle->id_proofs->delete();
                }
                $addCustomerVehicle->addMedia(storage_path('tmp/uploads/' . basename($request->input('id_proofs'))))->toMediaCollection('id_proofs');
            }
        } elseif ($addCustomerVehicle->id_proofs) {
            $addCustomerVehicle->id_proofs->delete();
        }

        if ($request->input('vehicle_photos', false)) {
            if (! $addCustomerVehicle->vehicle_photos || $request->input('vehicle_photos') !== $addCustomerVehicle->vehicle_photos->file_name) {
                if ($addCustomerVehicle->vehicle_photos) {
                    $addCustomerVehicle->vehicle_photos->delete();
                }
                $addCustomerVehicle->addMedia(storage_path('tmp/uploads/' . basename($request->input('vehicle_photos'))))->toMediaCollection('vehicle_photos');
            }
        } elseif ($addCustomerVehicle->vehicle_photos) {
            $addCustomerVehicle->vehicle_photos->delete();
        }

        if ($request->input('product_images', false)) {
            if (! $addCustomerVehicle->product_images || $request->input('product_images') !== $addCustomerVehicle->product_images->file_name) {
                if ($addCustomerVehicle->product_images) {
                    $addCustomerVehicle->product_images->delete();
                }
                $addCustomerVehicle->addMedia(storage_path('tmp/uploads/' . basename($request->input('product_images'))))->toMediaCollection('product_images');
            }
        } elseif ($addCustomerVehicle->product_images) {
            $addCustomerVehicle->product_images->delete();
        }

        return redirect()->route('admin.add-customer-vehicles.index');
    }

public function show(AddCustomerVehicle $addCustomerVehicle)
{
    abort_if(Gate::denies('add_customer_vehicle_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

    $addCustomerVehicle->load([
        'select_vehicle_type',
        'team',
        'product_master.product_model',
        'product_master.imei',
        'product_master.vts',
        'media', // For Spatie media
    ]);

    return view('admin.addCustomerVehicles.show', compact('addCustomerVehicle'));
}



    public function destroy(AddCustomerVehicle $addCustomerVehicle)
    {
        abort_if(Gate::denies('add_customer_vehicle_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $addCustomerVehicle->delete();

        return back();
    }

    public function massDestroy(MassDestroyAddCustomerVehicleRequest $request)
    {
        $addCustomerVehicles = AddCustomerVehicle::find(request('ids'));

        foreach ($addCustomerVehicles as $addCustomerVehicle) {
            $addCustomerVehicle->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('add_customer_vehicle_create') && Gate::denies('add_customer_vehicle_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model         = new AddCustomerVehicle();
        $model->id     = $request->input('crud_id', 0);
        $model->exists = true;
        $media         = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }
public function validatePassword(Request $request)
{
    return response()->json([
        'valid' => Hash::check($request->password, auth()->user()->password)
    ]);
}
}

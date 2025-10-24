<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroyUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\District;
use App\Models\Role;
use App\Models\State;
use App\Models\Team;
use App\Models\User;
use Gate;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class UsersController extends Controller
{
    use MediaUploadingTrait, CsvImportTrait;

public function index()
{
    abort_if(Gate::denies('user_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

    // Load users with related roles and team
   $users = User::with(['state', 'district', 'roles', 'team'])
    ->orderBy('id', 'desc') // or 'created_at' if you prefer
    ->get();


    // For each user, load vehicles and KYC status
    foreach ($users as $user) {
        $userVehicles = \App\Models\AddCustomerVehicle::where('owners_name', $user->id)->get();

        foreach ($userVehicles as $vehicle) {
            $kyc = \App\Models\KycRecharge::where('vehicle_number', $vehicle->vehicle_number)
                    ->where('payment_status', 'Completed') // Assuming payment_status = 'Completed' means KYC done
                    ->first();

            $vehicle->kyc_status = $kyc ? 'Completed' : 'Pending';
        }

        $user->vehicles = $userVehicles;
    }

    return view('admin.users.index', compact('users'));
}




    public function create()
    {
        abort_if(Gate::denies('user_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $states = State::pluck('state_name', 'id')->prepend(trans('global.pleaseSelect'), '');
        $districts = District::pluck('districts', 'id')->prepend(trans('global.pleaseSelect'), '');
        $teams = Team::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $allRoles = Role::pluck('title', 'id');
        $userRole = auth()->user()->roles->first()->title ?? '';

        if ($userRole === 'Admin') {
            $allowedRoles = $allRoles;
        } elseif ($userRole === 'CNF') {
            $allowedRoles = $allRoles->filter(function ($title) {
                return in_array($title, ['Distributer', 'Dealer', 'Customer','Sharing']);
            });
        } elseif ($userRole === 'Distributer') {
            $allowedRoles = $allRoles->filter(function ($title) {
                return in_array($title, ['Dealer', 'Customer','Sharing']);
            });
        } elseif ($userRole === 'Dealer') {
            $allowedRoles = $allRoles->filter(function ($title) {
                 return in_array($title, ['Customer','Sharing']);
            });
        } elseif ($userRole === 'Customer') {
            // ✅ Allow Customer to create Sharing role
            $allowedRoles = $allRoles->filter(function ($title) {
                return $title === 'Sharing';
            });
        } else {
            $allowedRoles = collect(); // No roles allowed
        }

        $roles = $allowedRoles;

        return view('admin.users.create', compact('districts', 'roles', 'states', 'teams'));
    }



public function store(StoreUserRequest $request)
{
    $data = $request->all();
    $data['created_by_id'] = auth()->id(); // logged-in user ka ID

    $user = User::create($data);
    $user->roles()->sync($request->input('roles', []));

    if ($request->input('profile_image', false)) {
        $user->addMedia(storage_path('tmp/uploads/' . basename($request->input('profile_image'))))->toMediaCollection('profile_image');
    }

    if ($request->input('upload_signature', false)) {
        $user->addMedia(storage_path('tmp/uploads/' . basename($request->input('upload_signature'))))->toMediaCollection('upload_signature');
    }

    if ($request->input('upload_pan_aadhar', false)) {
        $user->addMedia(storage_path('tmp/uploads/' . basename($request->input('upload_pan_aadhar'))))->toMediaCollection('upload_pan_aadhar');
    }

    if ($request->input('passbook_statement', false)) {
        $user->addMedia(storage_path('tmp/uploads/' . basename($request->input('passbook_statement'))))->toMediaCollection('passbook_statement');
    }

    if ($request->input('shop_photo', false)) {
        $user->addMedia(storage_path('tmp/uploads/' . basename($request->input('shop_photo'))))->toMediaCollection('shop_photo');
    }

    if ($request->input('gst_certificate', false)) {
        $user->addMedia(storage_path('tmp/uploads/' . basename($request->input('gst_certificate'))))->toMediaCollection('gst_certificate');
    }

    if ($media = $request->input('ck-media', false)) {
        Media::whereIn('id', $media)->update(['model_id' => $user->id]);
    }

    return redirect()->route('admin.users.index');
}


    public function edit(User $user)
    {
        abort_if(Gate::denies('user_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $states = State::pluck('state_name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $districts = District::pluck('districts', 'id')->prepend(trans('global.pleaseSelect'), '');

        $roles = Role::pluck('title', 'id');

        $teams = Team::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $user->load('state', 'district', 'roles', 'team');

        return view('admin.users.edit', compact('districts', 'roles', 'states', 'teams', 'user'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $user->update($request->all());
        $user->roles()->sync($request->input('roles', []));
        if ($request->input('profile_image', false)) {
            if (! $user->profile_image || $request->input('profile_image') !== $user->profile_image->file_name) {
                if ($user->profile_image) {
                    $user->profile_image->delete();
                }
                $user->addMedia(storage_path('tmp/uploads/' . basename($request->input('profile_image'))))->toMediaCollection('profile_image');
            }
        } elseif ($user->profile_image) {
            $user->profile_image->delete();
        }

        if ($request->input('upload_signature', false)) {
            if (! $user->upload_signature || $request->input('upload_signature') !== $user->upload_signature->file_name) {
                if ($user->upload_signature) {
                    $user->upload_signature->delete();
                }
                $user->addMedia(storage_path('tmp/uploads/' . basename($request->input('upload_signature'))))->toMediaCollection('upload_signature');
            }
        } elseif ($user->upload_signature) {
            $user->upload_signature->delete();
        }

        if ($request->input('upload_pan_aadhar', false)) {
            if (! $user->upload_pan_aadhar || $request->input('upload_pan_aadhar') !== $user->upload_pan_aadhar->file_name) {
                if ($user->upload_pan_aadhar) {
                    $user->upload_pan_aadhar->delete();
                }
                $user->addMedia(storage_path('tmp/uploads/' . basename($request->input('upload_pan_aadhar'))))->toMediaCollection('upload_pan_aadhar');
            }
        } elseif ($user->upload_pan_aadhar) {
            $user->upload_pan_aadhar->delete();
        }

        if ($request->input('passbook_statement', false)) {
            if (! $user->passbook_statement || $request->input('passbook_statement') !== $user->passbook_statement->file_name) {
                if ($user->passbook_statement) {
                    $user->passbook_statement->delete();
                }
                $user->addMedia(storage_path('tmp/uploads/' . basename($request->input('passbook_statement'))))->toMediaCollection('passbook_statement');
            }
        } elseif ($user->passbook_statement) {
            $user->passbook_statement->delete();
        }

        if ($request->input('shop_photo', false)) {
            if (! $user->shop_photo || $request->input('shop_photo') !== $user->shop_photo->file_name) {
                if ($user->shop_photo) {
                    $user->shop_photo->delete();
                }
                $user->addMedia(storage_path('tmp/uploads/' . basename($request->input('shop_photo'))))->toMediaCollection('shop_photo');
            }
        } elseif ($user->shop_photo) {
            $user->shop_photo->delete();
        }

        if ($request->input('gst_certificate', false)) {
            if (! $user->gst_certificate || $request->input('gst_certificate') !== $user->gst_certificate->file_name) {
                if ($user->gst_certificate) {
                    $user->gst_certificate->delete();
                }
                $user->addMedia(storage_path('tmp/uploads/' . basename($request->input('gst_certificate'))))->toMediaCollection('gst_certificate');
            }
        } elseif ($user->gst_certificate) {
            $user->gst_certificate->delete();
        }

        return redirect()->route('admin.users.index');
    }

    public function show(User $user)
    {
        abort_if(Gate::denies('user_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $user->load('state', 'district', 'roles', 'team', 'selectUserStockTransfers', 'selectPartyActivationRequests', 'selectUserAttachVeichles', 'userRechargeRequests', 'userUserAlerts', 'selectPartyCheckPartyStocks');

        return view('admin.users.show', compact('user'));
    }

    public function destroy(User $user)
    {
        abort_if(Gate::denies('user_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $user->delete();

        return back();
    }

    public function massDestroy(MassDestroyUserRequest $request)
    {
        $users = User::find(request('ids'));

        foreach ($users as $user) {
            $user->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('user_create') && Gate::denies('user_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model         = new User();
        $model->id     = $request->input('crud_id', 0);
        $model->exists = true;
        $media         = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }
    public function search(Request $request)
    {
        $query = $request->get('q');
        if (strlen($query) < 3) {
            return response()->json([]);
        }

        $users = User::query()
            ->where('name', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->orWhere('mobile_number', 'like', "%{$query}%")
            ->select('id', 'name', 'email', 'mobile_number')
            ->limit(10)
            ->get();

        return response()->json($users);
    }

}
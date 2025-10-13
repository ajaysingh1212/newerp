<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\Admin\UserResource;
use App\Models\User;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UsersApiController extends Controller
{
    use MediaUploadingTrait;

    public function index()
    {
        abort_if(Gate::denies('user_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new UserResource(User::with(['state', 'district', 'roles', 'team'])->get());
    }

    public function store(StoreUserRequest $request)
    {
        $user = User::create($request->all());
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

        return (new UserResource($user))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(User $user)
    {
        abort_if(Gate::denies('user_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new UserResource($user->load(['state', 'district', 'roles', 'team']));
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

        return (new UserResource($user))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(User $user)
    {
        abort_if(Gate::denies('user_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $user->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
    
    
    public function login(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
    
        // Attempt login
        $credentials = $request->only('email', 'password');
        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }
    
        // Authenticated user with roles
        $user = Auth::user()->load('roles');
        $token = $user->createToken('api-token')->plainTextToken;
    
        return response()->json([
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'company_name' => $user->company_name,
                'email' => $user->email,
                'gst_number' => $user->gst_number,
                'date_inc' => $user->date_inc,
                'date_joining' => $user->date_joining,
                'mobile_number' => $user->mobile_number,
                'whatsapp_number' => $user->whatsapp_number,
                'pin_code' => $user->pin_code,
                'full_address' => $user->full_address,
                'bank_name' => $user->bank_name,
                'branch_name' => $user->branch_name,
                'ifsc' => $user->ifsc,
                'ac_holder_name' => $user->ac_holder_name,
                'pan_number' => $user->pan_number,
                'status' => $user->status,
                'email_verified_at' => $user->email_verified_at,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
                'deleted_at' => $user->deleted_at,
                'state_id' => $user->state_id,
                'district_id' => $user->district_id,
                'team_id' => $user->team_id,
                'created_by_id' => $user->created_by_id,
                'status_cmd' => $user->status_cmd,
                'profile_image' => $user->profile_image,
                'upload_signature' => $user->upload_signature,
                'upload_pan_aadhar' => $user->upload_pan_aadhar,
                'passbook_statement' => $user->passbook_statement,
                'shop_photo' => $user->shop_photo,
                'gst_certificate' => $user->gst_certificate,
                'media' => $user->media,
                'roles' => $user->roles->pluck('title')  // Roles ko sirf title ke form me bhej rahe hai
            ]
        ]);
    }
    
    public function getUserById($id)
    {
        $user = User::with('roles')->find($id);
    
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
    
        return new UserResource($user);
    }
    
    
    public function register(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'mobile_number' => 'required|string|max:20|unique:users,mobile_number',
            'password' => 'required|string|min:6',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
    
        // Create user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'mobile_number' => $request->mobile_number,
            'password' => bcrypt($request->password),
        ]);
    
        // Assign default role id = 2
        $user->roles()->sync([2]);
    
        // Create token
        $token = $user->createToken('api-token')->plainTextToken;
    
        return response()->json([
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'mobile_number' => $user->mobile_number,
                'roles' => $user->roles->pluck('title')  // Role bhi response me denge
            ]
        ], 201);
    }


    
    
    
}

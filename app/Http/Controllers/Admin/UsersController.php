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

public function index(Request $request)
{
    abort_if(Gate::denies('user_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

    if ($request->ajax()) {
        $query = User::with(['state', 'district', 'roles', 'team']);

        // ✅ Only show own created users if not Admin
        if (!auth()->user()->roles->contains('title', 'Admin')) {
            $query->where('created_by_id', auth()->id());
        }

        $query = $query->select(sprintf('%s.*', (new User)->table));
        $table = Datatables::of($query);

        $table->addColumn('placeholder', '&nbsp;');
        $table->addColumn('actions', '&nbsp;');

        $table->editColumn('actions', function ($row) {
            $viewGate      = 'user_show';
            $editGate      = 'user_edit';
            $deleteGate    = 'user_delete';
            $crudRoutePart = 'users';

            return view('partials.datatablesActions', compact(
                'viewGate',
                'editGate',
                'deleteGate',
                'crudRoutePart',
                'row'
            ));
        });

        $table->editColumn('id', fn($row) => $row->id ?? '');
        $table->editColumn('name', fn($row) => $row->name ?? '');
        $table->editColumn('company_name', fn($row) => $row->company_name ?? '');
        $table->editColumn('email', fn($row) => $row->email ?? '');
        $table->editColumn('gst_number', fn($row) => $row->gst_number ?? '');
        $table->editColumn('date_joining', fn($row) => $row->date_joining ?? '');
        $table->editColumn('mobile_number', fn($row) => $row->mobile_number ?? '');
        $table->editColumn('whatsapp_number', fn($row) => $row->whatsapp_number ?? '');
        $table->addColumn('state_state_name', fn($row) => $row->state->state_name ?? '');
        $table->editColumn('state.country', fn($row) => $row->state ? (is_string($row->state) ? $row->state : $row->state->country) : '');
        $table->addColumn('district_districts', fn($row) => $row->district->districts ?? '');
        $table->editColumn('district.country', fn($row) => $row->district ? (is_string($row->district) ? $row->district : $row->district->country) : '');
        $table->editColumn('pin_code', fn($row) => $row->pin_code ?? '');
        $table->editColumn('bank_name', fn($row) => $row->bank_name ?? '');
        $table->editColumn('branch_name', fn($row) => $row->branch_name ?? '');
        $table->editColumn('ifsc', fn($row) => $row->ifsc ?? '');
        $table->editColumn('ac_holder_name', fn($row) => $row->ac_holder_name ?? '');
        $table->editColumn('pan_number', fn($row) => $row->pan_number ?? '');

        $table->editColumn('profile_image', function ($row) {
            if ($photo = $row->profile_image) {
                return sprintf('<a href="%s" target="_blank"><img src="%s" width="50px" height="50px"></a>', $photo->url, $photo->thumbnail);
            }
            return '';
        });

        $table->editColumn('upload_signature', function ($row) {
            if ($photo = $row->upload_signature) {
                return sprintf('<a href="%s" target="_blank"><img src="%s" width="50px" height="50px"></a>', $photo->url, $photo->thumbnail);
            }
            return '';
        });

        $table->editColumn('upload_pan_aadhar', fn($row) => $row->upload_pan_aadhar ? '<a href="' . $row->upload_pan_aadhar->getUrl() . '" target="_blank">' . trans('global.downloadFile') . '</a>' : '');
        $table->editColumn('passbook_statement', fn($row) => $row->passbook_statement ? '<a href="' . $row->passbook_statement->getUrl() . '" target="_blank">' . trans('global.downloadFile') . '</a>' : '');

        $table->editColumn('shop_photo', function ($row) {
            if ($photo = $row->shop_photo) {
                return sprintf('<a href="%s" target="_blank"><img src="%s" width="50px" height="50px"></a>', $photo->url, $photo->thumbnail);
            }
            return '';
        });

        $table->editColumn('gst_certificate', fn($row) => $row->gst_certificate ? '<a href="' . $row->gst_certificate->getUrl() . '" target="_blank">' . trans('global.downloadFile') . '</a>' : '');
        $table->editColumn('status', fn($row) => $row->status ? User::STATUS_SELECT[$row->status] : '');
        $table->editColumn('roles', function ($row) {
            $labels = [];
            foreach ($row->roles as $role) {
                $labels[] = sprintf('<span class="label label-info label-many">%s</span>', $role->title);
            }
            return implode(' ', $labels);
        });

        $table->rawColumns([
            'actions',
            'placeholder',
            'state',
            'district',
            'profile_image',
            'upload_signature',
            'upload_pan_aadhar',
            'passbook_statement',
            'shop_photo',
            'gst_certificate',
            'roles'
        ]);

        return $table->make(true);
    }

    return view('admin.users.index');
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
                return in_array($title, ['Distributer', 'Dealer', 'Customer']);
            });
        } elseif ($userRole === 'Distributer') {
            $allowedRoles = $allRoles->filter(function ($title) {
                return in_array($title, ['Dealer', 'Customer']);
            });
        } elseif ($userRole === 'Dealer') {
            $allowedRoles = $allRoles->filter(function ($title) {
                return $title === 'Customer';
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
}
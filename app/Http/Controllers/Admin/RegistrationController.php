<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroyRegistrationRequest;
use App\Http\Requests\StoreRegistrationRequest;
use App\Http\Requests\UpdateRegistrationRequest;
use App\Models\Registration;
use App\Models\User;
use Gate;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;

class RegistrationController extends Controller
{
    use MediaUploadingTrait, CsvImportTrait;

    public function index()
    {
        abort_if(Gate::denies('registration_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $registrations = Registration::with(['investor','created_by','media'])->get();

        return view('admin.registrations.index', compact('registrations'));
    }

    public function create()
    {
        abort_if(Gate::denies('registration_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $investors = User::pluck('name','id')->prepend('Please Select','');

        return view('admin.registrations.create', compact('investors'));
    }

    // ============================================================
    // STORE
    // ============================================================
public function store(StoreRegistrationRequest $request)
{
    // 1. Create Registration
    $registration = Registration::create($request->all());

    /* ---------------------------------------------
     *  PAN - MULTIPLE FILES
     * --------------------------------------------*/
    foreach ((array)$request->input('pan_card_image', []) as $file) {
        $registration
            ->addMedia(storage_path("tmp/uploads/" . basename($file)))
            ->toMediaCollection('pan_card_image');
    }

    /* ---------------------------------------------
     *  Aadhaar Front - SINGLE (first array value)
     * --------------------------------------------*/
    $aadhaarFront = $request->input('aadhaar_front_image');
    if (is_array($aadhaarFront) && isset($aadhaarFront[0])) {
        $registration
            ->addMedia(storage_path("tmp/uploads/" . basename($aadhaarFront[0])))
            ->toMediaCollection('aadhaar_front_image');
    }

    /* ---------------------------------------------
     *  Aadhaar Back - SINGLE
     * --------------------------------------------*/
    $aadhaarBack = $request->input('aadhaar_back_image');
    if (is_array($aadhaarBack) && isset($aadhaarBack[0])) {
        $registration
            ->addMedia(storage_path("tmp/uploads/" . basename($aadhaarBack[0])))
            ->toMediaCollection('aadhaar_back_image');
    }

    /* ---------------------------------------------
     *  Profile Image - SINGLE
     * --------------------------------------------*/
    $profile = $request->input('profile_image');
    if (is_array($profile) && isset($profile[0])) {
        $registration
            ->addMedia(storage_path("tmp/uploads/" . basename($profile[0])))
            ->toMediaCollection('profile_image');
    }

    /* ---------------------------------------------
     *  Signature Image - SINGLE
     * --------------------------------------------*/
    $signature = $request->input('signature_image');
    if (is_array($signature) && isset($signature[0])) {
        $registration
            ->addMedia(storage_path("tmp/uploads/" . basename($signature[0])))
            ->toMediaCollection('signature_image');
    }

    /* ---------------------------------------------
     *  Assign CK Media
     * --------------------------------------------*/
    if ($media = $request->input('ck-media', false)) {
        Media::whereIn('id', $media)->update(['model_id' => $registration->id]);
    }

    return redirect()->route('admin.registrations.index')
        ->with('success', 'Registration Created with Media!');
}

    // ============================================================
    // EDIT
    // ============================================================
    public function edit(Registration $registration)
    {
        abort_if(Gate::denies('registration_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $investors = User::pluck('name','id')->prepend('Please Select','');

        $registration->load('investor','created_by');

        return view('admin.registrations.edit', compact('investors','registration'));
    }

    // ============================================================
    // UPDATE
    // ============================================================
public function update(UpdateRegistrationRequest $request, Registration $registration)
{
    // Update normal fields
    $registration->update($request->all());

    /* ============================================================
     *  PAN CARD IMAGES (MULTIPLE) — SAFE LOGIC
     * ============================================================*/

    $incomingPan = $request->input('pan_card_image', null);

    // CASE 1: User DID NOT open/modify Dropzone → KEEP existing images
    if ($incomingPan !== null) {

        $existingPan = $registration->pan_card_image->pluck('file_name')->toArray();
        $incomingPan = (array)$incomingPan;  // ensure array format

        // Remove only deleted ones
        foreach ($registration->pan_card_image as $media) {
            if (!in_array($media->file_name, $incomingPan)) {
                $media->delete(); // only delete images removed by user
            }
        }

        // Add new uploads
        foreach ($incomingPan as $file) {
            if (!in_array($file, $existingPan)) {
                $registration->addMedia(storage_path('tmp/uploads/' . basename($file)))
                    ->toMediaCollection('pan_card_image');
            }
        }
    }
    // else: KEEP ALL OLD PAN IMAGES


    /* ============================================================
     *  HELPER FOR SINGLE IMAGE FIELDS
     * ============================================================*/

    $updateSingle = function ($field, $collection) use ($request, $registration) {

        $incoming = $request->input($field);

        // Convert array → single value
        if (is_array($incoming)) {
            $incoming = $incoming[0] ?? null;
        }

        $existing = $registration->{$collection};

        // CASE 1: User removed file manually
        if ($incoming === null && $request->has($field)) {
            if ($existing) {
                $existing->delete();
            }
            return;
        }

        // CASE 2: User did NOT upload new file — KEEP OLD IMAGE
        if ($incoming === null) {
            return;
        }

        // CASE 3: User uploaded new file — REPLACE
        if (!$existing || $incoming !== $existing->file_name) {
            if ($existing) {
                $existing->delete();
            }
            $registration->addMedia(storage_path('tmp/uploads/' . basename($incoming)))
                ->toMediaCollection($collection);
        }
    };


    /* ============================================================
     *  UPDATE SINGLE UPLOAD FIELDS
     * ============================================================*/

    $updateSingle('aadhaar_front_image', 'aadhaar_front_image');
    $updateSingle('aadhaar_back_image', 'aadhaar_back_image');
    $updateSingle('profile_image', 'profile_image');
    $updateSingle('signature_image', 'signature_image');


    return redirect()->route('admin.registrations.index')
        ->with('success', 'Registration updated successfully!');
}



    // ============================================================
    // SHOW
    // ============================================================
    public function show(Registration $registration)
    {
        abort_if(Gate::denies('registration_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $registration->load(
            'investor',
            'created_by',
           
        );

        return view('admin.registrations.show', compact('registration'));
    }

    // ============================================================
    // DESTROY
    // ============================================================
    public function destroy(Registration $registration)
    {
        abort_if(Gate::denies('registration_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $registration->delete();

        return back();
    }

    // ============================================================
    // MASS DESTROY
    // ============================================================
    public function massDestroy(MassDestroyRegistrationRequest $request)
    {
        $registrations = Registration::find(request('ids'));

        foreach ($registrations as $reg) {
            $reg->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    // ============================================================
    // DROPZONE MEDIA UPLOAD
    // ============================================================
    public function storeMedia(Request $request)
    {
        abort_if(Gate::denies('registration_create') && Gate::denies('registration_edit'), 403);

        $path = storage_path('tmp/uploads');

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        if ($request->hasFile('file')) {

            $file = $request->file('file');
            $name = uniqid().'_'.trim($file->getClientOriginalName());
            $file->move($path,$name);

            return response()->json([
                'name' => $name,
                'original_name' => $file->getClientOriginalName()
            ]);
        }

        return response()->json(['error'=>'No file uploaded'],400);
    }

    // ============================================================
    // CKEDITOR UPLOAD
    // ============================================================
    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('registration_create') && Gate::denies('registration_edit'),403);

        $model = new Registration();
        $model->id = 0;
        $model->exists = true;

        $media = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json([
            'id'=>$media->id,
            'url'=>$media->getUrl()
        ], Response::HTTP_CREATED);
    }
}

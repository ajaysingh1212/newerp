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

        $registrations = Registration::with(['investor', 'created_by', 'media'])->get();

        return view('admin.registrations.index', compact('registrations'));
    }

    public function create()
    {
        abort_if(Gate::denies('registration_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $investors = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.registrations.create', compact('investors'));
    }

    public function store(StoreRegistrationRequest $request)
    {
        $registration = Registration::create($request->all());

        foreach ($request->input('pan_card_image', []) as $file) {
            $registration->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('pan_card_image');
        }

        if ($request->input('aadhaar_front_image', false)) {
            $registration->addMedia(storage_path('tmp/uploads/' . basename($request->input('aadhaar_front_image'))))->toMediaCollection('aadhaar_front_image');
        }

        if ($request->input('aadhaar_back_image', false)) {
            $registration->addMedia(storage_path('tmp/uploads/' . basename($request->input('aadhaar_back_image'))))->toMediaCollection('aadhaar_back_image');
        }

        foreach ($request->input('profile_image', []) as $file) {
            $registration->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('profile_image');
        }

        foreach ($request->input('signature_image', []) as $file) {
            $registration->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('signature_image');
        }

        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $registration->id]);
        }

        return redirect()->route('admin.registrations.index');
    }

    public function edit(Registration $registration)
    {
        abort_if(Gate::denies('registration_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $investors = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $registration->load('investor', 'created_by');

        return view('admin.registrations.edit', compact('investors', 'registration'));
    }

    public function update(UpdateRegistrationRequest $request, Registration $registration)
    {
        $registration->update($request->all());

        if (count($registration->pan_card_image) > 0) {
            foreach ($registration->pan_card_image as $media) {
                if (! in_array($media->file_name, $request->input('pan_card_image', []))) {
                    $media->delete();
                }
            }
        }
        $media = $registration->pan_card_image->pluck('file_name')->toArray();
        foreach ($request->input('pan_card_image', []) as $file) {
            if (count($media) === 0 || ! in_array($file, $media)) {
                $registration->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('pan_card_image');
            }
        }

        if ($request->input('aadhaar_front_image', false)) {
            if (! $registration->aadhaar_front_image || $request->input('aadhaar_front_image') !== $registration->aadhaar_front_image->file_name) {
                if ($registration->aadhaar_front_image) {
                    $registration->aadhaar_front_image->delete();
                }
                $registration->addMedia(storage_path('tmp/uploads/' . basename($request->input('aadhaar_front_image'))))->toMediaCollection('aadhaar_front_image');
            }
        } elseif ($registration->aadhaar_front_image) {
            $registration->aadhaar_front_image->delete();
        }

        if ($request->input('aadhaar_back_image', false)) {
            if (! $registration->aadhaar_back_image || $request->input('aadhaar_back_image') !== $registration->aadhaar_back_image->file_name) {
                if ($registration->aadhaar_back_image) {
                    $registration->aadhaar_back_image->delete();
                }
                $registration->addMedia(storage_path('tmp/uploads/' . basename($request->input('aadhaar_back_image'))))->toMediaCollection('aadhaar_back_image');
            }
        } elseif ($registration->aadhaar_back_image) {
            $registration->aadhaar_back_image->delete();
        }

        if (count($registration->profile_image) > 0) {
            foreach ($registration->profile_image as $media) {
                if (! in_array($media->file_name, $request->input('profile_image', []))) {
                    $media->delete();
                }
            }
        }
        $media = $registration->profile_image->pluck('file_name')->toArray();
        foreach ($request->input('profile_image', []) as $file) {
            if (count($media) === 0 || ! in_array($file, $media)) {
                $registration->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('profile_image');
            }
        }

        if (count($registration->signature_image) > 0) {
            foreach ($registration->signature_image as $media) {
                if (! in_array($media->file_name, $request->input('signature_image', []))) {
                    $media->delete();
                }
            }
        }
        $media = $registration->signature_image->pluck('file_name')->toArray();
        foreach ($request->input('signature_image', []) as $file) {
            if (count($media) === 0 || ! in_array($file, $media)) {
                $registration->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('signature_image');
            }
        }

        return redirect()->route('admin.registrations.index');
    }

    public function show(Registration $registration)
    {
        abort_if(Gate::denies('registration_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $registration->load('investor', 'created_by', 'selectInvestorInvestments', 'investorMonthlyPayoutRecords', 'selectInvestorWithdrawalRequests', 'investmentInvestorTransactions');

        return view('admin.registrations.show', compact('registration'));
    }

    public function destroy(Registration $registration)
    {
        abort_if(Gate::denies('registration_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $registration->delete();

        return back();
    }

    public function massDestroy(MassDestroyRegistrationRequest $request)
    {
        $registrations = Registration::find(request('ids'));

        foreach ($registrations as $registration) {
            $registration->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('registration_create') && Gate::denies('registration_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model         = new Registration();
        $model->id     = $request->input('crud_id', 0);
        $model->exists = true;
        $media         = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }
}

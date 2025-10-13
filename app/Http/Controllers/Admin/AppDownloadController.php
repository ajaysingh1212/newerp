<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroyAppDownloadRequest;
use App\Http\Requests\StoreAppDownloadRequest;
use App\Http\Requests\UpdateAppDownloadRequest;
use App\Models\AppDownload;
use Gate;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;

class AppDownloadController extends Controller
{
    use MediaUploadingTrait, CsvImportTrait;

    public function index()
    {
        abort_if(Gate::denies('app_download_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        // You may use ->withoutGlobalScopes() if MultiTenant filters are affecting it
        $appDownloads = AppDownload::with(['team', 'media'])->get();

        return view('admin.appDownloads.index', compact('appDownloads'));
    }


    public function create()
    {
        abort_if(Gate::denies('app_download_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.appDownloads.create');
    }

    public function store(StoreAppDownloadRequest $request)
    {
        $appDownload = AppDownload::create($request->all());

        if ($request->input('appfile', false)) {
            $appDownload->addMedia(storage_path('tmp/uploads/' . basename($request->input('appfile'))))->toMediaCollection('appfile');
        }

        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $appDownload->id]);
        }

        return redirect()->route('admin.app-downloads.index');
    }

    public function edit(AppDownload $appDownload)
    {
        abort_if(Gate::denies('app_download_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $appDownload->load('team');

        return view('admin.appDownloads.edit', compact('appDownload'));
    }

    public function update(UpdateAppDownloadRequest $request, AppDownload $appDownload)
    {
        $appDownload->update($request->all());

        if ($request->input('appfile', false)) {
            if (! $appDownload->appfile || $request->input('appfile') !== $appDownload->appfile->file_name) {
                if ($appDownload->appfile) {
                    $appDownload->appfile->delete();
                }
                $appDownload->addMedia(storage_path('tmp/uploads/' . basename($request->input('appfile'))))->toMediaCollection('appfile');
            }
        } elseif ($appDownload->appfile) {
            $appDownload->appfile->delete();
        }

        return redirect()->route('admin.app-downloads.index');
    }

    public function show(AppDownload $appDownload)
    {
        abort_if(Gate::denies('app_download_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $appDownload->load('team');

        return view('admin.appDownloads.show', compact('appDownload'));
    }

    public function destroy(AppDownload $appDownload)
    {
        abort_if(Gate::denies('app_download_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $appDownload->delete();

        return back();
    }

    public function massDestroy(MassDestroyAppDownloadRequest $request)
    {
        $appDownloads = AppDownload::find(request('ids'));

        foreach ($appDownloads as $appDownload) {
            $appDownload->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('app_download_create') && Gate::denies('app_download_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model         = new AppDownload();
        $model->id     = $request->input('crud_id', 0);
        $model->exists = true;
        $media         = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }
}

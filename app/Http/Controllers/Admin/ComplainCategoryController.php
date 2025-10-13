<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroyComplainCategoryRequest;
use App\Http\Requests\StoreComplainCategoryRequest;
use App\Http\Requests\UpdateComplainCategoryRequest;
use App\Models\ComplainCategory;
use Gate;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class ComplainCategoryController extends Controller
{
    use MediaUploadingTrait, CsvImportTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('complain_category_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = ComplainCategory::with(['team'])->select(sprintf('%s.*', (new ComplainCategory)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'complain_category_show';
                $editGate      = 'complain_category_edit';
                $deleteGate    = 'complain_category_delete';
                $crudRoutePart = 'complain-categories';

                return view('partials.datatablesActions', compact(
                    'viewGate',
                    'editGate',
                    'deleteGate',
                    'crudRoutePart',
                    'row'
                ));
            });

            $table->editColumn('id', function ($row) {
                return $row->id ? $row->id : '';
            });
            $table->editColumn('title', function ($row) {
                return $row->title ? $row->title : '';
            });

            $table->rawColumns(['actions', 'placeholder']);

            return $table->make(true);
        }

        return view('admin.complainCategories.index');
    }

    public function create()
    {
        abort_if(Gate::denies('complain_category_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.complainCategories.create');
    }

    public function store(StoreComplainCategoryRequest $request)
    {
        $complainCategory = ComplainCategory::create($request->all());

        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $complainCategory->id]);
        }

        return redirect()->route('admin.complain-categories.index');
    }

    public function edit(ComplainCategory $complainCategory)
    {
        abort_if(Gate::denies('complain_category_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $complainCategory->load('team');

        return view('admin.complainCategories.edit', compact('complainCategory'));
    }

    public function update(UpdateComplainCategoryRequest $request, ComplainCategory $complainCategory)
    {
        $complainCategory->update($request->all());

        return redirect()->route('admin.complain-categories.index');
    }

    public function show(ComplainCategory $complainCategory)
    {
        abort_if(Gate::denies('complain_category_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $complainCategory->load('team', 'selectComplainCheckComplains');

        return view('admin.complainCategories.show', compact('complainCategory'));
    }

    public function destroy(ComplainCategory $complainCategory)
    {
        abort_if(Gate::denies('complain_category_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $complainCategory->delete();

        return back();
    }

    public function massDestroy(MassDestroyComplainCategoryRequest $request)
    {
        $complainCategories = ComplainCategory::find(request('ids'));

        foreach ($complainCategories as $complainCategory) {
            $complainCategory->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('complain_category_create') && Gate::denies('complain_category_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model         = new ComplainCategory();
        $model->id     = $request->input('crud_id', 0);
        $model->exists = true;
        $media         = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }
}

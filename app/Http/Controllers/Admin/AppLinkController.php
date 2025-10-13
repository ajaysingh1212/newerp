<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Requests\MassDestroyAppLinkRequest;
use App\Http\Requests\StoreAppLinkRequest;
use App\Http\Requests\UpdateAppLinkRequest;
use App\Models\AppLink;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class AppLinkController extends Controller
{
    use CsvImportTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('app_link_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = AppLink::with(['team'])->select(sprintf('%s.*', (new AppLink)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'app_link_show';
                $editGate      = 'app_link_edit';
                $deleteGate    = 'app_link_delete';
                $crudRoutePart = 'app-links';

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
            $table->editColumn('link', function ($row) {
                return $row->link ? $row->link : '';
            });

            $table->rawColumns(['actions', 'placeholder']);

            return $table->make(true);
        }

        return view('admin.appLinks.index');
    }

    public function create()
    {
        abort_if(Gate::denies('app_link_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.appLinks.create');
    }

    public function store(StoreAppLinkRequest $request)
    {
        $appLink = AppLink::create($request->all());

        return redirect()->route('admin.app-links.index');
    }

    public function edit(AppLink $appLink)
    {
        abort_if(Gate::denies('app_link_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $appLink->load('team');

        return view('admin.appLinks.edit', compact('appLink'));
    }

    public function update(UpdateAppLinkRequest $request, AppLink $appLink)
    {
        $appLink->update($request->all());

        return redirect()->route('admin.app-links.index');
    }

    public function show(AppLink $appLink)
    {
        abort_if(Gate::denies('app_link_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $appLink->load('team');

        return view('admin.appLinks.show', compact('appLink'));
    }

    public function destroy(AppLink $appLink)
    {
        abort_if(Gate::denies('app_link_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $appLink->delete();

        return back();
    }

    public function massDestroy(MassDestroyAppLinkRequest $request)
    {
        $appLinks = AppLink::find(request('ids'));

        foreach ($appLinks as $appLink) {
            $appLink->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}

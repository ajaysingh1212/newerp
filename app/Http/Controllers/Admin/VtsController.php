<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Requests\MassDestroyVtRequest;
use App\Http\Requests\StoreVtRequest;
use App\Http\Requests\UpdateVtRequest;
use App\Models\Vt;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class VtsController extends Controller
{
    use CsvImportTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('vt_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = Vt::with(['team'])->select(sprintf('%s.*', (new Vt)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'vt_show';
                $editGate      = 'vt_edit';
                $deleteGate    = 'vt_delete';
                $crudRoutePart = 'vts';

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
            $table->editColumn('vts_number', function ($row) {
                return $row->vts_number ? $row->vts_number : '';
            });
            $table->editColumn('sim_number', function ($row) {
                return $row->sim_number ? $row->sim_number : '';
            });
            $table->editColumn('operator', function ($row) {
                return $row->operator ? $row->operator : '';
            });
            $table->editColumn('product_status', function ($row) {
                return $row->product_status ? Vt::PRODUCT_STATUS_SELECT[$row->product_status] : '';
            });
            $table->editColumn('status', function ($row) {
                return $row->status ? Vt::STATUS_SELECT[$row->status] : '';
            });

            $table->rawColumns(['actions', 'placeholder']);

            return $table->make(true);
        }

        return view('admin.vts.index');
    }

    public function create()
    {
        abort_if(Gate::denies('vt_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.vts.create');
    }

    public function store(StoreVtRequest $request)
    {
        $vt = Vt::create($request->all());

        return redirect()->route('admin.vts.index');
    }

    public function edit(Vt $vt)
    {
        abort_if(Gate::denies('vt_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $vt->load('team');

        return view('admin.vts.edit', compact('vt'));
    }

    public function update(UpdateVtRequest $request, Vt $vt)
    {
        $vt->update($request->all());

        return redirect()->route('admin.vts.index');
    }

    public function show(Vt $vt)
    {
        abort_if(Gate::denies('vt_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $vt->load('team', 'vtsProductMasters');

        return view('admin.vts.show', compact('vt'));
    }

    public function destroy(Vt $vt)
    {
        abort_if(Gate::denies('vt_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $vt->delete();

        return back();
    }

    public function massDestroy(MassDestroyVtRequest $request)
    {
        $vts = Vt::find(request('ids'));

        foreach ($vts as $vt) {
            $vt->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}

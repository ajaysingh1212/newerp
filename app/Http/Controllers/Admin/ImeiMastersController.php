<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Requests\MassDestroyImeiMasterRequest;
use App\Http\Requests\StoreImeiMasterRequest;
use App\Http\Requests\UpdateImeiMasterRequest;
use App\Models\ImeiMaster;
use App\Models\ImeiModel;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class ImeiMastersController extends Controller
{
    use CsvImportTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('imei_master_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = ImeiMaster::with(['imei_model', 'team'])->select(sprintf('%s.*', (new ImeiMaster)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'imei_master_show';
                $editGate      = 'imei_master_edit';
                $deleteGate    = 'imei_master_delete';
                $crudRoutePart = 'imei-masters';

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
            $table->addColumn('imei_model_imei_model_number', function ($row) {
                return $row->imei_model ? $row->imei_model->imei_model_number : '';
            });

            $table->editColumn('imei_model.status', function ($row) {
                return $row->imei_model ? (is_string($row->imei_model) ? $row->imei_model : $row->imei_model->status) : '';
            });
            $table->editColumn('imei_number', function ($row) {
                return $row->imei_number ? $row->imei_number : '';
            });
            $table->editColumn('status', function ($row) {
                return $row->status ? ImeiMaster::STATUS_SELECT[$row->status] : '';
            });
            $table->editColumn('product_status', function ($row) {
                return $row->product_status ? ImeiMaster::PRODUCT_STATUS_SELECT[$row->product_status] : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'imei_model']);

            return $table->make(true);
        }

        return view('admin.imeiMasters.index');
    }

    public function create()
    {
        abort_if(Gate::denies('imei_master_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $imei_models = ImeiModel::pluck('imei_model_number', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.imeiMasters.create', compact('imei_models'));
    }

    public function store(StoreImeiMasterRequest $request)
    {
        $imeiMaster = ImeiMaster::create($request->all());

        return redirect()->route('admin.imei-masters.index');
    }

    public function edit(ImeiMaster $imeiMaster)
    {
        abort_if(Gate::denies('imei_master_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $imei_models = ImeiModel::pluck('imei_model_number', 'id')->prepend(trans('global.pleaseSelect'), '');

        $imeiMaster->load('imei_model', 'team');

        return view('admin.imeiMasters.edit', compact('imeiMaster', 'imei_models'));
    }

    public function update(UpdateImeiMasterRequest $request, ImeiMaster $imeiMaster)
    {
        $imeiMaster->update($request->all());

        return redirect()->route('admin.imei-masters.index');
    }

    public function show(ImeiMaster $imeiMaster)
    {
        abort_if(Gate::denies('imei_master_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $imeiMaster->load('imei_model', 'team', 'imeiProductMasters');

        return view('admin.imeiMasters.show', compact('imeiMaster'));
    }

    public function destroy(ImeiMaster $imeiMaster)
    {
        abort_if(Gate::denies('imei_master_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $imeiMaster->delete();

        return back();
    }

    public function massDestroy(MassDestroyImeiMasterRequest $request)
    {
        $imeiMasters = ImeiMaster::find(request('ids'));

        foreach ($imeiMasters as $imeiMaster) {
            $imeiMaster->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}

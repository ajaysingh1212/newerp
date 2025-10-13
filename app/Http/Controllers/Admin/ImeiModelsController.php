<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Requests\MassDestroyImeiModelRequest;
use App\Http\Requests\StoreImeiModelRequest;
use App\Http\Requests\UpdateImeiModelRequest;
use App\Models\ImeiModel;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class ImeiModelsController extends Controller
{
    use CsvImportTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('imei_model_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = ImeiModel::with(['team'])->select(sprintf('%s.*', (new ImeiModel)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'imei_model_show';
                $editGate      = 'imei_model_edit';
                $deleteGate    = 'imei_model_delete';
                $crudRoutePart = 'imei-models';

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
            $table->editColumn('imei_model_number', function ($row) {
                return $row->imei_model_number ? $row->imei_model_number : '';
            });
            $table->editColumn('status', function ($row) {
                return $row->status ? ImeiModel::STATUS_SELECT[$row->status] : '';
            });

            $table->rawColumns(['actions', 'placeholder']);

            return $table->make(true);
        }

        return view('admin.imeiModels.index');
    }

    public function create()
    {
        abort_if(Gate::denies('imei_model_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.imeiModels.create');
    }

    public function store(StoreImeiModelRequest $request)
    {
        $imeiModel = ImeiModel::create($request->all());

        return redirect()->route('admin.imei-models.index');
    }

    public function edit(ImeiModel $imeiModel)
    {
        abort_if(Gate::denies('imei_model_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $imeiModel->load('team');

        return view('admin.imeiModels.edit', compact('imeiModel'));
    }

    public function update(UpdateImeiModelRequest $request, ImeiModel $imeiModel)
    {
        $imeiModel->update($request->all());

        return redirect()->route('admin.imei-models.index');
    }

    public function show(ImeiModel $imeiModel)
    {
        abort_if(Gate::denies('imei_model_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $imeiModel->load('team', 'imeiModelImeiMasters');

        return view('admin.imeiModels.show', compact('imeiModel'));
    }

    public function destroy(ImeiModel $imeiModel)
    {
        abort_if(Gate::denies('imei_model_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $imeiModel->delete();

        return back();
    }

    public function massDestroy(MassDestroyImeiModelRequest $request)
    {
        $imeiModels = ImeiModel::find(request('ids'));

        foreach ($imeiModels as $imeiModel) {
            $imeiModel->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}

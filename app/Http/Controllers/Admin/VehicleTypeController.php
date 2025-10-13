<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroyVehicleTypeRequest;
use App\Http\Requests\StoreVehicleTypeRequest;
use App\Http\Requests\UpdateVehicleTypeRequest;
use App\Models\VehicleType;
use Gate;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class VehicleTypeController extends Controller
{
    use MediaUploadingTrait, CsvImportTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('vehicle_type_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = VehicleType::with(['team'])->select(sprintf('%s.*', (new VehicleType)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'vehicle_type_show';
                $editGate      = 'vehicle_type_edit';
                $deleteGate    = 'vehicle_type_delete';
                $crudRoutePart = 'vehicle-types';

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
            $table->editColumn('vehicle_type', function ($row) {
                return $row->vehicle_type ? $row->vehicle_type : '';
            });
            $table->editColumn('vehicle_icon', function ($row) {
                if ($photo = $row->vehicle_icon) {
                    return sprintf(
                        '<a href="%s" target="_blank"><img src="%s" width="50px" height="50px"></a>',
                        $photo->url,
                        $photo->thumbnail
                    );
                }

                return '';
            });

            $table->rawColumns(['actions', 'placeholder', 'vehicle_icon']);

            return $table->make(true);
        }

        return view('admin.vehicleTypes.index');
    }

    public function create()
    {
        abort_if(Gate::denies('vehicle_type_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.vehicleTypes.create');
    }

    public function store(StoreVehicleTypeRequest $request)
    {
        $vehicleType = VehicleType::create($request->all());

        if ($request->input('vehicle_icon', false)) {
            $vehicleType->addMedia(storage_path('tmp/uploads/' . basename($request->input('vehicle_icon'))))->toMediaCollection('vehicle_icon');
        }

        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $vehicleType->id]);
        }

        return redirect()->route('admin.vehicle-types.index');
    }

    public function edit(VehicleType $vehicleType)
    {
        abort_if(Gate::denies('vehicle_type_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $vehicleType->load('team');

        return view('admin.vehicleTypes.edit', compact('vehicleType'));
    }

    public function update(UpdateVehicleTypeRequest $request, VehicleType $vehicleType)
    {
        $vehicleType->update($request->all());

        if ($request->input('vehicle_icon', false)) {
            if (! $vehicleType->vehicle_icon || $request->input('vehicle_icon') !== $vehicleType->vehicle_icon->file_name) {
                if ($vehicleType->vehicle_icon) {
                    $vehicleType->vehicle_icon->delete();
                }
                $vehicleType->addMedia(storage_path('tmp/uploads/' . basename($request->input('vehicle_icon'))))->toMediaCollection('vehicle_icon');
            }
        } elseif ($vehicleType->vehicle_icon) {
            $vehicleType->vehicle_icon->delete();
        }

        return redirect()->route('admin.vehicle-types.index');
    }

    public function show(VehicleType $vehicleType)
    {
        abort_if(Gate::denies('vehicle_type_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $vehicleType->load('team', 'vehicleTypeActivationRequests');

        return view('admin.vehicleTypes.show', compact('vehicleType'));
    }

    public function destroy(VehicleType $vehicleType)
    {
        abort_if(Gate::denies('vehicle_type_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $vehicleType->delete();

        return back();
    }

    public function massDestroy(MassDestroyVehicleTypeRequest $request)
    {
        $vehicleTypes = VehicleType::find(request('ids'));

        foreach ($vehicleTypes as $vehicleType) {
            $vehicleType->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('vehicle_type_create') && Gate::denies('vehicle_type_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model         = new VehicleType();
        $model->id     = $request->input('crud_id', 0);
        $model->exists = true;
        $media         = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }
}

<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\StoreVehicleTypeRequest;
use App\Http\Requests\UpdateVehicleTypeRequest;
use App\Http\Resources\Admin\VehicleTypeResource;
use App\Models\VehicleType;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VehicleTypeApiController extends Controller
{
    use MediaUploadingTrait;

    public function index()
    {
        abort_if(Gate::denies('vehicle_type_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new VehicleTypeResource(VehicleType::with(['team'])->get());
    }

    public function store(StoreVehicleTypeRequest $request)
    {
        $vehicleType = VehicleType::create($request->all());

        if ($request->input('vehicle_icon', false)) {
            $vehicleType->addMedia(storage_path('tmp/uploads/' . basename($request->input('vehicle_icon'))))->toMediaCollection('vehicle_icon');
        }

        return (new VehicleTypeResource($vehicleType))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(VehicleType $vehicleType)
    {
        abort_if(Gate::denies('vehicle_type_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new VehicleTypeResource($vehicleType->load(['team']));
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

        return (new VehicleTypeResource($vehicleType))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(VehicleType $vehicleType)
    {
        abort_if(Gate::denies('vehicle_type_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $vehicleType->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
    
    
    public function getAllVehicleTypes()
    {
        return \App\Models\VehicleType::with(['media'])->get()->map(function ($vehicle) {
            return [
                'id' => $vehicle->id,
                'vehicle_type' => $vehicle->vehicle_type,
                'icon_url' => $vehicle->vehicle_icon ? $vehicle->vehicle_icon->url : null,
                'icon_thumb' => $vehicle->vehicle_icon ? $vehicle->vehicle_icon->thumbnail : null,
                'icon_preview' => $vehicle->vehicle_icon ? $vehicle->vehicle_icon->preview : null,
            ];
        });
    }

}

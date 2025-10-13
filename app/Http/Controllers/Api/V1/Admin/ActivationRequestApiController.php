<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\StoreActivationRequestRequest;
use App\Http\Requests\UpdateActivationRequestRequest;
use App\Http\Resources\Admin\ActivationRequestResource;
use App\Models\ActivationRequest;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ActivationRequestApiController extends Controller
{
    use MediaUploadingTrait;

    public function index()
    {
        abort_if(Gate::denies('activation_request_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new ActivationRequestResource(ActivationRequest::with(['party_type', 'select_party', 'product', 'state', 'disrict', 'vehicle_type', 'team'])->get());
    }

    public function store(StoreActivationRequestRequest $request)
    {
        $activationRequest = ActivationRequest::create($request->all());

        if ($request->input('id_proofs', false)) {
            $activationRequest->addMedia(storage_path('tmp/uploads/' . basename($request->input('id_proofs'))))->toMediaCollection('id_proofs');
        }

        if ($request->input('customer_image', false)) {
            $activationRequest->addMedia(storage_path('tmp/uploads/' . basename($request->input('customer_image'))))->toMediaCollection('customer_image');
        }

        if ($request->input('vehicle_photos', false)) {
            $activationRequest->addMedia(storage_path('tmp/uploads/' . basename($request->input('vehicle_photos'))))->toMediaCollection('vehicle_photos');
        }

        if ($request->input('product_images', false)) {
            $activationRequest->addMedia(storage_path('tmp/uploads/' . basename($request->input('product_images'))))->toMediaCollection('product_images');
        }

        return (new ActivationRequestResource($activationRequest))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(ActivationRequest $activationRequest)
    {
        abort_if(Gate::denies('activation_request_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new ActivationRequestResource($activationRequest->load(['party_type', 'select_party', 'product', 'state', 'disrict', 'vehicle_type', 'team']));
    }

    public function update(UpdateActivationRequestRequest $request, ActivationRequest $activationRequest)
    {
        $activationRequest->update($request->all());

        if ($request->input('id_proofs', false)) {
            if (! $activationRequest->id_proofs || $request->input('id_proofs') !== $activationRequest->id_proofs->file_name) {
                if ($activationRequest->id_proofs) {
                    $activationRequest->id_proofs->delete();
                }
                $activationRequest->addMedia(storage_path('tmp/uploads/' . basename($request->input('id_proofs'))))->toMediaCollection('id_proofs');
            }
        } elseif ($activationRequest->id_proofs) {
            $activationRequest->id_proofs->delete();
        }

        if ($request->input('customer_image', false)) {
            if (! $activationRequest->customer_image || $request->input('customer_image') !== $activationRequest->customer_image->file_name) {
                if ($activationRequest->customer_image) {
                    $activationRequest->customer_image->delete();
                }
                $activationRequest->addMedia(storage_path('tmp/uploads/' . basename($request->input('customer_image'))))->toMediaCollection('customer_image');
            }
        } elseif ($activationRequest->customer_image) {
            $activationRequest->customer_image->delete();
        }

        if ($request->input('vehicle_photos', false)) {
            if (! $activationRequest->vehicle_photos || $request->input('vehicle_photos') !== $activationRequest->vehicle_photos->file_name) {
                if ($activationRequest->vehicle_photos) {
                    $activationRequest->vehicle_photos->delete();
                }
                $activationRequest->addMedia(storage_path('tmp/uploads/' . basename($request->input('vehicle_photos'))))->toMediaCollection('vehicle_photos');
            }
        } elseif ($activationRequest->vehicle_photos) {
            $activationRequest->vehicle_photos->delete();
        }

        if ($request->input('product_images', false)) {
            if (! $activationRequest->product_images || $request->input('product_images') !== $activationRequest->product_images->file_name) {
                if ($activationRequest->product_images) {
                    $activationRequest->product_images->delete();
                }
                $activationRequest->addMedia(storage_path('tmp/uploads/' . basename($request->input('product_images'))))->toMediaCollection('product_images');
            }
        } elseif ($activationRequest->product_images) {
            $activationRequest->product_images->delete();
        }

        return (new ActivationRequestResource($activationRequest))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(ActivationRequest $activationRequest)
    {
        abort_if(Gate::denies('activation_request_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $activationRequest->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
    
    // GET /api/v1/activation-requests-by-user/{customer_name}
    public function getActivationRequestsByUser($customer_name)
    {
        $requests = ActivationRequest::with([
            'party_type',
            'select_party',
            'product',          // ✅ Load product details here
            'product_master',   // ✅ Load product_master if needed
            'state',
            'disrict',
            'vehicle_type',
            'team',
            'created_by',
            'app_link'
        ])
        ->where('customer_name', $customer_name)
        ->get();
    
        return response()->json([
            'success' => true,
            'data' => $requests // ✅ Raw data with loaded relationships
        ]);
    }




}

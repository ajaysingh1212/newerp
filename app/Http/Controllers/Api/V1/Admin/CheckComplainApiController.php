<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\StoreCheckComplainRequest;
use App\Http\Requests\UpdateCheckComplainRequest;
use App\Http\Resources\Admin\CheckComplainResource;
use App\Models\CheckComplain;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckComplainApiController extends Controller
{
    use MediaUploadingTrait;

    public function index()
    {
        abort_if(Gate::denies('check_complain_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new CheckComplainResource(CheckComplain::with(['team'])->get());
    }

    public function store(StoreCheckComplainRequest $request)
    {
        $checkComplain = CheckComplain::create($request->all());

        foreach ($request->input('attechment', []) as $file) {
            $checkComplain->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('attechment');
        }

        return (new CheckComplainResource($checkComplain))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(CheckComplain $checkComplain)
    {
        abort_if(Gate::denies('check_complain_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new CheckComplainResource($checkComplain->load(['team']));
    }

    public function update(UpdateCheckComplainRequest $request, CheckComplain $checkComplain)
    {
        $checkComplain->update($request->all());

        if (count($checkComplain->attechment) > 0) {
            foreach ($checkComplain->attechment as $media) {
                if (! in_array($media->file_name, $request->input('attechment', []))) {
                    $media->delete();
                }
            }
        }
        $media = $checkComplain->attechment->pluck('file_name')->toArray();
        foreach ($request->input('attechment', []) as $file) {
            if (count($media) === 0 || ! in_array($file, $media)) {
                $checkComplain->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('attechment');
            }
        }

        return (new CheckComplainResource($checkComplain))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(CheckComplain $checkComplain)
    {
        abort_if(Gate::denies('check_complain_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $checkComplain->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
    
    
    public function storeComplain(Request $request)
    {
        // Validation
        $validated = $request->validate([
            'ticket_number' => 'required|string|unique:check_complains,ticket_number',
            'vehicle_no' => 'required|string',
            'customer_name' => 'required|string',
            'phone_number' => 'required|string',
            'reason' => 'required|string',
            'vehicle_id' => 'nullable|exists:add_customer_vehicles,id',
            'select_complain_ids' => 'nullable|array',
            'select_complain_ids.*' => 'exists:complain_categories,id',
            'created_by_id' => 'nullable|exists:users,id',
            'attechment.*' => 'file|mimes:jpg,jpeg,png,pdf,docx|max:2048',
        ]);
    
        // Create the record
        $complain = CheckComplain::create([
            'ticket_number' => $request->ticket_number,
            'vehicle_no' => $request->vehicle_no,
            'customer_name' => $request->customer_name,
            'phone_number' => $request->phone_number,
            'reason' => $request->reason,
            'status' => 'Pending',
            'vehicle_id' => $request->vehicle_id,
            'created_by_id' => $request->created_by_id ?? auth()->id(), // manual or fallback
        ]);
    
        // Attach complain categories
        if ($request->has('select_complain_ids')) {
            $complain->select_complains()->sync($request->select_complain_ids);
        }
    
        // Handle media uploads
        if ($request->hasFile('attechment')) {
            foreach ($request->file('attechment') as $file) {
                $complain->addMedia($file)->toMediaCollection('attechment');
            }
        }
    
        return response()->json([
            'message' => 'Complaint created successfully',
            'data' => [
                'id' => $complain->id,
                'ticket_number' => $complain->ticket_number,
            ]
        ], 201);
    }

    public function storeUserComplain(Request $request)
{
    $validated = $request->validate([
        'user_id'       => 'required|exists:users,id',
        'reason'        => 'required|string',
        'select_complain_ids'   => 'required|array|min:1',
        'select_complain_ids.*' => 'exists:complain_categories,id',
        'vehicle_no'    => 'nullable|string',
        'vehicle_id'    => 'nullable|exists:add_customer_vehicles,id',
        'attechment.*'  => 'file|mimes:jpg,jpeg,png,pdf,docx|max:2048',
    ]);

    $user = \App\Models\User::find($request->user_id);

    // AUTO ticket number: CMP2512012XX (No hyphen, month in number)
    $last = CheckComplain::latest('id')->first();
    $number = $last ? $last->id + 1 : 1;

    $day   = date('d');
    $month = date('m');  // month number
    $autoNo = str_pad($number, 3, '0', STR_PAD_LEFT);

    $ticket_number = "CMP{$day}{$month}{$autoNo}";

    $complain = CheckComplain::create([
        'ticket_number' => $ticket_number,
        'vehicle_no'    => $request->vehicle_no,
        'vehicle_id'    => $request->vehicle_id,
        'customer_name' => $user->name ?? null,
        'phone_number'  => $user->phone ?? $user->mobile ?? null,
        'reason'        => $request->reason,
        'status'        => 'Pending',
        'created_by_id' => $user->id,
    ]);

    $complain->select_complains()->sync($request->select_complain_ids);

    if ($request->hasFile('attechment')) {
        foreach ($request->file('attechment') as $file) {
            $complain->addMedia($file)->toMediaCollection('attechment');
        }
    }

    return response()->json([
        'status'  => true,
        'message' => 'User complaint submitted successfully.',
        'data'    => $complain
    ], 201);
}


    
    public function getComplaintsByUser($user_id)
    {
        $complaints = CheckComplain::with(['select_complains', 'vehicle', 'team'])
            ->where('created_by_id', $user_id)
            ->latest()
            ->get();
    
        return response()->json([
            'status' => true,
            'message' => 'Complaints fetched successfully.',
            'data' => $complaints
        ]);
    }







}

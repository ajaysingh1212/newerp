<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroyCheckComplainRequest;
use App\Http\Requests\StoreCheckComplainRequest;
use App\Http\Requests\UpdateCheckComplainRequest;
use App\Models\AddCustomerVehicle;
use App\Models\CheckComplain;
use App\Models\ComplainCategory;
use App\Models\ProductMaster;
use Gate;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use PDF;


class CheckComplainController extends Controller
{
    use MediaUploadingTrait, CsvImportTrait;

public function index(Request $request)
{
    abort_if(Gate::denies('check_complain_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

    $query = CheckComplain::with(['select_complains', 'created_by'])
        ->when(!auth()->user()->roles->contains('title', 'Admin'), function ($q) {
            $q->where('created_by_id', auth()->id());
        });

    // ✅ Quick range filter
    switch ($request->range) {
        case 'today':
            $query->whereDate('created_at', today());
            break;
        case 'yesterday':
            $query->whereDate('created_at', today()->subDay());
            break;
        case 'this_week':
            $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
            break;
        case 'this_month':
            $query->whereMonth('created_at', now()->month);
            break;
        case 'last_3_months':
            $query->whereBetween('created_at', [now()->subMonths(3), now()]);
            break;
        case 'last_6_months':
            $query->whereBetween('created_at', [now()->subMonths(6), now()]);
            break;
        case 'this_year':
            $query->whereYear('created_at', now()->year);
            break;
    }

    // ✅ Custom date range
    if ($request->filled('from_date') && $request->filled('to_date')) {
        $query->whereBetween('created_at', [$request->from_date, $request->to_date]);
    }

    // ✅ Status filter
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    $checkComplains = $query->latest()->get();

    return view('admin.checkComplains.index', compact('checkComplains'));
}



public function create()
{
    abort_if(Gate::denies('check_complain_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

    $select_complains = ComplainCategory::pluck('title', 'id');

    // Fetch vehicles created by the logged-in user
    $vehicles = AddCustomerVehicle::where('activated', 'activated')
        ->where('created_by_id', auth()->id()) // 👈 Filter by logged-in user
        ->with([
            'media' => function($query) {
                $query->where('collection_name', 'vehicle_photos');
            },
            'creator:id,name,mobile_number'
        ])
        ->get(['id', 'vehicle_number', 'created_at', 'status', 'vehicle_color', 'created_by_id']);

    return view('admin.checkComplains.create', compact('select_complains', 'vehicles'));
}


public function store(StoreCheckComplainRequest $request)
{
    // Generate unique ticket number
    $ticketNumber = strtoupper(Str::random(4)) . mt_rand(10000, 99999);

    // Merge ticket number + default status + created_by_id
    $data = array_merge(
        $request->all(),
        [
            'ticket_number' => $ticketNumber,
            'status' => 'Pending',
            'created_by_id' => auth()->id(), // 👈 Store logged-in user's ID
        ]
    );

    $checkComplain = CheckComplain::create($data);

    // Sync relationships
    $checkComplain->select_complains()->sync($request->input('select_complains', []));
    $checkComplain->select_vehicles()->sync($request->input('select_vehicles', []));

    // Add attachments
    if ($request->has('attechment')) {
        foreach ($request->input('attechment', []) as $file) {
            $filePath = storage_path('tmp/uploads/' . basename($file));
            if (file_exists($filePath)) {
                $checkComplain->addMedia($filePath)->toMediaCollection('attechment');
            }
        }
    }

    // Update CKEditor media
    if ($media = $request->input('ck-media', false)) {
        Media::whereIn('id', $media)->update(['model_id' => $checkComplain->id]);
    }

    return redirect()->route('admin.check-complains.index')
                     ->with('success', 'Complaint created with Ticket No: ' . $ticketNumber);
}



    public function edit(CheckComplain $checkComplain)
    {
        abort_if(Gate::denies('check_complain_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $select_complains = ComplainCategory::pluck('title', 'id');

        $select_vehicles = AddCustomerVehicle::pluck('vehicle_number', 'id');

        $checkComplain->load('select_complains', 'select_vehicles', 'team');

        return view('admin.checkComplains.edit', compact('checkComplain', 'select_complains', 'select_vehicles'));
    }

public function update(UpdateCheckComplainRequest $request, CheckComplain $checkComplain)
{
    // ✅ Update the main complaint record
    $checkComplain->update($request->all());

    // ✅ Handle select_complains relation
    $checkComplain->select_complains()->sync(
        $request->has('select_complains') 
            ? $request->input('select_complains') 
            : $checkComplain->select_complains->pluck('id')->toArray()
    );

    // ✅ Handle select_vehicles relation
    $checkComplain->select_vehicles()->sync(
        $request->has('select_vehicles') 
            ? $request->input('select_vehicles') 
            : $checkComplain->select_vehicles->pluck('id')->toArray()
    );

    // ✅ Handle media attachments
    if (count($checkComplain->attechment) > 0) {
        foreach ($checkComplain->attechment as $media) {
            if (! in_array($media->file_name, $request->input('attechment', []))) {
                $media->delete();
            }
        }
    }

    $existingMedia = $checkComplain->attechment->pluck('file_name')->toArray();
    foreach ($request->input('attechment', []) as $file) {
        if (count($existingMedia) === 0 || ! in_array($file, $existingMedia)) {
            $checkComplain->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('attechment');
        }
    }

    // ✅ Create User Alert
    $alertText = "Complaint #{$checkComplain->ticket_number} updated.\n"
        . "Customer: {$checkComplain->customer_name}\n"
        . "Vehicle: {$checkComplain->vehicle_no}\n"
        . "Status: {$checkComplain->status}\n"
        . "Reason: {$checkComplain->reason}\n"
        . ($checkComplain->notes ? "Notes: {$checkComplain->notes}\n" : '');

    $alert = \App\Models\UserAlert::create([
        'alert_text' => $alertText,
        'alert_link' => route('admin.check-complains.show', $checkComplain->id),
    ]);

    // ✅ Attach alert to the user who created this complaint (or update user)
    $alert->users()->sync([$checkComplain->created_by_id]);

    return redirect()->route('admin.check-complains.index')
                     ->with('success', 'Complaint updated successfully and user notified.');
}



   public function show(CheckComplain $checkComplain)
{
    abort_if(Gate::denies('check_complain_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

    $checkComplain->load([
        'select_complains',
        'select_vehicles.product_master.product_model',
        'select_vehicles.product_master.imei',
        'select_vehicles.product_master.vts',
        'team',
        'vehicle.product_master.product_model',
        'vehicle.product_master.imei',
        'vehicle.product_master.vts',
    ]);
    
    // dd($checkComplain->vehicle->product_master->vts);

    return view('admin.checkComplains.show', compact('checkComplain'));
}




    public function destroy(CheckComplain $checkComplain)
    {
        abort_if(Gate::denies('check_complain_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $checkComplain->delete();

        return back();
    }

    public function massDestroy(MassDestroyCheckComplainRequest $request)
    {
        $checkComplains = CheckComplain::find(request('ids'));

        foreach ($checkComplains as $checkComplain) {
            $checkComplain->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('check_complain_create') && Gate::denies('check_complain_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model         = new CheckComplain();
        $model->id     = $request->input('crud_id', 0);
        $model->exists = true;
        $media         = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }
    public function showPdf($id)
{
    $complain = CheckComplain::with('select_complains')->findOrFail($id);

    // Return a blade view that shows the complain details in a PDF-friendly format
    return view('admin.checkComplains.pdf', compact('complain'));
}

public function invoice($id)
{
    $checkComplain = CheckComplain::with([
        'select_complains',
        'select_vehicles.product_master.product_model',
        'select_vehicles.product_master.imei',
        'select_vehicles.product_master.vts',
        'team',
        'vehicle.product_master.product_model',
        'vehicle.product_master.imei',
        'vehicle.product_master.vts',
    ])->findOrFail($id);

    $pdf = PDF::loadView('admin.checkcomplains.pdf', compact('checkComplain'));

    // Optional: force portrait A4 page size, 10mm margins (you can configure in view CSS too)
    $pdf->setPaper('a4', 'portrait');

    // return the PDF as a download with a filename
    return $pdf->download('complain_invoice_' . $id . '.pdf');
}

}

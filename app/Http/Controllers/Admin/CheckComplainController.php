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

    if ($request->ajax()) {
        $query = CheckComplain::with(['select_complains', 'select_vehicles', 'team', 'created_by'])
            ->select(sprintf('%s.*', (new CheckComplain)->table));

        // ✅ Filter: Only show current user's data unless the user is Admin
        if (!auth()->user()->roles->contains('title', 'Admin')) {
            $query->where('created_by_id', auth()->id());
        }

        $table = Datatables::of($query);

        $table->addColumn('placeholder', '&nbsp;');
        $table->addColumn('actions', '&nbsp;');

        $table->editColumn('actions', function ($row) {
            $viewGate = 'check_complain_show';
            $editGate = 'check_complain_edit';
            $deleteGate = 'check_complain_delete';
            $crudRoutePart = 'check-complains';

            $showDelete = true;

            // ✅ Show delete only if status is "solved" or user is Admin
            if (!auth()->user()->roles->contains('title', 'Admin') && $row->status !== 'solved') {
                $showDelete = false;
            }

            return view('partials.datatablesActions', compact(
                'viewGate',
                'editGate',
                'deleteGate',
                'crudRoutePart',
                'row',
                'showDelete'
            ));
        });

        $table->editColumn('id', fn($row) => $row->id ?? '');

        $table->editColumn('select_complain', function ($row) {
            $labels = [];
            foreach ($row->select_complains as $select_complain) {
                $labels[] = sprintf('<span class="label label-info label-many">%s</span>', $select_complain->title);
            }
            return implode(' ', $labels);
        });

        $table->editColumn('ticket_number', fn($row) => $row->ticket_number ?? '');
        $table->editColumn('vehicle_no', fn($row) => $row->vehicle_no ?? '');
        $table->editColumn('customer_name', fn($row) => $row->customer_name ?? '');
        $table->editColumn('phone_number', fn($row) => $row->phone_number ?? '');
        $table->editColumn('status', fn($row) => $row->status ? CheckComplain::STATUS_SELECT[$row->status] : '');
        $table->addColumn('created_by_name', function ($row) {
            $name = $row->created_by?->name ?? '-';
            $mobile = $row->created_by?->mobile_number ?? '-';
            $roles = $row->created_by && $row->created_by->roles->isNotEmpty()
                ? $row->created_by->roles->pluck('title')->implode(', ')
                : '-';

            return "<strong>Name:</strong> {$name}<br><strong>Mobile:</strong> {$mobile}<br><strong>Role:</strong> {$roles}";
        });


        $table->editColumn('attechment', function ($row) {
            if (!$row->attechment) return '';
            $links = [];
            foreach ($row->attechment as $media) {
                $links[] = '<a href="' . $media->getUrl() . '" target="_blank">' . trans('global.downloadFile') . '</a>';
            }
            return implode(', ', $links);
        });

        $table->editColumn('admin_message', fn($row) => strip_tags($row->admin_message) ?? '-');


        $table->editColumn('created_at', fn($row) => $row->created_at ? $row->created_at->format('d-m-Y H:i') : '');
        $table->editColumn('updated_at', fn($row) => $row->updated_at ? $row->updated_at->format('d-m-Y H:i') : '');

        $table->addColumn('status_duration', function ($row) {
            $now = \Carbon\Carbon::now();
            $created = $row->created_at;
            $updated = $row->updated_at;
            $status = $row->status;

            if ($status === 'Pending') {
                return '<span class="text-danger blink">Pending since ' . $created->diffInDays($now) . ' days</span>';
            } elseif ($status === 'processing') {
                return '<span class="text-danger blink">Processing since ' . $updated->diffInDays($now) . ' days</span>';
            } elseif ($status === 'reject') {
                return '<span class="text-danger blink">Rejected ' . $updated->diffInDays($now) . ' days ago</span>';
            } elseif ($status === 'solved') {
                return '<span class="text-success">Solved ' . $updated->diffInDays($now) . ' days ago</span>';
            } else {
                return '-';
            }
        });

        $table->rawColumns(['actions', 'placeholder', 'select_complain', 'attechment', 'status_duration','created_by_name']);

        return $table->make(true);
    }

    return view('admin.checkComplains.index');
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
    // Update the main complaint record
    $checkComplain->update($request->all());

    // Handle select_complains: use submitted data or fallback to existing IDs
    $checkComplain->select_complains()->sync(
        $request->has('select_complains') 
            ? $request->input('select_complains') 
            : $checkComplain->select_complains->pluck('id')->toArray()
    );

    // Handle select_vehicles: same fallback logic if needed
    $checkComplain->select_vehicles()->sync(
        $request->has('select_vehicles') 
            ? $request->input('select_vehicles') 
            : $checkComplain->select_vehicles->pluck('id')->toArray()
    );

    // Handle media attachments
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

    return redirect()->route('admin.check-complains.index')
                     ->with('success', 'Complaint updated successfully.');
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

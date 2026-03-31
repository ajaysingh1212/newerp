<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Models\DeleteData;
use Gate;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class DeleteDataController extends Controller
{
    use CsvImportTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('delete_data_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {

            $query = DeleteData::query()->select(sprintf('%s.*', (new DeleteData)->getTable()));
            $table = DataTables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            // ✅ UPDATED ACTION ICONS
            $table->editColumn('actions', function ($row) {

                return '
                    <a href="'.route('admin.delete-data.show',$row->id).'" class="btn btn-xs btn-info">
                        <i class="fas fa-eye"></i>
                    </a>

                    <a href="'.route('admin.delete-data.edit',$row->id).'" class="btn btn-xs btn-warning">
                        <i class="fas fa-edit"></i>
                    </a>

                    <form action="'.route('admin.delete-data.destroy',$row->id).'" method="POST" style="display:inline;">
                        '.csrf_field().method_field("DELETE").'
                        <button class="btn btn-xs btn-danger" onclick="return confirm(\'Are you sure?\')">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                ';
            });

            // 🔹 OLD + NEW FIELDS
            $table->editColumn('id', fn($row) => $row->id ?? '');
            $table->editColumn('user_name', fn($row) => $row->user_name ?? '');
            $table->editColumn('number', fn($row) => $row->number ?? '');
            $table->editColumn('email', fn($row) => $row->email ?? '');
            $table->editColumn('product', fn($row) => $row->product ?? '');
            $table->editColumn('counter_name', fn($row) => $row->counter_name ?? '');
            $table->editColumn('vehicle_no', fn($row) => $row->vehicle_no ?? '');
            $table->editColumn('imei_no', fn($row) => $row->imei_no ?? '');
            $table->editColumn('vts_no', fn($row) => $row->vts_no ?? '');

            // NEW FIELDS 🔥
            $table->editColumn('owner_name', fn($row) => $row->owner_name ?? '');
            $table->editColumn('owner_phone', fn($row) => $row->owner_phone ?? '');
            $table->editColumn('sim_number', fn($row) => $row->sim_number ?? '');

            $table->editColumn('delete_date', fn($row) => $row->delete_date ? date('d-m-Y H:i', strtotime($row->delete_date)) : '-');
            $table->editColumn('date_of_fitting', fn($row) => $row->date_of_fitting ?? '-');
            $table->editColumn('expiry_date', fn($row) => $row->expiry_date ?? '-');

            $table->rawColumns(['actions', 'placeholder']);

            return $table->make(true);
        }

        return view('admin.delete_data.index');
    }

    public function create()
    {
        abort_if(Gate::denies('delete_data_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return view('admin.delete_data.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_name' => 'required|string|max:100',
            'number' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:150',
            'product' => 'nullable|string|max:100',
            'counter_name' => 'nullable|string|max:100',
            'vehicle_no' => 'nullable|string|max:50',
            'imei_no' => 'nullable|string|max:50',
            'vts_no' => 'nullable|string|max:50',
            'delete_date' => 'nullable|date',

            // NEW
            'owner_name' => 'nullable|string|max:100',
            'owner_phone' => 'nullable|string|max:20',
            'date_of_fitting' => 'nullable|date',
            'expiry_date' => 'nullable|date',
            'sim_number' => 'nullable|string|max:50',
            'reason_for_deletion' => 'nullable|string',
        ]);

        DeleteData::create($data);

        return redirect()->route('admin.delete-data.index')->with('success', 'Record added successfully!');
    }

    public function edit(DeleteData $deleteData)
    {
        abort_if(Gate::denies('delete_data_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return view('admin.delete_data.edit', compact('deleteData'));
    }

    public function show($id)
    {
        abort_if(Gate::denies('delete_data_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $deleteData = DeleteData::findOrFail($id);

        return view('admin.delete_data.show', compact('deleteData'));
    }

    public function update(Request $request, DeleteData $deleteData)
    {
        $data = $request->validate([
            'user_name' => 'required|string|max:100',
            'number' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:150',
            'product' => 'nullable|string|max:100',
            'counter_name' => 'nullable|string|max:100',
            'vehicle_no' => 'nullable|string|max:50',
            'imei_no' => 'nullable|string|max:50',
            'vts_no' => 'nullable|string|max:50',
            'delete_date' => 'nullable|date',

            // NEW
            'owner_name' => 'nullable|string|max:100',
            'owner_phone' => 'nullable|string|max:20',
            'date_of_fitting' => 'nullable|date',
            'expiry_date' => 'nullable|date',
            'sim_number' => 'nullable|string|max:50',
            'reason_for_deletion' => 'nullable|string',
        ]);

        $deleteData->update($data);

        return redirect()->route('admin.delete-data.index')->with('success', 'Record updated successfully!');
    }

    public function destroy(DeleteData $deleteData)
    {
        abort_if(Gate::denies('delete_data_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $deleteData->delete();

        return back()->with('success', 'Record deleted successfully!');
    }

    public function massDestroy(Request $request)
    {
        DeleteData::whereIn('id', $request->input('ids', []))->delete();
        return response()->noContent();
    }



public function parseCsvImport(Request $request)
{
    try {

        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:5120',
        ]);

        $mapping = $request->mapping ?? [];

        if (empty($mapping)) {
            return back()->with('error', '⚠️ Mapping select karo pehle');
        }

        $path = $request->file('csv_file')->getRealPath();
        $data = array_map('str_getcsv', file($path));

        if (count($data) < 2) {
            return back()->with('error', '⚠️ CSV empty hai');
        }

        // ✅ HEADER CLEAN
        $header = array_map(function ($h) {
            return strtolower(trim($h));
        }, array_shift($data));

        $count = 0;
        $errors = [];

        foreach ($data as $index => $row) {

            // ❌ Skip empty row
            if (empty(array_filter($row))) continue;

            if (count($row) != count($header)) {
                $errors[] = "Row ".($index+2)." column mismatch";
                continue;
            }

            $rowData = array_combine($header, $row);

            $insert = [];

            foreach ($mapping as $dbField => $csvField) {

                if (empty($csvField)) continue;

                $csvField = strtolower(trim($csvField));

                if (!array_key_exists($csvField, $rowData)) {
                    $errors[] = "Row ".($index+2)." → Column '$csvField' not found";
                    continue;
                }

                $value = $rowData[$csvField] ?? null;

                // 🔥 DATE FIX LOGIC
                if (in_array($dbField, ['delete_date','date_of_fitting','expiry_date'])) {

                    try {

                        if (!empty($value)) {

                            // Fix invalid minutes (e.g. 17:75)
                            if (preg_match('/(\d{2}):(\d{2})$/', $value, $matches)) {
                                $hour = (int)$matches[1];
                                $minute = (int)$matches[2];

                                if ($minute > 59) {
                                    $extraHour = floor($minute / 60);
                                    $minute = $minute % 60;
                                    $hour = $hour + $extraHour;

                                    $value = preg_replace(
                                        '/\d{2}:\d{2}$/',
                                        sprintf('%02d:%02d', $hour, $minute),
                                        $value
                                    );
                                }
                            }

                            // Try multiple formats
                            $formats = [
                                'd-m-Y H:i',
                                'd-m-Y',
                                'Y-m-d H:i:s',
                                'Y-m-d'
                            ];

                            $parsed = null;

                            foreach ($formats as $format) {
                                try {
                                    $parsed = Carbon::createFromFormat($format, $value);
                                    break;
                                } catch (\Exception $e) {}
                            }

                            if ($parsed) {
                                $value = $parsed->format('Y-m-d H:i:s');
                            } else {
                                throw new \Exception("Invalid format");
                            }
                        }

                    } catch (\Exception $e) {
                        $errors[] = "Row ".($index+2)." Invalid date: ".$value;
                        $value = null;
                    }
                }

                $insert[$dbField] = $value;
            }

            try {
                DeleteData::create($insert);
                $count++;
            } catch (\Exception $e) {
                $errors[] = "Row ".($index+2)." DB Error: ".$e->getMessage();
            }
        }

        return back()->with([
            'success' => "✅ $count records imported",
            'import_errors' => $errors
        ]);

    } catch (\Exception $e) {
        return back()->with('error', '🚨 '.$e->getMessage());
    }
}
}

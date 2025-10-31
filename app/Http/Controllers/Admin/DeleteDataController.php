<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Models\DeleteData;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class DeleteDataController extends Controller
{
    use CsvImportTrait;

    /**
     * Display a listing of deleted data (with Ajax + DataTables)
     */
    public function index(Request $request)
    {
        abort_if(Gate::denies('delete_data_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = DeleteData::query()->select(sprintf('%s.*', (new DeleteData)->getTable()));
            $table = DataTables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            // 🔹 Actions column
            $table->editColumn('actions', function ($row) {
                $viewGate = 'delete_data_show';
                $editGate = 'delete_data_edit';
                $deleteGate = 'delete_data_delete';
                $crudRoutePart = 'delete-data';

                return view('partials.datatablesActions', compact(
                    'viewGate',
                    'editGate',
                    'deleteGate',
                    'crudRoutePart',
                    'row'
                ));
            });

            // 🔹 Table columns
            $table->editColumn('id', fn($row) => $row->id ?? '');
            $table->editColumn('user_name', fn($row) => $row->user_name ?? '');
            $table->editColumn('number', fn($row) => $row->number ?? '');
            $table->editColumn('email', fn($row) => $row->email ?? '');
            $table->editColumn('product', fn($row) => $row->product ?? '');
            $table->editColumn('counter_name', fn($row) => $row->counter_name ?? '');
            $table->editColumn('vehicle_no', fn($row) => $row->vehicle_no ?? '');
            $table->editColumn('imei_no', fn($row) => $row->imei_no ?? '');
            $table->editColumn('vts_no', fn($row) => $row->vts_no ?? '');
            $table->editColumn('delete_date', fn($row) => $row->delete_date ? date('d-m-Y H:i', strtotime($row->delete_date)) : '-');

            $table->rawColumns(['actions', 'placeholder']);
            return $table->make(true);
        }

        return view('admin.delete_data.index');
    }

    /**
     * Show the form for creating a new record.
     */
    public function create()
    {
        abort_if(Gate::denies('delete_data_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.delete_data.create');
    }

    /**
     * Store new record
     */
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
        ]);

        DeleteData::create($data);

        return redirect()->route('admin.delete-data.index')->with('success', 'Record added successfully!');
    }

    /**
     * Edit existing record
     */
    public function edit(DeleteData $deleteData)
    {
        abort_if(Gate::denies('delete_data_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.delete_data.edit', compact('deleteData'));
    }

    /**
     * Update existing record
     */
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
        ]);

        $deleteData->update($data);

        return redirect()->route('admin.delete-data.index')->with('success', 'Record updated successfully!');
    }

    /**
     * Delete a single record
     */
    public function destroy(DeleteData $deleteData)
    {
        abort_if(Gate::denies('delete_data_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $deleteData->delete();

        return back()->with('success', 'Record deleted successfully!');
    }

    /**
     * Mass Delete
     */
    public function massDestroy(Request $request)
    {
        DeleteData::whereIn('id', $request->input('ids', []))->delete();
        return response()->noContent();
    }

    /**
     * CSV Import
     */
 public function parseCsvImport(Request $request)
{
    try {
        // ✅ Validate CSV file
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:5120',
        ]);

        $path = $request->file('csv_file')->getRealPath();

        // ✅ Read file and convert to array
        $data = array_map('str_getcsv', file($path, FILE_SKIP_EMPTY_LINES));

        if (empty($data) || count($data) < 2) {
            return back()->with('error', '⚠️ The uploaded CSV file is empty or invalid.');
        }

        // ✅ Extract and clean header row
        $header = array_map(function ($h) {
            $h = trim($h);
            $h = str_replace(["\xC2\xA0", "\u{A0}", '–', '-'], '', $h); // remove non-breaking spaces or special chars
            $h = strtolower($h);
            $h = str_replace([' ', '.', '/'], '_', $h); // normalize spaces
            return $h;
        }, array_shift($data));

        // ✅ Remove any blank header cells
        $header = array_filter($header);

        // ✅ Expected header structure
        $expected = [
            'user_name', 'number', 'email', 'product',
            'counter_name', 'vehicle_no', 'imei_no', 'vts_no', 'delete_date'
        ];

        // ✅ Check header mismatch
        if ($header !== $expected) {
            \Log::error('CSV Header Mismatch:', [
                'received' => $header,
                'expected' => $expected
            ]);

            return back()->with('error', '⚠️ Invalid CSV header! Please check the column names and order.');
        }

        // ✅ Import data
        $count = 0;
        foreach ($data as $index => $row) {
            if (count($row) < count($expected)) {
                \Log::warning("Skipping incomplete row at line " . ($index + 2), ['row' => $row]);
                continue;
            }

            $rowData = @array_combine($expected, $row);
            if (!$rowData) continue;

            \App\Models\DeleteData::create([
                'user_name'    => $rowData['user_name'] ?? null,
                'number'       => $rowData['number'] ?? null,
                'email'        => $rowData['email'] ?? null,
                'product'      => $rowData['product'] ?? null,
                'counter_name' => $rowData['counter_name'] ?? null,
                'vehicle_no'   => $rowData['vehicle_no'] ?? null,
                'imei_no'      => $rowData['imei_no'] ?? null,
                'vts_no'       => $rowData['vts_no'] ?? null,
                'delete_date'  => !empty($rowData['delete_date']) ? $rowData['delete_date'] : now(),
            ]);

            $count++;
        }

        \Log::info("CSV Import completed successfully. Total inserted: {$count}");

        return redirect()->route('admin.delete-data.index')
            ->with('success', "✅ $count records imported successfully!");

    } catch (\Throwable $e) {
        \Log::error('CSV Import Failed: ' . $e->getMessage(), [
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ]);

        return back()->with('error', '🚨 Something went wrong during import. Check the log for details.');
    }
}


}

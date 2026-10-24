<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\MasterReportImporter;
use App\Services\PreparedBusinessWorkbook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PreparedBusinessImportController extends Controller
{
    private function manifest(Request $request, PreparedBusinessWorkbook $reader): array
    {
        Gate::authorize('business_management_import');
        // This import preserves all worksheets rather than a small flat CSV.
        // Raise limits only for this authorized request, never globally.
        $limit = ini_get('memory_limit');
        if ($limit !== '-1') {
            ini_set('memory_limit', '1024M');
        }
        set_time_limit(300);
        $request->validate(['ready_file' => ['required', 'file', 'mimes:xlsx', 'max:10240']]);

        return $reader->read($request->file('ready_file')->getRealPath());
    }

    public function preview(Request $request, PreparedBusinessWorkbook $reader)
    {
        $manifest = $this->manifest($request, $reader);

        return response()->json(['devices' => count($manifest['current_source_indexes']), 'parties' => count($manifest['parties']), 'source_rows' => count($manifest['rows']), 'conflicts' => $manifest['summary']['conflicting_devices']]);
    }

    public function store(Request $request, PreparedBusinessWorkbook $reader, MasterReportImporter $importer)
    {
        $manifest = $this->manifest($request, $reader);
        $file = $request->file('ready_file');
        $batch = $importer->import($manifest, $file->getRealPath(), auth()->id(), true, $file->getClientOriginalName());

        return redirect()->route('admin.business-imports.index', ['batch_id' => $batch->id])->with('status', 'Ready workbook imported. All source rows preserved; existing devices were retained and any conflicts are flagged in the source ledger.');
    }
}

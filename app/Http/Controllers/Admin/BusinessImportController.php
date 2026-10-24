<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BusinessImport;
use App\Models\BusinessImportRow;
use App\Models\ManualParty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class BusinessImportController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('business_management_report');
        $request->validate([
            'batch_id' => ['nullable', 'integer', 'exists:business_imports,id'], 'party_id' => ['nullable', 'integer'],
            'sheet' => ['nullable', 'string', 'max:100'], 'kind' => ['nullable', Rule::in(['sale', 'summary', 'payment', 'source', 'header', 'control', 'template', 'note'])],
            'review' => ['nullable', Rule::in(['1'])], 'export' => ['nullable', Rule::in(['csv'])],
        ]);
        $batches = BusinessImport::latest('id')->get();
        $batch = $batches->firstWhere('id', $request->input('batch_id', $batches->first()?->id));
        $query = BusinessImportRow::where('business_import_id', $batch?->id);
        foreach (['party_id' => 'manual_party_id', 'sheet' => 'sheet', 'kind' => 'kind'] as $filter => $column) {
            if ($request->filled($filter)) {
                $query->where($column, $request->input($filter));
            }
        }
        if ($request->input('review') === '1') {
            $query->where('needs_review', true);
        }
        if ($request->input('export') === 'csv') {
            return response()->streamDownload(function () use ($query) {
                $file = fopen('php://output', 'w');
                fputcsv($file, ['Sheet', 'Row', 'Party ID', 'Record type', 'Normalized fields', 'Original cells and formulas', 'Review notes']);
                foreach ($query->orderBy('id')->cursor() as $row) {
                    fputcsv($file, [$row->sheet, $row->row_number, $row->manual_party_id, $row->kind,
                        json_encode($row->normalized), json_encode($row->source_cells), json_encode($row->issues)]);
                }
                fclose($file);
            }, 'business-source-ledger.csv', ['Content-Type' => 'text/csv']);
        }
        if ($request->ajax()) {
            return DataTables::eloquent($query->select(['business_import_rows.id', 'sheet', 'row_number', 'manual_party_id', 'kind', 'vts_number', 'normalized', 'issues', 'needs_review'])->with('party:id,name'))
                ->makeHidden(['party', 'normalized', 'issues'])
                ->addColumn('party_name', fn ($row) => $row->party?->name ?? 'Workbook summary')
                ->addColumn('model', fn ($row) => $row->normalized['product_name'] ?? '')
                ->addColumn('quantity', fn ($row) => $row->normalized['quantity'] ?? '')
                ->addColumn('sale_date', fn ($row) => $row->normalized['assigned_at'] ?? '')
                ->addColumn('sales_value', fn ($row) => $row->normalized['sales_value'] ?? '')
                ->addColumn('review_notes', fn ($row) => implode(' ', $row->issues))
                ->addColumn('detail_url', fn ($row) => route('admin.business-imports.show', $row))
                ->filter(function ($query) use ($request) {
                    $search = $request->input('search.value');
                    if (is_string($search) && $search !== '') {
                        $query->where(fn ($q) => $q->where('normalized', 'like', '%'.$search.'%')->orWhere('sheet', 'like', '%'.$search.'%')->orWhere('issues', 'like', '%'.$search.'%')->orWhereHas('party', fn ($p) => $p->where('name', 'like', '%'.$search.'%')));
                    }
                })->escapeColumns([])->toJson();
        }
        $parties = ManualParty::withTrashed()->orderBy('name')->get(['id', 'name']);
        $sheets = BusinessImportRow::where('business_import_id', $batch?->id)->distinct()->orderBy('sheet')->pluck('sheet');

        return view('admin.business-management.ledger', compact('batch', 'batches', 'parties', 'sheets'));
    }

    public function show(BusinessImportRow $businessImportRow)
    {
        Gate::authorize('business_management_report');
        $businessImportRow->load('party');
        $header = BusinessImportRow::where('business_import_id', $businessImportRow->business_import_id)->where('sheet', $businessImportRow->sheet)->where('row_number', 4)->first();

        return view('admin.business-management.source-row', compact('businessImportRow', 'header'));
    }

    public function download(BusinessImport $businessImport)
    {
        Gate::authorize('business_management_report');

        return Storage::disk('local')->download($businessImport->archive_path, $businessImport->file_name);
    }
}

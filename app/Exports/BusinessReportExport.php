<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Cell\StringValueBinder;

class BusinessReportExport extends StringValueBinder implements FromCollection, WithCustomValueBinder, WithHeadings
{
    public function __construct(private $query) {}

    public function headings(): array
    {
        return ['Party', 'State', 'District', 'City', 'Model', 'VTS number', 'IMEI number', 'SIM number', 'Sales price', 'Assigned date', 'Status', 'Activated by', 'Activated at'];
    }

    public function collection()
    {
        return $this->query->with(['party', 'product', 'activator'])->get()->map(fn ($device) => [
            $device->party?->name, $device->party?->state, $device->party?->district, $device->party?->city,
            $device->product?->name, $device->vts_number, $device->imei_number, $device->sim_number, $device->sales_price,
            $device->assigned_at?->format('Y-m-d'), $device->status, $device->activator?->name, $device->activated_at?->format('Y-m-d H:i:s'),
        ]);
    }
}

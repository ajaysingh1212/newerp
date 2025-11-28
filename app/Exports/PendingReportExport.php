<?php
namespace App\Exports;

use App\Models\Registration;
use App\Models\Investment;
use App\Models\WithdrawalRequest;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Collection;

class PendingReportExport implements FromCollection, WithHeadings
{
    public function headings(): array
    {
        return [
            'Type',
            'ID',
            'Name / Investor',
            'Extra Info',
            'Status',
            'Created At'
        ];
    }

    public function collection()
    {
        $data = new Collection();

        // Pending Registrations
        foreach (Registration::where('account_status', 'Active')->get() as $r) {
            $data->push([
                'Registration',
                $r->id,
                $r->reg,
                $r->investor->name ?? 'N/A',
                $r->aadhaar_number,
                $r->pan_card_number,
                $r->kyc_status,
                $r->created_at,
                $r->bank_account_number,
                $r->bank_account_holder_name,
                $r->ifsc_code,
                $r->bank_name,
                $r->bank_branch,
                $r->account_status,
                $r->is_email_verified,
                $r->is_phone_verified,
              
                

            ]);
        }

        // Pending Investments
        foreach (Investment::where('status', 'active')->with('select_investor')->get() as $i) {
            $data->push([
                'Investment',
                $i->id,
                $i->select_investor->investor->name ?? 'N/A',
                $i->principal_amount,
                $i->status,
                $i->created_at,
            ]);
        }

        // Pending Withdrawals
        foreach (WithdrawalRequest::where('status', 'pending')->with('select_investor')->get() as $w) {
            $data->push([
                'Withdrawal',
                $w->id,
                $w->select_investor->investor->name ?? 'N/A',
                $w->amount,
                $w->type,
                $w->requested_at,
                $w->status,
                $w->created_at,
            ]);
        }

        return $data;
    }
}

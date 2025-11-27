<?php

namespace App\Models;

use App\Traits\Auditable;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Investment extends Model
{
    use SoftDeletes, Auditable, HasFactory;

    protected $table = 'investments';

    protected $dates = [
        'start_date',
        'lockin_end_date',
        'next_payout_date',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public const STATUS_SELECT = [
        'active'             => 'active',
        'completed'          => 'completed',
        'withdrawn'          => 'withdrawn',
        'withdraw_requested' => 'withdraw_requested',
        'pending'            => 'pending',
    ];

    protected $fillable = [
        'select_investor_id',
        'select_plan_id',
        'principal_amount',
        'secure_interest_percent',
        'market_interest_percent',
        'total_interest_percent',
        'start_date',
        'lockin_end_date',
        'next_payout_date',
        'status',
        'created_by_id',
        'deleted_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    /*---------------------------------------------
    | RELATIONSHIPS
    ----------------------------------------------*/

    // Monthly interest payouts (records)
    public function investmentMonthlyPayoutRecords()
    {
        return $this->hasMany(MonthlyPayoutRecord::class, 'investment_id', 'id');
    }

    // Withdrawal requests for this investment
    public function investmentWithdrawalRequests()
    {
        return $this->hasMany(WithdrawalRequest::class, 'investment_id', 'id');
    }

    // Investor Transactions (depends on your table structure)
    public function investorTransactions()
    {
        return $this->hasMany(Transaction::class, 'investment_id', 'id');
    }

    // Selected Investor (registration)
    public function select_investor()
    {
        return $this->belongsTo(Registration::class, 'select_investor_id', 'id');
    }

    // Selected Plan
    public function select_plan()
    {
        return $this->belongsTo(Plan::class, 'select_plan_id', 'id');
    }

    // Created By
    public function created_by()
    {
        return $this->belongsTo(User::class, 'created_by_id', 'id');
    }

    /*---------------------------------------------
    | DATE MUTATORS (UPDATED)
    ----------------------------------------------*/

    private function flexibleDateSetter($value)
    {
        if (!$value) {
            return null;
        }

        $value = trim($value);

        try {
            return Carbon::parse(str_replace('/', '-', $value))->format('Y-m-d');
        } catch (\Exception $e) {
            try {
                return Carbon::parse($value)->format('Y-m-d');
            } catch (\Exception $e2) {
                return null;
            }
        }
    }

    private function flexibleDateGetter($value)
    {
        return $value ? Carbon::parse($value)
            ->format(config('panel.date_format')) : null;
    }

    /* ---- start_date ---- */
    public function setStartDateAttribute($value)
    {
        $this->attributes['start_date'] = $this->flexibleDateSetter($value);
    }

    public function getStartDateAttribute($value)
    {
        return $this->flexibleDateGetter($value);
    }

    /* ---- lockin_end_date ---- */
    public function setLockinEndDateAttribute($value)
    {
        $this->attributes['lockin_end_date'] = $this->flexibleDateSetter($value);
    }

    public function getLockinEndDateAttribute($value)
    {
        return $this->flexibleDateGetter($value);
    }

    /* ---- next_payout_date ---- */
    public function setNextPayoutDateAttribute($value)
    {
        $this->attributes['next_payout_date'] = $this->flexibleDateSetter($value);
    }

    public function getNextPayoutDateAttribute($value)
    {
        return $this->flexibleDateGetter($value);
    }
}

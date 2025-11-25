<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\MultiTenantModelTrait;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Investment extends Model
{
    use SoftDeletes, MultiTenantModelTrait, Auditable, HasFactory;

    public $table = 'investments';

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
        'created_at',
        'updated_at',
        'deleted_at',
        'created_by_id',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function investmentMonthlyPayoutRecords()
    {
        return $this->hasMany(MonthlyPayoutRecord::class, 'investment_id', 'id');
    }

    public function investmentWithdrawalRequests()
    {
        return $this->hasMany(WithdrawalRequest::class, 'investment_id', 'id');
    }

    public function investorTransactions()
    {
        return $this->hasMany(Transaction::class, 'investor_id', 'id');
    }

    public function select_investor()
    {
        return $this->belongsTo(Registration::class, 'select_investor_id');
    }

    public function select_plan()
    {
        return $this->belongsTo(Plan::class, 'select_plan_id');
    }

    public function getStartDateAttribute($value)
    {
        return $value ? Carbon::parse($value)->format(config('panel.date_format')) : null;
    }

    public function setStartDateAttribute($value)
    {
        $this->attributes['start_date'] = $value ? Carbon::createFromFormat(config('panel.date_format'), $value)->format('Y-m-d') : null;
    }

    public function getLockinEndDateAttribute($value)
    {
        return $value ? Carbon::parse($value)->format(config('panel.date_format')) : null;
    }

    public function setLockinEndDateAttribute($value)
    {
        $this->attributes['lockin_end_date'] = $value ? Carbon::createFromFormat(config('panel.date_format'), $value)->format('Y-m-d') : null;
    }

    public function getNextPayoutDateAttribute($value)
    {
        return $value ? Carbon::parse($value)->format(config('panel.date_format')) : null;
    }

    public function setNextPayoutDateAttribute($value)
    {
        $this->attributes['next_payout_date'] = $value ? Carbon::createFromFormat(config('panel.date_format'), $value)->format('Y-m-d') : null;
    }

    public function created_by()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }
}

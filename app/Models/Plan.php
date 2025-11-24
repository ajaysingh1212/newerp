<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\MultiTenantModelTrait;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Plan extends Model
{
    use SoftDeletes, MultiTenantModelTrait, Auditable, HasFactory;

    public $table = 'plans';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public const STATUS_SELECT = [
        'Active'   => 'Active',
        'Inactive' => 'Inactive',
    ];

    public const PAYOUT_FREQUENCY_SELECT = [
        'Weekly'  => 'Weekly',
        'Monthly' => 'Monthly',
        'Yearly'  => 'Yearly',
    ];

    protected $fillable = [
        'plan_name',
        'secure_interest_percent',
        'market_interest_percent',
        'total_interest_percent',
        'payout_frequency',
        'min_invest_amount',
        'max_invest_amount',
        'lockin_days',
        'withdraw_processing_hours',
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

    public function selectPlanInvestments()
    {
        return $this->hasMany(Investment::class, 'select_plan_id', 'id');
    }

    public function created_by()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }
}
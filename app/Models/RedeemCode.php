<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RedeemCode extends Model
{
    use SoftDeletes, HasFactory;

    public $table = 'redeem_codes';

    protected $dates = [
        'valid_up_to',
        'used_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'recharge_plan_id',
        'code',
        'valid_up_to',
        'discount_type',
        'discount_value',
        'discount_amount',
        'status',
        'use_status',
        'used_by_id',
        'recharge_request_id',
        'used_at',
        'created_by_id',
        'creator_name',
        'creator_role',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function rechargePlan()
    {
        return $this->belongsTo(RechargePlan::class, 'recharge_plan_id');
    }

    public function usedBy()
    {
        return $this->belongsTo(User::class, 'used_by_id');
    }

    public function rechargeRequest()
    {
        return $this->belongsTo(RechargeRequest::class, 'recharge_request_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function calculateDiscount(float $planPrice): float
    {
        $discount = $this->discount_type === 'percent'
            ? ($planPrice * $this->discount_value / 100)
            : $this->discount_value;

        return round(min(max($discount, 0), $planPrice), 2);
    }
}

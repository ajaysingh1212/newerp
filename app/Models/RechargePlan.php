<?php

namespace App\Models;

use App\Traits\MultiTenantModelTrait;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RechargePlan extends Model
{
    use SoftDeletes, MultiTenantModelTrait, HasFactory;

    public $table = 'recharge_plans';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'type',
        'plan_name',
        'amc_duration',
        'warranty_duration',
        'subscription_duration',
        'discription',
        'price',
        'created_at',
        'updated_at',
        'deleted_at',
        'team_id',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function selectRechargeRechargeRequests()
    {
        return $this->hasMany(RechargeRequest::class, 'select_recharge_id', 'id');
    }

    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }
}
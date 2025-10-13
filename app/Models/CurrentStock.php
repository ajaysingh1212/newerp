<?php

namespace App\Models;

use App\Traits\MultiTenantModelTrait;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CurrentStock extends Model
{
    use SoftDeletes, MultiTenantModelTrait, HasFactory;

    public $table = 'current_stocks';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'sku',
        'product_id',
        'created_at',
        'updated_at',
        'deleted_at',
        'team_id',
        'transfer_user_id',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function productRechargeRequests()
    {
        return $this->hasMany(RechargeRequest::class, 'product_id', 'id');
    }

    public function selectProductStockTransfers()
    {
        return $this->belongsToMany(StockTransfer::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }
    public function product()
{
    return $this->belongsTo(\App\Models\ProductMaster::class, 'sku', 'sku');
}
public function select_parties()
{
    return $this->belongsToMany(User::class, 'check_party_stock_user', 'check_party_stock_id', 'user_id');
}

public function productById()
{
    return $this->belongsTo(\App\Models\ProductMaster::class, 'product_id');
}

// Add this method inside your CurrentStock model

public function transferUser()
{
    return $this->belongsTo(\App\Models\User::class, 'transfer_user_id', 'id');
}



}

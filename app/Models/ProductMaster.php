<?php

namespace App\Models;

use App\Traits\MultiTenantModelTrait;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductMaster extends Model
{
use SoftDeletes, MultiTenantModelTrait, HasFactory;

public $table = 'product_masters';

protected $dates = [
    'created_at',
    'updated_at',
    'deleted_at',
];

public const STATUS_SELECT = [
    'enable'  => 'Enable',
    'disable' => 'Disable',
];

protected $fillable = [
    'product_model_id',
    'imei_id',
    'vts_id',
    'warranty',
    'subscription',
    'amc',
    'status',
    'created_at',
    'updated_at',
    'deleted_at',
    'team_id',
    'sku',
];

protected function serializeDate(DateTimeInterface $date)
{
    return $date->format('Y-m-d H:i:s');
}

public function product_model()
{
    return $this->belongsTo(ProductModel::class, 'product_model_id');
}

public function imei()
{
    return $this->belongsTo(ImeiMaster::class, 'imei_id');
}

public function vts()
{
    return $this->belongsTo(Vt::class, 'vts_id');
}

public function team()
{
    return $this->belongsTo(Team::class, 'team_id');
}

public function productModel()
{
    return $this->belongsTo(ProductModel::class, 'product_model_id', 'id');
}

}

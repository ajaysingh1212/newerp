<?php

namespace App\Models;

use App\Traits\MultiTenantModelTrait;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductModel extends Model
{
    use SoftDeletes, MultiTenantModelTrait, HasFactory;

    public $table = 'product_models';

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
        'product_model',
        'warranty',
        'subscription',
        'amc',
        'mrp',
        'cnf_price',
        'distributor_price',
        'dealer_price',
        'customer_price',
        'status',
        'created_at',
        'updated_at',
        'deleted_at',
        'team_id',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function productModelProductMasters()
    {
        return $this->hasMany(ProductMaster::class, 'product_model_id', 'id');
    }

    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }
}

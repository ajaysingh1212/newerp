<?php

namespace App\Models;

use App\Traits\MultiTenantModelTrait;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ImeiMaster extends Model
{
    use SoftDeletes, MultiTenantModelTrait, HasFactory;

    public $table = 'imei_masters';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public const STATUS_SELECT = [
        'Enable'  => 'Enable',
        'Disable' => 'Disable',
    ];

    public const PRODUCT_STATUS_SELECT = [
        'Not Used' => 'Not Used',
        'Used'     => 'Used',
    ];

    protected $fillable = [
        'imei_model_id',
        'imei_number',
        'status',
        'product_status',
        'created_at',
        'updated_at',
        'deleted_at',
        'team_id',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function imeiProductMasters()
    {
        return $this->hasMany(ProductMaster::class, 'imei_id', 'id');
    }

    public function imei_model()
    {
        return $this->belongsTo(ImeiModel::class, 'imei_model_id');
    }

    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }
}

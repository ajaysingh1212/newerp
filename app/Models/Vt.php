<?php

namespace App\Models;

use App\Traits\MultiTenantModelTrait;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vt extends Model
{
    use SoftDeletes, MultiTenantModelTrait, HasFactory;

    public $table = 'vts';

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
        'Not Formed' => 'Not Formed',
        'Formed'     => 'Formed',
    ];

    protected $fillable = [
        'vts_number',
        'sim_number',
        'operator',
        'product_status',
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

    public function vtsProductMasters()
    {
        return $this->hasMany(ProductMaster::class, 'vts_id', 'id');
    }

    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }
}

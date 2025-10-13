<?php

namespace App\Models;

use App\Traits\MultiTenantModelTrait;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ImeiModel extends Model
{
    use SoftDeletes, MultiTenantModelTrait, HasFactory;

    public $table = 'imei_models';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public const STATUS_SELECT = [
        'Enable'  => 'Enable',
        'Disable' => 'Disable',
    ];

    protected $fillable = [
        'imei_model_number',
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

    public function imeiModelImeiMasters()
    {
        return $this->hasMany(ImeiMaster::class, 'imei_model_id', 'id');
    }

    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }
}

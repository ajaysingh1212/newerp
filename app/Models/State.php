<?php

namespace App\Models;

use App\Traits\MultiTenantModelTrait;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class State extends Model
{
    use SoftDeletes, MultiTenantModelTrait, HasFactory;

    public $table = 'states';

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
        'state_name',
        'country',
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

    public function selectStateDistricts()
    {
        return $this->hasMany(District::class, 'select_state_id', 'id');
    }

    public function stateActivationRequests()
    {
        return $this->hasMany(ActivationRequest::class, 'state_id', 'id');
    }

    public function stateUsers()
    {
        return $this->hasMany(User::class, 'state_id', 'id');
    }

    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }
}

<?php

namespace App\Models;

use App\Traits\MultiTenantModelTrait;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class District extends Model
{
    use SoftDeletes, MultiTenantModelTrait, HasFactory;

    public $table = 'districts';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'districts',
        'country',
        'select_state_id',
        'created_at',
        'updated_at',
        'deleted_at',
        'team_id',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function disrictActivationRequests()
    {
        return $this->hasMany(ActivationRequest::class, 'disrict_id', 'id');
    }

    public function select_state()
    {
        return $this->belongsTo(State::class, 'select_state_id');
    }

    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }
    
}

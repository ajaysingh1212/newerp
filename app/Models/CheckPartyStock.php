<?php

namespace App\Models;

use App\Traits\MultiTenantModelTrait;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CheckPartyStock extends Model
{
    use SoftDeletes, MultiTenantModelTrait, HasFactory;

    public $table = 'check_party_stocks';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'created_at',
        'updated_at',
        'deleted_at',
        'team_id',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

public function select_parties()
{
    return $this->belongsToMany(User::class, 'check_party_stock_user', 'check_party_stock_id', 'user_id');
}


    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }
}

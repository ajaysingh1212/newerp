<?php

namespace App\Models;

use App\Traits\MultiTenantModelTrait;
use Carbon\Carbon;
use App\Models\User;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockTransfer extends Model
{
    use SoftDeletes, MultiTenantModelTrait, HasFactory;

    public $table = 'stock_transfers';

    protected $dates = [
        'transfer_date',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'transfer_date',
        'select_user_id',
        'reseller_id',
        'created_at',
        'updated_at',
        'deleted_at',
        'team_id',
        'transfer_id',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function getTransferDateAttribute($value)
    {
        return $value ? Carbon::parse($value)->format(config('panel.date_format')) : null;
    }

    public function setTransferDateAttribute($value)
    {
        $this->attributes['transfer_date'] = $value ? Carbon::createFromFormat(config('panel.date_format'), $value)->format('Y-m-d') : null;
    }

public function select_user()
{
    return $this->belongsTo(User::class, 'select_user_id'); // ✅ CORRECT
}

    public function reseller()
    {
        return $this->belongsTo(User::class, 'reseller_id');
    }



    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }
    public function select_products()
{
    return $this->belongsToMany(CurrentStock::class, 'current_stock_stock_transfer', 'stock_transfer_id', 'current_stock_id')
                ->withPivot('warranty', 'amc', 'mrp', 'role_price', 'discount_type', 'discount_value', 'final_price');
}

public function transferUser()
{
    return $this->belongsTo(User::class, 'transfer_id');
}


}

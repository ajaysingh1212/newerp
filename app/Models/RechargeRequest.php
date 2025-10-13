<?php

namespace App\Models;

use App\Traits\MultiTenantModelTrait;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class RechargeRequest extends Model implements HasMedia
{
    use SoftDeletes, MultiTenantModelTrait, InteractsWithMedia, HasFactory;

    public $table = 'recharge_requests';

    protected $appends = [
        'attechment',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'user_id',
        'vehicle_number',
        'product_id',
        'select_recharge_id',
        'notes',
        'created_at',
        'updated_at',
        'deleted_at',
        'team_id',
        'payment_status',
        'payment_method',
        'razorpay_payment_id',
        'payment_amount',
        'redeem_amount',
        'payment_date',
        'payment_id',
        'created_by_id',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')->fit('crop', 50, 50);
        $this->addMediaConversion('preview')->fit('crop', 120, 120);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function product()
    {
        return $this->belongsTo(CurrentStock::class, 'product_id');
    }

    public function select_recharge()
    {
        return $this->belongsTo(RechargePlan::class, 'select_recharge_id');
    }

    public function getAttechmentAttribute()
    {
        return $this->getMedia('attechment')->last();
    }

    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }

  public function vehicle()
{
    return $this->hasOne(AddCustomerVehicle::class, 'vehicle_number', 'vehicle_number');
}
    public function created_by()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }


}

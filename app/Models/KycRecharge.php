<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class KycRecharge extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'user_id',
        'vehicle_id',
        'vehicle_number',
        'title',
        'description',
        'payment_status',
        'payment_method',
        'payment_amount',
        'payment_date',
        'created_by_id',
        'razorpay_order_id',
        'location',
        'latitude',
        'longitude',
    ];

    // 🔗 Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(AddCustomerVehicle::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    // 🖼️ Media Collection registration
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('kyc_recharge_images')->singleFile(); // Only 1 image per record
    }
}

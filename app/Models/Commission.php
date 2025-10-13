<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Commission extends Model
{
    // Mass assignable fields
    protected $fillable = [
        'recharge_request_id',
        'customer_id',
        'dealer_id',
        'distributor_id',
        'vehicle_id',  // Agar aapne vehicle_id column add kiya hai migrations me
        'dealer_commission',
        'distributor_commission',
    ];

    /**
     * Commission belongs to a RechargeRequest
     */
    public function rechargeRequest(): BelongsTo
    {
        return $this->belongsTo(RechargeRequest::class);
    }

    /**
     * Commission belongs to a Customer (User)
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    /**
     * Commission belongs to a Dealer (User)
     */
    public function dealer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dealer_id');
    }

    /**
     * Commission belongs to a Distributor (User)
     */
    public function distributor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'distributor_id');
    }

    /**
     * Commission belongs to a Vehicle (AddCustomerVehicle)
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(AddCustomerVehicle::class, 'vehicle_id');
    }
}

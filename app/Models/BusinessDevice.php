<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessDevice extends Model
{
    protected $fillable = ['manual_party_id', 'manual_product_id', 'vts_number', 'sim_number', 'sales_price', 'assigned_at', 'created_by'];

    protected $casts = ['sales_price' => 'decimal:2', 'assigned_at' => 'date', 'activated_at' => 'datetime'];

    public function party()
    {
        return $this->belongsTo(ManualParty::class, 'manual_party_id')->withTrashed();
    }

    public function product()
    {
        return $this->belongsTo(ManualProduct::class, 'manual_product_id')->withTrashed();
    }

    public function activator()
    {
        return $this->belongsTo(User::class, 'activated_by')->withTrashed();
    }
}

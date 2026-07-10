<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ManualActivation extends Model
{
    use SoftDeletes, HasFactory;

    public $table = 'manual_activations';

    protected $fillable = [
        'manual_party_id',
        'manual_product_id',
        'fitting_date',
        'customer_name',
        'customer_email',
        'customer_phone',
        'customer_address',
        'vehicle_number',
        'vehicle_model',
        'vehicle_chassis_number',
        'vehicle_engine_number',
        'vehicle_color',
        'aadhar_front_path',
        'aadhar_back_path',
        'status',
        'created_by',
    ];

    protected $dates = [
        'fitting_date',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function party()
    {
        return $this->belongsTo(ManualParty::class, 'manual_party_id');
    }

    public function product()
    {
        return $this->belongsTo(ManualProduct::class, 'manual_product_id');
    }

    public function documents()
    {
        return $this->hasMany(ManualActivationDocument::class, 'manual_activation_id');
    }
}

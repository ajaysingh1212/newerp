<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ManualParty extends Model
{
    use SoftDeletes, HasFactory;

    public $table = 'manual_parties';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'state',
        'district',
        'city',
        'pincode',
        'address',
        'gst_number',
        'pan_number',
        'bank_name',
        'account_holder_name',
        'account_number',
        'ifsc_code',
        'branch_name',
        'status',
        'created_by',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    // State/District/City ab plain string columns hain, koi relation nahi chahiye

    public function activations()
    {
        return $this->hasMany(ManualActivation::class, 'manual_party_id');
    }
}

<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ManualFitter extends Model
{
    use SoftDeletes, HasFactory;

    public $table = 'manual_fitters';

    protected $fillable = [
        'name',
        'phone',
        'email',
        'dob',
        'gender',
        'photo_path',
        'alternate_phone',
        'whatsapp_number',
        'aadhar_number',
        'id_proof_path',
        'address',
        'landmark',
        'state',
        'district',
        'city',
        'pincode',
        'status',
        'created_by',
    ];

    protected $dates = [
        'dob',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function activations()
    {
        return $this->hasMany(ManualActivation::class, 'manual_fitter_id');
    }
}

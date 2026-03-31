<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeleteData extends Model
{
    use HasFactory;

    protected $table = 'delete_data';

    protected $fillable = [
        // OLD FIELDS
        'user_name',
        'number',
        'email',
        'product',
        'counter_name',
        'vehicle_no',
        'imei_no',
        'vts_no',
        'delete_date',

        // NEW FIELDS 🔥
        'owner_name',
        'owner_phone',
        'date_of_fitting',
        'expiry_date',
        'sim_number',
        'reason_for_deletion',
    ];

    protected $casts = [
        'delete_date'      => 'datetime',
        'date_of_fitting'  => 'date',
        'expiry_date'      => 'date',
    ];
}

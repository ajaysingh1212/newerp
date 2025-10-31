<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeleteData extends Model
{
    use HasFactory;

    // 🧩 Table name specify (optional, Laravel auto detect kar lega if plural form used)
    protected $table = 'delete_data';

    // ✅ Allow mass assignment for these columns
    protected $fillable = [
        'user_name',
        'number',
        'email',
        'product',
        'counter_name',
        'vehicle_no',
        'imei_no',
        'vts_no',
        'delete_date',
    ];

    // 🔁 If you want delete_date as Carbon (date handling)
    protected $casts = [
        'delete_date' => 'datetime',
    ];
}

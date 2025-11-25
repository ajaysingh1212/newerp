<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DailyInterest extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'investment_id',
        'investor_id',
        'plan_id',
        'principal_amount',
        'daily_interest_amount',
        'interest_date',
    ];
}

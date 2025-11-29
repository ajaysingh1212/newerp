<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\MultiTenantModelTrait;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MonthlyPayoutRecord extends Model
{
    use SoftDeletes,  Auditable, HasFactory;

    public $table = 'monthly_payout_records';

    public const STATUS_SELECT = [
        'pending' => 'pending',
        'paid'    => 'paid',
    ];

    protected $dates = [
        'created_at',
        'month_for',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'investment_id',
        'created_at',
        'investor_id',
        'secure_interest_amount',
        'market_interest_amount',
        'total_payout_amount',
        'month_for',
        'status',
        'updated_at',
        'deleted_at',
        'created_by_id',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function investment()
    {
        return $this->belongsTo(Investment::class, 'investment_id');
    }

    public function investor()
    {
        return $this->belongsTo(Registration::class, 'investor_id');
    }

    public function getMonthForAttribute($value)
    {
        return $value ? Carbon::parse($value)->format(config('panel.date_format')) : null;
    }

    public function setMonthForAttribute($value)
    {
        $this->attributes['month_for'] = $value ? Carbon::createFromFormat(config('panel.date_format'), $value)->format('Y-m-d') : null;
    }

    public function created_by()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }
}
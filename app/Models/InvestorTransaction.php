<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\MultiTenantModelTrait;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class InvestorTransaction extends Model implements HasMedia
{
    use SoftDeletes, InteractsWithMedia, Auditable, HasFactory;

    public $table = 'investor_transactions';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public const STATUS_SELECT = [
        'pending' => 'Pending',
        'success' => 'Success',
        'failed'  => 'Failed',
    ];

    protected $fillable = [
        'investor_id',      // Registration ka ID
        'investment_id',    // Investment ka ID
        'transaction_type',
        'amount',
        'narration',
        'status',
        'created_by_id',
        'plan_id',
    ];

    public const TRANSACTION_TYPE_SELECT = [
        'investment'           => 'Investment',
        'interest_payout'      => 'Interest Payout',
        'withdrawal_interest'  => 'Withdrawal Interest',
        'withdrawal_principal' => 'Withdrawal Principal',
        'over_all'             => 'Overall',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')->fit('crop', 50, 50);
        $this->addMediaConversion('preview')->fit('crop', 120, 120);
    }

    /**
     * investor_id → Registration table
     */
    public function investor()
    {
        return $this->belongsTo(\App\Models\Registration::class, 'investor_id');
    }

    /**
     * investment_id → Investment table
     */
    public function investment()
    {
        return $this->belongsTo(\App\Models\Investment::class, 'investment_id');
    }

    public function created_by()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by_id');
    }
}

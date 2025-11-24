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
    use SoftDeletes, MultiTenantModelTrait, InteractsWithMedia, Auditable, HasFactory;

    public $table = 'investor_transactions';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public const STATUS_SELECT = [
        'pending' => 'pending',
        'success' => 'success',
        'failed'  => 'failed',
    ];

    protected $fillable = [
        'investor_id',
        'investment_id',
        'transaction_type',
        'amount',
        'narration',
        'status',
        'created_at',
        'updated_at',
        'deleted_at',
        'created_by_id',
    ];

    public const TRANSACTION_TYPE_SELECT = [
        'investment'           => 'investment',
        'interest_payout'      => 'interest_payout',
        'withdrawal_interest'  => 'withdrawal_interest',
        'withdrawal_principal' => 'withdrawal_principal',
        'over_all'             => 'over_all',
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

    public function investor()
    {
        return $this->belongsTo(Investment::class, 'investor_id');
    }

    public function investment()
    {
        return $this->belongsTo(Registration::class, 'investment_id');
    }

    public function created_by()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }
}
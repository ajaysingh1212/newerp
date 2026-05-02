<?php

namespace App\Models;

use App\Traits\MultiTenantModelTrait;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GpsCard extends Model
{
    use SoftDeletes, MultiTenantModelTrait, HasFactory;

    public const STATUS_SELECT = [
        'active'   => 'Active',
        'inactive' => 'Inactive',
    ];

    public $table = 'gps_cards';

    protected $fillable = [
        'batch_code',
        'product_model_id',
        'card_number',
        'valid_from',
        'valid_to',
        'status',
        'created_by_id',
        'used_by_id',
        'used_by_activation_request_id',
        'card_holder_name',
        'used_at',
        'printed_at',
        'printed_by_id',
        'team_id',
        'card_holder_name'

    ];

    protected $casts = [
        'valid_from' => 'date',
        'valid_to'   => 'date',
        'used_at'    => 'datetime',
        'printed_at' => 'datetime',
    ];

    protected $dates = [
        'valid_from',
        'valid_to',
        'used_at',
        'printed_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function productModel()
    {
        return $this->belongsTo(ProductModel::class, 'product_model_id')->withTrashed();
    }

    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function usedBy()
    {
        return $this->belongsTo(User::class, 'used_by_id');
    }

    public function printedBy()
    {
        return $this->belongsTo(User::class, 'printed_by_id');
    }

    public function assignedActivationRequest()
    {
        return $this->belongsTo(ActivationRequest::class, 'used_by_activation_request_id');
    }

    public function getFormattedCardNumberAttribute(): string
    {
        return trim(chunk_split((string) $this->card_number, 4, ' '));
    }

    public function getFormattedValidFromAttribute(): string
    {
        return $this->valid_from instanceof Carbon ? $this->valid_from->format('m / Y') : '-- / ----';
    }

    public function getFormattedValidToAttribute(): string
    {
        return $this->valid_to instanceof Carbon ? $this->valid_to->format('m / Y') : '-- / ----';
    }

    public function getDisplayStatusAttribute(): string
    {
        if ($this->status === 'inactive') {
            return 'Inactive';
        }

        return $this->isExpired() ? 'Expired' : 'Active';
    }

    public function isExpired(): bool
    {
        return $this->valid_to instanceof Carbon
            ? $this->valid_to->copy()->endOfMonth()->lt(now())
            : false;
    }

    public function isUsed(): bool
    {
        return ! is_null($this->used_by_activation_request_id);
    }

    public function isPrinted(): bool
    {
        return ! is_null($this->printed_at);
    }

    public function getUsageStatusAttribute(): string
    {
        return $this->isUsed() ? 'Used' : 'Available';
    }

    public function getPrintStatusAttribute(): string
    {
        return $this->isPrinted() ? 'Printed' : 'Not Printed';
    }
}

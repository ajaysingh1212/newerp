<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\MultiTenantModelTrait;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class WithdrawalRequest extends Model implements HasMedia
{
    use SoftDeletes, MultiTenantModelTrait, InteractsWithMedia, Auditable, HasFactory;

    public $table = 'withdrawal_requests';

    protected $dates = [
        'requested_at',
        'approved_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public const TYPE_SELECT = [
        'interest'  => 'interest',
        'principal' => 'principal',
        'total'     => 'total',
    ];

    public const STATUS_SELECT = [
        'pending'  => 'pending',
        'approved' => 'approved',
        'rejected' => 'rejected',
    ];

    protected $fillable = [
        'select_investor_id',
        'investment_id',
        'amount',
        'type',
        'status',
        'processing_hours',
        'requested_at',
        'approved_at',
        'notes',
        'remarks',
        'created_by_id',
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

    public function select_investor()
    {
        return $this->belongsTo(Registration::class, 'select_investor_id');
    }

    public function investment()
    {
        return $this->belongsTo(Investment::class, 'investment_id');
    }

    // ❌ REMOVED getter — it breaks JSON/AJAX
    // ❌ REMOVED setter — it caused Carbon format crash

    public function created_by()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }
}

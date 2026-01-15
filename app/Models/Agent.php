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

class Agent extends Model implements HasMedia
{
    use SoftDeletes, InteractsWithMedia, Auditable, HasFactory;

    public $table = 'agents';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $appends = [
        'aadhar_front',
        'aadhar_back',
        'pan_card',
        'additional_document',
    ];

    public const STATUS_SELECT = [
        'active'   => 'active',
        'inactive' => 'inactive',
        'hold'     => 'hold',
        'block'    => 'block',
    ];

    protected $fillable = [
        'full_name',
        'phone_number',
        'whatsapp_number',
        'email',
        'pin_code',
        'state',
        'city',
        'district',
        'present_address',
        'parmanent_address',
        'status',
        'created_at',
        'updated_at',
        'deleted_at',
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

    public function selectAgentInvestments()
    {
        return $this->hasMany(Investment::class, 'select_agent_id', 'id');
    }

    public function getAadharFrontAttribute()
    {
        return $this->getMedia('aadhar_front')->last();
    }

    public function getAadharBackAttribute()
    {
        return $this->getMedia('aadhar_back')->last();
    }

    public function getPanCardAttribute()
    {
        return $this->getMedia('pan_card')->last();
    }

    public function getAdditionalDocumentAttribute()
    {
        return $this->getMedia('additional_document');
    }

    public function created_by()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }
}
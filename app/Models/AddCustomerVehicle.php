<?php

namespace App\Models;

use App\Traits\MultiTenantModelTrait;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class AddCustomerVehicle extends Model implements HasMedia
{
    use SoftDeletes, MultiTenantModelTrait, InteractsWithMedia, HasFactory;

    public $table = 'add_customer_vehicles';

    protected $dates = [
        'insurance_expiry_date',
        'created_at',
        'updated_at',
        'deleted_at',
        'amc',
        'warranty',
        'subscription',
    ];

    protected $appends = [
        'insurance',
        'pollution',
        'registration_certificate',
        'id_proofs',
        'vehicle_photos',
        'product_images',
    ];

    public const STATUS_SELECT = [
        'Pending'    => 'Pending',
        'Processing' => 'Processing',
        'Inactive'   => 'Inactive',
        'Active'     => 'Active',
        'Suspended'  => 'Suspended',
    ];

    protected $fillable = [
        'select_vehicle_type_id',
        'vehicle_number',
        'owners_name',
        'insurance_expiry_date',
        'chassis_number',
        'vehicle_model',
        'owner_image',
        'vehicle_color',
        'status',
        'team_id',
        'select_vehicle_type_id',
        'created_by_id',
        'activated',
        'engine_number',
        'product_id',
        'request_date',
        'activation_id',
        'app_link_id',
        'amc',
        'warranty',
        'subscription',
        'user_id',
        'password',
        'app_url',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    // ✅ FIXED: Robust date setters with logging

    public function setInsuranceExpiryDateAttribute($value)
    {
        try {
            $this->attributes['insurance_expiry_date'] = $value ? Carbon::parse($value)->format('Y-m-d') : null;
        } catch (\Exception $e) {
            \Log::error('Insurance Date Parse Error', ['value' => $value, 'message' => $e->getMessage()]);
            $this->attributes['insurance_expiry_date'] = null;
        }
    }

    public function setAmcAttribute($value)
    {
        try {
            $this->attributes['amc'] = $value ? Carbon::parse($value)->format('Y-m-d') : null;
        } catch (\Exception $e) {
            \Log::error('AMC Date Parse Error', ['value' => $value, 'message' => $e->getMessage()]);
            $this->attributes['amc'] = null;
        }
    }

    public function setWarrantyAttribute($value)
    {
        try {
            $this->attributes['warranty'] = $value ? Carbon::parse($value)->format('Y-m-d') : null;
        } catch (\Exception $e) {
            \Log::error('Warranty Date Parse Error', ['value' => $value, 'message' => $e->getMessage()]);
            $this->attributes['warranty'] = null;
        }
    }

    public function setSubscriptionAttribute($value)
    {
        try {
            $this->attributes['subscription'] = $value ? Carbon::parse($value)->format('Y-m-d') : null;
        } catch (\Exception $e) {
            \Log::error('Subscription Date Parse Error', ['value' => $value, 'message' => $e->getMessage()]);
            $this->attributes['subscription'] = null;
        }
    }

    // Media Accessors

    public function getInsuranceAttribute()
    {
        $files = $this->getMedia('insurance');
        $files->each(function ($item) {
            $item->url = $item->getUrl();
            $item->thumbnail = $item->getUrl('thumb');
            $item->preview = $item->getUrl('preview');
        });
        return $files;
    }

    public function getPollutionAttribute()
    {
        $files = $this->getMedia('pollution');
        $files->each(function ($item) {
            $item->url = $item->getUrl();
            $item->thumbnail = $item->getUrl('thumb');
            $item->preview = $item->getUrl('preview');
        });
        return $files;
    }

    public function getRegistrationCertificateAttribute()
    {
        $files = $this->getMedia('registration_certificate');
        $files->each(function ($item) {
            $item->url = $item->getUrl();
            $item->thumbnail = $item->getUrl('thumb');
            $item->preview = $item->getUrl('preview');
        });
        return $files;
    }

    public function getIdProofsAttribute()
    {
        $file = $this->getMedia('id_proofs')->last();
        if ($file) {
            $file->url = $file->getUrl();
            $file->thumbnail = $file->getUrl('thumb');
            $file->preview = $file->getUrl('preview');
        }
        return $file;
    }

    public function getVehiclePhotosAttribute()
    {
        $file = $this->getMedia('vehicle_photos')->last();
        if ($file) {
            $file->url = $file->getUrl();
            $file->thumbnail = $file->getUrl('thumb');
            $file->preview = $file->getUrl('preview');
        }
        return $file;
    }

    public function getProductImagesAttribute()
    {
        $file = $this->getMedia('product_images')->last();
        if ($file) {
            $file->url = $file->getUrl();
            $file->thumbnail = $file->getUrl('thumb');
            $file->preview = $file->getUrl('preview');
        }
        return $file;
    }

    // Relations

    public function selectVehicleCheckComplains()
    {
        return $this->belongsToMany(CheckComplain::class);
    }

    public function select_vehicle_type()
    {
        return $this->belongsTo(VehicleType::class, 'select_vehicle_type_id');
    }

    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function product_master()
    {
        return $this->belongsTo(ProductMaster::class, 'product_id');
    }

    public function appLink()
    {
        return $this->belongsTo(AppLink::class, 'app_link_id');
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')->fit('crop', 50, 50);
        $this->addMediaConversion('preview')->fit('crop', 120, 120);
    }

    public function rechargeRequest()
{
    return $this->hasMany(\App\Models\ActivationRequest::class, 'vehicle_id', 'id');
}
}

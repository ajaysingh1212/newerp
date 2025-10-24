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

    protected $table = 'add_customer_vehicles';

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

    /*
    |--------------------------------------------------------------------------
    | Date Mutators (Safe Parsing)
    |--------------------------------------------------------------------------
    */
    public function setInsuranceExpiryDateAttribute($value)
    {
        $this->attributes['insurance_expiry_date'] = $this->safeDate($value);
    }

    public function setAmcAttribute($value)
    {
        $this->attributes['amc'] = $this->safeDate($value);
    }

    public function setWarrantyAttribute($value)
    {
        $this->attributes['warranty'] = $this->safeDate($value);
    }

    public function setSubscriptionAttribute($value)
    {
        $this->attributes['subscription'] = $this->safeDate($value);
    }

    protected function safeDate($value)
    {
        try {
            return $value ? Carbon::parse($value)->format('Y-m-d') : null;
        } catch (\Exception $e) {
            \Log::error('Date Parse Error', ['value' => $value, 'message' => $e->getMessage()]);
            return null;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Spatie Media Accessors (Fully Working)
    |--------------------------------------------------------------------------
    */
    public function getInsuranceAttribute()
    {
        return $this->getMediaData('insurance');
    }

    public function getPollutionAttribute()
    {
        return $this->getMediaData('pollution');
    }

    public function getRegistrationCertificateAttribute()
    {
        return $this->getMediaData('registration_certificate');
    }

    public function getIdProofsAttribute()
    {
        return $this->getMediaData('id_proofs', true);
    }

    public function getVehiclePhotosAttribute()
    {
        return $this->getMediaData('vehicle_photos', true);
    }

    public function getProductImagesAttribute()
    {
        return $this->getMediaData('product_images', true);
    }

    /**
     * 🔹 Helper for consistent media formatting
     */
    protected function getMediaData(string $collection, bool $single = false)
    {
        try {
            $files = $this->getMedia($collection);

            if ($single) {
                $file = $files->last();
                if (!$file) return null;

                return [
                    'id' => $file->id,
                    'file_name' => $file->file_name,
                    'url' => $file->getUrl(),
                    'thumbnail' => $file->getUrl('thumb'),
                    'preview' => $file->getUrl('preview'),
                ];
            }

            return $files->map(function ($file) {
                return [
                    'id' => $file->id,
                    'file_name' => $file->file_name,
                    'url' => $file->getUrl(),
                    'thumbnail' => $file->getUrl('thumb'),
                    'preview' => $file->getUrl('preview'),
                ];
            });
        } catch (\Exception $e) {
            \Log::error('Media Load Error', ['collection' => $collection, 'message' => $e->getMessage()]);
            return $single ? null : [];
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */
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

    public function rechargeRequest()
    {
        return $this->hasMany(\App\Models\ActivationRequest::class, 'vehicle_id', 'id');
    }

    /*
    |--------------------------------------------------------------------------
    | Spatie Conversions (thumbnails, previews)
    |--------------------------------------------------------------------------
    */
    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')->fit('crop', 50, 50);
        $this->addMediaConversion('preview')->fit('crop', 120, 120);
    }
}

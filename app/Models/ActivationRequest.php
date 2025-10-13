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

class ActivationRequest extends Model implements HasMedia
{
    use SoftDeletes, MultiTenantModelTrait, InteractsWithMedia, HasFactory;

    public $table = 'activation_requests';

    protected $dates = [
        'request_date',
        'amc',
        'warranty',
        'subscription',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $statusOptions = [
    'Pending' => 'Pending',
    'Approved' => 'Approved',
    'Rejected' => 'Rejected',
];

    protected $appends = [
        'id_proofs',
        'customer_image',
        'vehicle_photos',
        'product_images',
    ];

    protected $fillable = [
        'party_type_id',
        'select_party_id',
        'product_id',
        'customer_name',
        'mobile_number',
        'whatsapp_number',
        'email',
        'state_id',
        'disrict_id',
        'address',
        'request_date',
        'vehicle_model',
        'vehicle_type_id',
        'vehicle_reg_no',
        'chassis_number',
        'engine_number',
        'vehicle_color',
        'team_id',
        'status',
        'created_by_id',
        'created_at',
        'updated_at',
        'deleted_at',
        'fitter_name',
        'fitter_number',
        'vehicle_id',
        'app_link_id',
        'amc',
        'warranty',
        'subscription',
        'user_id',
        'password',
    ];

    // Status constants
    public const STATUS_PENDING    = 'pending';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_ACTIVATED  = 'activated';
    public const STATUS_REJECTED   = 'rejected';

    public static function getStatusOptions()
    {
        return [
            self::STATUS_PENDING    => 'Pending',
            self::STATUS_PROCESSING => 'Processing',
            self::STATUS_ACTIVATED  => 'Activated',
            self::STATUS_REJECTED   => 'Rejected',
        ];
    }

    public function getStatusLabelAttribute()
    {
        return self::getStatusOptions()[$this->status] ?? $this->status;
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')->fit('crop', 50, 50);
        $this->addMediaConversion('preview')->fit('crop', 120, 120);
    }

    public function vehicleAttachVeichles()
    {
        return $this->belongsToMany(AttachVeichle::class);
    }

    public function party_type()
    {
        return $this->belongsTo(Role::class, 'party_type_id');
    }

    public function select_party()
    {
        return $this->belongsTo(User::class, 'select_party_id');
    }

    public function product()
{
    return $this->belongsTo(CurrentStock::class, 'product_id');
}


    public function state()
    {
        return $this->belongsTo(State::class, 'state_id');
    }

    public function disrict()
    {
        return $this->belongsTo(District::class, 'disrict_id');
    }

    public function vehicle_type()
    {
        return $this->belongsTo(VehicleType::class, 'vehicle_type_id');
    }

    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }

    public function getRequestDateAttribute($value)
    {
        return $value ? Carbon::parse($value)->format(config('panel.date_format')) : null;
    }

    public function setRequestDateAttribute($value)
    {
        $this->attributes['request_date'] = $value
            ? Carbon::createFromFormat(config('panel.date_format'), $value)->format('Y-m-d')
            : null;
    }

    public function getIdProofsAttribute()
    {
        $file = $this->getMedia('id_proofs')->last();
        if ($file) {
            $file->url       = $file->getUrl();
            $file->thumbnail = $file->getUrl('thumb');
            $file->preview   = $file->getUrl('preview');
        }
        return $file;
    }

    public function getCustomerImageAttribute()
    {
        $file = $this->getMedia('customer_image')->last();
        if ($file) {
            $file->url       = $file->getUrl();
            $file->thumbnail = $file->getUrl('thumb');
            $file->preview   = $file->getUrl('preview');
        }
        return $file;
    }

    public function getVehiclePhotosAttribute()
    {
        $file = $this->getMedia('vehicle_photos')->last();
        if ($file) {
            $file->url       = $file->getUrl();
            $file->thumbnail = $file->getUrl('thumb');
            $file->preview   = $file->getUrl('preview');
        }
        return $file;
    }

    public function getProductImagesAttribute()
    {
        $file = $this->getMedia('product_images')->last();
        if ($file) {
            $file->url       = $file->getUrl();
            $file->thumbnail = $file->getUrl('thumb');
            $file->preview   = $file->getUrl('preview');
        }
        return $file;
    }

     public function created_by()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }


  


public function district() {  // fix spelling here!
    return $this->belongsTo(District::class, 'district_id');
}

public function createdBy() {
    return $this->belongsTo(User::class, 'created_by_id');
}


public function app_link()
{
    return $this->belongsTo(AppLink::class, 'app_link_id');
}
public function product_master()
{
    return $this->belongsTo(ProductMaster::class, 'product_id');
}

    
}


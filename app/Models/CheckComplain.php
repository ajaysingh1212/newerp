<?php

namespace App\Models;

use App\Traits\MultiTenantModelTrait;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class CheckComplain extends Model implements HasMedia
{
    use SoftDeletes, MultiTenantModelTrait, InteractsWithMedia, HasFactory;

    public $table = 'check_complains';

    protected $appends = [
        'attechment',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public const STATUS_SELECT = [
        'Pending'    => 'Pending',
        'processing' => 'Processing',
        'reject'     => 'Reject',
        'solved'     => 'Solved',
    ];

    protected $fillable = [
        'ticket_number',
     
        'vehicle_no',
        'customer_name',
        'phone_number',
        'reason',
        'status',
        'notes',
        'admin_message',
        'created_at',
        'updated_at',
        'deleted_at',
        'team_id',
        'created_by_id',
        'vehicle_id'
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

    public function select_complains()
    {
        return $this->belongsToMany(ComplainCategory::class);
    }

    public function select_vehicles()
    {
        return $this->belongsToMany(AddCustomerVehicle::class);
    }

    public function getAttechmentAttribute()
    {
        return $this->getMedia('attechment');
    }

    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }
    public function created_by()
{
    return $this->belongsTo(User::class, 'created_by_id');
}

public function vehicle()
{
    return $this->belongsTo(AddCustomerVehicle::class, 'vehicle_id');
}


}

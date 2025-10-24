<?php

namespace App\Models;

use App\Notifications\VerifyUserNotification;
use Carbon\Carbon;
use DateTimeInterface;
use Hash;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Permission\Models\Role; // ✅ Add this line
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements HasMedia
{
    use HasApiTokens, SoftDeletes, Notifiable, InteractsWithMedia, HasFactory;

    public $table = 'users';

    protected $hidden = [
        'remember_token',
        'password',
    ];

    public const STATUS_SELECT = [
        'enable'  => 'Enable',
        'disable' => 'Disable',
    ];

    protected $dates = [
        'created_at',
        'date_inc',
        'email_verified_at',
        'updated_at',
        'deleted_at',
    ];

    protected $appends = [
        'profile_image',
        'upload_signature',
        'upload_pan_aadhar',
        'passbook_statement',
        'shop_photo',
        'gst_certificate',
    ];

    protected $fillable = [
        'name',
        'company_name',
        'email',
        'created_at',
        'gst_number',
        'date_inc',
        'date_joining',
        'mobile_number',
        'whatsapp_number',
        'state_id',
        'district_id',
        'pin_code',
        'full_address',
        'bank_name',
        'branch_name',
        'ifsc',
        'ac_holder_name',
        'pan_number',
        'status',
        'password',
        'email_verified_at',
        'remember_token',
        'updated_at',
        'deleted_at',
        'team_id',
        'created_by_id',
        'status_cmd'
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function getIsAdminAttribute()
    {
        return $this->roles()->where('id', 1)->exists();
    }

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        self::created(function (self $user) {
            $registrationRole = config('panel.registration_default_role');
            if (! $user->roles()->get()->contains($registrationRole)) {
                $user->roles()->attach($registrationRole);
            }
        });
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')->fit('crop', 50, 50);
        $this->addMediaConversion('preview')->fit('crop', 120, 120);
    }

    public function selectPartyActivationRequests()
    {
        return $this->hasMany(ActivationRequest::class, 'select_party_id', 'id');
    }

    public function selectUserAttachVeichles()
    {
        return $this->hasMany(AttachVeichle::class, 'select_user_id', 'id');
    }

    public function userRechargeRequests()
    {
        return $this->hasMany(RechargeRequest::class, 'user_id', 'id');
    }

    public function resellerStockTransfers()
    {
        return $this->hasMany(StockTransfer::class, 'reseller_id', 'id');
    }

    public function userUserAlerts()
    {
        return $this->belongsToMany(UserAlert::class);
    }

    public function selectPartyCheckPartyStocks()
    {
        return $this->belongsToMany(CheckPartyStock::class);
    }

    public function getDateIncAttribute($value)
    {
        return $value ? Carbon::parse($value)->format(config('panel.date_format')) : null;
    }

    public function setDateIncAttribute($value)
    {
        $this->attributes['date_inc'] = $value ? Carbon::createFromFormat(config('panel.date_format'), $value)->format('Y-m-d') : null;
    }

    public function state()
    {
        return $this->belongsTo(State::class, 'state_id');
    }

    public function district()
    {
        return $this->belongsTo(District::class, 'district_id');
    }

    public function getProfileImageAttribute()
    {
        $file = $this->getMedia('profile_image')->last();
        if ($file) {
            $file->url       = $file->getUrl();
            $file->thumbnail = $file->getUrl('thumb');
            $file->preview   = $file->getUrl('preview');
        }

        return $file;
    }

    public function getUploadSignatureAttribute()
    {
        $file = $this->getMedia('upload_signature')->last();
        if ($file) {
            $file->url       = $file->getUrl();
            $file->thumbnail = $file->getUrl('thumb');
            $file->preview   = $file->getUrl('preview');
        }

        return $file;
    }

    public function getUploadPanAadharAttribute()
    {
        return $this->getMedia('upload_pan_aadhar')->last();
    }

    public function getPassbookStatementAttribute()
    {
        return $this->getMedia('passbook_statement')->last();
    }

    public function getShopPhotoAttribute()
    {
        $file = $this->getMedia('shop_photo')->last();
        if ($file) {
            $file->url       = $file->getUrl();
            $file->thumbnail = $file->getUrl('thumb');
            $file->preview   = $file->getUrl('preview');
        }

        return $file;
    }

    public function getGstCertificateAttribute()
    {
        return $this->getMedia('gst_certificate')->last();
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function setPasswordAttribute($input)
    {
        if ($input) {
            $this->attributes['password'] = app('hash')->needsRehash($input) ? Hash::make($input) : $input;
        }
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }

    public function getEmailVerifiedAtAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d H:i:s', $value)->format(config('panel.date_format') . ' ' . config('panel.time_format')) : null;
    }

    public function setEmailVerifiedAtAttribute($value)
    {
        $this->attributes['email_verified_at'] = $value ? Carbon::createFromFormat(config('panel.date_format') . ' ' . config('panel.time_format'), $value)->format('Y-m-d H:i:s') : null;
    }

    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }

    
public function createdBy()
{
    return $this->belongsTo(User::class, 'created_by_id');
}

public function vehicles()
{
    // Agar column user_id hai
    return $this->hasMany(AddCustomerVehicle::class, 'user_id', 'id');

    // Agar column created_by_id hai to ye mat badlo
    // return $this->hasMany(AddCustomerVehicle::class, 'created_by_id', 'id');
}


public function userAlerts()
{
    return $this->belongsToMany(UserAlert::class);
}


public function selectUserStockTransfers()
{
    return $this->hasMany(StockTransfer::class, 'select_user_id', 'id');
}

}



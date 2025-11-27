<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\MultiTenantModelTrait;
use Carbon\Carbon;
use App\Traits\AuditLog;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Registration extends Model implements HasMedia
{
   use SoftDeletes, MultiTenantModelTrait, InteractsWithMedia, Auditable, HasFactory;

    protected $table = 'registrations';

    //-----------------------------------
    // CONSTANTS
    //-----------------------------------

    public const IS_EMAIL_VERIFIED_RADIO = [
        'No'  => 'No',
        'Yes' => 'Yes',
    ];

    public const IS_PHONE_VERIFIED_RADIO = [
        'No'  => 'No',
        'Yes' => 'Yes',
    ];

    public const GENDER_SELECT = [
        'Male'   => 'Male',
        'Female' => 'Female',
        'Other'  => 'Other',
    ];

    public const OCCUPATION_SELECT = [
        'Job'      => 'Job',
        'Business' => 'Business',
        'employed' => 'employed',
    ];

    public const KYC_STATUS_SELECT = [
        'Pending'   => 'Pending',
        'Submitted' => 'Submitted',
        'Verified'  => 'Verified',
    ];

    public const INCOME_RANGE_SELECT = [
        '5L'     => '5L',
        '5-10L'  => '5-10L',
        '10-20L' => '10-20L',
        '20L+'   => '20L+',
    ];

    public const RISK_PROFILE_SELECT = [
        'Conservative' => 'Conservative',
        'Moderate'     => 'Moderate',
        'High'         => 'High',
    ];

    public const ACCOUNT_STATUS_SELECT = [
        'Active'    => 'Active',
        'Suspended' => 'Suspended',
        'Rejected'  => 'Rejected',
    ];

    public const INVESTMENT_EXPERIENCE_SELECT = [
        '0–1 yr' => '0–1 yr',
        '1–3 yr' => '1–3 yr',
        '3+ yr'  => '3+ yr',
    ];

    //-----------------------------------
    // DATES / APPENDS
    //-----------------------------------

    protected $dates = [
        'dob',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $appends = [
        'pan_card_image',
        'aadhaar_front_image',
        'aadhaar_back_image',
        'profile_image',
        'signature_image',
    ];

    //-----------------------------------
    // FILLABLE
    //-----------------------------------

    protected $fillable = [
        'reg',
        'investor_id',
        'referral_code',
        'aadhaar_number',
        'pan_number',
        'dob',
        'gender',
        'father_name',
        'address_line_1',
        'address_line_2',
        'pincode',
        'city',
        'state',
        'country',
        'bank_account_holder_name',
        'bank_account_number',
        'ifsc_code',
        'bank_name',
        'bank_branch',
        'income_range',
        'occupation',
        'risk_profile',
        'investment_experience',
        'kyc_status',
        'account_status',
        'is_email_verified',
        'is_phone_verified',
        'created_by_id',
    ];

    //-----------------------------------
    // DATE SERIALIZATION
    //-----------------------------------

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    //-----------------------------------
    // DOB ACCESSORS & MUTATORS (SAFE)
    //-----------------------------------

    public function getDobAttribute($value)
    {
        return $value ? Carbon::parse($value)->format(config('panel.date_format')) : null;
    }

    public function setDobAttribute($value)
    {
        try {
            $this->attributes['dob'] = $value
                ? Carbon::createFromFormat(config('panel.date_format'), $value)->format('Y-m-d')
                : null;
        } catch (\Exception $e) {
            \Log::error('DOB Parse Error', ['value' => $value, 'message' => $e->getMessage()]);
            $this->attributes['dob'] = null;
        }
    }

    //-----------------------------------
    // MEDIA CONVERSIONS
    //-----------------------------------

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')->fit('crop', 50, 50);
        $this->addMediaConversion('preview')->fit('crop', 120, 120);
    }

    //-----------------------------------
    // MEDIA ACCESSORS (CORRECT FORMAT)
    //-----------------------------------

    public function getPanCardImageAttribute()
    {
        $files = $this->getMedia('pan_card_image');
        $files->each(function ($item) {
            $item->url       = $item->getUrl();
            $item->thumbnail = $item->getUrl('thumb');
            $item->preview   = $item->getUrl('preview');
        });
        return $files;
    }

    public function getAadhaarFrontImageAttribute()
    {
        $file = $this->getMedia('aadhaar_front_image')->last();
        if ($file) {
            $file->url       = $file->getUrl();
            $file->thumbnail = $file->getUrl('thumb');
            $file->preview   = $file->getUrl('preview');
        }
        return $file;
    }

    public function getAadhaarBackImageAttribute()
    {
        $file = $this->getMedia('aadhaar_back_image')->last();
        if ($file) {
            $file->url       = $file->getUrl();
            $file->thumbnail = $file->getUrl('thumb');
            $file->preview   = $file->getUrl('preview');
        }
        return $file;
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

    public function getSignatureImageAttribute()
    {
        $file = $this->getMedia('signature_image')->last();
        if ($file) {
            $file->url       = $file->getUrl();
            $file->thumbnail = $file->getUrl('thumb');
            $file->preview   = $file->getUrl('preview');
        }
        return $file;
    }

    //-----------------------------------
    // RELATIONS
    //-----------------------------------

    public function investor()
    {
        return $this->belongsTo(User::class, 'investor_id');
    }

    public function created_by()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function selectInvestorInvestments()
    {
        return $this->hasMany(Investment::class, 'investor_id', 'id');
    }

    public function investorMonthlyPayoutRecords()
    {
        return $this->hasMany(MonthlyPayoutRecord::class, 'investor_id', 'id');
    }

    public function selectInvestorWithdrawalRequests()
    {
        return $this->hasMany(WithdrawalRequest::class, 'investor_id', 'id');
    }

    public function investmentTransactions()
    {
        return $this->hasMany(Transaction::class, 'investment_id', 'id');
    }
// In Registration model
public function user() {
    return $this->belongsTo(\App\Models\User::class, 'investor_id', 'id');
}



}

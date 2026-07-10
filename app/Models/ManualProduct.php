<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ManualProduct extends Model
{
    use SoftDeletes, HasFactory;

    public $table = 'manual_products';

    protected $fillable = [
        'name',
        'description',
        'status',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function activations()
    {
        return $this->hasMany(ManualActivation::class, 'manual_product_id');
    }
}

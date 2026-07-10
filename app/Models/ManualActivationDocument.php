<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManualActivationDocument extends Model
{
    public $table = 'manual_activation_documents';

    protected $fillable = [
        'manual_activation_id',
        'document_name',
        'file_path',
    ];

    public function activation()
    {
        return $this->belongsTo(ManualActivation::class, 'manual_activation_id');
    }
}

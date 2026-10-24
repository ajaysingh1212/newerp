<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessImportRow extends Model
{
    public $timestamps = false;

    protected $guarded = ['id'];

    protected $casts = ['source_cells' => 'array', 'normalized' => 'array', 'issues' => 'array', 'needs_review' => 'boolean'];

    public function party()
    {
        return $this->belongsTo(ManualParty::class, 'manual_party_id')->withTrashed();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitSelfie extends Model
{
    protected $fillable = ['field_visit_entry_id', 'image_path', 'image_url'];

    public function fieldVisitEntry()
    {
        return $this->belongsTo(FieldVisitEntry::class);
    }
}

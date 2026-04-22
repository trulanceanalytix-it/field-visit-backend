<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Remark extends Model
{
    protected $fillable = [
        'remark',
        'is_active',
        'field_visit_entry_id',
        'created_by'
    ];

    // ? Scope to get only active remarks
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}

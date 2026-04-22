<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompetitorBrand extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'sub_category',
        'is_active',
        'sort_order',
        // 'added_by' if you have it
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // This is the critical part – exact name "scopeActive"
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Optional global scope (if you want EVERY query to auto-apply active)
    protected static function booted()
    {
        static::addGlobalScope('active', function ($query) {
            $query->where('is_active', true);
        });
    }

    // Your other stuff...
}

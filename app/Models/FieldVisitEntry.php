<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// class FieldVisitEntry extends Model
// {
//     protected $fillable = [
//         'emp_id',
//         'emp_name',
//         'beat_id',
//         'beat_name',
//         'distributor_name',
//         'outlet_name',
//         'store_id',
//         'store_name',

//         'leggings_qty',
//         'non_leggings_qty',
//         'innerwear_qty',
//         'total_pcs',

//         'remark',
//         'observation',

//         'latitude',
//         'longitude',
//         'address',
//         'location_accuracy',
//         'location_captured_at',

//         'visited_at',
//         'visited_date',
//         'opening_soh',
//         'closing_soh',
//     ];
//     protected $casts = [
//         'remark' => 'array',
//     ];
// }
class FieldVisitEntry extends Model
{
    protected $fillable = [
        'emp_id',
        'emp_name',

        'distributor_id',
        'beat_id',
        'outlet_id',
        'is_phone_order',
        'leggings_qty',
        'non_leggings_qty',
        'innerwear_qty',
        'total_pcs',
        'stock_leggings',
        'stock_non_leggings',
        'stock_innerwear',
        // 🔹 Newly added fields
        'fsu_type_1',
        'fsu_type_2',
        'fsu_type_3',
        'fsu_image',

        'instore_branding',
        'branding_image',

        'competitor_brands',
        'other_brand_name',

        'store_grade',

        'remark',
        'observation',

        'latitude',
        'longitude',
        'address',
        'location_accuracy',
        'location_captured_at',

        'visited_at',
        'visited_date',
    ];

    protected $casts = [
        'remark'             => 'array',
        'competitor_brands'  => 'array',
        'instore_branding' => 'array',
        'visited_at'         => 'datetime',
        'visited_date'       => 'date',
    ];
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Only set created_at from visited_at if it exists
            if (empty($model->created_at) && !empty($model->visited_at)) {
                $model->created_at = $model->visited_at;
            }
            // Let Laravel auto-set updated_at
        });

        // No need to modify updating event - let updated_at auto-update
    }
    public function distributor()
    {
        return $this->belongsTo(DistributorMaster::class, 'distributor_id');
    }

    public function beat()
    {
        return $this->belongsTo(BeatMaster::class, 'beat_id');
    }

    public function outlet()
    {
        return $this->belongsTo(OutletMaster::class, 'outlet_id');
    }
    public function selfie()
    {
        return $this->hasOne(VisitSelfie::class);
    }
}

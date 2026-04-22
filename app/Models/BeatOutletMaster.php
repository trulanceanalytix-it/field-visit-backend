<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BeatOutletMaster extends Model
{
    protected $table = 'beat_outlet_master';

    protected $fillable = [
        'cluster_manager_id',
        'cluster_manager_name',
        'tse_id',
        'tse_name',
        'outlet_name',
        'beat_name',
        'distributor_name',
        'status',
        'beat_name_change',
        'remarks',
        'floors',
        'total_sft',
        'nearby_shops',
        'signage_image',
        'image_1',
        'image_2'
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OutletMaster extends Model
{
    protected $table = 'outlet_master';

    protected $fillable = [
        'beat_id',
        'outlet_name',
        'owner_name',
        'outlet_mobile',
        'outlet_whatsapp',
        'address',
        'gstin',
        'floors',
        'total_sft',
        'nearby_shops',
        'signage_image',
        'visiting_card',
        'shop_image',
        'status',
    ];
    public function beat()
    {
        return $this->belongsTo(BeatMaster::class, 'beat_id');
    }

    public function employeeMaps()
    {
        return $this->hasMany(EmployeeBeatOutletMap::class, 'outlet_id');
    }
}

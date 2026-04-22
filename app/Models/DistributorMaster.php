<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DistributorMaster extends Model
{
    protected $table = 'distributor_master';

    protected $fillable = [
        'cid',
        'distributor_name',
        'address1',
        'address2',
        'address3',
        'district',
        'state',
        'state_code',
        'pincode',
        'phone',
        'mobile',
        'email',
        'gstin',
        'is_active',
        'status', // keep if used elsewhere
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function beats()
    {
        return $this->hasMany(BeatMaster::class, 'distributor_id');
    }

    public function outlets()
    {
        return $this->hasManyThrough(
            OutletMaster::class,
            BeatMaster::class,
            'distributor_id', // FK on beat_master
            'beat_id',        // FK on outlet_master
            'id',             // PK on distributor_master
            'id'              // PK on beat_master
        );
    }
}

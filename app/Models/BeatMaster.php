<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BeatMaster extends Model
{
    protected $table = 'beat_master';

    protected $fillable = ['distributor_id', 'beat_name', 'status'];

    public function distributor()
    {
        return $this->belongsTo(DistributorMaster::class,'distributor_id', 'id');
    }

    public function outlets()
    {
        return $this->hasMany(OutletMaster::class);
    }
}

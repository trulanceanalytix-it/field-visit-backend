<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeBeatOutletMap extends Model
{
    protected $table = 'employee_beat_outlet_map';

    protected $fillable = [
        'employee_id',
        'distributor_id',
        'beat_id',
        'outlet_id',

        'cm_id',
        'cm_name',
        'employee_name',
        'district',
        'town_name',
        's_gr',
        'status',

        'assigned_from',
        'assigned_to',
    ];


    protected $casts = [
        'assigned_from' => 'date',
        'assigned_to'   => 'date',
    ];

    /* ===========================
     |  Relationships
     =========================== */

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'emp_id');
    }

    public function distributor()
    {
        return $this->belongsTo(DistributorMaster::class, 'distributor_id', 'id');
    }

    public function beat()
    {
        return $this->belongsTo(BeatMaster::class, 'beat_id', 'id');
    }

    public function outlet()
    {
        return $this->belongsTo(OutletMaster::class, 'outlet_id', 'id');
    }
}

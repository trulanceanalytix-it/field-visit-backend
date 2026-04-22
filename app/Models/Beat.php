<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Beat extends Model
{
    protected $table = 'beat_masters';

    protected $fillable = [
        'beat_id',
        'beat_name'
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    protected $table = 'store_master';

    protected $fillable = [
        'store_id',
        'store_name',
    ];
}

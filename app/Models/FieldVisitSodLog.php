<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FieldVisitSodLog extends Model
{
    protected $table = 'field_visit_sod_logs';

    protected $fillable = [
        'emp_id',
        'emp_name',
        'sod_date',
        'sod_time',
        'latitude',
        'longitude',
        'address',
        'location_accuracy',
        'selfie_image',
        'device_name',
        'app_version',
        'is_mock_location',
    ];
}
<?php

namespace App\Models;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Employee extends Authenticatable
{
    use HasApiTokens,Notifiable;

    protected $table = 'employee_masters';

    protected $primaryKey = 'emp_id';
    public $incrementing = false; // if emp_id is not auto-increment
    protected $keyType = 'string'; // or 'int' based on your DB

    protected $fillable = [
        'emp_id',
        'emp_name',
        'emp_designation',
        'assigned_region',
        'is_admin',
        'password',
        'status',
    ];
    protected $hidden = [
        'password',
    ];
    protected $casts = [
        'is_admin' => 'boolean',
    ];
    protected $attributes = [
        'status' => 'ACTIVE',
    ];
}

<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles;

    protected $fillable = [
        'name',
        'password',
        'pump_id'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
}

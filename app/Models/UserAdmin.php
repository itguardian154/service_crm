<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAdmin extends Model
{
    protected $table="users_admin";

    protected $fillable = [
        'id_user',
        'name',
        'password',
        'role',
        'is_status'
    ];

    protected $hidden = [
        'password'
    ];

}

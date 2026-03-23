<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserClient extends Model
{
    protected $table="users_client";

    protected $fillable = [
        'id_user_client',
        'name',
        'instansi',
        'email',
        'password',
        'telephone',
        'date_of_birth',
        'address',
        'city',
        'province',
        'img_profile',
        'img_ktp',
        'is_status',
        'type_register'
    ];

    protected $hidden = [
        'password'
    ];

    public $incrementing = false;
}

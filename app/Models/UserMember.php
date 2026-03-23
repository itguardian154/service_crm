<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserMember extends Model
{
    protected $table="users_member";

    protected $fillable = [
        'id_user_client',
        'id_member',
        'type_member',
        'tot_payment',
        'interval_month',
        'start_member',
        'expied_member',
        'image_eMember',
        'is_status'
    ];

    public $incrementing = false;

}

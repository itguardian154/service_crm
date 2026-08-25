<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserMember extends Model
{
    use HasFactory;

    protected $table = 'users_member';

    protected $fillable = [
        'id_user_client',
        'id_member',
        'type_member',
        'tot_payment',
        'interval_month',
        'start_member',
        'expied_member',
        'image_eMember',
        'is_status',
    ];

    protected $casts = [
        'start_member' => 'date',
        'expied_member' => 'date',
        'tot_payment' => 'integer',
        'interval_month' => 'integer',
        'is_status' => 'boolean',
    ];

    public $incrementing = false;

    public function extendUserMembers()
    {
        return $this->hasMany(
            ExtendUserMember::class,
            'user_member_id'
        );
    }
}
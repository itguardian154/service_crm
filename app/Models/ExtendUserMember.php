<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExtendUserMember extends Model
{
    use HasFactory;

    protected $table = 'extend_user_members';

    protected $fillable = [
        'user_member_id',
        'duration_month',
        'amount',
        'extended_from',
        'extended_until',
        'status',
    ];

    protected $casts = [
        'duration_month' => 'integer',
        'amount' => 'integer',
        'extended_from' => 'date',
        'extended_until' => 'date',
    ];

    public function userMember()
    {
        return $this->belongsTo(
            UserMember::class,
            'user_member_id'
        );
    }
}
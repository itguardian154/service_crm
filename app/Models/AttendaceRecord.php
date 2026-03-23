<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendaceRecord extends Model
{
    protected $table="attendace_record";

    protected $fillable = [
        'id_attendace',
        'id_user_client',
        'id_member',
        'tanggal',
        'jam',
        'reff',
    ];

    public $incrementing = false;
}

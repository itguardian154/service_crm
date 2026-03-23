<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// model
use App\Models\UserClient;
use Illuminate\Support\Facades\DB;

// package external
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Carbon\Carbon;

class Generate extends Controller
{
    // Generate ID Client Marketing
    public function idClient()
    {
        $id='-';
        $newDateTime = Carbon::now()->format('Ymdhis');
        $prefix = 'CUS-'.$newDateTime;
        $prefixSubs = substr($prefix,0,10);
     
        $count = DB::table('users_client')
        ->select(DB::raw('COUNT(ID) as count'))
        ->where('id_user_client','like','%'.$prefixSubs.'%')
        ->first();
        
        $formattedNumber = str_pad($count->count, 5, '0', STR_PAD_LEFT);
        $id = $prefix.'-'.$formattedNumber;
        return $id;
    }

    // Generate ID Client Sales
    public function idClientSales()
    {
        $id='-';
        $id = IdGenerator::generate(['table' => 'users_client', 'field' => 'id_user_client', 'length' => 10, 'prefix' => 'SAL-']);
        return $id;
    }
    
    // Generate ID Member
    public function idMember($typeMember)
    {
        $id='-';
        $newDateTime = Carbon::now()->format('ymd');
        $prefix = $typeMember.'-'.$newDateTime;
        $prefixSubs = substr($prefix,0,10);
     
        $count = DB::table('users_member')
        ->select(DB::raw('COUNT(ID) as count'))
        ->where('id_member','like','%'.$prefixSubs.'%')
        ->first();
        
        $formattedNumber = str_pad($count->count, 5, '0', STR_PAD_LEFT);
        $id = $prefix.'-'.$formattedNumber;
        return $id;
    }

    // Generate ID Admin
    public function idAdmin()
    {
        $id='-';
        $id = IdGenerator::generate(['table' => 'users_admin', 'field' => 'id_user', 'length' => 6, 'prefix' => 'ADM-']);
        return $id;
    }

     // Generate ID Attendace Record
     public function idAttendaceRecord()
     {
         $id='-';
         $id = IdGenerator::generate(['table' => 'attendace_record', 'field' => 'id_attendace', 'length' => 15, 'prefix' => 'AR-']);
         return $id;
     }
}

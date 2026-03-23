<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use GuzzleHttp\Client;
// Model
use App\Models\AttendaceRecord;
// Controller 
use App\Http\Controllers\Generate;
// export
use App\Exports\AttendanceRecord as export_AttendaceRecord;
use Maatwebsite\Excel\Facades\Excel;

class AttendanceRecord extends Controller
{
    // public function reedimMember(Request $request){
    //     $status='';
    //     $message='';
    //     $reff='-';
   
    //     $idMember = $request->id_member;
    //     if (isset($request->reff) && $request->reff != '') {
    //         $reff = $request->reff;
    //     }
    //     try {
    //             // get date now
    //             $dateNow = $this->getDateNow();

    //             $c_Server = new Server();
    //             $urlServer=$c_Server->serverLink();
    //             // Cek Existing Data
    //             $userMember_ = DB::table('users_member')
    //             ->select('users_client.id_user_client',
    //             'users_client.name',
    //             'users_member.id_member',
    //             'member.type_member',
    //             'users_member.interval_month',
    //             'users_member.start_member',
    //             'users_member.expied_member',
    //             DB::raw("CONCAT('".$urlServer."',users_client.img_profile) as url_profile"))
    //             ->join('member','member.id_member','users_member.type_member')
    //             ->join('users_client','users_client.id_user_client','users_member.id_user_client')
    //             ->where('users_member.id_member',$idMember)
    //             ->whereDate('users_member.expied_member','>=',$dateNow);

    //             if($userMember_->exists())
    //             {
    //                 $userMember = $userMember_->first();
                    
    //                 // cek double data dalam sehari
    //                 $dataAttendaceRecord_ = DB::table('attendace_record')
    //                 ->select('id')
    //                 ->where('id_member',$userMember->id_member)
    //                 ->where('tanggal',$dateNow);
    //                 if($dataAttendaceRecord_->exists())
    //                 {
    //                     // true double data
    //                     $result=response()->json([
    //                         'status' => 'failed',
    //                         'message' => 'ID Member : sudah di redeem hari ini'
    //                     ]);
    //                 }
    //                 else
    //                 {
    //                     // get time now
    //                     $timeNow = $this->getTimeNow();
    //                     $c_generate = new Generate;
    //                     // get ID Member
    //                     $idAttendaceRecord = $c_generate->idAttendaceRecord(); 
                        
    //                     AttendaceRecord::create([
    //                         'id_attendace' => $idAttendaceRecord,
    //                         'id_user_client' => $userMember->id_user_client,
    //                         'id_member' => $idMember,
    //                         'tanggal' => $dateNow,
    //                         'jam'=> $timeNow,
    //                         'reff'=> $reff
    //                     ]);

    //                     $result=response()->json([
    //                         'status' => 'success',
    //                         'message' => 'User Member Verified',
    //                         'profile' => $userMember
    //                     ]);
    //                 }
    //             }
    //             else
    //             {
    //                 $result=response()->json([
    //                     'status' => 'failed',
    //                     'message' => 'ID Member : '.$idMember.' not exists'
    //                 ]);
    //             }
    //         return $result;
    //     } catch (\Exception $ex) {
    //         return $ex;
    //     }
    // }

    public function reedimMember(Request $request)
    {
        $reff = $request->reff ?? '-';
        $idMember = $request->id_member;

        try {
            $dateNow = $this->getDateNow();
            $timeNow = $this->getTimeNow();

            $c_Server = new Server();
            $urlServer = $c_Server->serverLink();

            // Ambil data member
            $userMemberQuery = DB::table('users_member')
                ->select(
                    'users_client.id_user_client',
                    'users_client.name',
                    'users_member.id_member',
                    'member.type_member',
                    'users_member.interval_month',
                    'users_member.start_member',
                    'users_member.expied_member',
                    DB::raw("CONCAT('".$urlServer."',users_client.img_profile) as url_profile")
                )
                ->join('member','member.id_member','=','users_member.type_member')
                ->join('users_client','users_client.id_user_client','=','users_member.id_user_client')
                ->where('users_member.id_member',$idMember);

            if (!$userMemberQuery->exists()) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Member tidak terdaftar'
                ]);
            }

            $userMember = $userMemberQuery->first();

            // Cek expired
            if ($userMember->expied_member < $dateNow) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'ID Member sudah expired'
                ]);
            }

            // Cek sudah redeem hari ini
            $alreadyRedeemed = DB::table('attendace_record')
                ->where('id_member',$userMember->id_member)
                ->where('tanggal',$dateNow)
                ->exists();

            if ($alreadyRedeemed) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Data sudah diredeem hari ini'
                ]);
            }

            // Generate ID attendance
            $c_generate = new Generate();
            $idAttendaceRecord = $c_generate->idAttendaceRecord(); 

            // Simpan ke database
            AttendaceRecord::create([
                'id_attendace'   => $idAttendaceRecord,
                'id_user_client' => $userMember->id_user_client,
                'id_member'      => $idMember,
                'tanggal'        => $dateNow,
                'jam'            => $timeNow,
                'reff'           => $reff
            ]);

            return response()->json([
                'status'  => 'success',
                'message' => 'User Member Verified',
                'profile' => $userMember
            ]);

        } catch (\Exception $ex) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan: '.$ex->getMessage()
            ], 500);
        }
    }


    private function getDateNow()
    {
        $newDateTime = Carbon::now();
        return $newDateTime->format('Y-m-d');
    }
    
    private function getTimeNow()
    {
        $newDateTime = Carbon::now();
        return $newDateTime->format('H:i:m');
    }

    public function getAllAttendanceRecord(Request $request)
    {
        try {
            $dateStart = $request->date_start;
            $dateEnd = $request->date_end;

            $listAttendanceRecord_ =DB::table('attendace_record')
            ->select('attendace_record.id_attendace',
            'attendace_record.id_user_client',
            'attendace_record.id_member',
            'attendace_record.tanggal',
            'attendace_record.jam',
            'users_client.name',
            'users_client.email',
            'users_client.telephone',
            'users_client.date_of_birth',
            'users_client.address',
            'users_client.city',
            'users_client.province')
            ->join('users_client','users_client.id_user_client','attendace_record.id_user_client');

            // sameDay
            // if($dateStart==$dateEnd)
            // {
            //     $listAttendanceRecord_->where('attendace_record.tanggal','=',$dateStart);
            // }
            // else
            // {
            //     $listAttendanceRecord_->whereBetween('attendace_record.tanggal', array($dateStart." 00:00:00", $dateEnd." 23:59:59"));
            // }
            $listAttendanceRecord_->whereBetween('attendace_record.tanggal', array($dateStart." 00:00:00", $dateEnd." 23:59:59"));
            $listAttendanceRecord_->orderBy('attendace_record.created_at','asc');
            $listAttendanceRecord = $listAttendanceRecord_->get();
            return response()->json([
            'status' => 'success',
            'data' => $listAttendanceRecord
            ]);
      
        } catch (\Exception $ex) {
            return $ex;
        }
    }


    public function exportAttendanceRecord(Request $request) {
       $dateStart = $request->date_start;
       $dateEnd = $request->date_end;
        try {  
            return Excel::download(new export_AttendaceRecord($dateStart,$dateEnd), 'Export-Attendance Record-'.$dateStart.'-'.$dateEnd.'.xlsx');
        } catch (\Exception $ex) {
            return $ex;
        }
    }
}

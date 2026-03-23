<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use GuzzleHttp\Client;

// Model
use App\Models\UserClient;
use App\Models\UserMember;
use App\Models\AttendaceRecord;
// Controller 
use App\Http\Controllers\Generate;
use App\Http\Controllers\ClassUploadImage;
// Email
use Mail;
use App\Mail\EMemberMail;
// export
use App\Exports\UserMember as export_userMember;
use Maatwebsite\Excel\Facades\Excel;

class UserMemberSalesController extends Controller
{
    // public function register(Request $request){
    //     $status='';
    //     $message='';

    //     $idUserClient = $request->id_user_client;
    //     $typeMember = $request->type_member;
    //     $startMember = $request->start_member;
    //     $totPayment = $request->tot_payment;
    //     $startMember = $request->start_member;
    //     $expiedMember = $request->expied_member;

    //     try {
    //         DB::beginTransaction();
    //         // Cek Existing Data
    //         $user_ = UserClient::where('id_user_client',$idUserClient);
        
    //         if($user_->exists())
    //         {
    //             $userMember_ = DB::table('users_member')
    //             ->select('id')->where('id_user_client',$idUserClient)->where('type_member',$typeMember);

    //             if($userMember_->exists())
    //             {
    //                 $result=response()->json([
    //                     'status' => 'failed',
    //                     'message' => 'ID User Client : '.$idUserClient.' exists type Member '. $typeMember
    //                 ]);
    //             }
    //             else
    //             {
    //                 $user=$user_->first();
    //                 $c_generate = new Generate;
    //                 // get ID Member
    //                 $idMember = $c_generate->idMember($typeMember); 

    //                 $expiedDate ='2023-12-31';

                   
    //                 $c_uploadImage = new ClassUploadImage();
    //                 $urlPathImgProfile = $c_uploadImage->mergeImagesSales($idUserClient,$idMember,$expiedDate);
               
    //                 UserMember::create([
    //                     'id_user_client' => $idUserClient,
    //                     'id_member' => $idMember,
    //                     'type_member' => $typeMember,
    //                     'tot_payment' => $totPayment,
    //                     'interval_month'=> $expiedMember,
    //                     'start_member' => $startMember,
    //                     'expied_member' => $expiedDate,
    //                     'image_eMember' => $urlPathImgProfile,
    //                     'is_status' => '1',
    //                 ]);
    
    //                 $result=response()->json([
    //                     'status' => 'success',
    //                     'message' => 'User Member created successfully'
    //                 ]);
    //             }
    //         }
    //         else
    //         {
    //             $result=response()->json([
    //                 'status' => 'failed',
    //                 'message' => 'ID User Client : '.$idUserClient.' not exists'
    //             ]);
    //         }
    //         DB::commit();
    //         return $result;
    //     } catch (\Exception $ex) {
    //         DB::rollBack();
    //         return $ex;
    //     }
    // }

    public function reMember(Request $request){
        $status='';
        $message='';

        $idUserClient = $request->id_user_client;
        $typeMember = $request->type_member;

        try {
            DB::beginTransaction();
            // Cek Existing Data
            $user_ = UserClient::where('id_user_client',$idUserClient);
        
            if($user_->exists())
            {
                $userMember_ = DB::table('users_member')
                ->select('id')->where('id_user_client',$idUserClient)->where('type_member',$typeMember);

                if($userMember_->exists())
                {
                    $result=response()->json([
                        'status' => 'failed',
                        'message' => 'ID User Client : '.$idUserClient.' exists type Member '. $typeMember
                    ]);
                }
                else
                {
                    $user=$user_->first();
                   
                    $c_uploadImage = new ClassUploadImage();
                    $urlPathImgProfile = $c_uploadImage->mergeImagesSales($idUserClient,$idMember,$expiedDate);
               
                    $result=response()->json([
                        'status' => 'success',
                        'message' => 'Re-Member successfully'
                    ]);
                }
            }
            else
            {
                $result=response()->json([
                    'status' => 'failed',
                    'message' => 'ID User Client : '.$idUserClient.' not exists'
                ]);
            }
            DB::commit();
            return $result;
        } catch (\Exception $ex) {
            DB::rollBack();
            return $ex;
        }
    }
}

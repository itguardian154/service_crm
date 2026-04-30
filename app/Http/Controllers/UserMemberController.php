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
// Whatsapp
use App\Services\WhatsAppService;
// export
use App\Exports\UserMember as export_userMember;
use Maatwebsite\Excel\Facades\Excel;

use Illuminate\Support\Facades\Log;

class UserMemberController extends Controller
{
    protected $whatsAppService;

    public function __construct(WhatsAppService $whatsAppService)
    {
        $this->whatsAppService = $whatsAppService;
    }

    public function register(Request $request){
        $status='';
        $message='';

        $idUserClient = $request->id_user_client;
        $typeMember = $request->type_member;
        $totPayment = $request->tot_payment;
        $startMember = $request->start_member;
        $expiedMember = $request->expied_member;

        try {
            // DB::beginTransaction();
            // Cek Existing Data
            $user_ = UserClient::where('id_user_client',$idUserClient);
        
            if($user_->exists())
            {
                // $userMember_ = DB::table('users_member')
                // ->select('id')->where('id_user_client',$idUserClient)->where('type_member',$typeMember);

                // if($userMember_->exists())
                // {
                //     $result=response()->json([
                //         'status' => 'failed',
                //         'message' => 'ID User Client : '.$idUserClient.' exists type Member '. $typeMember
                //     ]);
                // }
                // else
                // {
                $userMemberExists = DB::table('users_member')
                    ->where('id_user_client', $idUserClient)
                    ->where('type_member', $typeMember)
                    ->where('expied_member', '>=', now()) // optional: only block if active
                    ->exists();
                
                if ($userMemberExists) {
                    return response()->json([
                        'status'  => 'failed',
                        'message' => 'ID User Client: ' . $idUserClient . ' already has active member type: ' . $typeMember,
                    ]);
                } else {
                
                    $user=$user_->first();

                    // punya Sales
                    if($typeMember=='B')
                    {
                        $expiedDate = $this->addMonth($startMember,$expiedMember);
                        $this->register_member_sales($typeMember,$idUserClient,$expiedDate,$totPayment,$expiedMember,$startMember);
                    }
                
                    // punya Marketing
                    else
                    { 
                        if($typeMember=='M')
                        {
                            $start = Carbon::parse($startMember);
                            $expired = Carbon::parse($expiedMember);
                            $intervalMonth = $start->diffInMonths($expired);
                            $this->register_member_marketing($typeMember,$idUserClient,$expiedMember,$totPayment,$intervalMonth,$startMember);
                        }
                        else
                        {
                            // calculate expired date
                            $expiedDate = $this->addMonth($startMember,$expiedMember);
                            $this->register_member_marketing($typeMember,$idUserClient,$expiedDate,$totPayment,$expiedMember,$startMember);
                        }
                    }
    
                    $result=response()->json([
                        'status' => 'success',
                        'message' => 'User Member created successfully'
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
            // DB::commit();
            return $result;
        } catch (\Exception $ex) {
            // DB::rollBack();
            return $ex;
        }
    }

    public function register_member_marketing($typeMember,$idUserClient,$expiedDate,$totPayment,$expiedMember,$startMember)
    {
        $c_generate = new Generate;
        // get ID Member
        $idMember = $c_generate->idMember($typeMember); 

        $c_uploadImage = new ClassUploadImage();
        $urlPathImgProfile = $c_uploadImage->mergeImages($idUserClient,$typeMember,$idMember,$expiedDate);
   
        UserMember::create([
            'id_user_client' => $idUserClient,
            'id_member' => $idMember,
            'type_member' => $typeMember,
            'tot_payment' => $totPayment,
            'interval_month'=> $expiedMember,
            'start_member' => $startMember,
            'expied_member' => $expiedDate,
            'image_eMember' => $urlPathImgProfile,
            'is_status' => '1',
        ]);
        
        // // email
        // $this->sentEmail($idMember);

        // whatsapp
        $this->sentWhatsapp($idMember);
        
    }

    public function register_member_sales($typeMember,$idUserClient,$expiedDate,$totPayment,$expiedMember,$startMember)
    {
        $c_generate = new Generate;
        // get ID Member
        $idMember = $c_generate->idMember($typeMember); 
        
        $c_uploadImage = new ClassUploadImage();
        $urlPathImgProfile = $c_uploadImage->mergeImagesSales($idUserClient,$idMember,$expiedDate);
   
        UserMember::create([
            'id_user_client' => $idUserClient,
            'id_member' => $idMember,
            'type_member' => $typeMember,
            'tot_payment' => $totPayment,
            'interval_month'=> $expiedMember,
            'start_member' => $startMember,
            'expied_member' => $expiedDate,
            'image_eMember' => $urlPathImgProfile,
            'is_status' => '1',
        ]);
    }

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
                ->select('id_member','expied_member')->where('id_user_client',$idUserClient)->where('type_member',$typeMember);

                if($userMember_->exists())
                {
                    $usersMember = $userMember_->first();
                    $idMember= $usersMember->id_member;
                    $expiedDate = $usersMember->expied_member;

                    // punya sales
                    if($typeMember=='B')
                    {
                        $c_uploadImage = new ClassUploadImage();
                        $urlPathImgProfile = $c_uploadImage->mergeImagesSales($idUserClient,$idMember,$expiedDate);
                    }
                    // punya marketing
                    else
                    {
                        $c_uploadImage = new ClassUploadImage();
                        $urlPathImgProfile = $c_uploadImage->mergeImages($idUserClient,$typeMember,$idMember,$expiedDate);
               
                        // whatsapp
                        $this->sentWhatsapp($idMember);
                        
                        // // email
                        // $this->sentEmail($idMember);
                    }
                   
                    $result=response()->json([
                        'status' => 'success',
                        'message' => 'Re-Member successfully'
                    ]);
                }
                else
                {
                    $result=response()->json([
                        'status' => 'failed',
                        'message' => 'ID Member : '.$idMember.' not exists'
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

    private function addMonth($date,$month)
    {
        $newDateTime = Carbon::parse($date)->addMonth($month);
        return $newDateTime->format('Y-m-d');
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
    /////////////////////////////////////////////////////////////////////////////////////////////////////

    public function sentEMemberEmail(Request $request)
    {
        $idMember = $request->id_member;
        try{

            $result = $this->sentEmail($idMember);
            return $result;
        } catch (\Exception $ex) {
            return $ex;
        }
    }

    public function sentEmail($idMember)
    {
            // Cek Existing Data // Cek Existing Data
            $user_ =DB::table('users_member')
            ->select('users_member.image_eMember','users_client.name','users_client.email','users_member.expied_member','users_member.interval_month')
            ->join('users_client','users_client.id_user_client','users_member.id_user_client')
            ->where('users_member.id_member',$idMember);
            
            if($user_->exists())
            {
                $user = $user_->first();

                $email=$user->email;
                $name=$user->name;
                $expiedDate = $user->expied_member;
                $intervalMonth = $user->interval_month;
                
                $data["email"] = $email;
                $data["title"] = "E-Member Salokapark";
                $data["body"] = "Member Active";
                $data["name"] = $name;
                $data["expiredMember"] = $expiedDate;
                $data["idMember"] = $idMember;
                $data["intervalMonth"] = $intervalMonth;

                $files = [
                    // public_path('img/membership-template-tc.png'),
                    public_path('storage/'.$user->image_eMember),
                ];

                Mail::send('email.EMember_Mail', $data, function($message)use($data, $files) {
                    $message->to($data["email"], $data["email"])
                            ->subject($data["title"]);
         
                    foreach ($files as $file){
                        $message->attach($file);
                    }
                    
                });
                
                $result=response()->json([
                    'status' => 'success',
                    'message' => 'Email is sent successfully.'
                ]);

            }
            else
            {
                $result=response()->json([
                    'status' => 'failed',
                    'message' => 'ID Member : '.$idMember.' not exists'
                ]);
            }
            return $result;
    }

    public function sentEMemberWhastapp(Request $request)
    {
        $idMember = $request->id_member;
        try{
            $result = $this->sentWhatsapp($idMember);
            return $result;
        } catch (\Exception $ex) {
            return $ex;
        }
    }

    public function sentWhatsapp($idMember)
    {
        Log::info('Sending WhatsApp notification for Member ID: ' . $idMember);

        $c_Server = new Server();
        $urlServer = $c_Server->serverLink();

        $user_ = DB::table('users_member')
            ->select(
                'users_member.image_eMember',
                'users_client.name',
                'users_client.telephone',
                'users_member.expied_member',
                DB::raw("CONCAT('" . $urlServer . "', users_member.image_eMember) as image_eMember"),
                'users_member.interval_month'
            )
            ->join('users_client', 'users_client.id_user_client', '=', 'users_member.id_user_client')
            ->where('users_member.id_member', $idMember);

        if (!$user_->exists()) {
            return response()->json([
                'status'  => 'failed',
                'message' => 'ID Member : ' . $idMember . ' not exists'
            ]);
        }

        $user = $user_->first();

        $requestCaption = [];
        $requestCaption['name'] = $user->name;
        $requestCaption['interval_month'] = $user->interval_month;

        $classCaptionWhatsapp = new ClassCaptionWhatsapp();
        $whatsappMessage = $classCaptionWhatsapp->captionMemberTypeB($requestCaption);

        $result = $this->whatsAppService->sendImage(
            $user->telephone,
            $whatsappMessage,
            $user->image_eMember,
            $user->name,
            'Membership Notification'
        );

        Log::info('WhatsApp notification result for Member ID ' . $idMember . ': ' . json_encode($result)); 

        return response()->json([
            'status'  => $result['success'] ? 'success' : 'failed',
            'message' => $result['message'],
            'data'    => $result['data'] ?? null,
            'error'   => $result['error'] ?? null,
        ]);
    }

    public function sentWhatsappSyaratDanKetentuan($telephone)
    {
        $result = $this->whatsAppService->sendImage(
            $telephone,
            '',
            config('whatsapp.assets.syarat_ketentuan'),
            'Member',
            'Syarat & Ketentuan Membership'
        );

        return response()->json([
            'status'  => $result['success'] ? 'success' : 'failed',
            'message' => $result['message'],
            'data'    => $result['data'] ?? null,
            'error'   => $result['error'] ?? null,
        ]);
    }

    ///////////////////////////////////////////////////////////////
    public function getCount()
    {
        try{
            $totMember ='0';
            $dateNow = Carbon::now()->format('Y-m-d');
            $data = DB::table('users_member')
            ->select(DB::raw('COUNT(id) as total_member'))
            ->where('created_at','like', "%{$dateNow}%")
            ->first();
            $totMember=$data->total_member;
            $result=response()->json([
                'status' => 'success',
                'member_today' => $totMember
            ]);
            return $result;
        } catch (\Exception $ex) {
            DB::rollBack();
            return $ex;
        }
    }

    public function getCountAttendanceRecord()
    {
        try{
            $totMember ='0';
            $dateNow = Carbon::now()->format('Y-m-d');
            $data = DB::table('attendace_record')
            ->select(DB::raw('COUNT(id) as total_member'))
            ->whereBetween('created_at', [
                $dateNow. ' 00:00:00',
                $dateNow. ' 23:59:59'
            ])
            ->first();
            $totMember=$data->total_member;
            $result=response()->json([
                'status' => 'success',
                'attendace_member_today' => $totMember
            ]);
            return $result;
        } catch (\Exception $ex) {
            DB::rollBack();
            return $ex;
        }
    }

    public function getAllDataMember(Request $request)
    {
        $typeMember = $request->type_member;
     
        try {
            // Cek Existing Data // Cek Existing Data
            $c_Server = new Server();
            $urlServer=$c_Server->serverLink();

            $user_ =DB::table('users_client')
            ->select('users_client.id_user_client',
            'users_client.name','users_client.email',
            'users_client.telephone',
            'users_client.date_of_birth',
            'users_client.address',
            'users_client.city',
            'users_client.province',
            'users_member.id_member',
            'users_member.type_member',
            'users_member.tot_payment',
            'users_member.interval_month',
            'users_member.start_member',
            'users_member.expied_member',
            DB::raw("CONCAT('".$urlServer."',users_member.image_eMember) as image_eMember"),
            DB::raw("CONCAT('".$urlServer."',users_client.img_profile) as img_profile"),
            DB::raw("CONCAT('".$urlServer."',users_client.img_ktp) as img_ktp"),
            'users_client.is_status')
            ->join('users_member','users_member.id_user_client','users_client.id_user_client');
            if($typeMember=='99')
            {
                // all data
            }
            else
            {
                $user_->where('type_member',$typeMember);
            }
       
            if($user_->exists())
            {
                $user = $user_->get();
                    return response()->json([
                    'status' => 'success',
                    'user' => $user
                ]);
            }
            else
            {
                $result=response()->json([
                    'status' => 'failed',
                    'message' => 'Data not exists'
                ]);
            }
            return $result;
        } catch (\Exception $ex) {
            return $ex;
        }
    }

    public function getTypeMember()
    {
        try {

            $member_ =DB::table('member')
            ->select('member.id_member',
            'member.type_member','member.is_status');
            if($member_->exists())
            {
                $listTypeMember = $member_->get();
                    return response()->json([
                    'status' => 'success',
                    'data' => $listTypeMember
                ]);
            }
            else
            {
                $result=response()->json([
                    'status' => 'failed',
                    'message' => 'Data not exists'
                ]);
            }
            return $result;
        } catch (\Exception $ex) {
            return $ex;
        }
    }

    public function exportMember() {
        try {  
        
            return Excel::download(new export_userMember(), 'Export-User Member.xlsx');
        } catch (\Exception $ex) {
            return $ex;
        }
    }

    // public function editMember()
    // {
    //     $idUserClient = $request->id_user_client;
    //     $typeMember = $request->type_member;
    //     $totPayment = $request->tot_payment;
    //     $startMember = $request->start_member;
    //     $expiedMember = $request->expied_member;
        
    //     try {
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
    //                 if($typeMember=='A')
    //                 {
    //                     $expiedDate ='2023-11-30';
    //                 }
    //                 elseif($typeMember=='B')
    //                 {
    //                     $expiedDate ='2023-12-31';
    //                 }
    //                 else
    //                 {
    //                     // calculate expired date
    //                     $expiedDate = $this->addMonth($startMember,$expiedMember);
    //                 }
    //                 $c_uploadImage = new ClassUploadImage();
    //                 $urlPathImgProfile = $c_uploadImage->mergeImages($idUserClient,$idMember,$expiedDate);
                    
    //                 // update table
    //                 DB::table('users_member')
    //                 ->where('id_user_client',$idUserClient)->where('type_member',$typeMember)
    //                 ->update([
    //                     'type_member' => $typeMember,
    //                     'tot_payment' => $totPayment,
    //                     'interval_month'=> $expiedMember,
    //                     'start_member' => $startMember,
    //                     'expied_member' => $expiedDate,
    //                     'image_eMember' => $urlPathImgProfile
    //                 ]);

    //                 // whatsapp
    //                 $this->sentWhatsapp($idMember);
                    
    //                 // email
    //                 $this->sentEmail($idMember);
    
    //                 $result=response()->json([
    //                     'status' => 'success',
    //                     'message' => 'Edited Member successfully'
    //                 ]);
    //             }

    //         return $result;
    //     } catch (\Exception $ex) {
    //         return $ex;
    //     }
    // }
}

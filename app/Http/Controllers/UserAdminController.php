<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
// Model
use App\Models\UserAdmin;

class UserAdminController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('auth:api', ['except' => ['login','register']]);
    // }

    public function register(Request $request){
        $status='';
        $message='';
    
        $request->validate([
            'id_user' => 'required|int',
            'name' => 'required|string|max:255',
            'password' => 'required|string|min:6',
        ]);

        try {
            // Cek Existing Data
            $user_ =UserAdmin::where('id_user',$request->id_user);
            if($user_->exists())
            {
                $result=response()->json([
                    'status' => 'failed',
                    'message' => 'User ID : '.$request->id_user.' exists'
                ]);
            }
            else
            {
                // $token = Auth::login($user);
             
                // Insert To table user_admin
                $user = UserAdmin::create([
                    'id_user' => $request->id_user,
                    'name' => $request->name,
                    'password' => Crypt::encryptString($request->password),
                    'role'=>$request->role,
                    // status 1 = active, status 0 = non active
                    'is_status'=> '1'
                ]);
                return $user;
                $result=response()->json([
                    'status' => 'success',
                    'message' => 'User created successfully',
                    'user' => $user
                    // 'authorisation' => [
                    //     'token' => $token,
                    //     'type' => 'bearer',
                    // ]
                ]);
            }
            return $result;
        } catch (\Exception $ex) {
            return$ex;
        }
    }


    public function login(Request $request)
    {
      
        $request->validate([
            'id_user' => 'required|int',
            'password' => 'required|string|min:4',
        ]);
    
        try {
            // Cek Existing Data // Cek Existing Data
            $user_ =UserAdmin::where('id_user',$request->id_user);
       
            if($user_->exists())
            {
                // $credentials = $request->only('id_user', 'password');
             
                // $token = Auth::attempt($credentials);
                // if (!$token) {
                //     return response()->json([
                //         'status' => 'error',
                //         'message' => 'Unauthorized',
                //     ], 401);
                // }
        
                // $user = Auth::user();
                $user = $user_->first();
                  
                if(Crypt::decryptString($user->password) == $request->password)
                {
                    return response()->json([
                        'status' => 'success',
                        'user' => $user
                        // 'authorisation' => [
                        //     'token' => $token,
                        //     'type' => 'bearer',
                        // ]
                        ]);
                }
                else
                {
                    $result=response()->json([
                        'status' => 'failed',
                        'message' => 'Password invalid'
                    ]);
                }

            }
            else
            {
                $result=response()->json([
                    'status' => 'failed',
                    'message' => 'User ID : '.$request->id_user.' not exists'
                ]);
            }
            return $result;
        } catch (\Exception $ex) {
            return $ex;
        }
    }


    public function getAllData()
    {
        try {
            // Cek Existing Data // Cek Existing Data
            $user_ =DB::table('users_admin')
            ->select('id_user','name','role','is_status');
       
            if($user_->exists())
            {
                $user = $user_->get();
                    return response()->json([
                    'status' => 'success',
                    'user' => $user
                    // 'authorisation' => [
                    //     'token' => $token,
                    //     'type' => 'bearer',
                    // ]
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

    public function getByIDData(Request $request)
    {
        try {
    
            // Cek Existing Data // Cek Existing Data
            $user_ =DB::table('users_admin')
            ->select('id_user','name','is_status','role')
            ->where('id_user',$request->id_user);
         
            if($user_->exists())
            {
                $user = $user_->first();
                    return response()->json([
                    'status' => 'success',
                    'user' => $user
                    // 'authorisation' => [
                    //     'token' => $token,
                    //     'type' => 'bearer',
                    // ]
                ]);
            }
            else
            {
                $result=response()->json([
                    'status' => 'failed',
                    'message' => 'User ID : '.$request->id_user.' not exists'
                ]);
            }
            return $result;
        } catch (\Exception $ex) {
            return $ex;
        }
    }

    public function disableByIdData(Request $request)
    {
        try {
            // Cek Existing Data // Cek Existing Data
            $user_ =DB::table('users_admin')
            ->select('id_user','name','is_status')
            ->where('id_user',$request->id_user);
         
            if($user_->exists())
            {
                    DB::table('users_admin')
                    ->where('id_user','=',$request->id_user)
                    ->update([
                        'is_status' => '2',
                    ]);

                    return response()->json([
                    'status' => 'success',
                    'message' => 'User ID : '.$request->id_user.' has disabled'
                ]);
            }
            else
            {
                $result=response()->json([
                    'status' => 'failed',
                    'message' => 'User ID : '.$request->id_user.' not exists'
                ]);
            }
            return $result;
        } catch (\Exception $ex) {
            return $ex;
        }
    }

    public function enableByIdData(Request $request)
    {
        try {
            // Cek Existing Data // Cek Existing Data
            $user_ =DB::table('users_admin')
            ->select('id_user','name','is_status')
            ->where('id_user',$request->id_user);
         
            if($user_->exists())
            {
                    DB::table('users_admin')
                    ->where('id_user','=',$request->id_user)
                    ->update([
                        'is_status' => '1',
                    ]);

                    return response()->json([
                    'status' => 'success',
                    'message' => 'User ID : '.$request->id_user.' has enabled'
                ]);
            }
            else
            {
                $result=response()->json([
                    'status' => 'failed',
                    'message' => 'User ID : '.$request->id_user.' not exists'
                ]);
            }
            return $result;
        } catch (\Exception $ex) {
            return $ex;
        }
    }

    public function removeById(Request $request)
    {
        try {
            // Cek Existing Data // Cek Existing Data
            $user_ =DB::table('users_admin')
            ->select('id_user','name','is_status')
            ->where('id_user',$request->id_user);
         
            if($user_->exists())
            {
                    $user_->delete();

                    return response()->json([
                    'status' => 'success',
                    'message' => 'User ID : '.$request->id_user.' has removed'
                ]);
            }
            else
            {
                $result=response()->json([
                    'status' => 'failed',
                    'message' => 'User ID : '.$request->id_user.' not exists'
                ]);
            }
            return $result;
        } catch (\Exception $ex) {
            return $ex;
        }
    }

    public function resetPassword(Request $request)
    {
        try {
            // Cek Existing Data // Cek Existing Data
            $user_ =DB::table('users_admin')
            ->select('id_user','name','is_status')
            ->where('id_user',$request->id_user);
         
            if($user_->exists())
            {
                    DB::table('users_admin')
                    ->where('id_user','=',$request->id_user)
                    ->update([
                        'password' => Crypt::encryptString($request->id_user),
                    ]);

                    return response()->json([
                    'status' => 'success',
                    'message' => 'Reset Password User ID : '.$request->id_user.' success'
                ]);
            }
            else
            {
                $result=response()->json([
                    'status' => 'failed',
                    'message' => 'User ID : '.$request->id_user.' not exists'
                ]);
            }
            return $result;
        } catch (\Exception $ex) {
            return $ex;
        }
    }

    public function editProfile(Request $request)
    {
        try {
            // Cek Existing Data // Cek Existing Data
            $user_ =DB::table('users_admin')
            ->select('id_user','name','is_status')
            ->where('id_user',$request->id_user);
         
            if($user_->exists())
            {
                    DB::table('users_admin')
                    ->where('id_user','=',$request->id_user)
                    ->update([
                        'name' => $request->name,
                        'password' => Crypt::encryptString($request->password),
                        'role'=>$request->role
                    ]);

                    return response()->json([
                    'status' => 'success',
                    'message' => 'User ID : '.$request->id_user.' has edited'
                ]);
            }
            else
            {
                $result=response()->json([
                    'status' => 'failed',
                    'message' => 'User ID : '.$request->id_user.' not exists'
                ]);
            }
            return $result;
        } catch (\Exception $ex) {
            return $ex;
        }
    }


}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
// Model
use App\Models\UserClient;
// Controller 
use App\Http\Controllers\Generate;
use App\Http\Controllers\ClassUploadImage;
// export
use App\Exports\UserClient as export_userClient;
use Maatwebsite\Excel\Facades\Excel;

class UserClientController extends Controller
{

    // public function register(Request $request){
    //     $status='';
    //     $message='';
    //     $typeRegister = $request->type_register;
    //     try {
            
    //             // Cek Existing Data
    //             // $user_ = UserClient::where('email',$request->email);
    //             // if($user_->exists())
    //             // {
    //             //     $result=response()->json([
    //             //         'status' => 'failed',
    //             //         'message' => 'Email : '.$request->email.' exists'
    //             //     ]);
    //             // }
    //             // else
    //             // {
    //                 // 1= marketing ; 2=sales
    //                 if($typeRegister=='1')
    //                 {
    //                     $this->register_client_marketing($request);
    //                 }
    //                 elseif($typeRegister=='2')
    //                 {
    //                     $this->register_client_sales($request);
    //                 }

    //                 $user = $this->getProfileUserClinet($request->email);
    //                 $result=response()->json([
    //                     'status' => 'success',
    //                     'message' => 'User created successfully',
    //                     'user' => $user
    //                 ]);
    //             // }
            
    //         return $result;
    //     } catch (\Exception $ex) {
    //         return $ex;
    //     }
    // }

    public function register(Request $request)
    {
        try {
            $typeRegister = $request->type_register;

            // Format dan validasi nomor telepon
            $telephone = $this->formatTelephone($request->telephone);
            if ($telephone === false) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Nomor telepon tidak valid. Pastikan hanya berisi angka dan minimal 9 digit.'
                ], 422);
            }

            // Gabungkan hasil ke request
            $request->merge(['telephone' => $telephone]);

            if ($typeRegister == '1') {
                $this->register_client_marketing($request);
            } elseif ($typeRegister == '2') {
                $this->register_client_sales($request);
            } else {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Tipe registrasi tidak valid',
                ], 400);
            }

            $user = $this->getProfileUserClinet($request->email);
            return response()->json([
                'status' => 'success',
                'message' => 'User created successfully',
                'user' => $user
            ]);

        } catch (\Exception $ex) {
            return response()->json([
                'status' => 'failed',
                'message' => $ex->getMessage()
            ], 500);
        }
    }
    
    private function register_client_marketing($request)
    {
     
            $c_generate = new Generate;
            // get ID Client
            $id_user = $c_generate->idClient(); 

            $urlPathImgProfile='-';
            $urlPathImgKtp = '-';
            if($request->file('img_profile')!=null)
            {
                // call class upload Image Profile
                $c_uploadImage = new ClassUploadImage();
                $urlPathImgProfile = $c_uploadImage->processImage_profile($request->file('img_profile'),$id_user);
            }
         
            if($request->file('img_ktp') !=null)
            {
                // call class upload Image KTP
                $c_uploadImage = new ClassUploadImage();
                $urlPathImgKtp = $c_uploadImage->processImage_ktp($request->file('img_ktp'),$id_user);
            }
        
            $user = UserClient::create([
                'id_user_client' => $id_user,
                'name' => $request->name,
                'instansi' => '-',
                'email' => $request->email,
                'password' => Crypt::encryptString($request->password),
                'telephone' => $request->telephone,
                'date_of_birth' => $request->date_of_birth,
                'address' => $request->address,
                'city' => $request->city,
                'province' => $request->province,
                'img_profile' => $urlPathImgProfile,
                'img_ktp' => $urlPathImgKtp,
                'type_register' => $request->type_register,
                'is_status' => '1',
            ]);
    }

    private function register_client_sales($request)
    {
            $c_generate = new Generate;
            // get ID Client
            $id_user = $c_generate->idClientSales(); 

            // call class upload Image Profile
            $c_uploadImage = new ClassUploadImage();
            $urlPathImgProfile = $c_uploadImage->processImage_profile($request->file('img_profile'),$id_user);
        
            $user = UserClient::create([
                'id_user_client' => $id_user,
                'name' => $request->name,
                'instansi' => $request->instansi,
                'email' => $request->email,
                'password' => Crypt::encryptString($request->password),
                'telephone' => $request->telephone,
                'date_of_birth' => $request->date_of_birth,
                'address' => $request->address,
                'city' => $request->city,
                'province' => $request->province,
                'img_profile' => $urlPathImgProfile,
                'img_ktp' => '-',
                'type_register' => $request->type_register,
                'is_status' => '1',
            ]);
    }
    // -----------------------end Register-------------------------------------------------------

    public function getProfileUserClinet($email)
    {
        $c_Server = new Server();
        $urlServer=$c_Server->serverLink();
        
        $user = DB::table('users_client')
        ->select('id_user_client',
        'name',
        'instansi',
        'email',
        'telephone',
        'date_of_birth',
        'address',
        'city',
        'province',
        DB::raw("CONCAT('".$urlServer."',img_profile) as img_profile"),
        DB::raw("CONCAT('".$urlServer."',img_ktp) as img_ktp"),
        'type_register',
        'is_status')
        ->where('email',$email)
        ->first();
        return $user;
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string|min:6',
        ]);
     
        try {
            // Cek Existing Data // Cek Existing Data
            $user_ =UserClient::where('email',$request->email);
            
            if($user_->exists())
            {
             
                   $user = $user_->first();
                   if(Crypt::decryptString($user->password) == $request->password)
                   {    

                        $user = $this->getProfileUserClinet($request->email);
                        return response()->json([
                        'status' => 'success',
                        'user' => $user
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
                    'message' => 'Email : '.$request->email.' not exists'
                ]);
            }
            return $result;
        } catch (\Exception $ex) {
            return $ex;
        }
    }

    public function getAllData(Request $request)
    {
        $typeRegister = $request->type_register;
        try {
            // Cek Existing Data // Cek Existing Data
            $c_Server = new Server();
            $urlServer=$c_Server->serverLink();

            $user_ =DB::table('users_client')
            ->select('id_user_client','name','instansi','email','telephone','date_of_birth','address','city','province','type_register',
            DB::raw("CONCAT('".$urlServer."',img_profile) as img_profile"),
            DB::raw("CONCAT('".$urlServer."',img_ktp) as img_ktp"),'is_status')
            ->where('type_register','like', "%{$typeRegister}%");
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

    public function getProfile(Request $request)
    {
        try {
            // Cek Existing Data // Cek Existing Data
            $c_Server = new Server();
            $urlServer=$c_Server->serverLink();
            
            $user_ =DB::table('users_client')
            ->select('id_user_client','name','instansi','email','telephone','date_of_birth','address','city','province','type_register',
            DB::raw("CONCAT('".$urlServer."',img_profile) as img_profile"),
            DB::raw("CONCAT('".$urlServer."',img_ktp) as img_ktp"),'is_status')
            ->where('id_user_client',$request->id_user_client);
         
            if($user_->exists())
            {
                $user = $user_->first();
                    return response()->json([
                    'status' => 'success',
                    'user' => $user

                ]);
            }
            else
            {
                $result=response()->json([
                    'status' => 'failed',
                    'message' => 'User ID : '.$request->id_user_client.' not exists'
                ]);
            }
            return $result;
        } catch (\Exception $ex) {
            return $ex;
        }
    }

    public function disableClient(Request $request)
    {
        try {
            // Cek Existing Data // Cek Existing Data
            $user_ =DB::table('users_client')
            ->select('id_user_client')
            ->where('id_user_client',$request->id_user_client);
         
            if($user_->exists())
            {
                    DB::table('users_client')
                    ->where('id_user_client','=',$request->id_user_client)
                    ->update([
                        'is_status' => '2',
                    ]);

                    return response()->json([
                    'status' => 'success',
                    'message' => 'User ID Client : '.$request->id_user_client.' has disabled'
                ]);
            }
            else
            {
                $result=response()->json([
                    'status' => 'failed',
                    'message' => 'User ID Client : '.$request->id_user_client.' not exists'
                ]);
            }
            return $result;
        } catch (\Exception $ex) {
            return $ex;
        }
    }

    public function enableClient(Request $request)
    {
        try {
            // Cek Existing Data // Cek Existing Data
            $user_ =DB::table('users_client')
            ->select('id_user_client')
            ->where('id_user_client',$request->id_user_client);
         
            if($user_->exists())
            {
                    DB::table('users_client')
                    ->where('id_user_client','=',$request->id_user_client)
                    ->update([
                        'is_status' => '1',
                    ]);

                    return response()->json([
                    'status' => 'success',
                    'message' => 'User ID Client : '.$request->id_user_client.' has enabled'
                ]);
            }
            else
            {
                $result=response()->json([
                    'status' => 'failed',
                    'message' => 'User ID Client : '.$request->id_user_client.' not exists'
                ]);
            }
            return $result;
        } catch (\Exception $ex) {
            return $ex;
        }
    }

    public function removeClient(Request $request)
    {
        try {
            // Cek Existing Data // Cek Existing Data
            $user_ =DB::table('users_client')
            ->select('id_user_client')
            ->where('id_user_client',$request->id_user_client);
         
            if($user_->exists())
            {
                    $user_->delete();

                    return response()->json([
                    'status' => 'success',
                    'message' => 'User ID Client : '.$request->id_user_client.' has removed'
                ]);
            }
            else
            {
                $result=response()->json([
                    'status' => 'failed',
                    'message' => 'User ID Client : '.$request->id_user_client.' not exists'
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
            $user_ =DB::table('users_client')
            ->select('id_user_client')
            ->where('email',$request->email);
         
            if($user_->exists())
            {
                    DB::table('users_client')
                    ->where('email','=',$request->email)
                    ->update([
                        'password' => Crypt::encryptString($request->email),
                    ]);

                    return response()->json([
                    'status' => 'success',
                    'message' => 'Reset Password Email : '.$request->email.' success'
                ]);
            }
            else
            {
                $result=response()->json([
                    'status' => 'failed',
                    'message' => 'Email : '.$request->id_user_client.' not exists'
                ]);
            }
            return $result;
        } catch (\Exception $ex) {
            return $ex;
        }
    }

    // public function editProfile(Request $request)
    // {
    //     $idUserClient = $request->id_user_client;
    //     $name = $request->name;
    //     $instansi = $request->instansi;
    //     $email = $request->email;
    //     $password = $request->password;
    //     $telephone = $request->telephone;
    //     $dateOfBirth = $request->date_of_birth;
    //     $address = $request->address;
    //     $city = $request->city;
    //     $province = $request->province;
    //     // $imgProfile = $request->img_profile;
    //     // $imgKtp = $request->img_ktp;
    
    //     try {
    //         // Cek Existing Data // Cek Existing Data
    //         $user_ =DB::table('users_client')
    //         ->select('id_user_client')
    //         ->where('id_user_client',$idUserClient);
         
    //         if($user_->exists())
    //         {
    //                 $data =  DB::table('users_client')
    //                 ->where('id_user_client','=',$idUserClient);

    //                 // Format dan validasi nomor telepon
    //                 $telephone = $this->formatTelephone($request->telephone);
    //                 if ($telephone === false) {
    //                     return response()->json([
    //                         'status' => 'failed',
    //                         'message' => 'Nomor telepon tidak valid. Pastikan hanya berisi angka dan minimal 9 digit.'
    //                     ], 422);
    //                 }
            
    //                     $data->update([
    //                         'name' => $name,
    //                         'instansi' => $instansi,
    //                         'email' => $email,
    //                         'telephone' => $telephone,
    //                         'date_of_birth' => $dateOfBirth,
    //                         'address'=> $address,
    //                         'city'=>$city,
    //                         'province'=>$province
    //                     ]);
           
    //                     // update password
    //                     if($password!='') {
    //                         $data->update([
    //                             'password' => Crypt::encryptString($password)
    //                         ]);
    //                     } 
                   
    //                     // update image Profile
    //                     if($request->file('img_profile') !=null) {
                
    //                         // call class upload Image
    //                         $c_uploadImage = new ClassUploadImage();
    //                         $urlPathImgProfile = $c_uploadImage->processImage_profile($request->file('img_profile'),$idUserClient);
                          
    //                         $data =  DB::table('users_client')
    //                         ->where('id_user_client','=',$idUserClient);
    //                         $data->update([                    
    //                             'img_profile'=>$urlPathImgProfile,
    //                         ]);
                            
    //                     } 
    //                      // update image KTP
    //                     if($request->file('img_ktp')!=null) {
    //                         // call class upload Image
    //                         $c_uploadImage = new ClassUploadImage();
    //                         $urlPathImgKtp = $c_uploadImage->processImage_ktp($request->file('img_ktp'),$idUserClient);

    //                         $data->update([                    
    //                             'img_ktp'=>$urlPathImgKtp,
    //                         ]);
    //                     } 

    //                 return response()->json([
    //                 'status' => 'success',
    //                 'message' => 'User ID : '.$idUserClient.' has edited'
    //             ]);
    //         }
    //         else
    //         {
    //             $result=response()->json([
    //                 'status' => 'failed',
    //                 'message' => 'User ID : '.$idUserClient.' not exists'
    //             ]);
    //         }
    //         return $result;
    //     } catch (\Exception $ex) {
    //         return $ex;
    //     }
    // }

    public function editProfile(Request $request)
    {
        try {
            $idUserClient = $request->id_user_client;

            // Cek apakah user ada
            $user = DB::table('users_client')
                ->where('id_user_client', $idUserClient)
                ->first();

            if (!$user) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'User ID: ' . $idUserClient . ' tidak ditemukan.'
                ], 404);
            }

            // Format dan validasi nomor telepon
            $telephone = $this->formatTelephone($request->telephone);
            if ($telephone === false) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Nomor telepon tidak valid. Pastikan hanya berisi angka dan minimal 9 digit.'
                ], 422);
            }

            // Data update dasar
            $updateData = [
                'name'          => $request->name,
                'instansi'      => $request->instansi,
                'email'         => $request->email,
                'telephone'     => $telephone,
                'date_of_birth' => $request->date_of_birth,
                'address'       => $request->address,
                'city'          => $request->city,
                'province'      => $request->province,
            ];

            // Update password jika diisi
            if (!empty($request->password)) {
                $updateData['password'] = Crypt::encryptString($request->password);
            }

            // Upload dan update foto profile jika ada
            if ($request->hasFile('img_profile')) {
                $c_uploadImage = new ClassUploadImage();
                $urlPathImgProfile = $c_uploadImage->processImage_profile(
                    $request->file('img_profile'),
                    $idUserClient
                );
                $updateData['img_profile'] = $urlPathImgProfile;
            }

            // Upload dan update foto KTP jika ada
            if ($request->hasFile('img_ktp')) {
                $c_uploadImage = new ClassUploadImage();
                $urlPathImgKtp = $c_uploadImage->processImage_ktp(
                    $request->file('img_ktp'),
                    $idUserClient
                );
                $updateData['img_ktp'] = $urlPathImgKtp;
            }

            // Lakukan update data
            DB::table('users_client')
                ->where('id_user_client', $idUserClient)
                ->update($updateData);

            return response()->json([
                'status'  => 'success',
                'message' => 'User ID: ' . $idUserClient . ' berhasil diperbarui.'
            ]);
        } catch (\Exception $ex) {
            return response()->json([
                'status' => 'failed',
                'message' => $ex->getMessage()
            ], 500);
        }
    }


    public function exportUserClient() 
    {
        try 
        {  
             return Excel::download(new export_userClient(), 'Export-User Client.xlsx');
        } catch (\Exception $ex) {
             return $ex;
         }
    }

    /**
     * Format dan validasi nomor telepon
     * - Hapus spasi, tanda plus, dan tanda minus
     * - Jika dimulai dengan "0", ubah ke "62"
     * - Jika sudah dimulai dengan "62", biarkan
     * - Harus minimal 9 digit
     * - Hanya boleh angka
     */
    private function formatTelephone($telephone)
    {
        if (empty($telephone)) {
            return false;
        }

        // Hilangkan spasi, tanda +, dan -
        $telephone = preg_replace('/[\s\-\+]/', '', $telephone);

        // Ubah awalan 0 jadi 62
        if (substr($telephone, 0, 1) == '0') {
            $telephone = '62' . substr($telephone, 1);
        }

        // Jika sudah dimulai 62, biarkan
        elseif (substr($telephone, 0, 2) !== '62') {
            // Jika tidak dimulai 0 atau 62, invalid
            return false;
        }

        // Cek hanya angka
        if (!preg_match('/^[0-9]+$/', $telephone)) {
            return false;
        }

        // Minimal panjang 9 digit
        if (strlen($telephone) < 9) {
            return false;
        }

        return $telephone;
    }
}

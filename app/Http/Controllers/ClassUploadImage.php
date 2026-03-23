<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ClassUploadImage extends Controller
{
        private function tempateCardMember($idMember)
        {
            if($idMember=='A')
            {
                // Type Member Spesial Hari Pelanggan
                return 'img/membership-template.png';
            }
            if($idMember=='C')
            {
                // Type Member Spesial Festival Naga
                return 'img/template-spesial-festival-naga.png';
            }
            if($idMember=='D')
            {
                // Type Member Spesial Lebaran
                return 'img/template-spesial-lebaran.png';
            }
            if($idMember=='E')
            {
                // Type Member Goes to School
                return 'img/template-goes-to-school.png';
            }
            if($idMember=='F')
            {
                // Type Member Goes to School
                return 'img/template-spesial-kemerdekaan.png';
            }
            if($idMember=='ID')
            {
                // Type Member Membership Reguler
                return 'img/template-membership-reguler.png';
            }
            if($idMember=='IS')
            {
                // Type Member Membership Reguler Tahunan
                return 'img/template-membership-reguler-tahunan.png';
            }
            if($idMember=='G')
            {
                // Type Member Membership Reguler Tahunan
                return 'img/template-spesial-bulan-pelanggan.png';
            }
            if($idMember=='H')
            {
                // Type Member Membership Reguler Halloween
                return 'img/template-spesial-halloween.png';
            }
            if($idMember=='I')
            {
                // Type Member Membership Spesial Kampung Natal
                return 'img/template-spesial-kampung-natal.png';
            }
            if($idMember=='J')
            {
                // Type Member Membership Spesial Kampung Natal
                return 'img/template-spesial-gong-xi-fa-cai.png';
            }
            if($idMember=='K')
            {
                // Type Member Membership Spesial Kampung Natal
                return 'img/template-spesial-mudik-saloka.png';
            }
            if($idMember=='L')
            {
                // Type Member Membership Imlek
                return 'img/template-imlek.png';
            }
            if($idMember=='AA')
            {
                // Type Member Membership Spesial Kampung Natal
                return 'img/template-panarama.png';
            }
        }
        
        public function processImage_ktp(UploadedFile $image,$idUserClient)
        {
            $imageName = 'KTP_'. $idUserClient. '.' . $image->getClientOriginalExtension();
            $image = Image::make($image)->resize(800, null, function ($constraint) {
                $constraint->aspectRatio();
            });
            $path = 'image_ktp' . '/' . $imageName;
            $image->save(public_path('storage' . DIRECTORY_SEPARATOR . $path));
            return  $path;
        }

        public function processImage_profile(UploadedFile $image,$idUserClient)
        {
            $imageName = 'PROFILE_'. $idUserClient. '.' . $image->getClientOriginalExtension();
            $image = Image::make($image)->resize(800, null, function ($constraint) {
                $constraint->aspectRatio();
            });
            $path = 'image_profile' . '/' . $imageName;
            $image->save(public_path('storage' . DIRECTORY_SEPARATOR . $path));
            return $path;
        }

        public function mergeImages($idUserClient,$typeMember,$idMember,$expiredDate)
        {
            // membership spesial hari pelanggan
            if($typeMember=='A')
            {
                $path = $this->memberA($idUserClient,$typeMember,$idMember,$expiredDate);
            }
            // membership spesial festival naga
            if($typeMember=='C')
            {
                $path = $this->memberC($idUserClient,$typeMember,$idMember,$expiredDate);
            }
            // member spesial lebaran
            if($typeMember=='D')
            {
                $path = $this->memberD($idUserClient,$typeMember,$idMember,$expiredDate);
            }
            if($typeMember=='E')
            {
                $path = $this->memberE($idUserClient,$typeMember,$idMember,$expiredDate);
            }
             // member spesial Kemerdekaan
            if($typeMember=='F')
            {
                $path = $this->memberF($idUserClient,$typeMember,$idMember,$expiredDate);
            }
            // membership reguler
            if($typeMember=='ID')
            {
                $path = $this->memberID($idUserClient,$typeMember,$idMember,$expiredDate);
            }
            // membership reguler satu tahun 
            if($typeMember=='IS')
            {
                $path = $this->memberIS($idUserClient,$typeMember,$idMember,$expiredDate);
            }
            // member spesial bulan pelanggan
            if($typeMember=='G')
            {
                $path = $this->memberG($idUserClient,$typeMember,$idMember,$expiredDate);
            }
            // member spesial bulan halloween
            if($typeMember=='H')
            {
                $path = $this->memberH($idUserClient,$typeMember,$idMember,$expiredDate);
            }
            // member spesial Kampung Natal
            if($typeMember=='I')
            {
                $path = $this->memberI($idUserClient,$typeMember,$idMember,$expiredDate);
            }
            // member spesial Gong Xi Fa Cai
            if($typeMember=='J')
            {
                $path = $this->memberJ($idUserClient,$typeMember,$idMember,$expiredDate);
            }
            if($typeMember=='K')
            {
                $path = $this->memberK($idUserClient,$typeMember,$idMember,$expiredDate);
            }
            if($typeMember=='L')
            {
                $path = $this->memberL($idUserClient,$typeMember,$idMember,$expiredDate);
            }
            // member Panarama
            if($typeMember=='AA')
            {
                $path = $this->memberAA($idUserClient,$typeMember,$idMember,$expiredDate);
            }
            return $path;
        }

        private function memberA($idUserClient,$typeMember,$idMember,$expiredDate)
        {
            // call template image
            $urlTemplate = $this->tempateCardMember($typeMember);
            $image1 = Image::make(public_path($urlTemplate));
         
            // get profile user
            $userProfile = DB::table('users_client')
            ->select('id_user_client','name','date_of_birth','img_profile')
            ->where('id_user_client',$idUserClient)
            ->first();
            $urlImageProfile = $userProfile->img_profile;

            // call image from local picture
            $image2 = Image::make(public_path('storage/'.$urlImageProfile));

            // create qr code imgage
            $qrcodePath = public_path('qrcode.png');
            QrCode::size(380)->format('png')->generate($idMember, $qrcodePath);

            $image3 = Image::make($qrcodePath);

            $image2->fit(840, 1090);
            $image1->insert($image2, 'top-left', 130, 430);
            $image1->insert($image3, 'bottom-left', 1135, 447);

            $id_member = $idMember;
            $name = $userProfile->name;
            $birthday = $userProfile->date_of_birth;

            $date = Carbon::parse($expiredDate);
            $expired_date = $date->format('j M Y');

            // id Member
            $image1->text($id_member, 1090, 930, function ($font) {
                $font->file(public_path('alright-sans-bold.ttf'));
                $font->size(125);
                $font->color('#294d9c');
                $font->align('left');
                $font->valign('top');
            });

            $image1->text($name, 1090, 550, function ($font) {
                $font->file(public_path('alright-sans-black.ttf'));
                $font->size(125);
                $font->color('#294d9c');
                $font->align('left');
                $font->valign('top');
            });
            
            $birthday = date('d-m-Y', strtotime($birthday));
            $image1->text($birthday, 1360, 700, function ($font) {
                $font->file(public_path('alright-sans-black.ttf'));
                $font->size(100);
                $font->color('#294d9c');
                $font->align('center');
                $font->valign('top');
            });

            $image1->text($expired_date, 500, 1720, function ($font) {
                $font->file(public_path('alright-sans-black.ttf'));
                $font->size(125);
                $font->color('#fff');
                $font->align('center');
                $font->valign('top');
            });

            $fileName = $idUserClient.'-'.$idMember.'.jpg';; 
            // Storage::disk('uploads_eMember')->put($fileName, $image1->encode('jpg'));

            $image = Image::make($image1)->resize(800, null, function ($constraint) {
                $constraint->aspectRatio();
            });
            $path = 'image_eMember' . '/' . $fileName;
            $image->save(public_path('storage' . DIRECTORY_SEPARATOR . $path));
            return $path;
        }

        private function memberC($idUserClient,$typeMember,$idMember,$expiredDate)
        {
            // call template image
            $urlTemplate = $this->tempateCardMember($typeMember);
            $image1 = Image::make(public_path($urlTemplate));

            // get profile user
            $userProfile = DB::table('users_client')
            ->select('id_user_client','name','date_of_birth','img_profile')
            ->where('id_user_client',$idUserClient)
            ->first();
            $urlImageProfile = $userProfile->img_profile;

            // call image from local picture
            $image2 = Image::make(public_path('storage/'.$urlImageProfile));

            // create qr code imgage
            $qrcodePath = public_path('qrcode.png');
            QrCode::size(380)->format('png')->generate($idMember, $qrcodePath);

            $image3 = Image::make($qrcodePath);

            $image2->fit(840, 1090);
            $image1->insert($image2, 'top-left', 130, 430);
            $image1->insert($image3, 'bottom-left', 1135, 490);

            $id_member = $idMember;
            $name = $userProfile->name;
            $birthday = $userProfile->date_of_birth;

            $date = Carbon::parse($expiredDate);
            $expired_date = $date->format('j M Y');

            // id Member
            $image1->text($id_member, 1090, 900, function ($font) {
                $font->file(public_path('alright-sans-bold.ttf'));
                $font->size(125);
                $font->color('#ffffff');
                $font->align('left');
                $font->valign('top');
            });

            $image1->text($name, 1090, 550, function ($font) {
                $font->file(public_path('alright-sans-black.ttf'));
                $font->size(125);
                $font->color('#ffffff');
                $font->align('left');
                $font->valign('top');
            });
            
            $birthday = date('d-m-Y', strtotime($birthday));
            $image1->text($birthday, 1360, 700, function ($font) {
                $font->file(public_path('alright-sans-black.ttf'));
                $font->size(100);
                $font->color('#ffffff');
                $font->align('center');
                $font->valign('top');
            });

            $image1->text($expired_date, 500, 1720, function ($font) {
                $font->file(public_path('alright-sans-black.ttf'));
                $font->size(125);
                $font->color('#ffffff');
                $font->align('center');
                $font->valign('top');
            });

            $fileName = $idUserClient.'-'.$idMember.'.jpg';; 
            // Storage::disk('uploads_eMember')->put($fileName, $image1->encode('jpg'));

            $image = Image::make($image1)->resize(800, null, function ($constraint) {
                $constraint->aspectRatio();
            });
            $path = 'image_eMember' . '/' . $fileName;
            $image->save(public_path('storage' . DIRECTORY_SEPARATOR . $path));
            return $path;
        }

        private function memberD($idUserClient,$typeMember,$idMember,$expiredDate)
        {
            // call template image
            $urlTemplate = $this->tempateCardMember($typeMember);
            $image1 = Image::make(public_path($urlTemplate));

            // get profile user
            $userProfile = DB::table('users_client')
            ->select('id_user_client','name','date_of_birth','img_profile')
            ->where('id_user_client',$idUserClient)
            ->first();
            $urlImageProfile = $userProfile->img_profile;

            // call image from local picture
            $image2 = Image::make(public_path('storage/'.$urlImageProfile));

            // create qr code imgage
            $qrcodePath = public_path('qrcode.png');
            QrCode::size(380)->format('png')->generate($idMember, $qrcodePath);

            $image3 = Image::make($qrcodePath);

            $image2->fit(840, 1090);
            $image1->insert($image2, 'top-left', 130, 430);
            $image1->insert($image3, 'bottom-left', 1135, 490);

            $id_member = $idMember;
            $name = $userProfile->name;
            $birthday = $userProfile->date_of_birth;

            $date = Carbon::parse($expiredDate);
            $expired_date = $date->format('j M Y');

            // id Member
            $image1->text($id_member, 1090, 900, function ($font) {
                $font->file(public_path('alright-sans-bold.ttf'));
                $font->size(125);
                $font->color('#9E2133');
                $font->align('left');
                $font->valign('top');
            });

            $image1->text($name, 1090, 550, function ($font) {
                $font->file(public_path('alright-sans-black.ttf'));
                $font->size(125);
                $font->color('#9E2133');
                $font->align('left');
                $font->valign('top');
            });
            
            $birthday = date('d-m-Y', strtotime($birthday));
            $image1->text($birthday, 1360, 700, function ($font) {
                $font->file(public_path('alright-sans-black.ttf'));
                $font->size(100);
                $font->color('#9E2133');
                $font->align('center');
                $font->valign('top');
            });

            $image1->text($expired_date, 500, 1720, function ($font) {
                $font->file(public_path('alright-sans-black.ttf'));
                $font->size(125);
                $font->color('#9E2133');
                $font->align('center');
                $font->valign('top');
            });

            $fileName = $idUserClient.'-'.$idMember.'.jpg';; 
            // Storage::disk('uploads_eMember')->put($fileName, $image1->encode('jpg'));

            $image = Image::make($image1)->resize(800, null, function ($constraint) {
                $constraint->aspectRatio();
            });
            $path = 'image_eMember' . '/' . $fileName;
            $image->save(public_path('storage' . DIRECTORY_SEPARATOR . $path));
            return $path;
        }

        private function memberE($idUserClient,$typeMember,$idMember,$expiredDate)
        {
            // call template image
            $urlTemplate = $this->tempateCardMember($typeMember);
            $image1 = Image::make(public_path($urlTemplate));

            // get profile user
            $userProfile = DB::table('users_client')
            ->select('id_user_client','name','date_of_birth','img_profile')
            ->where('id_user_client',$idUserClient)
            ->first();
            $urlImageProfile = $userProfile->img_profile;

            // call image from local picture
            $image2 = Image::make(public_path('storage/'.$urlImageProfile));

            // create qr code imgage
            $qrcodePath = public_path('qrcode.png');
            QrCode::size(380)->format('png')->generate($idMember, $qrcodePath);

            $image3 = Image::make($qrcodePath);

            $image2->fit(840, 1090);
            $image1->insert($image2, 'top-left', 130, 430);
            $image1->insert($image3, 'bottom-left', 1150, 450);

            $id_member = $idMember;
            $name = $userProfile->name;
            $birthday = $userProfile->date_of_birth;

            $date = Carbon::parse($expiredDate);
            $expired_date = $date->format('j M Y');

            // id Member
            $image1->text($id_member, 1090, 900, function ($font) {
                $font->file(public_path('alright-sans-bold.ttf'));
                $font->size(125);
                $font->color('#ffffff');
                $font->align('left');
                $font->valign('top');
            });

            $image1->text($name, 1090, 550, function ($font) {
                $font->file(public_path('alright-sans-black.ttf'));
                $font->size(125);
                $font->color('#ffffff');
                $font->align('left');
                $font->valign('top');
            });
            
            $birthday = date('d-m-Y', strtotime($birthday));
            $image1->text($birthday, 1360, 700, function ($font) {
                $font->file(public_path('alright-sans-black.ttf'));
                $font->size(100);
                $font->color('#ffffff');
                $font->align('center');
                $font->valign('top');
            });

            $image1->text($expired_date, 450, 1720, function ($font) {
                $font->file(public_path('alright-sans-black.ttf'));
                $font->size(100);
                $font->color('#ffffff');
                $font->align('center');
                $font->valign('top');
            });

            $fileName = $idUserClient.'-'.$idMember.'.jpg';; 
            // Storage::disk('uploads_eMember')->put($fileName, $image1->encode('jpg'));

            $image = Image::make($image1)->resize(800, null, function ($constraint) {
                $constraint->aspectRatio();
            });
            $path = 'image_eMember' . '/' . $fileName;
            $image->save(public_path('storage' . DIRECTORY_SEPARATOR . $path));
            return $path;
        }

        private function memberF($idUserClient,$typeMember,$idMember,$expiredDate)
        {
            // call template image
            $urlTemplate = $this->tempateCardMember($typeMember);
            $image1 = Image::make(public_path($urlTemplate));

            // get profile user
            $userProfile = DB::table('users_client')
            ->select('id_user_client','name','date_of_birth','img_profile')
            ->where('id_user_client',$idUserClient)
            ->first();
            $urlImageProfile = $userProfile->img_profile;

            // call image from local picture
            $image2 = Image::make(public_path('storage/'.$urlImageProfile));

            // create qr code imgage
            $qrcodePath = public_path('qrcode.png');
            QrCode::size(380)->format('png')->generate($idMember, $qrcodePath);

            $image3 = Image::make($qrcodePath);

            $image2->fit(840, 1090);
            $image1->insert($image2, 'top-left', 130, 430);
            $image1->insert($image3, 'bottom-left', 1130, 450); //kanan kiri // naik turun

            $id_member = $idMember;
            $name = $userProfile->name;
            $birthday = $userProfile->date_of_birth;

            $date = Carbon::parse($expiredDate);
            $expired_date = $date->format('j M Y');

            // id Member
            $image1->text($id_member, 1090, 900, function ($font) {
                $font->file(public_path('alright-sans-bold.ttf'));
                $font->size(125);
                $font->color('#ffffff');
                $font->align('left');
                $font->valign('top');
            });

            $image1->text($name, 1090, 550, function ($font) {
                $font->file(public_path('alright-sans-black.ttf'));
                $font->size(125);
                $font->color('#ffffff');
                $font->align('left');
                $font->valign('top');
            });
            
            $birthday = date('d-m-Y', strtotime($birthday));
            $image1->text($birthday, 1360, 700, function ($font) {
                $font->file(public_path('alright-sans-black.ttf'));
                $font->size(100);
                $font->color('#ffffff');
                $font->align('center');
                $font->valign('top');
            });

            $image1->text($expired_date, 450, 1720, function ($font) {
                $font->file(public_path('alright-sans-black.ttf'));
                $font->size(100);
                $font->color('#ffffff');
                $font->align('center');
                $font->valign('top');
            });

            $fileName = $idUserClient.'-'.$idMember.'.jpg';; 
            // Storage::disk('uploads_eMember')->put($fileName, $image1->encode('jpg'));

            $image = Image::make($image1)->resize(800, null, function ($constraint) {
                $constraint->aspectRatio();
            });
            $path = 'image_eMember' . '/' . $fileName;
            $image->save(public_path('storage' . DIRECTORY_SEPARATOR . $path));
            return $path;
        }

        private function memberG($idUserClient,$typeMember,$idMember,$expiredDate)
        {
            // call template image
            $urlTemplate = $this->tempateCardMember($typeMember);
            $image1 = Image::make(public_path($urlTemplate));

            // get profile user
            $userProfile = DB::table('users_client')
            ->select('id_user_client','name','date_of_birth','img_profile')
            ->where('id_user_client',$idUserClient)
            ->first();
            $urlImageProfile = $userProfile->img_profile;

            // call image from local picture
            $image2 = Image::make(public_path('storage/'.$urlImageProfile));

            // create qr code imgage
            $qrcodePath = public_path('qrcode.png');
            QrCode::size(380)->format('png')->generate($idMember, $qrcodePath);

            $image3 = Image::make($qrcodePath);

            $image2->fit(840, 1090);
            $image1->insert($image2, 'top-left', 130, 430);
            $image1->insert($image3, 'bottom-left', 1130, 450); //kanan kiri // naik turun

            $id_member = $idMember;
            $name = $userProfile->name;
            $birthday = $userProfile->date_of_birth;

            $date = Carbon::parse($expiredDate);
            $expired_date = $date->format('j M Y');

            // id Member
            $image1->text($id_member, 1090, 900, function ($font) {
                $font->file(public_path('alright-sans-bold.ttf'));
                $font->size(125);
                $font->color('#ffffff');
                $font->align('left');
                $font->valign('top');
            });

            $image1->text($name, 1090, 550, function ($font) {
                $font->file(public_path('alright-sans-black.ttf'));
                $font->size(125);
                $font->color('#ffffff');
                $font->align('left');
                $font->valign('top');
            });
            
            $birthday = date('d-m-Y', strtotime($birthday));
            $image1->text($birthday, 1360, 700, function ($font) {
                $font->file(public_path('alright-sans-black.ttf'));
                $font->size(100);
                $font->color('#ffffff');
                $font->align('center');
                $font->valign('top');
            });

            $image1->text($expired_date, 500, 1720, function ($font) {
                $font->file(public_path('alright-sans-black.ttf'));
                $font->size(100);
                $font->color('#ffffff');
                $font->align('center');
                $font->valign('top');
            });

            $fileName = $idUserClient.'-'.$idMember.'.jpg';; 
            // Storage::disk('uploads_eMember')->put($fileName, $image1->encode('jpg'));

            $image = Image::make($image1)->resize(800, null, function ($constraint) {
                $constraint->aspectRatio();
            });
            $path = 'image_eMember' . '/' . $fileName;
            $image->save(public_path('storage' . DIRECTORY_SEPARATOR . $path));
            return $path;
        }

        private function memberH($idUserClient,$typeMember,$idMember,$expiredDate)
        {
            // call template image
            $urlTemplate = $this->tempateCardMember($typeMember);
            $image1 = Image::make(public_path($urlTemplate));

            // get profile user
            $userProfile = DB::table('users_client')
            ->select('id_user_client','name','date_of_birth','img_profile')
            ->where('id_user_client',$idUserClient)
            ->first();
            $urlImageProfile = $userProfile->img_profile;

            // call image from local picture
            $image2 = Image::make(public_path('storage/'.$urlImageProfile));

            // create qr code imgage
            $qrcodePath = public_path('qrcode.png');
            QrCode::size(380)->format('png')->generate($idMember, $qrcodePath);

            $image3 = Image::make($qrcodePath);

            $image2->fit(840, 1090);
            $image1->insert($image2, 'top-left', 130, 430);
            $image1->insert($image3, 'bottom-left', 1130, 450); //kanan kiri // naik turun

            $id_member = $idMember;
            $name = $userProfile->name;
            $birthday = $userProfile->date_of_birth;

            $date = Carbon::parse($expiredDate);
            $expired_date = $date->format('j M Y');

            // id Member
            $image1->text($id_member, 1090, 900, function ($font) {
                $font->file(public_path('alright-sans-bold.ttf'));
                $font->size(125);
                $font->color('#ffffff');
                $font->align('left');
                $font->valign('top');
            });

            $image1->text($name, 1090, 550, function ($font) {
                $font->file(public_path('alright-sans-black.ttf'));
                $font->size(125);
                $font->color('#ffffff');
                $font->align('left');
                $font->valign('top');
            });
            
            $birthday = date('d-m-Y', strtotime($birthday));
            $image1->text($birthday, 1360, 700, function ($font) {
                $font->file(public_path('alright-sans-black.ttf'));
                $font->size(100);
                $font->color('#ffffff');
                $font->align('center');
                $font->valign('top');
            });

            $image1->text($expired_date, 450, 1720, function ($font) {
                $font->file(public_path('alright-sans-black.ttf'));
                $font->size(100);
                $font->color('#ffffff');
                $font->align('center');
                $font->valign('top');
            });

            $fileName = $idUserClient.'-'.$idMember.'.jpg';; 
            // Storage::disk('uploads_eMember')->put($fileName, $image1->encode('jpg'));

            $image = Image::make($image1)->resize(800, null, function ($constraint) {
                $constraint->aspectRatio();
            });
            $path = 'image_eMember' . '/' . $fileName;
            $image->save(public_path('storage' . DIRECTORY_SEPARATOR . $path));
            return $path;
        }

        private function memberI($idUserClient,$typeMember,$idMember,$expiredDate)
        {
            // call template image
            $urlTemplate = $this->tempateCardMember($typeMember);
            $image1 = Image::make(public_path($urlTemplate));

            // get profile user
            $userProfile = DB::table('users_client')
            ->select('id_user_client','name','date_of_birth','img_profile')
            ->where('id_user_client',$idUserClient)
            ->first();
            $urlImageProfile = $userProfile->img_profile;

            // call image from local picture
            $image2 = Image::make(public_path('storage/'.$urlImageProfile));

            // create qr code imgage
            $qrcodePath = public_path('qrcode.png');
            QrCode::size(380)->format('png')->generate($idMember, $qrcodePath);

            $image3 = Image::make($qrcodePath);

            $image2->fit(840, 1090);
            $image1->insert($image2, 'top-left', 130, 430);
            $image1->insert($image3, 'bottom-left', 1150, 470); //kanan kiri // naik turun

            $id_member = $idMember;
            $name = $userProfile->name;
            $birthday = $userProfile->date_of_birth;

            $date = Carbon::parse($expiredDate);
            $expired_date = $date->format('j M Y');

            // id Member
            $image1->text($id_member, 1090, 900, function ($font) {
                $font->file(public_path('alright-sans-bold.ttf'));
                $font->size(125);
                $font->color('#ffffff');
                $font->align('left');
                $font->valign('top');
            });

            $image1->text($name, 1090, 550, function ($font) {
                $font->file(public_path('alright-sans-black.ttf'));
                $font->size(125);
                $font->color('#ffffff');
                $font->align('left');
                $font->valign('top');
            });
            
            $birthday = date('d-m-Y', strtotime($birthday));
            $image1->text($birthday, 1360, 700, function ($font) {
                $font->file(public_path('alright-sans-black.ttf'));
                $font->size(100);
                $font->color('#ffffff');
                $font->align('center');
                $font->valign('top');
            });

            $image1->text($expired_date, 450, 1720, function ($font) {
                $font->file(public_path('alright-sans-black.ttf'));
                $font->size(100);
                $font->color('#ffffff');
                $font->align('center');
                $font->valign('top');
            });

            $fileName = $idUserClient.'-'.$idMember.'.jpg';; 
            // Storage::disk('uploads_eMember')->put($fileName, $image1->encode('jpg'));

            $image = Image::make($image1)->resize(800, null, function ($constraint) {
                $constraint->aspectRatio();
            });
            $path = 'image_eMember' . '/' . $fileName;
            $image->save(public_path('storage' . DIRECTORY_SEPARATOR . $path));
            return $path;
        }

        private function memberJ($idUserClient,$typeMember,$idMember,$expiredDate)
        {
            // call template image
            $urlTemplate = $this->tempateCardMember($typeMember);
            $image1 = Image::make(public_path($urlTemplate));

            // get profile user
            $userProfile = DB::table('users_client')
            ->select('id_user_client','name','date_of_birth','img_profile')
            ->where('id_user_client',$idUserClient)
            ->first();
            $urlImageProfile = $userProfile->img_profile;

            // call image from local picture
            $image2 = Image::make(public_path('storage/'.$urlImageProfile));

            // create qr code imgage
            $qrcodePath = public_path('qrcode.png');
            QrCode::size(380)->format('png')->generate($idMember, $qrcodePath);

            $image3 = Image::make($qrcodePath);

            $image2->fit(840, 1090);
            $image1->insert($image2, 'top-left', 130, 430);
            $image1->insert($image3, 'bottom-left', 1130, 460); //kanan kiri // naik turun

            $id_member = $idMember;
            $name = $userProfile->name;
            $birthday = $userProfile->date_of_birth;

            $date = Carbon::parse($expiredDate);
            $expired_date = $date->format('j M Y');

            // id Member
            $image1->text($id_member, 1090, 900, function ($font) {
                $font->file(public_path('alright-sans-bold.ttf'));
                $font->size(125);
                $font->color('#ffffff');
                $font->align('left');
                $font->valign('top');
            });

            $image1->text($name, 1090, 550, function ($font) {
                $font->file(public_path('alright-sans-black.ttf'));
                $font->size(125);
                $font->color('#ffffff');
                $font->align('left');
                $font->valign('top');
            });
            
            $birthday = date('d-m-Y', strtotime($birthday));
            $image1->text($birthday, 1360, 700, function ($font) {
                $font->file(public_path('alright-sans-black.ttf'));
                $font->size(100);
                $font->color('#ffffff');
                $font->align('center');
                $font->valign('top');
            });

            $image1->text($expired_date, 450, 1720, function ($font) {
                $font->file(public_path('alright-sans-black.ttf'));
                $font->size(100);
                $font->color('#ffffff');
                $font->align('center');
                $font->valign('top');
            });

            $fileName = $idUserClient.'-'.$idMember.'.jpg';; 
            // Storage::disk('uploads_eMember')->put($fileName, $image1->encode('jpg'));

            $image = Image::make($image1)->resize(800, null, function ($constraint) {
                $constraint->aspectRatio();
            });
            $path = 'image_eMember' . '/' . $fileName;
            $image->save(public_path('storage' . DIRECTORY_SEPARATOR . $path));
            return $path;
        }

        private function memberK($idUserClient,$typeMember,$idMember,$expiredDate)
        {
            // call template image
            $urlTemplate = $this->tempateCardMember($typeMember);
            $image1 = Image::make(public_path($urlTemplate));

            // get profile user
            $userProfile = DB::table('users_client')
            ->select('id_user_client','name','date_of_birth','img_profile')
            ->where('id_user_client',$idUserClient)
            ->first();
            $urlImageProfile = $userProfile->img_profile;

            // call image from local picture
            $image2 = Image::make(public_path('storage/'.$urlImageProfile));

            // create qr code imgage
            $qrcodePath = public_path('qrcode.png');
            QrCode::size(380)->format('png')->generate($idMember, $qrcodePath);

            $image3 = Image::make($qrcodePath);

            $image2->fit(840, 1090);
            $image1->insert($image2, 'top-left', 130, 430);
            $image1->insert($image3, 'bottom-left', 1130, 455); //kanan kiri // naik turun

            $id_member = $idMember;
            $name = $userProfile->name;
            $birthday = $userProfile->date_of_birth;

            $date = Carbon::parse($expiredDate);
            $expired_date = $date->format('j M Y');

            // id Member
            $image1->text($id_member, 1090, 900, function ($font) {
                $font->file(public_path('alright-sans-bold.ttf'));
                $font->size(125);
                $font->color('#ffffff');
                $font->align('left');
                $font->valign('top');
            });

            $image1->text($name, 1090, 550, function ($font) {
                $font->file(public_path('alright-sans-black.ttf'));
                $font->size(125);
                $font->color('#ffffff');
                $font->align('left');
                $font->valign('top');
            });
            
            $birthday = date('d-m-Y', strtotime($birthday));
            $image1->text($birthday, 1360, 700, function ($font) {
                $font->file(public_path('alright-sans-black.ttf'));
                $font->size(100);
                $font->color('#ffffff');
                $font->align('center');
                $font->valign('top');
            });

            $image1->text($expired_date, 450, 1720, function ($font) {
                $font->file(public_path('alright-sans-black.ttf'));
                $font->size(100);
                $font->color('#ffffff');
                $font->align('center');
                $font->valign('top');
            });

            $fileName = $idUserClient.'-'.$idMember.'.jpg';; 
            // Storage::disk('uploads_eMember')->put($fileName, $image1->encode('jpg'));

            $image = Image::make($image1)->resize(800, null, function ($constraint) {
                $constraint->aspectRatio();
            });
            $path = 'image_eMember' . '/' . $fileName;
            $image->save(public_path('storage' . DIRECTORY_SEPARATOR . $path));
            return $path;
        }

        private function memberL($idUserClient,$typeMember,$idMember,$expiredDate)
        {
            // call template image
            $urlTemplate = $this->tempateCardMember($typeMember);
            $image1 = Image::make(public_path($urlTemplate));

            // get profile user
            $userProfile = DB::table('users_client')
            ->select('id_user_client','name','date_of_birth','img_profile')
            ->where('id_user_client',$idUserClient)
            ->first();
            $urlImageProfile = $userProfile->img_profile;

            // call image from local picture
            $image2 = Image::make(public_path('storage/'.$urlImageProfile));

            // create qr code imgage
            $qrcodePath = public_path('qrcode.png');
            QrCode::size(380)->format('png')->generate($idMember, $qrcodePath);

            $image3 = Image::make($qrcodePath);

            $image2->fit(840, 1090);
            $image1->insert($image2, 'top-left', 130, 430);
            $image1->insert($image3, 'bottom-left', 1135, 450);

            $id_member = $idMember;
            $name = $userProfile->name;
            $birthday = $userProfile->date_of_birth;

            $date = Carbon::parse($expiredDate);
            $expired_date = $date->format('j M Y');

            // id Member
            $image1->text($id_member, 1090, 900, function ($font) {
                $font->file(public_path('alright-sans-bold.ttf'));
                $font->size(125);
                $font->color('#ffffff');
                $font->align('left');
                $font->valign('top');
            });

            $image1->text($name, 1090, 550, function ($font) {
                $font->file(public_path('alright-sans-black.ttf'));
                $font->size(125);
                $font->color('#ffffff');
                $font->align('left');
                $font->valign('top');
            });
            
            $birthday = date('d-m-Y', strtotime($birthday));
            $image1->text($birthday, 1360, 700, function ($font) {
                $font->file(public_path('alright-sans-black.ttf'));
                $font->size(100);
                $font->color('#ffffff');
                $font->align('center');
                $font->valign('top');
            });

            $image1->text($expired_date, 500, 1720, function ($font) {
                $font->file(public_path('alright-sans-black.ttf'));
                $font->size(125);
                $font->color('#ffffff');
                $font->align('center');
                $font->valign('top');
            });

            $fileName = $idUserClient.'-'.$idMember.'.jpg';; 
            // Storage::disk('uploads_eMember')->put($fileName, $image1->encode('jpg'));

            $image = Image::make($image1)->resize(800, null, function ($constraint) {
                $constraint->aspectRatio();
            });
            $path = 'image_eMember' . '/' . $fileName;
            $image->save(public_path('storage' . DIRECTORY_SEPARATOR . $path));
            return $path;
        }

        private function memberAA($idUserClient,$typeMember,$idMember,$expiredDate)
        {
            // call template image
            $urlTemplate = $this->tempateCardMember($typeMember);
            $image1 = Image::make(public_path($urlTemplate));

            // get profile user
            $userProfile = DB::table('users_client')
            ->select('id_user_client','name','date_of_birth','img_profile')
            ->where('id_user_client',$idUserClient)
            ->first();
            $urlImageProfile = $userProfile->img_profile;

            // call image from local picture
            // $image2 = Image::make(public_path('storage/'.$urlImageProfile));

            // create qr code imgage
            $qrcodePath = public_path('qrcode.png');
            QrCode::size(240)->format('png')->generate($idMember, $qrcodePath);

            $image3 = Image::make($qrcodePath);

            // $image2->fit(840, 1090);
            // $image1->insert($image2, 'top-left', 130, 430);
            $image1->insert($image3, 'bottom-left', 170, 125); //kanan kiri // naik turun

            $id_member = $idMember;
            $name = $userProfile->name;
            // $birthday = $userProfile->date_of_birth;

            $date = Carbon::parse($expiredDate);
            $expired_date = $date->format('j M Y');

            // id Member
            $image1->text($id_member, 140, 1350, function ($font) {
                $font->file(public_path('alright-sans-bold.ttf'));
                $font->size(125);
                $font->color('#ffffff');
                $font->align('left');
                $font->valign('top');
            });

            $image1->text($name, 140, 1200, function ($font) {
                $font->file(public_path('alright-sans-black.ttf'));
                $font->size(125);
                $font->color('#ffffff');
                $font->align('left');
                $font->valign('top');
            });
            
            // $birthday = date('d-m-Y', strtotime($birthday));
            // $image1->text($birthday, 1360, 700, function ($font) {
            //     $font->file(public_path('alright-sans-black.ttf'));
            //     $font->size(100);
            //     $font->color('#ffffff');
            //     $font->align('center');
            //     $font->valign('top');
            // });

            $image1->text($expired_date, 760, 1730, function ($font) {
                $font->file(public_path('alright-sans-black.ttf'));
                $font->size(100);
                $font->color('#ffffff');
                $font->align('center');
                $font->valign('top');
            });

            $fileName = $idUserClient.'-'.$idMember.'.jpg';; 
            $image = Image::make($image1)->resize(800, null, function ($constraint) {
                $constraint->aspectRatio();
            });
            $path = 'image_eMember' . '/' . $fileName;
            $image->save(public_path('storage' . DIRECTORY_SEPARATOR . $path));
            return $path;
        }

        private function memberID($idUserClient,$typeMember,$idMember,$expiredDate)
        {
            // call template image
            $urlTemplate = $this->tempateCardMember($typeMember);
            $image1 = Image::make(public_path($urlTemplate));

            // get profile user
            $userProfile = DB::table('users_client')
            ->select('id_user_client','name','date_of_birth','img_profile')
            ->where('id_user_client',$idUserClient)
            ->first();
            $urlImageProfile = $userProfile->img_profile;

            // call image from local picture
            $image2 = Image::make(public_path('storage/'.$urlImageProfile));

            // create qr code imgage
            $qrcodePath = public_path('qrcode.png');
            QrCode::size(380)->format('png')->generate($idMember, $qrcodePath);

            $image3 = Image::make($qrcodePath);

            $image2->fit(840, 1090);
            $image1->insert($image2, 'top-left', 130, 430);
            $image1->insert($image3, 'bottom-left', 1135, 490);

            $id_member = $idMember;
            $name = $userProfile->name;
            $birthday = $userProfile->date_of_birth;

            $date = Carbon::parse($expiredDate);
            $expired_date = $date->format('j M Y');

            // id Member
            $image1->text($id_member, 1090, 900, function ($font) {
                $font->file(public_path('alright-sans-bold.ttf'));
                $font->size(125);
                $font->color('#ffffff');
                $font->align('left');
                $font->valign('top');
            });

            $image1->text($name, 1090, 550, function ($font) {
                $font->file(public_path('alright-sans-black.ttf'));
                $font->size(125);
                $font->color('#ffffff');
                $font->align('left');
                $font->valign('top');
            });
            
            $birthday = date('d-m-Y', strtotime($birthday));
            $image1->text($birthday, 1360, 700, function ($font) {
                $font->file(public_path('alright-sans-black.ttf'));
                $font->size(100);
                $font->color('#ffffff');
                $font->align('center');
                $font->valign('top');
            });

            $image1->text($expired_date, 500, 1720, function ($font) {
                $font->file(public_path('alright-sans-black.ttf'));
                $font->size(125);
                $font->color('#ffffff');
                $font->align('center');
                $font->valign('top');
            });

            $fileName = $idUserClient.'-'.$idMember.'.jpg';; 
            // Storage::disk('uploads_eMember')->put($fileName, $image1->encode('jpg'));

            $image = Image::make($image1)->resize(800, null, function ($constraint) {
                $constraint->aspectRatio();
            });
            $path = 'image_eMember' . '/' . $fileName;
            $image->save(public_path('storage' . DIRECTORY_SEPARATOR . $path));
            return $path;
        }

        private function memberIS($idUserClient,$typeMember,$idMember,$expiredDate)
        {
            // call template image
            $urlTemplate = $this->tempateCardMember($typeMember);
            $image1 = Image::make(public_path($urlTemplate));

            // get profile user
            $userProfile = DB::table('users_client')
            ->select('id_user_client','name','date_of_birth','img_profile')
            ->where('id_user_client',$idUserClient)
            ->first();
            $urlImageProfile = $userProfile->img_profile;

            // call image from local picture
            $image2 = Image::make(public_path('storage/'.$urlImageProfile));

            // create qr code imgage
            $qrcodePath = public_path('qrcode.png');
            QrCode::size(380)->format('png')->generate($idMember, $qrcodePath);

            $image3 = Image::make($qrcodePath);

            $image2->fit(840, 1090);
            $image1->insert($image2, 'top-left', 130, 430);
            $image1->insert($image3, 'bottom-left', 1125, 460);

            $id_member = $idMember;
            $name = $userProfile->name;
            $birthday = $userProfile->date_of_birth;

            $date = Carbon::parse($expiredDate);
            $expired_date = $date->format('j M Y');

            // id Member
            $image1->text($id_member, 1090, 900, function ($font) {
                $font->file(public_path('alright-sans-bold.ttf'));
                $font->size(125);
                $font->color('#ffffff');
                $font->align('left');
                $font->valign('top');
            });

            $image1->text($name, 1090, 550, function ($font) {
                $font->file(public_path('alright-sans-black.ttf'));
                $font->size(125);
                $font->color('#ffffff');
                $font->align('left');
                $font->valign('top');
            });
            
            $birthday = date('d-m-Y', strtotime($birthday));
            $image1->text($birthday, 1360, 700, function ($font) {
                $font->file(public_path('alright-sans-black.ttf'));
                $font->size(100);
                $font->color('#ffffff');
                $font->align('center');
                $font->valign('top');
            });

            $image1->text($expired_date, 500, 1720, function ($font) {
                $font->file(public_path('alright-sans-black.ttf'));
                $font->size(125);
                $font->color('#ffffff');
                $font->align('center');
                $font->valign('top');
            });

            $fileName = $idUserClient.'-'.$idMember.'.jpg';; 
            // Storage::disk('uploads_eMember')->put($fileName, $image1->encode('jpg'));

            $image = Image::make($image1)->resize(800, null, function ($constraint) {
                $constraint->aspectRatio();
            });
            $path = 'image_eMember' . '/' . $fileName;
            $image->save(public_path('storage' . DIRECTORY_SEPARATOR . $path));
            return $path;
        }

        // ---------------------------------------------------------------------------------------------------------------------------
        // desain sales
        public function mergeImagesSales($idUserClient,$idMember,$expiredDate){
            // call template image
            $image1 = Image::make(public_path('img/membership-sales-template.png'));

            // get profile user
            $userProfile = DB::table('users_client')
            ->select('id_user_client','name','date_of_birth','img_profile')
            ->where('id_user_client',$idUserClient)
            ->first();
            
            $urlImageProfile = $userProfile->img_profile;
        
            // call image from local picture
            $image2 = Image::make(public_path('storage/'.$urlImageProfile));
        
            $image2->fit(1140, 1485);
            $image1->insert($image2, 'top-left', 165, 600);

            // create qr code imgage
            $qrcodePath = public_path('qrcode.png');
            QrCode::size(380)->format('png')->generate($idMember, $qrcodePath);

            $image3 = Image::make($qrcodePath);

            // $image2->fit(840, 1085);
            // $image1->insert($image2, 'top-left', 130, 450);
            $image1->insert($image3, 'bottom-left', 1400, 655);
    
            $id_member = $idMember;
            $name = $userProfile->name;
            $birthday = $userProfile->date_of_birth;
            $date = Carbon::parse($expiredDate);
            $expired_date = $date->format('j M Y');
    
            $image1->text($name, 1500, 1000, function ($font) {
                $font->file(public_path('alright-sans-bold.ttf'));
                $font->size(125);
                $font->color('#294d9c');
                $font->align('left');
                $font->valign('top');
            });
            $birthday = date('d-m-Y', strtotime($birthday));
            $image1->text($birthday, 1500, 1150, function ($font) {
                $font->file(public_path('alright-sans-bold.ttf'));
                $font->size(125);
                $font->color('#294d9c');
                $font->align('left');
                $font->valign('top');
            });

            $image1->text($id_member, 1500, 1320, function ($font) {
                $font->file(public_path('alright-sans-bold.ttf'));
                $font->size(125);
                $font->color('#294d9c');
                $font->align('left');
                $font->valign('top');
            });
    
            $image1->text($expired_date, 600, 2300, function ($font) {
                $font->file(public_path('alright-sans-bold.ttf'));
                $font->size(125);
                $font->color('#fff');
                $font->align('center');
                $font->valign('top');
            });
       
            $fileName = $idUserClient.'-'.$idMember.'.jpg';; 
            // Storage::disk('uploads_eMember')->put($fileName, $image1->encode('jpg'));
           
            $image = Image::make($image1)->resize(800, null, function ($constraint) {
                $constraint->aspectRatio();
            });
            $path = 'image_eMember' . '/' . $fileName;
            $image->save(public_path('storage' . DIRECTORY_SEPARATOR . $path));

            return $path;
        }
}
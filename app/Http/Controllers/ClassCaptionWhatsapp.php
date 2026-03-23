<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Carbon\Carbon;

class ClassCaptionWhatsapp extends Controller
{
    public function captionMemberTypeA($request)
    {
        $name = $request['name'];

        $whatsappMessage = "Hai ".$name.
        " Kartu membership Saloka punya kamu sudah jadi nih \n\n".
        " Sebelum pakai kartu membershipnya, baca syarat dan ketentuan dibawah ini dulu yuk! \n".
        "1. Membership Saloka berlaku hanya untuk 1 orang saja (tidak bisa digunakan untuk bergantian). \n". 
        "2. Membership Saloka berlaku s/d 30 November 2023. \n".
        "3. Membership merupakan tiket terusan yang dapat digunakan pada hari weekdays maupun weekend. \n". 
        "4. Membership Saloka tidak berlaku untuk tiket parkir, resto atau tenant dan toko merchandise. \n". 
        "5. Membership Saloka tidak berlaku jalur khusus untuk antrean wahana. \n". 
        "6. Membership Saloka berbentuk kartu digital. \n".
        "7. Membership Saloka bisa menggunakan diskon di partner Saloka. \n".
        "8. Membership yang menyalahi Syarat & Ketentuan yang berlaku akan dinonaktifkan dan tidak bisa diaktifkan kembali. \n".
        "Ayo, berpetualang di Saloka Theme Park dengan kartu membership Saloka! \n\n". 
        "Salam, Ceria Tiada Habisnya \n".
        "Untuk info lebih lanjut silahkan hubungi SaloMin di https://wa.me/6287838890777";
        return $whatsappMessage;
    }

    public function captionMemberTypeB($request)
    {
        // $name = $request['name'];
        // $intervalMonth = $request['interval_month'];

        // $whatsappMessage = "Hai ".$name. "\n".
        // "Kartu membership Saloka punya kamu sudah jadi nih \n\n".
        // "Sebelum pakai kartu membershipnya, baca syarat dan ketentuan dibawah ini dulu yuk!\n".
        // "1.Membership Saloka berlaku hanya untuk 1 orang saja (tidak dapat dipindahtangankan).\n". 
        // "2.Membership Saloka berlaku untuk 1 (satu) kali masuk dalam satu hari.\n".
        // "3.Membership Saloka berlaku s/d ".$intervalMonth." bulan terhitung sejak masaaktif kartu member.\n".
        // "4.Membership merupakan tiket terusan yang dapat digunakan pada hari weekdays maupun weekend.\n". 
        // "5.Membership Saloka dapat berlaku untuk promo di resto, tenant, dan toko merchandise, sesuai program yang sedang berlaku.\n".
        // "6.Membership Saloka bisa menggunakan diskon di partner Saloka, sesuai program yang sedang berlaku.\n".
        // "7.Membership Saloka berbentuk kartu digital.\n".
        // "8.Membership yang menyalahi Syarat & Ketentuan yang berlaku akan dinonaktifkan dan tidak bisa diaktifkan kembali.\n".
        // "9.Apabila terjadi kehilangan kartu, dapat menghubungi admin Saloka Theme Park untuk informasi lebih lanjut.\n".
        // "Ayo, berpetualang di Saloka Theme Park dengan kartu membership Saloka!\n\n".
        // "Salam, Ceria Tiada Habisnya \n". 
        // "Untuk info lebih lanjut silahkan hubungi SaloMin di https://wa.me/6287838890777";
        // return $whatsappMessage;

        $name = $request['name'];
        $intervalMonth = $request['interval_month'];

        $whatsappMessage = 
            "🌟 Hai *{$name}!* 🌟\n\n" .
            "Kartu *Membership Saloka* kamu sudah jadi nih! 🎉\n\n" .
            "Sebelum digunakan, yuk baca dulu *Syarat & Ketentuannya* di bawah ini 👇\n\n" .
            "1️⃣ Membership Saloka berlaku hanya untuk *1 orang saja* (tidak dapat dipindahtangankan).\n" .
            "2️⃣ Berlaku untuk *1 kali masuk per hari*.\n" .
            "3️⃣ Berlaku selama *{$intervalMonth} bulan* terhitung sejak masa aktif kartu.\n" .
            "4️⃣ Dapat digunakan *setiap hari* (weekday & weekend).\n" .
            "5️⃣ Dapat digunakan untuk *promo di resto, tenant, dan toko merchandise* sesuai program yang berlaku.\n" .
            "6️⃣ Dapat menggunakan *diskon di partner Saloka*, sesuai program yang berlaku.\n" .
            "7️⃣ Membership berbentuk *kartu digital*.\n" .
            "8️⃣ Membership yang menyalahi ketentuan akan *dinonaktifkan dan tidak bisa diaktifkan kembali*.\n" .
            "9️⃣ Jika kartu hilang, silakan hubungi *Admin Saloka Theme Park* untuk informasi lebih lanjut.\n\n" .
            "🎢 Ayo, berpetualang di *Saloka Theme Park* dengan kartu membership Saloka kamu!\n\n" .
            "Salam, *Ceria Tiada Habisnya!* 🌈\n\n" .
            "📱 Untuk info lebih lanjut, hubungi *SaloMin* di:\n" .
            "👉 https://wa.me/6287838890777";

        return $whatsappMessage;
    }
}

<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithHeadings;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Session;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

// Controller 
use App\Http\Controllers\Server;

class UserMember implements FromView, WithColumnFormatting,WithColumnWidths
{
    function __construct() {
    }

    public function view(): View
    {
        try {
            $c_Server = new Server();
            $urlServer=$c_Server->serverLink();

            $listUserMember_ =DB::table('users_client')
            ->select('users_client.id_user_client',
            'users_member.id_member',
            'users_client.name',
            'member.type_member',
            'users_member.interval_month',
            'users_member.start_member',
            'users_member.expied_member',
            'users_member.tot_payment',
            'users_client.email',
            'users_client.telephone',
            'users_client.date_of_birth',
            'users_client.address',
            'users_client.city',
            'users_client.province',
            'users_member.created_at',
            DB::raw("CONCAT('".$urlServer."',users_client.img_profile) as img_profile"),
            DB::raw("CONCAT('".$urlServer."',users_member.image_eMember) as image_eMember"))
            ->join('users_member','users_member.id_user_client','users_client.id_user_client')
            ->join('member','member.id_member','users_member.type_member');
           
            $list['data'] =  $listUserMember_->get();
     
            return view('export.UserMember', $list);
        } catch (\Exception $ex) {
            dd($ex);
            return response()->json($ex);
        }
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_TEXT,
            'B' => NumberFormat::FORMAT_TEXT,
            'C' => NumberFormat::FORMAT_TEXT,
            'D' => NumberFormat::FORMAT_TEXT,
            'E' => NumberFormat::FORMAT_TEXT,
            'F' => NumberFormat::FORMAT_TEXT,
            'G' => NumberFormat::FORMAT_TEXT,
            'H' => NumberFormat::FORMAT_TEXT,
            'I' => NumberFormat::FORMAT_TEXT,
            'J' => NumberFormat::FORMAT_TEXT,
            'K' => NumberFormat::FORMAT_TEXT,
            'L' => NumberFormat::FORMAT_TEXT,
            'M' => NumberFormat::FORMAT_TEXT,
            'N' => NumberFormat::FORMAT_TEXT,
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 4,
            'B' => 20,
            'C' => 20,
            'D' => 25,   
            'E' => 20, 
            'F' => 20, 
            'G' => 20, 
            'H' => 20,    
            'I' => 20,
            'J' => 20,
            'K' => 20,
            'L' => 20,
            'M' => 20,
            'N' => 20,
        ];
    }
}
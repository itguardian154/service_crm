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

class UserClient implements FromView, WithColumnFormatting,WithColumnWidths
{
    function __construct() {
    }

    public function view(): View
    {
        try {
            $c_Server = new Server();
            $urlServer=$c_Server->serverLink();

            $listUserClient_ =DB::table('users_client')
            ->select(
            'users_client.id_user_client',
            'users_client.name',
            'users_client.email',
            'users_client.telephone',
            'users_client.date_of_birth',
            'users_client.address',
            'users_client.city',
            'users_client.province',
            DB::raw("CONCAT('".$urlServer."',img_profile) as img_profile"),
            );
           
            $list['data'] =  $listUserClient_->get();
            return view('export.UserClient', $list);
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
           
        ];
    }
}
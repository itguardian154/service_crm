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

class AttendanceRecord implements FromView, WithColumnFormatting,WithColumnWidths
{
    function __construct($dateStart_,$dateEnd_) {
        $this->dateStart = $dateStart_;
        $this->dateEnd = $dateEnd_;
    }

    public function view(): View
    {
 
        $dateStart = $this->dateStart;
        $dateEnd = $this->dateEnd;
      
        try {

            $listAttendanceRecord_ = DB::table('attendace_record')
            ->select(
                'attendace_record.id_attendace',
                'attendace_record.id_user_client',
                'attendace_record.id_member',
                DB::raw('(select member.type_member from users_member inner join member on users_member.type_member=member.id_member where users_member.id_member=attendace_record.id_member limit 1 )as type_member'),
                'attendace_record.tanggal',
                'attendace_record.jam',
                'users_client.name',
                'users_client.email',
                'users_client.telephone',
                'users_client.date_of_birth',
                'users_client.address',
                'users_client.city',
                'users_client.province',
                DB::raw('(select users_member.interval_month from users_member where users_member.id_member=attendace_record.id_member limit 1 )as interval_month'),
                DB::raw('(select users_member.start_member from users_member where users_member.id_member=attendace_record.id_member limit 1 )as start_member'),
                DB::raw('(select users_member.expied_member from users_member where users_member.id_member=attendace_record.id_member limit 1 )as expired_member')
            )
            ->join('users_client', 'users_client.id_user_client', '=', 'attendace_record.id_user_client') 
            ->whereBetween('attendace_record.tanggal', [$dateStart . ' 00:00:00', $dateEnd . ' 23:59:59'])
            ->orderBy('attendace_record.created_at', 'asc')
            ->get();

        $list['data'] = $listAttendanceRecord_;

            return view('export.AttendaceRecord', $list);
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
            'O' => NumberFormat::FORMAT_TEXT,
            'P' => NumberFormat::FORMAT_TEXT,
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 4,
            'B' => 20,
            'C' => 20,
            'D' => 20,   
            'E' => 10, 
            'F' => 10, 
            'G' => 20, 
            'H' => 25,    
            'I' => 20,
            'J' => 20,
            'K' => 20,
            'L' => 20,
            'M' => 20,
            'N' => 20,
            'O' => 20,
            'P' => 20,
        ];
    }
}
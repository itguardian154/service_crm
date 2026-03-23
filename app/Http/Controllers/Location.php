<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class Location extends Controller
{
     public function getProvinces()
     {
        try {  
            $list = DB::table('provinces')
            ->select ('prov_id','prov_name')
            ->where('status','1')
            ->get();

            return response()->json([
                'status' => 'success',
                'data' => $list
                ]);
          
        } catch (\Exception $ex) {
            return $ex;
        }
     }

      public function getCities(Request $request)
      {
            $idProvinces = $request->id_province;
            try { 

                $list = DB::table('cities')
                ->select ('city_id','city_name')
                ->where('prov_id',$idProvinces)
                ->get();
    
                return response()->json([
                'status' => 'success',
                'data' => $list
                ]);

            } catch (\Exception $ex) {
                return $ex;
            }
      }
}

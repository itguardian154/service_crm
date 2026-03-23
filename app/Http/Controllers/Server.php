<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Server extends Controller
{
      // Generate ID Client
      public function serverLink()
      {
            $link = DB::table('server')
            ->select ('url')->where('is_active','1')
            ->first();
            $links=$link->url;

          return $links;
      }
}

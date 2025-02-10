<?php

namespace App\Http\Controllers\agency\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RegionDistrictController extends Controller
{
    public function getDistricts($id)
    {
        $districts = DB::table('districts')->where('region_id', $id)->get();
        return response()->json(['districts'=>$districts]);
    }
}
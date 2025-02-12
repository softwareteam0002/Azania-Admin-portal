<?php

namespace App\Http\Controllers\agency\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RegionDistrictController extends Controller
{
    public function getDistricts($id)
    {
        if (is_null($id)) {
            return response()->json(['error' => true, 'message' => 'Region is not selected!'], 400);
        }

        $districts = DB::table('districts')->where('region_id', $id)->get();
        return response()->json(['districts' => $districts]);
    }
}

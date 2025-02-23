<?php

namespace App\Modules\Map\Controller;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MapController extends Controller
{
    public function showMap()
    {
        return view('Map::map');
    }

    public function handleMapData(Request $request)
    {
        $longitude = $request->input('longitude');
        $latitude  = $request->input('latitude');
        $address   = $request->input('address');

        // Xử lý dữ liệu

        return response()->json([
            'longitude' => $longitude,
            'latitude'  => $latitude,
            'address'   => $address,
        ]);
    }
}

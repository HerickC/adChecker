<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ad;
use Illuminate\Support\Facades\Artisan;

class AdController extends Controller
{
    public function listAds(Request $request)
    {
        $ads = Ad::all();

        return response()->json($ads);
    }

    public function resetAllAds()
    {
        Artisan::call('migrate:refresh --seed');

        return response()->json([
            'status' => true
        ]);
    }
}
